<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadApiController;
use App\Http\Controllers\Api\PropertyApiController;
use App\Http\Controllers\RealTimeDashboardController;
use App\Http\Controllers\Currency\CurrencyController;
use App\Http\Controllers\Api\Intelligence\MapLayerController as IntelligenceMapLayerController;
use App\Http\Controllers\Api\Intelligence\PricingController as IntelligencePricingController;
use App\Http\Controllers\Api\Intelligence\RecommendationController as IntelligenceRecommendationController;
use App\Http\Controllers\Intelligence\PropertyIntelligenceController;
use App\Http\Controllers\Api\LocationApiController;

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
    Route::get('/dashboard/realtime/stats', [RealTimeDashboardController::class, 'stats'])->name('api.dashboard.realtime.stats');
    Route::get('/dashboard/realtime/security', [RealTimeDashboardController::class, 'securityFeed'])->name('api.dashboard.realtime.security');
    Route::get('/dashboard/realtime/iot', [RealTimeDashboardController::class, 'iotStatus'])->name('api.dashboard.realtime.iot');

    // Currency API Routes
    Route::post('/currency/convert', [CurrencyController::class, 'convert']);
    Route::get('/currency/rates', [CurrencyController::class, 'getRates']);
    Route::get('/currency/history', [CurrencyController::class, 'getHistory']);
    Route::get('/currency/statistics', [CurrencyController::class, 'getStatistics']);
    Route::post('/currency/update-rates', [CurrencyController::class, 'updateRates']);
    Route::post('/currency/set-preferred', [CurrencyController::class, 'setPreferredCurrency']);
    Route::get('/currency/supported', [CurrencyController::class, 'getSupported']);

    Route::get('/stats', function () {
        return response()->json([
            'message' => 'API is running',
            'version' => config('app.version', '1.0.0'),
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
        ]);
    });

    // Location API Routes
    Route::get('/locations/countries', [LocationApiController::class, 'getCountries']);
    Route::get('/locations/countries/{countryId}/governorates', [LocationApiController::class, 'getGovernorates']);
    Route::get('/locations/governorates/{governorateId}/cities', [LocationApiController::class, 'getCities']);
    Route::get('/locations/cities/{cityId}/districts', [LocationApiController::class, 'getDistricts']);
});

// IoT Device Routes (Device Authentication)
Route::prefix('api/v1/iot')->middleware(['api'])->group(function () {
    Route::post('ingest', [\App\Http\Controllers\Api\IotIngestionController::class, 'ingest']);
});

// Intelligence API
Route::prefix('api/v1/intelligence')->middleware(['api', 'auth:api'])->group(function () {
    // Map layers
    Route::get('layers/price-heatmap', [IntelligenceMapLayerController::class, 'priceHeatmap']);
    Route::get('layers/demand-density', [IntelligenceMapLayerController::class, 'demandDensity']);
    // Pricing
    Route::get('pricing/property/{id}', [IntelligencePricingController::class, 'predictForProperty']);
    // Recommendations
    Route::get('recommendations/feed', [IntelligenceRecommendationController::class, 'feed']);
    // Trigger pipeline for a property
    Route::post('properties/{id}/run', [PropertyIntelligenceController::class, 'run']);
});

// ─────────────────────────────────────────────────────────────────────────────
// AI System Routes
// ─────────────────────────────────────────────────────────────────────────────

Route::prefix('api/ai')->middleware(['api'])->group(function () {

    // 1. Smart Search (Natural Language)
    Route::post('search', [\App\Http\Controllers\AI\SmartSearchController::class, 'search'])
        ->middleware('throttle:20,1'); // 20 req/min

    // 2. Recommendations
    Route::get('recommendations', [\App\Http\Controllers\AI\RecommendationController::class, 'forUser'])
        ->middleware('auth:api');

    Route::get('recommendations/{id}/similar', [\App\Http\Controllers\AI\RecommendationController::class, 'similar']);

    // 3. Price Prediction
    Route::post('price-prediction', [\App\Http\Controllers\AI\PricePredictionController::class, 'predict'])
        ->middleware('throttle:200,60'); // 200 req/hour ≈ 3/min

    Route::post('price-prediction/property/{id}', [\App\Http\Controllers\AI\PricePredictionController::class, 'predictForProperty'])
        ->middleware('throttle:200,60');

    // 4. AI Chatbot
    Route::post('chat', [\App\Http\Controllers\AI\ChatbotController::class, 'chat'])
        ->middleware('throttle:10,1'); // 10 req/min

    Route::delete('chat/{sid}', [\App\Http\Controllers\AI\ChatbotController::class, 'clearSession']);

    // 5. Image Analysis
    Route::post('images/analyze', [\App\Http\Controllers\AI\ImageAnalysisController::class, 'upload'])
        ->middleware(['auth:api', 'throttle:100,60']); // 100 req/hour

    Route::get('images/{id}/result', [\App\Http\Controllers\AI\ImageAnalysisController::class, 'result']);

    // Admin only
    Route::post('images/analyze-url', [\App\Http\Controllers\AI\ImageAnalysisController::class, 'analyzeUrl'])
        ->middleware(['auth:api', 'throttle:100,60', 'admin']);
});
