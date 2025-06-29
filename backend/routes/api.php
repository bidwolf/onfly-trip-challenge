<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh')->middleware(['auth:api']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware(['auth:api']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::apiResource('travel-orders', TravelOrderController::class);
    Route::match(['patch', 'put'], 'travel-orders/{travel_order}/approve', [TravelOrderController::class, 'approve']);
    Route::match(['patch', 'put'], 'travel-orders/{travel_order}/cancel', [TravelOrderController::class, 'cancel']);
});
