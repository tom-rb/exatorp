<?php

namespace App\Members\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MustBeAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::guard('member')->user();

        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        return redirect()->route('member.home');
    }
}
