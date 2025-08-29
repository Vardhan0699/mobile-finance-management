<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\EmiSchedule;
use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Support\Facades\Response;

class TransactionController extends Controller
{
  public function index(Request $request)
  {
    $users = Transaction::select('by')->distinct()->pluck('by');

    $query = Transaction::with('staff')->orderBy('id', 'desc');

    if ($request->has('by') && $request->by != '') {
      $query->where('by', $request->by);
    }

    if ($request->has('status') && in_array($request->status, ['paid', 'unpaid'])) {
      $query->where('status', $request->status);
    }

    // New filters
    if ($request->filled('mobile')) {
      $query->where('mobile_no', 'like', '%' . $request->mobile . '%');
    }

    if ($request->filled('date_from')) {
      $query->whereDate('trans_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
      $query->whereDate('trans_date', '<=', $request->date_to);
    }

    $summaryQuery = clone $query;

    $transactions = $query->paginate(10)->withQueryString();

    $totalAmount = $summaryQuery->sum('amount');
    $paidAmount = (clone $summaryQuery)->where('status', 'paid')->sum('amount');
    $unpaidAmount = (clone $summaryQuery)->where('status', 'unpaid')->sum('amount');

    return view('transactions.index', compact('transactions', 'users', 'totalAmount', 'paidAmount', 'unpaidAmount'));
  }


  public function store(Request $request)
  {
    $today = now()->toDateString();

    $request->validate([
      'amount' => 'required|numeric|min:0',
      'transactionId' => 'nullable|string|unique:transactions,trans_id',
      'trans_date' => [
        'required',
        'date',
        function ($attribute, $value, $fail) {
          $today = now()->toDateString();
          if (auth()->guard('admin')->user()->role !== 'super admin' && $value > $today) {
            $fail('You are not allowed to select a future date.');
          }
        }
      ],

    ]);

    $transaction = new Transaction();
    $transaction->trans_id = $request->transactionId;
    $transaction->amount = $request->amount;
    $transaction->trans_date = $request->trans_date;
    $transaction->status = 'unpaid';

    if (Auth::guard('admin')->check()) {
      $admin = Auth::guard('admin')->user();

      // \Log::info('Admin ID: ' . $admin->id);
      //  \Log::info('Admin Role ID: ' . $admin->role_id);
      // \Log::info('Admin Role Name: ' . optional($admin->role)->name);

      $transaction->by = optional($admin->role)->name ?? 'Unknown Role';
      $transaction->staff_id = $admin->id;
    } else {
      $transaction->staff_id = null;
      $transaction->by = 'Unknown Role';
    }

    $transaction->save();

    return response()->json(['success' => 'Transaction added successfully.']);
  }

  public function edit($id)
  {
    $transaction = Transaction::findOrFail($id);
    return response()->json($transaction);
  }

  public function update(Request $request, $id)
  {
    $transaction = Transaction::findOrFail($id);

    $validator = Validator::make($request->all(), [
      'amount' => 'required|numeric|min:0',
      'transactionId' => 'nullable|string|unique:transactions,trans_id,' . $transaction->id,
      'trans_date' => 'nullable|date',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $transaction->update([
      'amount' => $request->amount,
      'trans_id' => $request->transactionId,
      'trans_date' => $request->trans_date,
    ]);

    return response()->json(['success' => 'Transaction updated successfully.']);
  }

  public function destroy($id)
  {
    $transaction = Transaction::findOrFail($id);
    $transaction->delete();

    return response()->json(['success' => 'Transaction deleted successfully.']);
  }

  public function verifyCustomer(Request $request)
  {
    $search = $request->get('search');

    $customers = Customer::select(
      'customer.id',
      'customer.customer_firstname',
      'customer.customer_lastname',
      'customer.mobile',
      'loans.loanID',
      'loans.emi',
      'loans.status',
      'loans.id as loan_id'
    )
      ->leftJoin('loans', 'loans.customer_id', '=', 'customer.id')
      ->where(function ($query) use ($search) {
        $query->where('customer.mobile', $search)
          ->orWhere('customer.customer_firstname', 'like', "%{$search}%")
          ->orWhere('loans.loanID', 'like', "%{$search}%");
      })
      ->where('customer.status', 1) // Optional: Only Active Customers
      ->get();

    if ($customers->isEmpty()) {
      return response()->json(['success' => false]);
    }

    return response()->json([
      'success' => true,
      'customers' => $customers
    ]);
  }

  public function getCustomerEmiDetails($loanId)
  {
    $loan = Loan::where('loanID', $loanId)->first();

    if (!$loan) {
      return response()->json(['success' => false, 'message' => 'Loan not found.']);
    }

    $customer = Customer::find($loan->customer_id);

    if (!$customer) {
      return response()->json(['success' => false, 'message' => 'Customer not found.']);
    }

    $emiSchedule = EmiSchedule::where('loan_id', $loan->loanID)->get();

    foreach ($emiSchedule as $emi) {
      if ($emi->status === 'recovery') {
        $emi->late_fees = 195;
        $emi->save();
      }
    }

    $transaction = Transaction::where('customer_id', $customer->id)
      ->where('status', 'unpaid')
      ->latest()
      ->first();

    return response()->json([
      'success' => true,
      'customer' => $customer,
      'loan' => $loan,
      'emi_schedule' => $emiSchedule,
      'transaction_id' => $transaction->id ?? null,
      'trans_date' => $transaction->trans_date ?? null,
    ]);
  }


  public function getTransDate($id)
  {
    $transaction = Transaction::find($id);

    if (!$transaction) {
      return response()->json(['success' => false, 'message' => 'Transaction not found.']);
    }

    return response()->json([
      'success' => true,
      'trans_date' => $transaction->trans_date ? $transaction->trans_date->format('Y-m-d') : null,
    ]);
  }

  public function markEmiPaid(Request $request, $id)
  {
    $request->validate([
      'mobile_no' => 'required|regex:/^[6-9]\d{9}$/',
      'transaction_id' => 'nullable|integer',
    ]);

    $emi = EmiSchedule::find($id);

    if (!$emi) {
      return response()->json(['success' => false, 'message' => 'EMI not found.']);
    }

    if ($emi->status == 'paid') {
      return response()->json(['success' => false, 'message' => 'This EMI is already marked as paid.']);
    }

    $loan = Loan::where('loanID', $emi->loan_id)->first();

    if (!$loan) {
      return response()->json(['success' => false, 'message' => 'Loan not found for this EMI.']);
    }

    $customer = Customer::where('id', $loan->customer_id)
      ->where('mobile', $request->mobile_no)
      ->first();

    if (!$customer) {
      return response()->json([
        'success' => false,
        'message' => 'Mobile number does not match any customer for this loan.'
      ]);
    }

    $transaction = Transaction::where('id', $request->transaction_id)
      ->where('status', 'unpaid')
      ->first();

    if (!$transaction) {
      return response()->json([
        'success' => false,
        'message' => 'Transaction not found or already paid.'
      ]);
    }

    $emiDate = Carbon::parse($emi->emi_date)->toDateString();
    $transDate = Carbon::parse($transaction->trans_date)->toDateString();

    $emiAmount = intval(round($emi->amount * 100));
    $transactionAmount = intval(round($transaction->amount * 100));

    if ($emiDate === $transDate) {
      $emi->late_fees = null;
      $emi->was_recovery = 0;

      if (abs($transactionAmount - $emiAmount) > 500) {
        return response()->json([
          'success' => false,
          'message' => 'Transaction amount differs by more than ₹5 from EMI amount.'
        ]);
      }
    } else {
      $expectedAmount = $emiAmount + intval(round($emi->late_fees ?? 0) * 100);

      if (abs($transactionAmount - $expectedAmount) > 500) {
        return response()->json([
          'success' => false,
          'message' => 'Transaction amount differs by more than ₹5 from EMI amount including late fee.'
        ]);
      }
    }

    $emi->status = 'paid';
    $emi->save();

    $transaction->update([
      'status' => 'paid',
      'paid_date' => $transaction->trans_date,
      'mobile_no' => $request->mobile_no,
      'customer_id' => $customer->id,
    ]);

    // ✅ Check if all EMIs are paid for this loan
    $totalEmis = EmiSchedule::where('loan_id', $emi->loan_id)->count();
    $paidEmis = EmiSchedule::where('loan_id', $emi->loan_id)->where('status', 'paid')->count();

    if ($totalEmis > 0 && $totalEmis === $paidEmis) {
      $loan->status = 2;
      $loan->save();
    }

    return response()->json(['success' => true]);
  }


  public function exportTransactionCSV()
  {
    $transactions = Transaction::with(['staff', 'customer'])->get();

    $filename = "transactions_" . now()->format('Ymd_His') . ".csv";

    $headers = [
      "Content-type" => "text/csv",
      "Content-Disposition" => "attachment; filename={$filename}",
      "Pragma" => "no-cache",
      "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
      "Expires" => "0"
    ];

    $callback = function () use ($transactions) {
      $handle = fopen('php://output', 'w');

      // Header row
      fputcsv($handle, [
        'Transaction ID',
        'Customer Name',
        'Staff Name',
        'Amount',
        'Transaction Ref',
        'Transaction Date',
        'Status',
        'Mobile No',
        'By',
        'Paid Date',
      ]);

      foreach ($transactions as $transaction) {
        fputcsv($handle, [
          $transaction->id,
          $transaction->customer ? $transaction->customer->customer_firstname . ' ' . $transaction->customer->customer_lastname : 'N/A',
          $transaction->staff ? $transaction->staff->firstname . ' ' . $transaction->staff->lastname : 'N/A',
          $transaction->amount,
          $transaction->trans_id,
          $transaction->trans_date,
          $transaction->status,
          $transaction->mobile_no,
          $transaction->by,
          $transaction->paid_date,
        ]);
      }

      fclose($handle);
    };

    return Response::stream($callback, 200, $headers);
  }


}
