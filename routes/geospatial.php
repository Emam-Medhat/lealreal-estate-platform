<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Geospatial\GeospatialAnalyticsController;
use App\Http\Controllers\Geospatial\CrimeMapController;
use App\Http\Controllers\Geospatial\DemographicAnalysisController;
use App\Http\Controllers\Geospatial\HeatmapController;
use App\Http\Controllers\Geospatial\LocationIntelligenceController;
use App\Http\Controllers\Geospatial\PropertyAppreciationMapController;
use App\Http\Controllers\Geospatial\SchoolDistrictController;
use App\Http\Controllers\Geospatial\TransitScoreController;
use App\Http\Controllers\Geospatial\WalkScoreController;

Route::middleware(['auth'])->prefix('geospatial')->name('geospatial.')->group(function () {
    Route::get('/analytics', [GeospatialAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/crime-map', [CrimeMapController::class, 'index'])->name('crime-map.index');
    Route::get('/demographics', [DemographicAnalysisController::class, 'index'])->name('demographics.index');
    Route::get('/heatmap', [HeatmapController::class, 'index'])->name('heatmap.index');
    Route::get('/location-intelligence', [LocationIntelligenceController::class, 'index'])->name('location-intelligence.index');
    Route::get('/appreciation-map', [PropertyAppreciationMapController::class, 'index'])->name('appreciation-map.index');
    Route::get('/schools', [SchoolDistrictController::class, 'index'])->name('schools.index');
    Route::get('/transit-score', [TransitScoreController::class, 'index'])->name('transit-score.index');
    Route::get('/walk-score', [WalkScoreController::class, 'index'])->name('walk-score.index');
});
