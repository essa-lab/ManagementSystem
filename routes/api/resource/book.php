<?php
use App\Http\Controllers\Admin\Book\BookController;
use App\Http\Controllers\Admin\Book\PoetryCollectionController;
use App\Http\Controllers\Admin\Book\PrintConditionController;
use App\Http\Controllers\Admin\Book\PrintTypeController;
use App\Http\Controllers\Admin\Book\TranslationTypeController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {

Route::get('books', [BookController::class,'index']);
Route::get('books/{id}', [BookController::class,'show']);
Route::post('books', [BookController::class,'store']);

Route::post('books/{bookId}/poetry-collection', [BookController::class,'storePoetryCollection']);
Route::delete('poetry-collection-name/{id}', [BookController::class,'deletePoetryCollection']);

Route::post('books/{bookId}/specific-subject', [BookController::class,'storeSpecificSubject']);
Route::delete('specific-subject/{id}', [BookController::class,'deleteSpecificSubject']);

Route::post('books/{bookId}/translator-type', [BookController::class,'storeTranslatorType']);
Route::delete('translator/{id}', [BookController::class,'deleteTranslator']);

Route::post('books/{bookId}/print-information', [BookController::class,'storePrintInformation']);

//meta-data
Route::apiResource('poetry-collections', PoetryCollectionController::class);
Route::apiResource('translation-types', TranslationTypeController::class);
Route::apiResource('print-types', PrintTypeController::class);
Route::apiResource('print-conditions', PrintConditionController::class);

});