<?php

namespace App\Services;

use App\Repositories\GovernmentEntityRepository;

class GovernmentEntityService
{
    /**
     * Create a new class instance.
     */
    protected GovernmentEntityRepository $repository;

    public function __construct(GovernmentEntityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllEntities()
    {
        return $this->repository->all();
    }
}
