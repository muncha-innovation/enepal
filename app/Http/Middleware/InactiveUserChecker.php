<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InactiveUserChecker
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is inactive
        if ($request->user() && !$request->user()->active) {
            return redirect()->route('profile.update');
        }

        return $next($request);
    }
}
