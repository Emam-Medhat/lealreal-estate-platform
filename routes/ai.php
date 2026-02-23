<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AI\SmartSearchController;
use App\Http\Controllers\AI\RecommendationController;
use App\Http\Controllers\AI\PricePredictionController;
use App\Http\Controllers\AI\ChatbotController;
use App\Http\Controllers\AI\ImageAnalysisController;

// Debug
Route::get('/debug-ai-test', fn() => 'ai routes loaded')->name('debug.ai.test');

// ─── Web Views (auth required) ────────────────────────────────────────────
Route::middleware(['auth'])->prefix('ai')->name('ai.')->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\AI\AIController::class, 'dashboard'])->name('dashboard');
    Route::get('/chat', [\App\Http\Controllers\AI\AIController::class, 'chat'])->name('chat');
    Route::get('/analytics', [\App\Http\Controllers\AI\AIController::class, 'analytics'])->name('analytics');
    Route::get('/descriptions', [\App\Http\Controllers\AI\AIController::class, 'descriptions'])->name('descriptions');
    Route::get('/images', [\App\Http\Controllers\AI\AIController::class, 'images'])->name('images');
    Route::get('/price-prediction', [\App\Http\Controllers\AI\AIController::class, 'pricePrediction'])->name('price-prediction');
    Route::get('/fraud-detection', [\App\Http\Controllers\AI\AIController::class, 'fraudDetection'])->name('fraud-detection');
    Route::get('/virtual-reality', [\App\Http\Controllers\AI\AIController::class, 'virtualReality'])->name('virtual-reality');
    Route::get('/virtual-staging', [\App\Http\Controllers\AI\AIController::class, 'virtualStaging'])->name('virtual-staging');
    Route::get('/investment-advisor', [\App\Http\Controllers\AI\AIController::class, 'investmentAdvisor'])->name('investment-advisor');
    Route::get('/market-insights', [\App\Http\Controllers\AI\AIController::class, 'marketInsights'])->name('market-insights');

    // NEW: Smart Search UI
    Route::get('/smart-search', [SmartSearchController::class, 'index'])->name('smart-search');

    // NEW: Chatbot UI
    Route::get('/chatbot-ui', [ChatbotController::class, 'index'])->name('chatbot-ui');

    // Valuation Dashboard
    Route::get('/property-valuation', [\App\Http\Controllers\AI\AIController::class, 'propertyValuation'])->name('valuation.dashboard');
    Route::get('/property-valuation/index', [\App\Http\Controllers\AI\AIController::class, 'propertyValuation'])->name('valuation.index');
    Route::post('/property-valuation', [\App\Http\Controllers\AI\AIController::class, 'storeValuation'])->name('valuation.store');
    Route::get('/property-valuation/{id}', [\App\Http\Controllers\AI\AIController::class, 'showValuation'])->name('valuation.show');
    Route::get('/property-valuation/{id}/download', [\App\Http\Controllers\AI\AIController::class, 'downloadValuation'])->name('valuation.download');
});

// ─── API Routes ───────────────────────────────────────────────────────────
Route::prefix('api/ai')->name('api.ai.')->group(function () {

    // ── Smart NL Search  (guest + auth) ──────────────────────────────────
    Route::post(
        '/search',
        [SmartSearchController::class, 'search']
    )
        ->middleware(['throttle:ai-search', 'ai.rate:search'])
        ->name('search');

    // ── Recommendations ───────────────────────────────────────────────────
    Route::middleware(['auth'])->group(function () {
        Route::get(
            '/recommendations',
            [RecommendationController::class, 'forUser']
        )
            ->middleware('ai.rate:search')
            ->name('recommendations.user');
    });

    Route::get(
        '/recommendations/{propertyId}/similar',
        [RecommendationController::class, 'similar']
    )
        ->middleware('ai.rate:search')
        ->name('recommendations.similar');

    // ── Price Prediction ──────────────────────────────────────────────────
    Route::post(
        '/price-prediction',
        [PricePredictionController::class, 'predict']
    )
        ->middleware('ai.rate:price_prediction')
        ->name('price-prediction.predict');

    Route::post(
        '/price-prediction/property/{propertyId}',
        [PricePredictionController::class, 'predictForProperty']
    )
        ->middleware('ai.rate:price_prediction')
        ->name('price-prediction.property');

    // ── Chatbot  (session-based, no auth required) ───────────────────────
    Route::post(
        '/chat',
        [ChatbotController::class, 'chat']
    )
        ->middleware('ai.rate:chat')
        ->name('chat.message');

    Route::delete(
        '/chat/{sessionId}',
        [ChatbotController::class, 'clearSession']
    )
        ->name('chat.clear');

    // ── Image Analysis ────────────────────────────────────────────────────
    Route::middleware(['auth'])->group(function () {
        Route::post(
            '/images/analyze',
            [ImageAnalysisController::class, 'upload']
        )
            ->middleware('ai.rate:image_analysis')
            ->name('images.analyze');

        Route::get(
            '/images/{id}/result',
            [ImageAnalysisController::class, 'result']
        )
            ->name('images.result');

        // Admin-only: by URL
        Route::post(
            '/images/analyze-url',
            [ImageAnalysisController::class, 'analyzeUrl']
        )
            ->middleware('ai.rate:image_analysis')
            ->name('images.analyze-url');
    });
});
