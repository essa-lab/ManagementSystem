<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MeilisearchController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;

require_once __DIR__.'/api/user.php';
require_once __DIR__.'/api/patron.php';
require_once __DIR__.'/api/resource/resource.php';
require_once __DIR__.'/api/library_management.php';
require_once __DIR__.'/api/report.php';
require_once __DIR__.'/api/order.php';
require_once __DIR__.'/api/content_management.php';
require_once __DIR__.'/api/guest.php';
require_once __DIR__.'/api/circulation.php';

Route::get('/meilisearch/key', [MeilisearchController::class, 'getKey']);
Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {
    Route::post('file/upload', [FileController::class,'uploadFile']);
    Route::get('activity-logs',[ActivityLogController::class,'index']);    
});

