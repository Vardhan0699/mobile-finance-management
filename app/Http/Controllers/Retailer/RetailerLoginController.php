<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Retailer;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Customer;


class RetailerLoginController extends Controller
{
  public function showLoginForm()
  {
    if (session()->has('retailer_id')) {
      return redirect()->route('retialerLogin.dashboard');
    }
    return view('retailerLogin.auth.login');
  }

  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    $retailer = Retailer::where('email', $request->email)->first();

    if ($retailer && Hash::check($request->password, $retailer->password)) {

      session(['retailer_id' => $retailer->id]);

      return redirect()->route('retailer.profile', ['id' => $retailer->id]);
    }

    return back()->withErrors(['email' => 'Invalid email or password']);
  }

  public function logout(Request $request)
  {
    Auth::guard('retailer')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
  }

  public function dashboard()
  {
    $retailerId = session('retailer_id');

    $customers = Customer::where('retailer_id', $retailerId)->paginate(5);
    $customerCount = Customer::where('retailer_id', $retailerId)->count();
    $products = Product::count();
    $brands = Brand::count();

    return view('retailerLogin.dashboard', compact('products', 'brands', 'customers', 'customerCount'));
  }

  
  
}
