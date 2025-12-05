<?php

namespace App\Repositories;

use App\Models\ComplaintHistory;

class ComplaintHistoryRepository
{
    public function findByComplaint(int $complaintId)
    {
        return ComplaintHistory::where('complaint_id', $complaintId)
            ->with('changer')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
