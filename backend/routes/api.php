<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register')->withoutMiddleware(['auth:api']);
    Route::post('/login', [AuthController::class, 'login'])->name('login')->withoutMiddleware(['auth:api']);
});


Route::get('/me', [AuthController::class, 'me'])->name('me');
Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('refresh');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');
