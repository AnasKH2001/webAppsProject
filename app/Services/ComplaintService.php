<?php

namespace App\Services;

use App\Repositories\ComplaintRepository;
use App\Models\User;
use App\Models\Complaint;

class ComplaintService
{
    protected ComplaintRepository $repository;

    public function __construct(ComplaintRepository $repository)
    {
        $this->repository = $repository;
    }

    public function submitComplaint(array $data, User $citizen): Complaint
    {
        if ($citizen->role !== 'citizen') {
            throw new \Exception('Only citizens can submit complaints.');
        }

        $data['citizen_id'] = $citizen->id;
        return $this->repository->create($data);
    }

    public function updateComplaintForCitizen(int $complaintId, int $citizenId, array $data)
    {
        $complaint = $this->repository->findById($complaintId);

        if (! $complaint || $complaint->citizen_id !== $citizenId) {
            return null; // not authorized
        }

        // Only allow editable fields
        $allowed = collect($data)->only(['description','attachments'])->toArray();

        return $this->repository->update($complaint, $allowed);
    }


    
    public function getComplaintByReference(string $ref, User $user): Complaint
    {
        $complaint = $this->repository->findByReference($ref);

        if (!$complaint) {
            throw new \Exception('Complaint not found.');
        }

        if ($user->role === 'citizen') {
            if ($complaint->citizen_id !== $user->id) {
                throw new \Exception('Forbidden: You can only access your own complaints.');
            }
        } elseif ($user->role === 'employee') {
            if ($complaint->entity_id !== $user->entity_id) {
                throw new \Exception('Forbidden: You can only access complaints for your entity.');
            }
        } elseif ($user->role === 'admin') {
            // Admins can see everything → no restriction
                    return $complaint;

        } else {
            throw new \Exception('Forbidden: Role not allowed to access complaints.');
        }

        return $complaint;
    }


    public function listCitizenComplaints(User $citizen)
    {
        if ($citizen->role !== 'citizen') {
            throw new \Exception('Only citizens can list their own complaints.');
        }

        return $this->repository->findByCitizenId($citizen->id);
    }

    public function listEntityComplaints(int $entityId)
    {
        return $this->repository->findByEntityId($entityId);
    }

    public function updateStatusByEmployee(int $id, User $employee, string $newStatus): Complaint
    {
        $complaint = $this->repository->findById($id);

        if($employee->entity_id!==$complaint->entity_id){
            throw new \Exception('Complaint belongs to different entity');
        }
        if ($complaint->locked && $complaint->locked_by !== $employee->id) {
            throw new \Exception('Complaint is locked by another employee');
        }

        $data = [
            'status' => $newStatus,
            'locked' => true,
            'locked_by' => $employee->id,
            'locked_at' => now(),
        ];

        $complaint = $this->repository->update($complaint, $data);

        // if (in_array($newStatus, ['resolved','rejected'])) {
        //     $complaint = $this->repository->update($complaint, [
        //         'locked' => false,
        //         'locked_by' => null,
        //         'locked_at' => null,
        //     ]);
        // }

        return $complaint;
    }


    public function getComplaint(int $id): ?Complaint
    {
        return $this->repository->findById($id);
    }

    public function listComplaints()
    {
        return $this->repository->all();
    }
}
