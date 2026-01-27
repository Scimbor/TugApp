<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthMobileApiController;

Route::prefix('mobile')->group(function () {
    Route::post('/login', [AuthMobileApiController::class, 'login']);
});

Route::prefix('mobile')->middleware(['auth:sanctum'], function () {
    Route::post('/auth/mobile/logout', [AuthMobileApiController::class, 'logout']);

    Route::prefix('incidents')->group(function () {
        Route::post('/get', [IncidentsMobileApiController::class, 'getIncidents']);
        Route::post('/update/tatus', [IncidentsMobileApiController::class, 'updateIncidentStatus']);
        Route::post('/save/images', [IncidentsMobileApiController::class, 'saveIncidentImages']);
    });
});