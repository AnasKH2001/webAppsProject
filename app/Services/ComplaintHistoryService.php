<?php

namespace App\Services;

use App\Repositories\ComplaintHistoryRepository;

class ComplaintHistoryService
{
    protected ComplaintHistoryRepository $repository;

    public function __construct(ComplaintHistoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getComplaintHistory(int $complaintId)
    {
        return $this->repository->findByComplaint($complaintId);
    }
}
