<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BigData\PredictiveAIController;
use App\Http\Controllers\BigData\HeatmapController;
use App\Http\Controllers\BigData\SentimentAnalysisController;

Route::middleware(['auth', 'admin'])->prefix('bigdata')->name('bigdata.')->group(function () {
    // Predictive AI Route
    Route::get('/predictive-ai', [PredictiveAIController::class, 'index'])->name('predictive-ai');
    Route::get('/predictive-ai/dashboard', [PredictiveAIController::class, 'dashboard'])->name('predictive-ai.dashboard');
    Route::post('/predictive-ai/analyze', [PredictiveAIController::class, 'analyze'])->name('predictive-ai.analyze');
    Route::get('/predictive-ai/predictions', [PredictiveAIController::class, 'getPredictions'])->name('predictive-ai.predictions');
    Route::post('/predictive-ai/generate-report', [PredictiveAIController::class, 'generateReport'])->name('predictive-ai.generate-report');
    
    // Heatmaps Route
    Route::get('/heatmaps', [HeatmapController::class, 'index'])->name('heatmaps');
    Route::get('/heatmaps/dashboard', [HeatmapController::class, 'dashboard'])->name('heatmaps.dashboard');
    Route::get('/heatmaps/property-prices', [HeatmapController::class, 'propertyPrices'])->name('heatmaps.property-prices');
    Route::get('/heatmaps/market-demand', [HeatmapController::class, 'marketDemand'])->name('heatmaps.market-demand');
    Route::get('/heatmaps/investment-opportunities', [HeatmapController::class, 'investmentOpportunities'])->name('heatmaps.investment-opportunities');
    Route::get('/heatmaps/data/{type}', [HeatmapController::class, 'getHeatmapData'])->name('heatmaps.data');
    
    // Sentiment Analysis Route
    Route::get('/sentiment-analysis', [SentimentAnalysisController::class, 'index'])->name('sentiment-analysis');
    Route::get('/sentiment-analysis/dashboard', [SentimentAnalysisController::class, 'dashboard'])->name('sentiment-analysis.dashboard');
    Route::post('/sentiment-analysis/analyze', [SentimentAnalysisController::class, 'analyze'])->name('sentiment-analysis.analyze');
    Route::get('/sentiment-analysis/reviews', [SentimentAnalysisController::class, 'reviews'])->name('sentiment-analysis.reviews');
    Route::get('/sentiment-analysis/social-media', [SentimentAnalysisController::class, 'socialMedia'])->name('sentiment-analysis.social-media');
    Route::get('/sentiment-analysis/trends', [SentimentAnalysisController::class, 'trends'])->name('sentiment-analysis.trends');
    Route::get('/sentiment-analysis/report', [SentimentAnalysisController::class, 'generateReport'])->name('sentiment-analysis.report');
});
