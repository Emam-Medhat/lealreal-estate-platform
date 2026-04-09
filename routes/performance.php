<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerformanceController;

/*
|--------------------------------------------------------------------------
| Performance Routes
|--------------------------------------------------------------------------
|
| These routes provide access to performance monitoring and metrics.
|
*/

Route::middleware(['web', 'auth'])->prefix('performance')->group(function () {
    Route::get('/', [PerformanceController::class, 'index'])->name('performance.dashboard');
    Route::get('/database', [PerformanceController::class, 'database'])->name('performance.database');
    Route::get('/cache', [PerformanceController::class, 'cache'])->name('performance.cache');
    Route::get('/queries', [PerformanceController::class, 'queries'])->name('performance.queries');
    Route::get('/requests', [PerformanceController::class, 'requests'])->name('performance.requests');
    Route::get('/system', [PerformanceController::class, 'system'])->name('performance.system');
    Route::get('/realtime', [PerformanceController::class, 'realtime'])->name('performance.realtime');
    Route::get('/recommendations', [PerformanceController::class, 'recommendations'])->name('performance.recommendations');
    Route::post('/clear-cache', [PerformanceController::class, 'flushCache'])->name('performance.clear_cache');
});

// API routes for performance data
Route::middleware(['api', 'auth:api'])->prefix('api/v1/performance')->group(function () {
    Route::get('/', [PerformanceController::class, 'index'])->name('api.performance.index');
    Route::get('/database', [PerformanceController::class, 'database'])->name('api.performance.database');
    Route::get('/cache', [PerformanceController::class, 'cache'])->name('api.performance.cache');
    Route::get('/queries', [PerformanceController::class, 'queries'])->name('api.performance.queries');
    Route::get('/requests', [PerformanceController::class, 'requests'])->name('api.performance.requests');
    Route::get('/system', [PerformanceController::class, 'system'])->name('api.performance.system');
    Route::get('/realtime', [PerformanceController::class, 'realtime'])->name('api.performance.realtime');
    Route::get('/recommendations', [PerformanceController::class, 'recommendations'])->name('api.performance.recommendations');
    Route::post('/clear-cache', [PerformanceController::class, 'flushCache'])->name('api.performance.clear_cache');
});

// Admin performance routes
Route::middleware(['web', 'auth', 'admin'])->prefix('admin/performance')->group(function () {
    Route::get('/', [PerformanceController::class, 'index'])->name('admin.performance.dashboard');
    Route::get('/database', [PerformanceController::class, 'database'])->name('admin.performance.database');
    Route::get('/cache', [PerformanceController::class, 'cache'])->name('admin.performance.cache');
    Route::get('/queries', [PerformanceController::class, 'queries'])->name('admin.performance.queries');
    Route::get('/system', [PerformanceController::class, 'index'])->name('admin.performance.system');
    Route::get('/realtime', [PerformanceController::class, 'realtime'])->name('admin.performance.realtime');
    Route::get('/recommendations', [PerformanceController::class, 'recommendations'])->name('admin.performance.recommendations');
    Route::match(['get', 'post'], '/clear-cache', [PerformanceController::class, 'flushCache'])->name('admin.performance.clear_cache');
    // Note: clear-cache route handles both GET and POST
});
