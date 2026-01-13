<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has admin role
        // For now, check if user_id is 1 (first admin user)
        // In production, add an is_admin or role column to users table
        if (auth()->check() && auth()->user()->id === 1) {
            return $next($request);
        }

        abort(403, 'Unauthorized access to admin panel');
    }
}
