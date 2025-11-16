<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'changed_by',
        'old_status',
        'new_status',
        'old_desc',
        'new_desc',
        'old_attachments',
        'new_attachments',
    ];

    protected $casts = [
        'old_attachments' => 'array',
        'new_attachments' => 'array',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
