<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthMobileApiController;
use App\Http\Controllers\IncidentsMobileApiController;

Route::prefix('mobile')->group(function () {
    Route::post('/login', [AuthMobileApiController::class, 'login']);
});

Route::prefix('mobile')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('logout', [AuthMobileApiController::class, 'logout']);

        Route::prefix('incidents')->group(function () {
            Route::get('/get', [IncidentsMobileApiController::class, 'getIncidents']);
            Route::post('/update/status/{id}', [IncidentsMobileApiController::class, 'updateIncidentStatus']);
            Route::post('/save/images/{id}', [IncidentsMobileApiController::class, 'saveIncidentImages']);
        });
});