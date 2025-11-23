<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the authenticated user is an admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden: Admins only'
            ], 403);
        }

        return $next($request);
    }
}
