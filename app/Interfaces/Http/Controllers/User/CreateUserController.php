<?php

namespace App\Interfaces\Http\Controllers\User;

use App\Interfaces\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\UseCases\CreateUserUseCase;

class CreateUserController extends Controller
{
    protected CreateUserUseCase $useCase;

    public function __construct(CreateUserUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function store(Request $request)
    {
        $data = $request->only(['name', 'email', 'password']);

        $user = $this->useCase->execute($data);

        return response()->json($user, 201);
    }
}
