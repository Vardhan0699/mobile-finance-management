<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('created_at', 'asc')->get();
        return view('role.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->route('admin.role_index')
            ->with([
                'success' => 'Role created successfully!',
                'title' => 'Success!'
            ]);
    }

    public function destroy($id)
    {
        $roles = Role::findOrFail($id);
        $roles->delete();

        return redirect()->back()->with([
            'success' => 'Role deleted successfully!',
            'title' => 'Deleted!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        return redirect()->back()->with([
            'success' => 'Role updated successfully!',
            'title' => 'Updated!'
        ]);
    }

}
