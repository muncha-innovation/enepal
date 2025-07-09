<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForcePasswordUpdate
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if ($request->user() && $request->user()->force_update_password && !$request->is('profile*')) {
            // Redirect to the profile page with a session value to show the security tab
            return redirect()->route('profile')->with('active_profile_tab', 'security');
        }

        return $next($request);
    }
}
