<?php

use App\Http\Controllers\Auth\JWTPatronAuthController;
use App\Http\Controllers\Auth\PatronPasswordResetController;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Patron\PatronController;
use App\Http\Controllers\Admin\Circulation\CirculationController;
use App\Http\Controllers\Admin\Resource\ReviewController;
use App\Http\Controllers\FileController;


// -------------- Patron Auth Routes -------------- //
// re enable throttle here
Route::prefix('patron-auth')->middleware([LocalizationMiddleware::class])->group(function () {
    Route::post('login', [JWTPatronAuthController::class, 'login']);
    Route::Post('register', [JWTPatronAuthController::class, 'register']);
    Route::post('forgot-password', [PatronPasswordResetController::class, 'forgotPassword']);
    Route::post('refresh', [JWTPatronAuthController::class, 'refreshTokens']);
});


// re enable throttle here
Route::prefix('patron-auth')->group(function () {
    Route::get('activate-account/{token}',[PatronPasswordResetController::class,'activateAccount']);

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
// ---------- End Patron Auth Routes ---------- //

// ---------- Patron Routes ---------- //
Route::middleware(['jwt.patron', LocalizationMiddleware::class])->group(function () {

    // Book Routes
    Route::get('/resource-counts',[PatronController::class,'resourceCount']);
    Route::get('/library-list',[PatronController::class,'libraryList']);

});
Route::middleware(['jwt.patron','verified', LocalizationMiddleware::class])->group(function () {

    Route::post('/request-resource',[CirculationController::class,'requestResource']);
    Route::post('/renew-resource',[CirculationController::class,'renewResource']);
    Route::get('/patron-circulation',[CirculationController::class,'circulationPatronLog']);

    Route::post('/review-resource',[ReviewController::class,'store']);
    Route::put('/review-resource/{id}',[ReviewController::class,'update']);
    Route::delete('/review-resource/{id}',[ReviewController::class,'delete']);
    Route::get('/my-review',[ReviewController::class,'show']);
    Route::get('/review/{id}',[ReviewController::class,'getOneReview']);

    Route::get('/check-penalty',[CirculationController::class,'checkPenalty']);
    
    Route::post('image/upload', [FileController::class,'uploadFile']);


});

