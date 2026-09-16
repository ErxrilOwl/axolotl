<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])
        ->name('index')->middleware('permission:users.view');

    Route::get('/create', [UserController::class, 'create'])
        ->name('create')->middleware('permission:users.create');
    Route::post('/', [UserController::class, 'store'])
        ->name('store')->middleware('permission:users.create');

    Route::get('/{user}/edit', [UserController::class, 'edit'])
        ->name('edit')->middleware('permission:users.edit');
    Route::put('/{user}', [UserController::class, 'update'])
        ->name('update')->middleware('permission:users.edit');
    Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('reset-password')->middleware('permission:users.edit');

    Route::delete('/{user}', [UserController::class, 'destroy'])
        ->name('destroy')->middleware('permission:users.delete');
});
