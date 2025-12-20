<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ComplaintService;
use App\Models\Complaint;

class ComplaintController extends Controller
{
    protected ComplaintService $service;

    public function __construct(ComplaintService $service)
    {
        $this->service = $service;
    }

    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_id'   => 'required|exists:government_entities,id',
            'type'        => 'required|string',
            'location'    => 'nullable|string',
            'description' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048', // each file
        ]);

        // Handle file uploads
        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('complaints', 'public'); 
            }
            $validated['attachments'] = $paths;
        }

        $complaint = $this->service->submitComplaint($validated, $request->user());

        return response()->json($complaint, 201);
    }

    public function index(Request $request)
    {
            $user = $request->user();

        if ($user->role === 'citizen') {
            // Only their own complaints
            $complaints = $this->service->listCitizenComplaints($user);
        } elseif ($user->role === 'employee') {
            // Only complaints for their entity
            $complaints = $this->service->listEntityComplaints($user->entity_id);
        } elseif ($user->role === 'admin') {
            // All complaints
            $complaints = $this->service->listComplaints();
        } else {
            return response()->json([
                'message' => 'Forbidden: role not allowed to list complaints.'
            ], 403);
        }

        return response()->json($complaints);
    }


    public function show($id)
    {
        $complaint = $this->service->getComplaint($id);
        return response()->json($complaint->toArray());
    }

    public function update(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);
        $user = $request->user();

        // Citizens can only edit their own complaint
        if ($user->role !== 'citizen'||$user->id!==$complaint->citizen_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Validate input
        $validated = $request->validate([
            'description'  => 'nullable|string|max:1000',
            'attachments'  => 'nullable|array',
        ]);


        $complaint = $this->service->updateComplaintForCitizen($id, $user->id, $validated);

        if (! $complaint) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'message'   => 'Complaint updated successfully',
            'complaint' => $complaint
        ]);
    }

    
    public function employeeUpdate(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'employee') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,resolved,rejected',
        ]);

        try {
            $complaint = $this->service->updateStatusByEmployee($id, $user, $validated['status']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 423);
        }

        return response()->json([
            'message'   => 'Status updated successfully',
            'complaint' => $complaint,
        ]);
    }

    
    public function requestInfo(Request $request, $id)
    {
        $user = $request->user();
        
        // Authorization check
        if ($user->role !== 'employee') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|min:10|max:2000',
        ]);

        try {
            $complaint = $this->service->requestInformation($id, $user, $validated['message']);
            return response()->json([
                'message' => 'Information request sent to citizen successfully.',
                'complaint' => $complaint
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 423);
        }
    }

    public function showByReference(Request $request, $ref)
    {
        try {
            $complaint = $this->service->getComplaintByReference($ref, $request->user());
            return response()->json($complaint);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }


}
