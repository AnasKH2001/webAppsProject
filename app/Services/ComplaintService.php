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

    public function updateStatus(Complaint $complaint, string $status, User $employee): Complaint
    {
        if (!in_array($employee->role, ['employee','admin'])) {
            throw new \Exception('Unauthorized: Only employees or admins can update complaints.');
        }

        $complaint->status = $status;
        return $this->repository->update($complaint, ['status' => $status]);
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
