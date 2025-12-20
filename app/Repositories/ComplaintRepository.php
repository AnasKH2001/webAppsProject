<?php

namespace App\Repositories;

use App\Models\Complaint;
use App\Models\GovernmentEntity;

class ComplaintRepository
{
    public function create(array $data): Complaint
    {
        return Complaint::create($data);
    }

    public function findById(int $id): ?Complaint
    {
        return Complaint::find($id);
    }

    public function findByReference(string $ref): ?Complaint
    {
        return Complaint::where('reference_number', $ref)->first();
    }

    public function findByCitizenId(int $citizenId)
    {
        return Complaint::where('citizen_id', $citizenId)->get();
    }

    public function findByEntityId(int $entityId)
    {
        return Complaint::where('entity_id', $entityId)->get();
    }

    public function all()
    {
        return Complaint::all();
    }

    public function update(Complaint $complaint, array $data): Complaint
    {
        $complaint->update($data);
        $complaint->locked=false;
        $complaint->locked_at=null;
        $complaint->locked_by=null;
        $complaint->save();
        
        return $complaint;
    }


    public function getEntityStats()
    {
        // We use withCount to get the total and specific counts using sub-queries
        return GovernmentEntity::withCount([
            'complaints as total_count',
            'complaints as pending_count' => function ($query) {
                $query->where('status', 'pending');
            },
            'complaints as resolved_count' => function ($query) {
                $query->where('status', 'resolved');
            },
            'complaints as in_progress_count' => function ($query) {
                $query->where('status', 'in_progress');
            },
            'complaints as rejected_count' => function ($query) {
                $query->where('status', 'rejected');
            }
        ])->get();
    }

    public function delete(Complaint $complaint): bool
    {
        return $complaint->delete();
    }
}
