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
