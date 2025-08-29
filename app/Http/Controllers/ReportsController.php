<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\EmiSchedule;
use App\Models\Retailer;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{

  public function index(Request $request)
  {
    $query = Customer::with('retailer')
      ->withCount([
        'loans as active_loan_count' => function ($q) {
          $q->where('status', 1); // Active
        },
        'loans as inactive_loan_count' => function ($q) {
          $q->where('status', 0); // Inactive
        },
        'loans as closed_loan_count' => function ($q) {
          $q->where('status', 2); // Closed
        },
        'loans as total_loan_count'
      ]);

    if ($request->filled('customer_name')) {
      $query->where(function ($q) use ($request) {
        $q->where('customer_firstname', 'like', '%' . $request->customer_name . '%')
          ->orWhere('customer_lastname', 'like', '%' . $request->customer_name . '%');
      });
    }

    if ($request->filled('retailer_name')) {
      $query->whereHas('retailer', function ($q) use ($request) {
        $q->where('firstname', 'like', '%' . $request->retailer_name . '%')
          ->orWhere('lastname', 'like', '%' . $request->retailer_name . '%');
      });
    }

    if ($request->filled('loan_id')) {
      $query->whereHas('loans', function ($q) use ($request) {
        $q->where('loanID', 'like', '%' . $request->loan_id . '%');
      });
    }


    if ($request->filled('date_from')) {
      $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
      $query->whereDate('created_at', '<=', $request->date_to);
    }

    $emis = $query->latest()->paginate(10)->appends($request->all());

    return view('reports.emi_index', compact('emis'));
  }


  public function show_emi_report($id)
  {
    $customer = Customer::with('retailer')->findOrFail($id);

    $loans = Loan::with(['brand', 'product', 'emiSchedules'])
      ->where('customer_id', $id)
      ->get();

    return view('reports.show_emi_report', compact('customer', 'loans'));
  }


  public function emi_list(Request $request)
  {
    $query = EmiSchedule::with('customer');

    // Filter by customer name
    if ($request->filled('customer_name')) {
      $query->whereHas('customer', function ($q) use ($request) {
        $q->where('name', 'like', '%' . $request->customer_name . '%');
      });
    }

    // Filter by date range
    if ($request->filled('date_from')) {
      $query->whereDate('emi_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
      $query->whereDate('emi_date', '<=', $request->date_to);
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    $emis = $query->orderBy('emi_date', 'asc')
      ->paginate(10);

    return view('reports.emi_list', compact('emis'));
  }


  public function exportEMICSV(Request $request)
  {
    $query = DB::table('emi_schedule')
      ->leftJoin('customer', 'customer.id', '=', 'emi_schedule.customer_id')
      ->leftJoin('retailer', 'retailer.id', '=', 'customer.retailer_id')
      ->leftJoin('loans', 'loans.customer_id', '=', 'customer.id')
      ->leftJoin('transactions', 'transactions.customer_id', '=', 'customer.id')
      ->leftJoin('admin', 'admin.id', '=', 'transactions.staff_id')
      ->select(
      DB::raw("CONCAT(customer.customer_firstname, ' ', customer.customer_lastname) as customer_name"),
      'customer.mobile',
      'retailer.shop_name as retailer_company_name',
      'customer.father_name as parent_name',
      DB::raw("CONCAT(customer.address1) as address"),
      'emi_schedule.status',
      DB::raw("CONCAT(admin.firstname, ' ', admin.lastname) as accept_by"),
      'transactions.by as accept_role',
      'emi_schedule.amount',
      'emi_schedule.emi_date',
      'emi_schedule.late_fees'
    )
      ->groupBy(
      'emi_schedule.id',
      'customer.customer_firstname',
      'customer.customer_lastname',
      'customer.mobile',
      'retailer.shop_name',
      'customer.father_name',
      'customer.address1',
      'customer.address2',
      'customer.city_id',
      'emi_schedule.status',
      'admin.firstname',
      'admin.lastname',
      'transactions.by',
      'emi_schedule.amount',
      'emi_schedule.emi_date',
      'emi_schedule.late_fees'
    );

    // Apply Filters
    if ($request->filled('customer_name')) {
      $query->where(function ($q) use ($request) {
        $q->where('customer.customer_firstname', 'like', '%' . $request->customer_name . '%')
          ->orWhere('customer.customer_lastname', 'like', '%' . $request->customer_name . '%');
      });
    }

    if ($request->filled('retailer_name')) {
      $query->where(function ($q) use ($request) {
        $q->where('retailer.firstname', 'like', '%' . $request->retailer_name . '%')
          ->orWhere('retailer.lastname', 'like', '%' . $request->retailer_name . '%');
      });
    }

    if ($request->filled('loan_id')) {
      $query->where('loans.loanID', 'like', '%' . $request->loan_id . '%');
    }

    if ($request->filled('date_from')) {
      $query->whereDate('emi_schedule.emi_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
      $query->whereDate('emi_schedule.emi_date', '<=', $request->date_to);
    }

    $data = $query->get();

    $filename = "emi_report_" . now()->format('Ymd_His') . ".csv";

    $headers = [
      "Content-type" => "text/csv",
      "Content-Disposition" => "attachment; filename={$filename}",
      "Pragma" => "no-cache",
      "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
      "Expires" => "0"
    ];

    $callback = function () use ($data) {
      $handle = fopen('php://output', 'w');

      // CSV Header
      fputcsv($handle, [
        'Customer Name', 'Mobile No', 'Retailer Company Name', 'Parent Name',
        'Address', 'Status', 'Accept By', 'Accept Role',
        'Amount', 'EMI Date', 'Late Fees', 'Total Paid Amount'
      ]);

      foreach ($data as $row) {
        // Only show accepted_by and accepted_role if EMI is paid
        $acceptedBy = '';
        $acceptedRole = '';

        if (strtolower($row->status) === 'paid') {
          $acceptedBy = $row->accept_by ?? '';
          $acceptedRole = $row->accept_role ?? '';
        }

        // Calculate Total Paid Amount for this EMI row
        $totalPaidAmount = ($row->amount ?? 0) + ($row->late_fees ?? 0);

        fputcsv($handle, [
          $row->customer_name ?? 'N/A',
          $row->mobile ?? 'N/A',
          $row->retailer_company_name ?? 'N/A',
          $row->parent_name ?? 'N/A',
          $row->address ?? 'N/A',
          $row->status ?? 'N/A',
          $acceptedBy,
          $acceptedRole,
          $row->amount ?? '',
          $row->emi_date ?? 'N/A',
          $row->late_fees ?? '0',
          $totalPaidAmount,
        ]);
      }

      fclose($handle);
    };

    return Response::stream($callback, 200, $headers);
  }


  public function exportEMIListCSV(Request $request)
  {
    $query = DB::table('emi_schedule')
      ->leftJoin('customer', 'customer.id', '=', 'emi_schedule.customer_id')
      ->leftJoin('loans', 'loans.id', '=', 'emi_schedule.loan_id')
      ->leftJoin('retailer', 'retailer.id', '=', 'customer.retailer_id')
      ->leftJoin('transactions', function ($join) {
        $join->on('transactions.customer_id', '=', 'customer.id')
          ->where('transactions.status', '=', 1); // assuming status 1 means 'Paid'
      })
      ->leftJoin('admin', 'admin.id', '=', 'transactions.staff_id')
      ->leftJoin('roles', 'roles.id', '=', 'admin.role_id')
      ->select(
      DB::raw("CONCAT(customer.customer_firstname, ' ', customer.customer_lastname) as customer_name"),
      'customer.mobile',
      'customer.father_name',
      'retailer.shop_name as retailer_company_name',
      'customer.address1 as address',
      'emi_schedule.status',
      DB::raw("CONCAT(admin.firstname, ' ', admin.lastname) as accepted_by"),
      'roles.name as accepted_role',
      'emi_schedule.amount',
      'emi_schedule.emi_date',
      'emi_schedule.late_fees',
      DB::raw('(SELECT SUM(transactions.amount) FROM transactions WHERE transactions.customer_id = customer.id AND transactions.status = 1) as total_paid_amount')
    );

    // Apply filters if provided
    if ($request->filled('customer_name')) {
      $query->where(function ($q) use ($request) {
        $q->where('customer.customer_firstname', 'like', '%' . $request->customer_name . '%')
          ->orWhere('customer.customer_lastname', 'like', '%' . $request->customer_name . '%');
      });
    }

    if ($request->filled('date_from')) {
      $query->whereDate('emi_schedule.emi_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
      $query->whereDate('emi_schedule.emi_date', '<=', $request->date_to);
    }

    if ($request->filled('status')) {
      $query->where('emi_schedule.status', $request->status);
    }

    $records = $query->get();

    $filename = "EmiListReport_" . now()->format('Ymd_His') . ".csv";

    $headers = [
      "Content-type" => "text/csv",
      "Content-Disposition" => "attachment; filename={$filename}",
      "Pragma" => "no-cache",
      "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
      "Expires" => "0"
    ];

    $callback = function () use ($records) {
      $handle = fopen('php://output', 'w');

      // CSV Header
      fputcsv($handle, [
        'S.No',
        'Customer Name',
        'Mobile No',
        'Father Name',
        'Retailer Company Name',
        'Address',
        'Status',
        'Accepted By',
        'Accept Role',
        'Amount',
        'EMI Date',
        'Late Fees',
        'Total Paid Amount'
      ]);

      $i = 1;
      foreach ($records as $record) {

        // Only show accepted_by and accepted_role if EMI is paid
        $acceptedBy = '';
        $acceptedRole = '';

        if (strtolower($record->status) === 'paid') {
          $acceptedBy = $record->accepted_by ?? '';
          $acceptedRole = $record->accepted_role ?? '';
        }

        fputcsv($handle, [
          $i++,
          $record->customer_name ?? 'N/A',
          $record->mobile ?? 'N/A',
          $record->father_name ?? 'N/A',
          $record->retailer_company_name ?? 'N/A',
          $record->address ?? 'N/A',
          $record->status ?? 'N/A',
          $acceptedBy,
          $acceptedRole,
          $record->amount ?? 0,
          $record->emi_date ?? 'N/A',
          $record->late_fees ?? 0,
          $record->total_paid_amount ?? 0,
        ]);
      }


      fclose($handle);
    };

    return Response::stream($callback, 200, $headers);
  }

  public function retailer_report(Request $request)
  {
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    $query = \DB::table('retailer')
      ->leftJoin('customer', 'retailer.id', '=', 'customer.retailer_id')
      ->leftJoin('loans', 'customer.id', '=', 'loans.customer_id')
      ->select(
      'retailer.id',
      'retailer.firstname',
      'retailer.lastname',
      'retailer.shop_name',
      \DB::raw('SUM(loans.sell_price) as total_landing'),
      \DB::raw('SUM(loans.downpayment) as total_downpayment'),
      \DB::raw('SUM(loans.disburse_amount) as find_payment'),
      \DB::raw('COUNT(loans.id) as new_phone')
    );

    if ($startDate && $endDate) {
      $query->whereBetween('loans.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    }

    $retailers = $query
      ->groupBy('retailer.id', 'retailer.firstname', 'retailer.lastname', 'retailer.shop_name')
      ->get();

    return view('reports.retailer_report', compact('retailers', 'startDate', 'endDate'));
  }


  public function retailerReportExport(Request $request)
  {
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    $query = DB::table('retailer')
      ->leftJoin('customer', 'retailer.id', '=', 'customer.retailer_id')
      ->leftJoin('loans', 'customer.id', '=', 'loans.customer_id')
      ->select(
      'retailer.firstname',
      'retailer.lastname',
      'retailer.shop_name',
      DB::raw('SUM(loans.sell_price) as total_landing'),
      DB::raw('SUM(loans.downpayment) as total_downpayment'),
      DB::raw('SUM(loans.disburse_amount) as find_payment'),
      DB::raw('COUNT(loans.id) as new_phone')
    );

    if ($startDate && $endDate) {
      $query->whereBetween('loans.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    }

    $retailers = $query
      ->groupBy('retailer.id', 'retailer.firstname', 'retailer.lastname', 'retailer.shop_name')
      ->get();

    $filename = "retailer_report_" . date('Y-m-d_H-i-s') . ".csv";

    $headers = [
      "Content-type" => "text/csv",
      "Content-Disposition" => "attachment; filename=$filename",
      "Pragma" => "no-cache",
      "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
      "Expires" => "0"
    ];

    $columns = ['Retailer Name', 'Shop Name', 'Total Landing (₹)', 'Total Downpayment (₹)', 'DP Pending', 'EMI Paid', 'DP Paid', 'Find Payment (₹)', 'New Phone'];

    $callback = function () use ($retailers, $columns) {
      $file = fopen('php://output', 'w');
      fputcsv($file, $columns);

      foreach ($retailers as $retailer) {
        fputcsv($file, [
          $retailer->firstname . ' ' . $retailer->lastname,
          $retailer->shop_name,
          number_format($retailer->total_landing ?? 0, 2),
          number_format($retailer->total_downpayment ?? 0, 2),
          '', // DP Pending
          '', // EMI Paid
          '', // DP Paid
          number_format($retailer->find_payment ?? 0, 2),
          $retailer->new_phone
        ]);
      }

      fclose($file);
    };

    return response()->stream($callback, 200, $headers);
  }

}
