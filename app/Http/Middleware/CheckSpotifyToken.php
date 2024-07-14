<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSpotifyToken
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->spotify_expires_in->isPast()) {
            app('App\Http\Controllers\SpotifyController')->refreshAccessToken();
        }

        return $next($request);
    }
}