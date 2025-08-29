<?php

use Illuminate\Support\Facades\Session;

if (!function_exists('admin_login')) {
    function admin_login($admin)
    {
        session(['admin_id' => $admin->id]);
        session(['admin_email' => $admin->email]);
    }
}

if (!function_exists('admin_logout')) {
    function admin_logout()
    {
        session()->forget(['admin_id', 'admin_email']);
        session()->flush();
    }
}

if (!function_exists('is_admin_logged_in')) {
    function is_admin_logged_in()
    {
        return session()->has('admin_id');
    }
}
