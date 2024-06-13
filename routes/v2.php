<?php

use App\Http\Controllers\v2\LawyerController;
use App\Http\Controllers\v2\AuthController;
use App\Http\Controllers\v2\JudicialDistrictController;
use Illuminate\Support\Facades\Route;

Route::prefix('/user')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('judicial-districts', JudicialDistrictController::class);

    Route::apiResource('lawyers', LawyerController::class);
});
