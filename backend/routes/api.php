<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('oauth/google')->group(function () {
    Route::get('url', [AuthController::class, 'redirect']);
    Route::get('callback', [AuthController::class, 'callback']);
});

Route::prefix('users')->group(function () {
    Route::put('complete', [UserController::class, 'complete']);
    Route::get('/', [UserController::class, 'index']);
});
