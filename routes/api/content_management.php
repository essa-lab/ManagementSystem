<?php

use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Dashboard\ContactController;
use App\Http\Controllers\Admin\Dashboard\NavigationController;
use App\Http\Controllers\Admin\Dashboard\QuickLinksController;
use App\Http\Controllers\Admin\Dashboard\SocialFooterController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\Resource\ResourceCopyController;

Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {
    Route::apiResource('navigations', NavigationController::class);
    Route::apiResource('quick-links', QuickLinksController::class);
    Route::apiResource('footer-social', SocialFooterController::class);
    Route::apiResource('footer-contact', ContactController::class);


    Route::get('home-banner',[HomeController::class,'getHomeConetnt']);
    Route::get('about-banner',[HomeController::class,'getAbout']);
    Route::get('about-contact',[HomeController::class,'getAboutContent']);

    Route::put('home-banner',[HomeController::class,'updateHomeConetnt']);
    Route::put('about-banner',[HomeController::class,'updateAbout']);
    Route::put('about-contact',[HomeController::class,'updateAboutContent']);
});