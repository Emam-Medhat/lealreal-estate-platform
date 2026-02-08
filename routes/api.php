<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadApiController;
use App\Http\Controllers\Api\PropertyApiController;
use App\Http\Controllers\RealTimeDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('api/v1')->middleware(['api', 'auth:api'])->group(function () {
    // Lead API Routes
    Route::resource('leads', LeadApiController::class)->except(['create', 'edit', 'update']);
    Route::post('leads', [LeadApiController::class, 'store']);
    Route::put('leads/{lead}', [LeadApiController::class, 'update']);
    Route::post('leads/search', [LeadApiController::class, 'search']);
    Route::get('leads/dashboard', [LeadApiController::class, 'dashboard']);
    Route::get('leads/funnel', [LeadApiController::class, 'funnel']);
    Route::get('leads/stats', [LeadApiController::class, 'stats']);
    Route::post('leads/bulk-update', [LeadApiController::class, 'bulkUpdate']);
    Route::post('leads/export', [LeadApiController::class, 'export']);
    Route::get('leads/{lead}/activities', [LeadApiController::class, 'activities']);

    // Property API Routes
    Route::resource('properties', PropertyApiController::class)->except(['create', 'edit', 'update']);
    Route::post('properties', [PropertyApiController::class, 'store']);
    Route::put('properties/{property}', [PropertyApiController::class, 'update']);
    Route::post('properties/search', [PropertyApiController::class, 'search']);
    Route::get('properties/featured', [PropertyApiController::class, 'featured']);
    Route::get('properties/location', [PropertyApiController::class, 'byLocation']);
    Route::get('properties/dashboard', [PropertyApiController::class, 'dashboard']);
    Route::get('properties/recommendations', [PropertyApiController::class, 'recommendations']);
    Route::get('properties/performance', [PropertyApiController::class, 'performance']);
    Route::post('properties/bulk-update', [PropertyApiController::class, 'bulkUpdate']);
    Route::post('properties/export', [PropertyApiController::class, 'export']);
    Route::post('properties/{property}/toggle-featured', [PropertyApiController::class, 'toggleFeatured']);
    Route::get('properties/{property}/images', [PropertyApiController::class, 'images']);
    Route::get('properties/{property}/analytics', [PropertyApiController::class, 'analytics']);

    // General API Routes
    Route::get('/dashboard/realtime/stats', [RealTimeDashboardController::class, 'stats']);
    Route::get('/dashboard/realtime/security', [RealTimeDashboardController::class, 'securityFeed']);
    Route::get('/dashboard/realtime/iot', [RealTimeDashboardController::class, 'iotStatus']);

    Route::get('/stats', function () {
        return response()->json([
            'message' => 'API is running',
            'version' => config('app.version', '1.0.0'),
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
        ]);
    });
});

// IoT Device Routes (Device Authentication)
Route::prefix('api/v1/iot')->middleware(['api'])->group(function () {
    Route::post('ingest', [\App\Http\Controllers\Api\IotIngestionController::class, 'ingest']);
});
