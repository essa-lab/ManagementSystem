<?php
use App\Http\Controllers\Admin\Resource\ResourceSettingController;

use App\Http\Controllers\Admin\CommonResource\LanguageController;
use App\Http\Controllers\Admin\CommonResource\LibraryController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CommonResource\SourceController;
use App\Http\Controllers\Admin\CommonResource\SubjectController;
use App\Http\Controllers\Admin\Resource\ResourceController;
use App\Http\Controllers\Admin\Resource\ResourceCopyController;
use App\Http\Controllers\Admin\Resource\ReviewController;

require_once __DIR__.'/book.php';
require_once __DIR__.'/article.php';
require_once __DIR__.'/digital_resource.php';
require_once __DIR__.'/research.php';

Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {

Route::apiResource('resources', ResourceController::class);
    Route::post('resources/{resourceId}/subject',[ResourceController::class,'storeSubject']);
    Route::post('resources/{resourceId}/media',[ResourceController::class,'storeMedia']);
    Route::post('resources/{resourceId}/curator',[ResourceController::class,'storeCurators']);

    Route::delete('curator/{id}',[ResourceController::class,'deleteCurator']);

    Route::delete('media/{id}',[ResourceController::class,'deleteMedia']);

    Route::post('resources/{resourceId}/authors',[ResourceController::class,'storeCurators']);
    Route::delete('author/{id}',[ResourceController::class,'deleteCurator']);

    Route::get('resource/{id}/copies',[ResourceCopyController::class,'showCopyForResource']);

    Route::post('resources/{resourceId}/source',[ResourceController::class,'storeSource']);
    Route::post('resources/{resourceId}/editor',[ResourceController::class,'storeEditor']);

    Route::post('resource-setting',[ResourceSettingController::class,'store']);
    Route::put('resource-setting/{id}',[ResourceSettingController::class,'update']);
    Route::get('resource/{resourceId}/settings',[ResourceSettingController::class,'getResourceSetting']);

    Route::apiResource('languages', LanguageController::class);
    Route::apiResource('libraries', LibraryController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('sources', SourceController::class);
    Route::apiResource('resource-copies', ResourceCopyController::class);


    Route::get('/resource-overview',[ResourceController::class,'viewResources']);

    Route::get('generate-barcode/{id}',[ResourceCopyController::class,'getBarcode']);

    Route::get('/reviews',[ReviewController::class,'index']);
    Route::post('/ban-review',[ReviewController::class,'banReview']);

});