<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function createEmployee(array $data): User
    {
        return User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => bcrypt($data['password']),
            'role'       => 'employee', // default role
            'entity_id'  => $data['entity_id'],
            'email_verified_at' => now()
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
