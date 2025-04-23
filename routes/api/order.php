<?php


use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Aquestion\OrderController;

Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {

    Route::get('orders',[OrderController::class,'index']);
    Route::post('orders',[OrderController::class,'store']);
    Route::get('orders/{id}',[OrderController::class,'show']);
    Route::put('orders/{id}',[OrderController::class,'changeStatus']);
    Route::post('order-items/{id}',[OrderController::class,'storeItem']);

   
});