<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminComplaintAccess
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden: Only admins can access this route.'
            ], 403);
        }

        return $next($request);
    }
}
