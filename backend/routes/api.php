<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register')->withoutMiddleware(['auth:api']);
});


Route::get('/user', function (Request $request) {
    try {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json($user);
    } catch (JWTException $e) {
        return response()->json(['error' => 'Failed to fetch user profile'], 500);
    }
}, 200);
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');
