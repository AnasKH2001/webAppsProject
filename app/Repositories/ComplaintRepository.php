<?php

namespace App\Repositories;

use App\Models\Complaint;

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
        
        return $complaint;
    }

    public function delete(Complaint $complaint): bool
    {
        return $complaint->delete();
    }
}
