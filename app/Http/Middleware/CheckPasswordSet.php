<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPasswordSet
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->password_set) {
            return redirect('/password-setup');
        }

        return $next($request);
    }
}