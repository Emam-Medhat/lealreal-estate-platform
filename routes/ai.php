<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiPropertyValuationController;
use App\Http\Controllers\AiPricePredictorController;
use App\Http\Controllers\AiPropertyDescriptionController;
use App\Http\Controllers\AiChatbotController;
use App\Http\Controllers\AiFraudDetectionController;
use App\Http\Controllers\AiImageRecognitionController;
use App\Http\Controllers\AiInsightsController;
use App\Http\Controllers\AiInvestmentAdvisorController;
use App\Http\Controllers\AiLeadScoringController;
use App\Http\Controllers\AiMarketAnalysisController;
use App\Http\Controllers\AiPropertyMatchingController;
use App\Http\Controllers\AiRentalPriceOptimizationController;
use App\Http\Controllers\AiVirtualStagingController;

Route::middleware(['auth'])->prefix('ai')->name('ai.')->group(function () {
    // Main AI Dashboard & Features (from AIController)
    Route::get('/dashboard', [\App\Http\Controllers\AI\AIController::class, 'dashboard'])->name('dashboard');
    Route::get('/chat', [\App\Http\Controllers\AI\AIController::class, 'chat'])->name('chat');
    Route::get('/analytics', [\App\Http\Controllers\AI\AIController::class, 'analytics'])->name('analytics');
    Route::get('/descriptions', [\App\Http\Controllers\AI\AIController::class, 'descriptions'])->name('descriptions');
    Route::get('/images', [\App\Http\Controllers\AI\AIController::class, 'images'])->name('images');

    // AI Dashboard/Index
    Route::get('/', [AiInsightsController::class, 'index'])->name('index');
    
    // AI Insights API Routes
    Route::post('/insights/generate', [AiInsightsController::class, 'generateInsights'])->name('insights.generate');
    Route::get('/anomaly-detection', [AiInsightsController::class, 'anomalyDetection'])->name('anomaly-detection');
    Route::get('/pattern-recognition', [AiInsightsController::class, 'patternRecognition'])->name('pattern-recognition');
    Route::get('/predictive-insights', [AiInsightsController::class, 'predictiveInsights'])->name('predictive-insights');
    Route::get('/recommendations', [AiInsightsController::class, 'recommendations'])->name('recommendations');
    
    // Property Valuation
    Route::get('/valuation/dashboard', [AiPropertyValuationController::class, 'dashboard'])->name('valuation.dashboard');
    Route::resource('/valuation', AiPropertyValuationController::class);

    // Price Prediction
    Route::get('/price-predictor/dashboard', [AiPricePredictorController::class, 'dashboard'])->name('price-predictor.dashboard');
    Route::resource('/price-predictor', AiPricePredictorController::class);

    // Property Description Generator
    Route::get('/description-generator/dashboard', [AiPropertyDescriptionController::class, 'dashboard'])->name('description-generator.dashboard');
    Route::resource('/description-generator', AiPropertyDescriptionController::class);

    // Chatbot
    Route::get('/chatbot/dashboard', [AiChatbotController::class, 'dashboard'])->name('chatbot.dashboard');
    Route::resource('/chatbot', AiChatbotController::class);
    Route::post('/chatbot/{conversation}/message', [AiChatbotController::class, 'sendMessage'])->name('chatbot.message');

    // Fraud Detection
    Route::get('/fraud-detection', [AiFraudDetectionController::class, 'index'])->name('fraud-detection.index');

    // Image Recognition
    Route::get('/image-recognition', [AiImageRecognitionController::class, 'index'])->name('image-recognition.index');

    // Insights
    Route::get('/insights', [AiInsightsController::class, 'index'])->name('insights.index');

    // Investment Advisor
    Route::get('/investment-advisor', [AiInvestmentAdvisorController::class, 'index'])->name('investment-advisor.index');

    // Lead Scoring
    Route::get('/lead-scoring', [AiLeadScoringController::class, 'index'])->name('lead-scoring.index');

    // Market Analysis
    Route::get('/market-analysis', [AiMarketAnalysisController::class, 'index'])->name('market-analysis.index');

    // Property Matching
    Route::get('/property-matching', [AiPropertyMatchingController::class, 'index'])->name('property-matching.index');

    // Rental Price Optimization
    Route::get('/rental-optimization', [AiRentalPriceOptimizationController::class, 'index'])->name('rental-optimization.index');

    // Virtual Staging
    Route::get('/virtual-staging', [AiVirtualStagingController::class, 'index'])->name('virtual-staging.index');
});
