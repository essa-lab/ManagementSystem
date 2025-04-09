<?php

use App\Http\Controllers\Auth\JWTPatronAuthController;
use App\Http\Controllers\Auth\JWTUserAuthController;
use App\Http\Controllers\Auth\PatronPasswordResetController;
use App\Http\Controllers\Auth\UserPasswordResetController;
use App\Http\Middleware\LocalizationMiddleware;
use App\Http\Middleware\VerifiedMiddleware;
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

// re enable throttle here
Route::prefix('patron-auth')->middleware([LocalizationMiddleware::class])->group(function () {
    Route::post('login', [JWTPatronAuthController::class, 'login']);
    Route::Post('register', [JWTPatronAuthController::class, 'register']);
    Route::post('forgot-password', [PatronPasswordResetController::class, 'forgotPassword']);
    Route::post('refresh', [JWTPatronAuthController::class, 'refreshTokens']);
    // Route::get('activate-account/{token}',[JWTPatronAuthController::class,'activateAccount']);
});


// re enable throttle here
Route::prefix('patron-auth')->group(function () {
    Route::get('activate-account/{token}',[PatronPasswordResetController::class,'activateAccount']);

    // Route::Post('register', [JWTPatronAuthController::class, 'register']);
});

// re enable throttle here
Route::prefix('patron')->middleware([LocalizationMiddleware::class])->group(function () {
    Route::post('reset-password', [PatronPasswordResetController::class, 'resetPassword']);
});

Route::prefix('patron-auth')->middleware(['jwt.patron', LocalizationMiddleware::class])->group(function () {

    Route::post('logout', [JWTPatronAuthController::class, 'logout']);
    Route::get('self', [JWTPatronAuthController::class, 'getPatron']);
    Route::post('resend-activate-email',[PatronPasswordResetController::class,'resendActivationEmail'])->middleware('throttle:3,60');
});

// re enable throttle here

Route::prefix('patron-auth')
    ->middleware(['jwt.patron', LocalizationMiddleware::class, 'verified'])
    ->group(function () {
        Route::put('update-account', [JWTPatronAuthController::class, 'updateAccount']);
        Route::post('request-update-email', [JWTPatronAuthController::class, 'requestUpdateEmail']);
        Route::post('update-password', [JWTPatronAuthController::class, 'updatePassword']);
        Route::post('update-email', [JWTPatronAuthController::class, 'updateEmail']);
        Route::post('request-update-password', [JWTPatronAuthController::class, 'requestUpdatePassword']);
    });
