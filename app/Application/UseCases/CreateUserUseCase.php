<?php

namespace App\Application\UseCases;

use App\Domain\Entities\User;
use App\Domain\Repositories\User\UserRepositoryInterface;

class CreateUserUseCase
{
    protected UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data): \App\Models\User
    {
        $user = new User(
            $data['name'],
            $data['email'],
            bcrypt($data['password'])
        );

        return $this->repository->create($user);
    }
}
