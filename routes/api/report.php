<?php


use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Resource\ResourceController;
use App\Http\Controllers\Admin\ExportController;

Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {

    Route::get('resource/resource-counts-per-library-language',[ResourceController::class,'resourceCountPerLibraryAndLanguage']);
    Route::get('resource/resource-counts-per-library',[ResourceController::class,'resourceCountPerLibrary']);


    Route::get('top-ten',[ResourceController::class,'topTen']);

    Route::get('generate-report',[ExportController::class,'exportResources']);

    Route::get('generate-season-report',[ExportController::class,'season']);
    Route::post('send-season-report',[ExportController::class,'sendEmailToHeads']);

    Route::get('count-per-subject',[ResourceController::class,'resourceCountPerSubject']);
});