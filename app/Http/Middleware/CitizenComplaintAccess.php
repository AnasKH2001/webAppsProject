<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Complaint;

class CitizenComplaintAccess
{
    public function handle(Request $request, Closure $next)
    {
        $complaintId = $request->route('id');
        $complaint   = Complaint::find($complaintId);

        if (!$complaint || $complaint->citizen_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Forbidden: You can only access your own complaints.'
            ], 403);
        }

        return $next($request);
    }
}
