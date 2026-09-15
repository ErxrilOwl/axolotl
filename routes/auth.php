<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('signup', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('signup', [RegisteredUserController::class, 'store']);

    Route::get('signin', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('signin', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
