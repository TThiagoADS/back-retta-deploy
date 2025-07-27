<?php

namespace App\Domain\Repositories\User;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function create(User $user): \App\Models\User;
    public function verifyPassword(User $user, string $password): bool;
    public function findByEmail(string $email): ?User;
}
