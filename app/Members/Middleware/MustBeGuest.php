<?php

namespace App\Members\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MustBeGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $guards_routes = [
            'member' => 'member.home',
            'student' => 'student.home'
        ];

        foreach ($guards_routes as $guard => $route) {
            if (Auth::guard($guard)->check()) {
                return redirect()->route($route);
            }
        }

        return $next($request);
    }
}
