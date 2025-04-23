<?php

use App\Http\Controllers\Admin\Article\ArticleController;
use App\Http\Controllers\Admin\Article\ArticleTypeController;
use App\Http\Controllers\Admin\Article\ScientificBranchesController;
use App\Http\Controllers\Admin\Article\SpecificationController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {

Route::get('articles', [ArticleController::class,'index']);
Route::get('articles/{id}', [ArticleController::class,'show']);

Route::post('articles', [ArticleController::class,'store']);
Route::post('articles/{articleId}/keywords', [ArticleController::class,'storeKeywords']);
Route::delete('article-keyword/{id}', [ArticleController::class,'deleteArticleKeyword']);

Route::apiResource('scientific-classification', ScientificBranchesController::class);
Route::apiResource('article-types', ArticleTypeController::class);
Route::apiResource('article-specifications', SpecificationController::class);
});