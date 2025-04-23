<?php

use App\Http\Controllers\Admin\Resource\ReviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([LocalizationMiddleware::class])->group(function () {

    Route::get('self-register', [UserController::class,'getselfRegisteration']);
    Route::get('/library-homepage',[DashboardController::class,"getLibraryData"]);
    Route::get('/library-about',[DashboardController::class,"getAboutPage"]);
    
    Route::get('/library-navigation',[DashboardController::class,"getLibraryNavigation"]);
    Route::get('/review-resource',[ReviewController::class,'showReview']);
    
    });