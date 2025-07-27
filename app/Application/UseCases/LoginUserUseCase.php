<?php
namespace App\Application\UseCases;

use App\Domain\Repositories\User\UserRepositoryInterface;
use App\Domain\Entities\User;
use Illuminate\Validation\ValidationException;

class LoginUserUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws ValidationException
     */
    public function execute(string $email, string $password): string
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$this->userRepository->verifyPassword($user, $password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $eloquentUser = \App\Models\User::where('email', $email)->first();
        $token = $eloquentUser->createToken('api-token')->plainTextToken;

        return $token;
    }
}
