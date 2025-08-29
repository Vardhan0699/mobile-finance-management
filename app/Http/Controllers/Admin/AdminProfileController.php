<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class AdminProfileController extends Controller
{
  public function showProfile($id)
  {
    $admin = Admin::find($id);

    if (!$admin) {
      return abort(404, 'Admin not found');
    }

    Auth::guard('admin')->login($admin);

    return view('admin.admin-profile', compact('admin'));
  }
  
  public function profileUpdate(Request $request)
  {
    $request->validate([
      'firstname' => 'required|string|max:255',
      'lastname' => 'required|string|max:255',
      'mobile_no' => 'required|string|max:10',
    ]);

    $admin = Auth::guard('admin')->user();

    if (!$admin) {
      return abort(404, 'Admin not authenticated');
    }

    $admin->firstname = $request->input('firstname');
    $admin->lastname = $request->input('lastname');
    $admin->mobile_no = $request->input('mobile_no');
    $admin->save();

    return redirect()->back()->with('success', 'Profile updated successfully.');
  }



}
