<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register')->withoutMiddleware(['auth:api']);
    Route::post('/login', [AuthController::class, 'login'])->name('login')->withoutMiddleware(['auth:api']);
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


Route::get('/me', [AuthController::class, 'me'])->name('me');
Route::apiResource('travel-orders', TravelOrderController::class);
