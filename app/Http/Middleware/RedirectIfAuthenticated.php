<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // public function handle(Request $request, Closure $next, ...$guards)
    // {
    //     $guards = empty($guards) ? [null] : $guards;
    //     foreach ($guards as $guard) {
    //         if (Auth::guard($guard)->check()) {
    //             return redirect(RouteServiceProvider::HOME);
    //         }
    //     }
    //     return $next($request);
    // }

    public function handle($request, Closure $next, $guard = null) {
        if (Auth::guard($guard)->check()) {

            $role = Auth::user()->role;

            switch ($role) {
            case '1':
                return redirect('/superadmin/dashboard');
                break;
            case '2':
                return redirect('/admin/dashboard');
                break;
            case '3':
                return redirect('/business/dashboard');
                break;
            case '4':
                return redirect('/employee/dashboard');
                break;
            case '5':
                return redirect('/consumer/dashboard');
                break;
            default:
                return redirect('/login');
                break;
            }
        }
    return $next($request);
    }
}
