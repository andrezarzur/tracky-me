<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareInertiaData
{
    public function handle(Request $request, Closure $next)
    {
        // Add user data to every Inertia page
        Inertia::share('user', function () use ($request) {
            return $request->user()
                ? $request->user()->only('id', 'name', 'email', 'spotify_image', 'spotify_followers', 'spotify_product') // Customize as needed
                : null;
        });

        return $next($request);
    }
}