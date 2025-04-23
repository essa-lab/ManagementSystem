<?php

use App\Http\Controllers\Admin\Research\ResearchController;
use App\Http\Controllers\Admin\Research\EducationLevelController;
use App\Http\Controllers\Admin\Research\ResearchTypeController;
use App\Http\Controllers\Admin\Research\ResearchFormatController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {

Route::get('researches', [ResearchController::class,'index']);
 Route::get('researches/{id}', [ResearchController::class,'show']);
 Route::post('researches', [ResearchController::class,'store']);
 
 Route::post('researches/{researcheId}/keywords', [ResearchController::class,'storeKeywords']);
 Route::delete('research-keyword/{id}', [ResearchController::class,'deleteResearchKeyword']);

 Route::apiResource('research-types', ResearchTypeController::class);
 Route::apiResource('research-formats', ResearchFormatController::class);
 Route::apiResource('education-levels', EducationLevelController::class);
});