<?php

use App\Http\Controllers\OptimizedPropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Optimized Property Routes
|--------------------------------------------------------------------------
|
| These routes use the OptimizedPropertyController which includes
| caching and performance optimizations for the property system.
|
*/

Route::prefix('optimized')->name('optimized.')->group(function () {
    Route::get('/properties/search', [OptimizedPropertyController::class, 'search'])->name('properties.search');
    Route::resource('properties', OptimizedPropertyController::class);
});
