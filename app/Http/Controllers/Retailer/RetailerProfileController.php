<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Retailer;
use App\Models\States;
use App\Models\Cities;


class RetailerProfileController extends Controller
{
  public function showProfile($id)
  {
    $retailer = Retailer::find($id);

    if (!$retailer) {
      return abort(404, 'Rertailer not found');
    }

    Auth::guard('retailer')->login($retailer);

    $states = States::all();
    $cities = [];

    if (old('state_id', $retailer->state_id)) {
      $cities = Cities::where('state_id', old('state_id', $retailer->state_id))->get();
    }

    return view('retailerLogin.profile', compact('retailer','states','cities'));
  }



  public function profileUpdate(Request $request)
  {
    $retailer = Auth::guard('retailer')->user();

    $request->validate([
      'firstname' => 'required|string|max:255',
      'lastname' => 'required|string|max:255',
      'mobile_no' => [
        'required',
        'string',
        'max:15',
        Rule::unique('retailer', 'mobile_no')->ignore($retailer->id),
      ],
      'shop_name' => 'required|string|max:100',
      'address1' => 'required|string|max:255',
      'address2' => 'nullable|string|max:255',
      'state_id' => 'required|integer',
      'city_id' => 'required|integer',
      'zipcode' => 'required|string|max:10',
      'email' => 'required|email|max:255',
    ]);

    $retailer->firstname = $request->firstname;
    $retailer->lastname = $request->lastname;
    $retailer->mobile_no = $request->mobile_no;
    $retailer->shop_name = $request->shop_name;
    $retailer->address1 = $request->address1;
    $retailer->address2 = $request->address2;
    $retailer->state_id = $request->state_id;
    $retailer->city_id = $request->city_id;
    $retailer->zipcode = $request->zipcode;
    $retailer->email = $request->email;

    $retailer->save();

    return redirect()->back()->with('success', 'Profile updated successfully.');
  }



}
