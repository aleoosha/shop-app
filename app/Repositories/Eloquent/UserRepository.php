<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryContract
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }
}
