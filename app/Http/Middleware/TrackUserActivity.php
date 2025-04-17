<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            auth()->user()->update(['last_active_at' => now()]);
        }

        return $next($request);
    }
}
