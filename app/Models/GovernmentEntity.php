<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernmentEntity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Relationships
     */

    // One government entity has many employees (users with role employee/admin)
    public function users()
    {
        return $this->hasMany(User::class, 'entity_id');
    }

    // Later: one government entity has many complaints
    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'entity_id');
    }
}
