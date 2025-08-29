<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $page, $permissionType)
    {
        $user = auth()->guard('admin')->user(); // or 'retailer', depending on your guard

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // ✅ Super Admin bypass check (use either condition based on your schema)
        if (isset($user->is_super_admin) && $user->is_super_admin) {
            return $next($request);
        }

        // OR if using role_id to identify super admin (example: role_id = 1 is Super Admin)
        if ($user->role_id == 1) {
            return $next($request);
        }

        // Get role_id from user
        $roleId = $user->role_id;

        // Get page and permission IDs
        $pageId = DB::table('page')->where('page_name', $page)->value('id');
        $permissionId = DB::table('permissions')->where('name', $permissionType)->value('id');

        // Check role_permission table
        $hasPermission = DB::table('role_permission')
            ->where('role_id', $roleId)
            ->where('page_id', $pageId)
            ->where('permission_id', $permissionId)
            ->exists();

        if (!$hasPermission) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
