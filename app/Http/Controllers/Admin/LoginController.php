<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Pincode;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
  public function showLoginForm()
  {
    if (session()->has('admin_id')) {
      return redirect()->route('admin.dashboard');
    }
    return view('admin.login');
  }


  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required'
    ]);
    // dd($request->email);
    $admin = Admin::where('email', $request->email)->first();

    if ($admin && Hash::check($request->password, $admin->password)) {
      admin_login($admin);

      return redirect()->route('admin.profile', ['id' => $admin->id]);
    }

    return back()->withErrors(['email' => 'Invalid credentials.']);
  }

  public function logout()
  {

    Auth::logout();

    session()->invalidate();

    session()->regenerateToken();

    return redirect()->route('retailerLogin');
  }

  






}
