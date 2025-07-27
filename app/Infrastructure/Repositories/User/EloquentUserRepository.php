<?php

namespace App\Infrastructure\Repositories\User;

use App\Models\User as EloquentUser;
use App\Domain\Entities\User as DomainUser;
use App\Domain\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function create(DomainUser $user): EloquentUser
    {
        return EloquentUser::create([
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
        ]);
    }

    public function findByEmail(string $email): ?DomainUser
    {
        $user = EloquentUser::where('email', $email)->first();

        if(!$user) { return null; }

        return new DomainUser($user->name, $user->email, $user->password);
    }

    public function verifyPassword(DomainUser $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}
