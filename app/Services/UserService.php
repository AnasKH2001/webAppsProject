<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createEmployee(array $data, User $creator): User
    {
        if ($creator->role !== 'admin') {
            throw new \Exception('Only admins can create employee accounts.');
        }

        $data['entity_id'] = $creator->entity_id;

        // $data['role'] = 'employee';

        return $this->userRepository->createEmployee($data);
    }

}
