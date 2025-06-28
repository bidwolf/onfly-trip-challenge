<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
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
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ], [
            'email.required'    => 'Por favor, insira seu e-mail.',
            'email.email'       => 'Informe um e-mail válido.',
            'password.required' => 'Por favor, insira sua senha.'
        ]);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Credenciais inválidas.'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possivel efetuar login.'], 500);
        }

        return $this->respondWithToken($token);
    }
    public function me()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            return response()->json($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to fetch user profile'], 500);
        }
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Usuário deslogado com sucesso.']);
    }
    public function refresh()
    {
        try {
            return $this->respondWithToken(auth()->refresh());
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expirado. Por favor, faça login novamente.'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido ou ausente. Por favor, faça login novamente.'], 401);
        }
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
