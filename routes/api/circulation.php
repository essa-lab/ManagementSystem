<?php

use App\Http\Controllers\Admin\Circulation\CirculationController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {

Route::post('resource-check-in',[CirculationController::class,'adminCheckIn']);
Route::post('resource-check-out',[CirculationController::class,'adminCheckOut']);
Route::post('change-circulation-status',[CirculationController::class,'changeCirculationStatus']);
Route::post('renew-circulation-status',[CirculationController::class,'changeCirculationRenewStatus']);

Route::get('circulations',[CirculationController::class,'index']);
Route::get('overdue-circulations/{id}',[CirculationController::class,'showOverdue']);

Route::get('circulation-logs/{id}',[CirculationController::class,'logIndex']);
Route::get('pay-penalty/{id}',[CirculationController::class,'payPenalty']);
Route::post('waive-penalty',[CirculationController::class,'waivePenalty']);
Route::get('renew-circulation-requests',[CirculationController::class,'getRenewalRequest']);



Route::get('track-inventory',[CirculationController::class,"getResourceCopyCounts"]);

Route::get('/check-patron-penalty',[CirculationController::class,'checkPenaltyForPatron']);
Route::get('/generate-patron-penalty',[CirculationController::class,'generatePenaltyForPatron']);
});