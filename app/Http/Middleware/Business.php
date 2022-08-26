<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Business
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role == 1) {
            return redirect()->route('superadmin.dashboard');
        }

        if (Auth::user()->role == 2) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::user()->role == 4) {
            return redirect()->route('employee.dashboard');
        }

        if (Auth::user()->role == 5) {
            return redirect()->route('consumer.dashboard');
        }

        if (Auth::user()->role == 3) {
            return $next($request);
        }

    }
}
