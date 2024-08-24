<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPasswordSet
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if (!$user->password_set && !$request->is('password-setup')) {
                return redirect('/password-setup');
            }

            if ($user->password_set && $request->is('password-setup')) {
                return redirect('/home'); 
            }
        }

        return $next($request);
    }
}