<?php

namespace App\Http\Middleware;

use Closure;

class StatusMiddleware
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
        if (!$request->user()->active) {
            // return response(trans('Your status is set to inactive. Please contact admin for further details'), 403);
            return response()->view('auth.inactive');
        }

        return $next($request);
    }
}