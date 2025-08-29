<?php

namespace App\Http\Controllers;

use App\Models\Retailer;
use App\Models\States;
use App\Models\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class RetailerController extends Controller
{
  public function index()
  {
    $retailers = Retailer::paginate(10);
    return view('retailer.index', compact('retailers'));
  }

  public function create()
  {
    $states = States::all();
    return view('retailer.create', compact('states'));
  }

  public function getCities($state_id)
  {
    $cities = Cities::where('state_id', $state_id)->get();

    return response()->json($cities);
  }

  public function store(Request $request)
  {

    $validated = $request->validate([
      'firstname' => 'required|string',
      'lastname' => 'required|string',
      'shop_name' => 'required|string',
      'address1' => 'required|string',
      'address2' => 'nullable|string',
      'state_id' => 'required|integer',
      'city_id' => 'required|integer',
      'zipcode' => 'required|integer',
      'mobile_no' => 'required|string|min:10',
      'email' => 'required|email|unique:retailer,email',
      'password' => 'required|min:8',
    ]);

    $validated['password'] = Hash::make($validated['password']);

    Retailer::create($validated);

    return redirect()->route('admin.retailerIndex')->with('success', 'Retailer created successfully.');
  }

  public function edit($id)
  {
    $retailer = Retailer::findOrFail($id);
    $states = States::all();
    return view('retailer.edit', compact('retailer', 'states'));
  }

  public function update(Request $request, $id)
  {
    $retailer = Retailer::findOrFail($id);

    $validated = $request->validate([
      'firstname' => 'required|string',
      'lastname' => 'required|string',
      'shop_name' => 'required|string',
      'address1' => 'required|string',
      'address2' => 'nullable|string',
      'state_id' => 'required|integer',
      'city_id' => 'required|integer',
      'zipcode' => 'required|integer',
      'mobile_no' => 'required|string|min:10',
      'email' => 'required|email|unique:retailer,email,' . $retailer->id,
      'password' => 'nullable|min:8',
    ]);

    if ($request->filled('password')) {
      $validated['password'] = Hash::make($validated['password']);
    } else {
      unset($validated['password']);
    }

    $retailer->update($validated);

    return redirect()->route('admin.retailerIndex')->with('success', 'Retailer updated successfully.');
  }

  public function destroy($id)
  {
    $retailer = Retailer::findOrFail($id);
    $retailer->delete();

    return redirect()->route('admin.retailerIndex')->with('success', 'Retailer deleted successfully.');
  }


}
