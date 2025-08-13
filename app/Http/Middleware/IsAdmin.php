<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->is_admin != 1) {
                abort(401);
            }
        } else {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
