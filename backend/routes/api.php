<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function (): void {
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:10,1')
        ->name('auth.register');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('auth.login');
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'auth'], function (): void {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });

    Route::group(['prefix' => 'tasks'], function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store');

        Route::group(['prefix' => '{task}'], function () {
            Route::get('/', [TaskController::class, 'show'])->name('tasks.show');
            Route::put('/', [TaskController::class, 'update'])->name('tasks.update');
            Route::delete('/', [TaskController::class, 'destroy'])->name('tasks.destroy');
        });
    });
});
