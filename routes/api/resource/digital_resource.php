<?php

use App\Http\Controllers\Admin\DigitalResource\DigitalResourceController;
use App\Http\Controllers\Admin\DigitalResource\DigitalFormatController;
use App\Http\Controllers\Admin\DigitalResource\DigitalResourceTypeController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {

    Route::get('digital-resources', [DigitalResourceController::class, 'index']);
    Route::get('digital-resources/{id}', [DigitalResourceController::class, 'show']);

    Route::post('digital-resources', [DigitalResourceController::class, 'store']);
    Route::post('digital-resources/{digitalResourceId}/relations', [DigitalResourceController::class, 'storeRelations']);
    Route::post('digital-resources/{digitalResourceId}/right', [DigitalResourceController::class, 'storeRight']);
    Route::post('digital-resources/{digitalResourceId}/specific-subject', [DigitalResourceController::class, 'storeSpecificSubject']);

    Route::apiResource('digital-formats', DigitalFormatController::class);
    Route::apiResource('digital-types', DigitalResourceTypeController::class);
});
