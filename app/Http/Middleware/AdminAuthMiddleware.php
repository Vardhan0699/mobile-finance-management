<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ Get admin ID from session
        $adminId = session('admin_id');
        // dd(session('admin_id'));
        if (!$adminId) {
            return redirect()->route('admin.login');
        }

        if (!Admin::find($adminId)) {
            session()->forget('admin_id');
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
