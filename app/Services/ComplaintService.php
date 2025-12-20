<?php

namespace App\Services;

use App\Models\User;
use App\Models\Complaint;
use App\Repositories\ComplaintRepository;
use Illuminate\Support\Facades\Mail;

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

    public function getGlobalStatistics()
    {
        $entities = $this->repository->getEntityStats();

        return $entities->map(function ($entity) {
            $total = $entity->total_count;
            return [
                'entity_name' => $entity->name,
                'stats' => [
                    'total'       => $total,
                    'pending'     => $entity->pending_count,
                    'resolved'    => $entity->resolved_count,
                    'in_progress' => $entity->in_progress_count,
                    'rejected' => $entity->rejected_count,
                    // Calculate resolution rate safely
                    'resolution_rate' => $total > 0 
                        ? round(($entity->resolved_count / $total) * 100, 2) . '%' 
                        : '0%'
                ]
            ];
        });
    }

    public function getStatsCsv()
    {
        $stats = $this->getGlobalStatistics();
        
        // Open a memory stream
        $handle = fopen('php://temp', 'r+');
        
        // Add CSV Headers
        fputcsv($handle, ['Government Entity', 'Total', 'Pending', 'In Progress', 'Resolved', 'Resolution Rate']);

        // Add Data Rows
        foreach ($stats as $item) {
            fputcsv($handle, [
                $item['entity_name'],
                $item['stats']['total'],
                $item['stats']['pending'],
                $item['stats']['in_progress'],
                $item['stats']['resolved'],
                $item['stats']['resolution_rate'],
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return $csvContent;
    }

    public function requestInformation(int $complaintId, User $employee, string $message): Complaint
    {
        
        $complaint = $this->repository->findById($complaintId);

        if (!$complaint) {
            throw new \Exception('Complaint not found.');
        }

        if ($complaint->entity_id !== $employee->entity_id) {
            throw new \Exception('Unauthorized: Complaint belongs to another entity.');
        }

        if ($complaint->locked && $complaint->locked_by !== $employee->id) {
            throw new \Exception('This complaint is currently being processed by another employee.');
        }

        Mail::to($complaint->citizen->email)
            ->queue(new \App\Mail\MoreInfoRequestedMail($complaint, $message));

        return $complaint;
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
