<?php

use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CommonResource\LibrarySettingController;
use App\Http\Controllers\Admin\Circulation\PenaltyValueController;
use App\Http\Controllers\Admin\PatronController;
use App\Http\Controllers\Admin\PrivilageController;
use App\Http\Controllers\Admin\UserController;
Route::middleware([JwtMiddleware::class, LocalizationMiddleware::class])->group(function () {

Route::apiResource('users', UserController::class);
Route::post('users/update-profile', [UserController::class,'updateProfile']);

Route::apiResource('patrons', PatronController::class);
Route::post('patrons/suspend/{id}', [PatronController::class,'suspend']);
Route::get('privilages', [PrivilageController::class,'index']); 

Route::get('library-settings', [LibrarySettingController::class,'index']);
Route::get('library-settings/{id}', [LibrarySettingController::class,'show']);
Route::put('library-settings/{id}', [LibrarySettingController::class,'update']);

Route::apiResource('penalty-values', PenaltyValueController::class);

Route::post('patron-self-register', [UserController::class,'selfRegisteration']);
Route::get('patron-self-register', [UserController::class,'getselfRegisteration']);
});