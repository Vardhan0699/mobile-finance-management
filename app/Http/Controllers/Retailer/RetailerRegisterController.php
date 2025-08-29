<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retailer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RetailerRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('retailerLogin.auth.register');
    }

    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100|unique:retailer',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Save retailer
        $retailer = new Retailer();
        $retailer->email = $request->email;
        $retailer->password = Hash::make($request->password);
        $retailer->status = 1; // Active by default, change as needed
        $retailer->save();

        // Auto login retailer after registration
        Auth::guard('retailer')->login($retailer);

        return redirect()->route('retailer.dashboard')->with('success', 'Registration successful!');
    }
}
