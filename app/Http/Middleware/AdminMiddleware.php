<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Allows only authenticated users whose role is "admin".
     * On failure, redirects to the dashboard with a session error flash.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'Only admins can access this page.');
        }

        return $next($request);
    }
}
