<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RetailerResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        return view('retailerLogin.auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::broker('retailer')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($retailer, $password) {
                $retailer->password = Hash::make($password);
                $retailer->setRememberToken(Str::random(60));
                $retailer->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('retailer.loginForm')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
