<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Complaint;

class EmployeeComplaintAccess
{
    // public function handle(Request $request, Closure $next)
    // {
    //     $complaintId = $request->route('id');
    //     $complaint   = Complaint::find($complaintId);

    //     if (!$complaint || $complaint->entity_id !== $request->user()->entity_id) {
    //         return response()->json([
    //             'message' => 'Forbidden: You can only access complaints for your entity.'
    //         ], 403);
    //     }

    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next)
    {
        $complaintId = $request->route('id');

        // If listing complaints (no ID in route), skip this check
        if ($complaintId) {
            $complaint = Complaint::find($complaintId);

            if (!$complaint || $complaint->entity_id !== $request->user()->entity_id) {
                return response()->json([
                    'message' => 'Forbidden: complaint does not exist or is not of your entity.'
                ], 403);
            }
        }

        return $next($request);
    }

}
