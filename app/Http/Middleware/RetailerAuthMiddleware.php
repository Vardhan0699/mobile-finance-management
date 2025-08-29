<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class RetailerAuthMiddleware
{
  /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

  public function handle(Request $request, Closure $next)
  {
    if (!$request->session()->has('retailer_id')) {
      return redirect()->route('retailer.loginForm')->with('error', 'Please login first.');
    }

    $retailer = DB::table('retailer')->where('id', $request->session()->get('retailer_id'))->first();

    if (!$retailer) {
      $request->session()->forget('retailer_id');
      return redirect()->route('retailer.loginForm')->with('error', 'Invalid retailer session.');
    }

    return $next($request);
  }
}
