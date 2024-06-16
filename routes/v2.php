<?php

use App\Http\Controllers\v2\ProceedingController;
use App\Http\Controllers\v2\LawyerController;
use App\Http\Controllers\v2\AuthController;
use App\Http\Controllers\v2\JudicialDistrictController;
use App\Http\Controllers\v2\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('/user')->group(function () {
    // Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('judicial-districts', JudicialDistrictController::class);

    Route::apiResource('lawyers', LawyerController::class);

    Route::apiResource('proceedings', ProceedingController::class);

    Route::prefix('reports')->group(function () {
        Route::get('executionAmounts', [ReportController::class, 'executionAmounts']);
    });
    
});
