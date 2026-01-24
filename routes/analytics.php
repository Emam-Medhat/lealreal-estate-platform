<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BigDataController;
use App\Http\Controllers\PredictiveAnalyticsController;
use App\Http\Controllers\MarketAnalyticsController;
use App\Http\Controllers\UserBehaviorController;
use App\Http\Controllers\HeatmapController;
use App\Http\Controllers\FunnelAnalysisController;
use App\Http\Controllers\CohortAnalysisController;
use App\Http\Controllers\AiInsightsController;
use App\Http\Controllers\SentimentAnalysisController;
use App\Http\Controllers\TrendAnalysisController;
use App\Http\Controllers\CompetitiveAnalysisController;
use Illuminate\Support\Facades\Route;

// Analytics Routes - Complete with all middleware
Route::middleware(['auth', 'verified', 'banned', 'email.verified', 'device.fingerprint', 'track.activity'])->prefix('analytics')->name('analytics.')->group(function () {
    
    // Main Analytics Routes - Premium feature
    Route::middleware(['premium', 'kyc', 'permission:analytics.read'])->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/overview', [AnalyticsController::class, 'overview'])->name('overview');
        Route::get('/real-time', [AnalyticsController::class, 'realTime'])->name('real-time');
        Route::get('/reports', [AnalyticsController::class, 'reports'])->name('reports');
        Route::get('/insights', [AnalyticsController::class, 'insights'])->name('insights');
        Route::get('/segmentation', [AnalyticsController::class, 'segmentation'])->name('segmentation');
        
        Route::middleware('permission:analytics.create')->group(function () {
            Route::post('/track-event', [AnalyticsController::class, 'trackEvent'])->name('trackEvent');
            Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
        });
    });
    
    // Big Data Routes
    Route::prefix('bigdata')->name('bigdata.')->group(function () {
        Route::get('/dashboard', [BigDataController::class, 'dashboard'])->name('dashboard');
        Route::get('/ingestion', [BigDataController::class, 'dataIngestion'])->name('ingestion');
        Route::get('/quality', [BigDataController::class, 'dataQuality'])->name('quality');
        Route::get('/transformation', [BigDataController::class, 'dataTransformation'])->name('transformation');
        Route::get('/aggregation', [BigDataController::class, 'dataAggregation'])->name('aggregation');
        Route::get('/mining', [BigDataController::class, 'dataMining'])->name('mining');
        Route::get('/visualization', [BigDataController::class, 'dataVisualization'])->name('visualization');
        Route::get('/streaming', [BigDataController::class, 'dataStreaming'])->name('streaming');
        Route::get('/storage', [BigDataController::class, 'dataStorage'])->name('storage');
        Route::get('/security', [BigDataController::class, 'dataSecurity'])->name('security');
        Route::get('/governance', [BigDataController::class, 'dataGovernance'])->name('governance');
        
        Route::post('/process', [BigDataController::class, 'processData'])->name('process');
        Route::post('/transformation', [BigDataController::class, 'createTransformation'])->name('createTransformation');
        Route::post('/aggregation', [BigDataController::class, 'createAggregation'])->name('createAggregation');
        Route::post('/mining', [BigDataController::class, 'runMiningAlgorithm'])->name('runMining');
        Route::post('/visualization', [BigDataController::class, 'createVisualization'])->name('createVisualization');
        Route::post('/streaming', [BigDataController::class, 'startStream'])->name('startStream');
    });
    
    // Predictive Analytics Routes
    Route::prefix('predictive')->name('predictive.')->group(function () {
        Route::get('/', [PredictiveAnalyticsController::class, 'index'])->name('index');
        Route::get('/models', [PredictiveAnalyticsController::class, 'models'])->name('models');
        Route::get('/train/{model}', [PredictiveAnalyticsController::class, 'trainModel'])->name('trainModel');
        Route::get('/predict/{model}', [PredictiveAnalyticsController::class, 'predict'])->name('predict');
        Route::get('/accuracy/{model}', [PredictiveAnalyticsController::class, 'accuracy'])->name('accuracy');
        Route::get('/forecast', [PredictiveAnalyticsController::class, 'forecast'])->name('forecast');
        Route::get('/scenarios', [PredictiveAnalyticsController::class, 'scenarios'])->name('scenarios');
        
        Route::get('/create-model', [PredictiveAnalyticsController::class, 'createModel'])->name('createModel');
        Route::post('/create-model', [PredictiveAnalyticsController::class, 'storeModel'])->name('storeModel');
        Route::post('/train/{model}', [PredictiveAnalyticsController::class, 'executeTraining'])->name('executeTraining');
        Route::post('/predict/{model}', [PredictiveAnalyticsController::class, 'makePrediction'])->name('makePrediction');
        Route::post('/forecast', [PredictiveAnalyticsController::class, 'generateForecast'])->name('generateForecast');
        Route::post('/scenarios', [PredictiveAnalyticsController::class, 'runScenarios'])->name('runScenarios');
    });
    
    // Market Analytics Routes
    Route::prefix('market')->name('market.')->group(function () {
        Route::get('/', [MarketAnalyticsController::class, 'index'])->name('index');
        Route::get('/trends', [MarketAnalyticsController::class, 'trends'])->name('trends');
        Route::get('/analysis', [MarketAnalyticsController::class, 'analysis'])->name('analysis');
        Route::get('/segments', [MarketAnalyticsController::class, 'segments'])->name('segments');
        Route::get('/competitors', [MarketAnalyticsController::class, 'competitors'])->name('competitors');
        Route::get('/opportunities', [MarketAnalyticsController::class, 'opportunities'])->name('opportunities');
        Route::get('/reports', [MarketAnalyticsController::class, 'reports'])->name('reports');
        Route::get('/export', [MarketAnalyticsController::class, 'export'])->name('export');
        Route::get('/forecast', [MarketAnalyticsController::class, 'forecast'])->name('forecast');
        Route::post('/forecast', [MarketAnalyticsController::class, 'generateMarketForecast'])->name('generateForecast');
        
        Route::post('/analyze', [MarketAnalyticsController::class, 'analyzeMarket'])->name('analyzeMarket');
        Route::post('/competitor-analysis', [MarketAnalyticsController::class, 'analyzeCompetitor'])->name('analyzeCompetitor');
        Route::post('/identify-opportunities', [MarketAnalyticsController::class, 'identifyOpportunities'])->name('identifyOpportunities');
        Route::post('/generate-report', [MarketAnalyticsController::class, 'generateReport'])->name('generateReport');
    });
    
    // User Behavior Analytics Routes
    Route::prefix('behavior')->name('behavior.')->group(function () {
        Route::get('/', [UserBehaviorController::class, 'index'])->name('index');
        Route::get('/tracking', [UserBehaviorController::class, 'tracking'])->name('tracking');
        Route::get('/funnels', [UserBehaviorController::class, 'funnels'])->name('funnels');
        Route::get('/retention', [UserBehaviorController::class, 'retention'])->name('retention');
        Route::get('/engagement', [UserBehaviorController::class, 'engagement'])->name('engagement');
        Route::get('/journeys', [UserBehaviorController::class, 'journeys'])->name('journeys');
        Route::get('/segments', [UserBehaviorController::class, 'segments'])->name('segments');
        
        Route::post('/track', [UserBehaviorController::class, 'trackUser'])->name('trackUser');
        Route::post('/analyze-journey', [UserBehaviorController::class, 'analyzeJourney'])->name('analyzeJourney');
        Route::post('/create-segment', [UserBehaviorController::class, 'createSegment'])->name('createSegment');
    });
    
    // Heatmap Analytics Routes
    Route::prefix('heatmap')->name('heatmap.')->group(function () {
        Route::get('/', [HeatmapController::class, 'index'])->name('index');
        Route::get('/clicks', [HeatmapController::class, 'clicks'])->name('clicks');
        Route::get('/scrolls', [HeatmapController::class, 'scrolls'])->name('scrolls');
        Route::get('/mouse', [HeatmapController::class, 'mouse'])->name('mouse');
        Route::get('/attention', [HeatmapController::class, 'attention'])->name('attention');
        Route::get('/forms', [HeatmapController::class, 'forms'])->name('forms');
        
        Route::post('/generate', [HeatmapController::class, 'generateHeatmap'])->name('generateHeatmap');
        Route::post('/record', [HeatmapController::class, 'recordInteraction'])->name('recordInteraction');
    });
    
    // Funnel Analysis Routes
    Route::prefix('funnel')->name('funnel.')->group(function () {
        Route::get('/', [FunnelAnalysisController::class, 'index'])->name('index');
        Route::get('/create', [FunnelAnalysisController::class, 'create'])->name('create');
        Route::post('/', [FunnelAnalysisController::class, 'store'])->name('store');
        Route::get('/{funnel}', [FunnelAnalysisController::class, 'show'])->name('show');
        Route::get('/{funnel}/analysis', [FunnelAnalysisController::class, 'analysis'])->name('analysis');
        Route::get('/{funnel}/steps', [FunnelAnalysisController::class, 'steps'])->name('steps');
        Route::get('/{funnel}/conversions', [FunnelAnalysisController::class, 'conversions'])->name('conversions');
        Route::get('/{funnel}/drop-offs', [FunnelAnalysisController::class, 'dropOffs'])->name('dropOffs');
        Route::get('/{funnel}/edit', [FunnelAnalysisController::class, 'edit'])->name('edit');
        Route::put('/{funnel}', [FunnelAnalysisController::class, 'update'])->name('update');
        Route::delete('/{funnel}', [FunnelAnalysisController::class, 'destroy'])->name('destroy');
    });
    
    // Cohort Analysis Routes
    Route::prefix('cohort')->name('cohort.')->group(function () {
        Route::get('/', [CohortAnalysisController::class, 'index'])->name('index');
        Route::get('/create', [CohortAnalysisController::class, 'create'])->name('create');
        Route::post('/', [CohortAnalysisController::class, 'store'])->name('store');
        Route::get('/{cohort}', [CohortAnalysisController::class, 'show'])->name('show');
        Route::get('/{cohort}/retention', [CohortAnalysisController::class, 'retention'])->name('retention');
        Route::get('/{cohort}/churn', [CohortAnalysisController::class, 'churn'])->name('churn');
        Route::get('/{cohort}/lifetime-value', [CohortAnalysisController::class, 'lifetimeValue'])->name('lifetimeValue');
        Route::get('/{cohort}/edit', [CohortAnalysisController::class, 'edit'])->name('edit');
        Route::put('/{cohort}', [CohortAnalysisController::class, 'update'])->name('update');
        Route::delete('/{cohort}', [CohortAnalysisController::class, 'destroy'])->name('destroy');
    });
    
    // AI Insights Routes
    Route::prefix('ai-insights')->name('aiInsights.')->group(function () {
        Route::get('/', [AiInsightsController::class, 'index'])->name('index');
        Route::get('/generate', [AiInsightsController::class, 'generate'])->name('generate');
        Route::post('/generate', [AiInsightsController::class, 'generateInsights'])->name('generateInsights');
        Route::get('/{insight}', [AiInsightsController::class, 'show'])->name('show');
        Route::get('/{insight}/details', [AiInsightsController::class, 'details'])->name('details');
        Route::get('/{insight}/actionable', [AiInsightsController::class, 'actionable'])->name('actionable');
        Route::post('/{insight}/action', [AiInsightsController::class, 'takeAction'])->name('takeAction');
        Route::get('/history', [AiInsightsController::class, 'history'])->name('history');
        Route::get('/settings', [AiInsightsController::class, 'settings'])->name('settings');
        Route::put('/settings', [AiInsightsController::class, 'updateSettings'])->name('updateSettings');
    });
    
    // Sentiment Analysis Routes
    Route::prefix('sentiment')->name('sentiment.')->group(function () {
        Route::get('/', [SentimentAnalysisController::class, 'index'])->name('index');
        Route::get('/analyze', [SentimentAnalysisController::class, 'analyze'])->name('analyze');
        Route::post('/analyze', [SentimentAnalysisController::class, 'performAnalysis'])->name('performAnalysis');
        Route::get('/{analysis}', [SentimentAnalysisController::class, 'show'])->name('show');
        Route::get('/{analysis}/details', [SentimentAnalysisController::class, 'details'])->name('details');
        Route::get('/trends', [SentimentAnalysisController::class, 'trends'])->name('trends');
        Route::get('/reports', [SentimentAnalysisController::class, 'reports'])->name('reports');
    });
    
    // Trend Analysis Routes
    Route::prefix('trends')->name('trends.')->group(function () {
        Route::get('/', [TrendAnalysisController::class, 'index'])->name('index');
        Route::get('/analyze', [TrendAnalysisController::class, 'analyze'])->name('analyze');
        Route::post('/analyze', [TrendAnalysisController::class, 'performAnalysis'])->name('performAnalysis');
        Route::get('/{trend}', [TrendAnalysisController::class, 'show'])->name('show');
        Route::get('/{trend}/details', [TrendAnalysisController::class, 'details'])->name('details');
        Route::get('/{trend}/forecast', [TrendAnalysisController::class, 'forecast'])->name('forecast');
        Route::get('/reports', [TrendAnalysisController::class, 'reports'])->name('reports');
    });
    
    // Competitive Analysis Routes
    Route::prefix('competitive')->name('competitive.')->group(function () {
        Route::get('/', [CompetitiveAnalysisController::class, 'index'])->name('index');
        Route::get('/analyze', [CompetitiveAnalysisController::class, 'analyze'])->name('analyze');
        Route::get('/{analysis}', [CompetitiveAnalysisController::class, 'show'])->name('show');
        Route::get('/{analysis}/details', [CompetitiveAnalysisController::class, 'details'])->name('details');
        Route::get('/{analysis}/comparison', [CompetitiveAnalysisController::class, 'comparison'])->name('comparison');
        Route::get('/reports', [CompetitiveAnalysisController::class, 'reports'])->name('reports');
        
        Route::post('/analyze', [CompetitiveAnalysisController::class, 'performAnalysis'])->name('performAnalysis');
    });
});
