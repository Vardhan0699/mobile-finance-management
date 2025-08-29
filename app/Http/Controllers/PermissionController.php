<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index($roleId)
    {
        $role = Role::findOrFail($roleId);

        // Static page names (matching page_name in DB)
        $pages = ['staff','role','permission','dashboard', 'retailer', 'customer',
                  'transactions', 'reports', 'recovery', 'product', 'brand', 'approved pincode'];

        // Fetch all permissions from DB grouped by page_name
        $existingPermissions = DB::table('role_permission')
            ->join('page', 'role_permission.page_id', '=', 'page.id')
            ->join('permissions', 'role_permission.permission_id', '=', 'permissions.id')
            ->where('role_permission.role_id', $roleId)
            ->select('page.page_name', 'permissions.name as permission_name')
            ->get()
            ->groupBy('page_name')
            ->map(function ($items) {
                return $items->pluck('permission_name')->all();
            });

        return view('permission.index', compact('role', 'pages', 'existingPermissions'));
    }

    public function update(Request $request, $roleId)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'array',
            'permissions.*.*' => 'in:0,1',
        ]);

        $data = $request->input('permissions', []);

        $pageMap = DB::table('page')->pluck('id', 'page_name');
        $permMap = DB::table('permissions')->pluck('id', 'name');

        DB::transaction(function () use ($data, $pageMap, $permMap, $roleId) {
            DB::table('role_permission')->where('role_id', $roleId)->delete();

            foreach ($data as $pageName => $perms) {
                $pageId = $pageMap[$pageName] ?? null;

                if (!$pageId)
                    continue;

                foreach ($perms as $permName => $value) {
                    if ($value && isset($permMap[$permName])) {
                        DB::table('role_permission')->insert([
                            'role_id' => $roleId,
                            'page_id' => $pageId,
                            'permission_id' => $permMap[$permName],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Permissions updated successfully.');
    }

}
