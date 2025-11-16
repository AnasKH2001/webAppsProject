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
    ];

    // Cast attachments JSON to array automatically
    protected $casts = [
        'attachments' => 'array',
    ];

    
    //  Relationships
     

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
