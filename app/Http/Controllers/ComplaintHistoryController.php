<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ComplaintHistoryService;

class ComplaintHistoryController extends Controller
{
    protected ComplaintHistoryService $service;

    public function __construct(ComplaintHistoryService $service)
    {
        $this->service = $service;
    }

    public function history($id)
    {
        $complaint = Complaint::findOrFail($id);
        $user = Auth::user();

        // Citizens can only view their own complaint
        if ($user->role === 'citizen' && $complaint->citizen_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Employees can only view complaints belonging to their own government entity
        if ($user->role === 'employee' && $complaint->government_entity_id !== $user->government_entity_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Admins can view everything, others forbidden
        if (! in_array($user->role, ['citizen', 'employee', 'admin'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // If authorized, return history
        $histories = $this->service->getComplaintHistory($id);
        return response()->json($histories);
    }
}
