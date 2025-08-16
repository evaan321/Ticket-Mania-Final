<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictToOrganizer
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'organizer') {
            abort(403, 'Unauthorized: Only organizers can access this page.');
        }

        return $next($request);
    }
}
