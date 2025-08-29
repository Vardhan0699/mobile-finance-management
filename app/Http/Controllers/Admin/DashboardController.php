<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retailer;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Admin;
use App\Models\EmiSchedule;
use App\Models\Role;
use App\Models\Pincode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;


class DashboardController extends Controller
{
  public function dashboard()
  {

    $products = Product::count();   
    $brands = Brand::count();
    $admins = Admin::count();    
    $retailer = Retailer::count();
    $retailers = Retailer::paginate(5);

    $admin = Auth::guard('admin')->user()->id;

    // dd($admin);

    // Get loan_ids assigned to this admin from recovery table
    $loanIds = \DB::table('recovery')
      ->where('staff_id', $admin)
      ->pluck('loan_id');

    // Step 2: Get all EMI schedules for those loan_ids with status 'recovery' or was_recovery = 1
    $allRecoveryEmis = EmiSchedule::whereIn('loan_id', $loanIds)
      ->where(function ($query) {
        $query->where('status', 'recovery')
          ->orWhere('was_recovery', 1);
      })
      ->get();

    $totalRecovery = $allRecoveryEmis->sum(function ($emi) {
      return $emi->amount + ($emi->late_fees ?? 0);
    });

    $collectedRecovery = $allRecoveryEmis
      ->where('status', 'paid')
      ->where('was_recovery', true)
      ->sum(function ($emi) {
        return $emi->amount + ($emi->late_fees ?? 0);
      });

    $pendingRecovery = $totalRecovery - $collectedRecovery;

    return view('admin.dashboard', compact(
      'retailers', 'products', 'brands', 'retailer', 'admins',
      'totalRecovery', 'collectedRecovery', 'pendingRecovery'
    ));
  }

  public function admin_list()
  {
    $currentAdminId = auth()->guard('admin')->id(); // assuming you're using 'admin' guard
    $admins = Admin::with('role')->where('id', '!=', $currentAdminId)->paginate(10);
    // $admins = Admin::all();

    // $currentAdmin = Auth::guard('admin')->user();

    return view('admin.index',compact('admins'));
  }

//   public function destroy($id)
//   {
//     $admin = Admin::find($id);
//     if (!$admin) {
//       return redirect()->back()->withErrors(['error' => 'Admin not found.']);
//     }

//     $admin->delete();

//     return redirect()->route('admin.dashboard')->with('success', 'Admin deleted successfully.');
//   }


public function destroy($id)
{
    if (Auth::guard('admin')->id() == $id) {
        return redirect()->back()->withErrors(['error' => 'You cannot delete your own account while logged in.']);
    }

    $admin = Admin::find($id);
    if (!$admin) {
        return redirect()->back()->withErrors(['error' => 'Admin not found.']);
    }

    $admin->delete();

    return redirect()->route('admin.dashboard')->with('success', 'Admin deleted successfully.');
}


  public function create(Request $request)
  {
    $roles=Role::where('id', '!=', 1)->get();
    $pincodes=Pincode::all();
    return view('admin.create',compact('roles','pincodes'));
  }

  public function register(Request $request)
  {
    // Validate the form input
    $request->validate([
      'firstname' => 'required|string|max:255',      
      'lastname' => 'required|string|max:255',
      'mobile_no' => 'required|string|max:10',
      'email' => 'required|email|unique:admin,email',
      'password' => 'required|string|min:8|confirmed',
      'role_id'=>'required',
      'zipcode' => 'nullable|array',
    ]);

    $admin = Admin::create([
      'firstname'  => $request->firstname,
      'lastname'   => $request->lastname,
      'mobile_no'  => $request->mobile_no,
      'email'      => $request->email,
      'role_id'    => $request->role_id,
      'zipcode'	   => $request->zipcode ? json_encode($request->zipcode) : null, // store array as json
      'password'   => Hash::make($request->password),
    ]);

    // Log in the new admin using custom helper
    admin_login($admin);

    return redirect()->route('admin.adminList')->with('success', 'Admin registered and logged in successfully.');
  }

  public function edit($id)
  {
    $admin = Admin::find($id);
    $roles = Role::all();
    $pincodes = Pincode::all();
    return view('admin.staff_edit', compact('admin','roles','pincodes'));
  }

  public function update(Request $request, $id)
  {
    // Validate the form input
    $request->validate([
      'firstname' => 'required|string|max:255',
      'lastname' => 'required|string|max:255',
      'mobile_no' => 'required|string|max:10',
      'email' => 'required|email|unique:admin,email,' . $id,
      'password' => 'nullable|string|min:8|confirmed',
      'role_id' => 'required',
      'zipcode' => 'nullable|array',
    ]);

    $admin = Admin::findOrFail($id);

    $admin->firstname = $request->firstname;
    $admin->lastname = $request->lastname;
    $admin->mobile_no = $request->mobile_no;
    $admin->email = $request->email;
    $admin->role_id = $request->role_id;
    $admin->zipcode = $request->zipcode ? json_encode($request->zipcode) : null;

    if ($request->password) {
      $admin->password = Hash::make($request->password);
    }

    $admin->save();

    return redirect()->route('admin.adminList')->with('success', 'Admin updated successfully.');
  }

  public function exportAdminCSV()
  {
    $admins = Admin::all();
    
    $roles = Role::all();

    $filename = "admins_" . now()->format('Ymd_His') . ".csv";

    $headers = [
      "Content-type" => "text/csv",
      "Content-Disposition" => "attachment; filename={$filename}",
      "Pragma" => "no-cache",
      "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
      "Expires" => "0"
    ];

    $callback = function () use ($admins) {
      $handle = fopen('php://output', 'w');

      // Header row
      fputcsv($handle, [
        'ID',
        'First Name',
        'Last Name',
        'Mobile',
        'Email',
        'Role ID',
        'Zipcode',
        'Created At',
      ]);

      foreach ($admins as $admin) {
        fputcsv($handle, [
          $admin->id,
          $admin->firstname,
          $admin->lastname,
          $admin->mobile_no,
          $admin->email,
          $admin->role->name,
          $admin->zipcode,
          $admin->created_at,
        ]);
      }

      fclose($handle);
    };

    return Response::stream($callback, 200, $headers);
  }



}
