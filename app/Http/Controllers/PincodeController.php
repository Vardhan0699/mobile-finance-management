<?php

namespace App\Http\Controllers;

use App\Models\Pincode;
use Illuminate\Http\Request;

class PincodeController extends Controller
{
    public function index()
    {
        $pincodes = Pincode::paginate(10);
        return view('pincode.index', compact('pincodes'));
    }

    public function create()
    {
        $pincode = Pincode::all();
        return view('pincode.create', compact('pincode'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'pincode' => ['required', 'digits:6', 'unique:approve_pincode,pincode'],
        ], [
            'pincode.required' => 'The pincode field is required.',
            'pincode.digits' => 'Pincode must be exactly 6 digits.',
            'pincode.unique' => 'This pincode already exists.',
        ]);


        Pincode::create($request->only('pincode'));

        return redirect()->back()->with('success', 'Pincode added successfully.');
    }

    public function edit($id)
    {
        $pincode = Pincode::findOrFail($id);
        $pincodes = Pincode::all();
        return view('pincode.index', compact('pincode', 'pincodes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pincode' => 'required|digits:6'
        ]);

        $pincode = Pincode::findOrFail($id);
        $pincode->pincode = $request->pincode;
        $pincode->save();

        return redirect()->route('admin.pincodeIndex')->with('success', 'Pincode updated successfully.');
    }

    public function destroy($id)
    {
        $pincode = Pincode::findOrFail($id);
        $pincode->delete();

        return redirect()->back()->with('success', 'Pincode deleted successfully.');
    }


}
