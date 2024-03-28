<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForcePasswordUpdate
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if ($request->user() && $request->user()->force_update_password) {
            // Redirect to the password update route
            return redirect()->route('profile.update');
        }

        return $next($request);
    }
}
