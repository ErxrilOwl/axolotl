<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])
            ->name('index')->middleware('permission:departments.view');

        Route::get('/create', [DepartmentController::class, 'create'])
            ->name('create')->middleware('permission:departments.create');
        Route::post('/', [DepartmentController::class, 'store'])
            ->name('store')->middleware('permission:departments.create');

        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])
            ->name('edit')->middleware('permission:departments.edit');
        Route::put('/{department}', [DepartmentController::class, 'update'])
            ->name('update')->middleware('permission:departments.edit');

        Route::delete('/{department}', [DepartmentController::class, 'destroy'])
            ->name('destroy')->middleware('permission:departments.delete');
    });

    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->name('index')->middleware('permission:roles.view');

        Route::get('/create', [RoleController::class, 'create'])
            ->name('create')->middleware('permission:roles.create');
        Route::post('/', [RoleController::class, 'store'])
            ->name('store')->middleware('permission:roles.create');

        Route::get('/{role}/edit', [RoleController::class, 'edit'])
            ->name('edit')->middleware('permission:roles.edit');
        Route::put('/{role}', [RoleController::class, 'update'])
            ->name('update')->middleware('permission:roles.edit');

        Route::delete('/{role}', [RoleController::class, 'destroy'])
            ->name('destroy')->middleware('permission:roles.delete');
    });

    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])
            ->name('index')->middleware('permission:permissions.view');

        Route::get('/create', [PermissionController::class, 'create'])
            ->name('create')->middleware('permission:permissions.create');
        Route::post('/', [PermissionController::class, 'store'])
            ->name('store')->middleware('permission:permissions.create');

        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])
            ->name('edit')->middleware('permission:permissions.edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])
            ->name('update')->middleware('permission:permissions.edit');

        Route::delete('/{permission}', [PermissionController::class, 'destroy'])
            ->name('destroy')->middleware('permission:permissions.delete');
    });
});
