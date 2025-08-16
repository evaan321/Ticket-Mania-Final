<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictToParticipant
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'participant') {
            abort(403, 'Unauthorized: Only participants can access this page.');
        }

        return $next($request);
    }
}
