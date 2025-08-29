<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EMICalculatorController extends Controller
{
    public function index()
    {
        return view('retailerLogin.emi_calculator');
    }
}
