<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = [
        'citizen_id',
        'entity_id',
        'type',
        'location',
        'description',
        'attachments',
        'reference_number',
        'status',
        'locked',
        'locked_by',
        'locked_at',
    ];

    // Cast attachments JSON to array automatically
    protected $casts = [
        'attachments' => 'array',
        'locked'      => 'boolean',
        'locked_at'   => 'datetime',
    ];

    
    //  Relationships
     
    public function locker()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    // Complaint belongs to a citizen (user with role = citizen)
    public function citizen()
    {
        return $this->belongsTo(User::class, 'citizen_id');
    }

    // Complaint belongs to a government entity
    public function entity()
    {
        return $this->belongsTo(GovernmentEntity::class, 'entity_id');
    }

    public function getAttachmentsAttribute($value)
    {
        $files = json_decode($value, true) ?? [];
        return array_map(fn($path) => asset('storage/' . $path), $files);
    }

    public function histories()
    {
        return $this->hasMany(\App\Models\ComplaintHistory::class);
    }

    
    public function isLocked(): bool
    {
        return $this->locked && $this->locked_by !== null;
    }

    //   Boot method to auto-generate reference number
     
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($complaint) {
            $datePart = now()->format('Ymd');
            $randomPart = strtoupper(uniqid());
            $complaint->reference_number = 'CMP-' . $datePart . '-' . substr($randomPart, -4);
        });
    }
}
