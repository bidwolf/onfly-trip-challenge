<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $user = Arr::except($request->validated(), 'password_confirmation');
        if (isset($user['password'])) {
            $user['password'] = Hash::make($user['password']);
        }
        $createdUser = User::create($user);

        try {
            $token = JWTAuth::fromUser($createdUser);
            return response()->json([
                'token' => $token,
                'type' => 'Bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'message' => 'Usuário cadastrado com sucesso!'
            ], 201);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Um erro inesperado ocorreu.'], 500);
        }
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Usuário deslogado com sucesso.']);
    }
}
