<?php

namespace App\Observers;

use App\Models\Complaint;
use App\Models\ComplaintHistory;
use Illuminate\Support\Facades\Auth;

class ComplaintObserver
{
    /**
     * Handle the Complaint "created" event.
     */
    public function created(Complaint $complaint): void
    {
        //

        ComplaintHistory::create([
            'complaint_id'    => $complaint->id,
            'changed_by'      => Auth::id(),
            'new_status'      => 'pending',
            'new_desc'        => $complaint->description,
            'new_attachments' => $complaint->attachments,
        ]);
    
    }

    /**
     * Handle the Complaint "updated" event.
     */
    public function updated(Complaint $complaint): void
    {
        //
        $dirty = $complaint->getChanges();      // fields being changed
        $original = $complaint->getOriginal(); // old values

        // Only act if one of the concerned fields changed
        $concerned = ['status', 'description', 'attachments'];

        // Intersect dirty keys with concerned fields
        $changedFields = array_intersect(array_keys($dirty), $concerned);

        if (empty($changedFields)) {
            return; // nothing relevant changed, skip history
        }

        ComplaintHistory::create([
            'complaint_id'    => $complaint->id,
            'changed_by'      => Auth::id(),
            'old_status'      => $original['status'] ?? null,
            'new_status'      => $dirty['status'] ?? $complaint->status,
            'old_desc'        => $original['description'] ?? null,
            'new_desc'        => $dirty['description'] ?? $complaint->description,
            'old_attachments' => $original['attachments'] ?? [],
            'new_attachments' => $dirty['attachments'] ?? $complaint->attachments,
        ]);
    }

    /**
     * Handle the Complaint "deleted" event.
     */
    public function deleted(Complaint $complaint): void
    {
        //
    }

    /**
     * Handle the Complaint "restored" event.
     */
    public function restored(Complaint $complaint): void
    {
        //
    }

    /**
     * Handle the Complaint "force deleted" event.
     */
    public function forceDeleted(Complaint $complaint): void
    {
        //
    }
}
