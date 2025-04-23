<?php

use App\Http\Controllers\Auth\JWTUserAuthController;
use App\Http\Controllers\Auth\UserPasswordResetController;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('user-auth')->middleware([LocalizationMiddleware::class,'throttle:3,1'])->group(function () {
    Route::post('login', [JWTUserAuthController::class, 'login']);
    Route::post('forgot-password', [UserPasswordResetController::class, 'forgotPassword']);
    Route::post('refresh', [JWTUserAuthController::class, 'refreshTokens']);
});

Route::prefix('staff')->middleware([LocalizationMiddleware::class,'throttle:3,1'])->group(function () {
    Route::post('reset-password', [UserPasswordResetController::class, 'resetPassword']);
});

Route::prefix('user-auth')->middleware(['jwt.user', LocalizationMiddleware::class])->group(function () {
    Route::get('self', [JWTUserAuthController::class, 'getUser']);
    Route::post('logout', [JWTUserAuthController::class, 'logout']);
    Route::post('update-password', [JWTUserAuthController::class, 'updatePasswordUser']);

});