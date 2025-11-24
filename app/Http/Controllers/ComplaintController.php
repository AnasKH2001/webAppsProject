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

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'entity_id'   => 'required|exists:government_entities,id',
    //         'type'        => 'required|string',
    //         'location'    => 'nullable|string',
    //         'description' => 'nullable|string',
    //         'attachments' => 'nullable|array',
    //     ]);

    //     $complaint = $this->service->submitComplaint($validated, $request->user());

    //     return response()->json($complaint, 201);
    // }

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
        return response()->json($complaint);
    }

    public function update(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);
        $status = $request->validate(['status' => 'required|string'])['status'];

        $updated = $this->service->updateStatus($complaint, $status, $request->user());

        return response()->json($updated);
    }
}
