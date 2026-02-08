<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IotIngestionController;

/*
|--------------------------------------------------------------------------
| IoT Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::prefix('v1/iot')->group(function () {
    // Device Telemetry
    Route::post('/telemetry', [IotIngestionController::class, 'updateTelemetry']);
    
    // Device Events
    Route::post('/events', [IotIngestionController::class, 'handleEvent']);
    
    // Future endpoints for device provisioning, etc.
});
