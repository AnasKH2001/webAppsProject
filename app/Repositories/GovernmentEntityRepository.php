<?php

namespace App\Repositories;

use App\Models\GovernmentEntity;

class GovernmentEntityRepository
{
    public function all()
    {
        return GovernmentEntity::all(['id', 'name']);
    }
}
