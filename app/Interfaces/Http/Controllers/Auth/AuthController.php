<?php
namespace App\Interfaces\Http\Controllers\Auth;

use App\Application\UseCases\LoginUserUseCase;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private LoginUserUseCase $loginUserUseCase;

    public function __construct(LoginUserUseCase $loginUserUseCase)
    {
        $this->loginUserUseCase = $loginUserUseCase;
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        try {
            $token = $this->loginUserUseCase->execute($data['email'], $data['password']);
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
