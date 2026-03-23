<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('oauth/google')->group(function () {
    Route::get('url', [AuthController::class, 'redirect']);
    Route::get('callback', [AuthController::class, 'callback']);
});
