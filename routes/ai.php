<?php

use App\Http\Controllers\AiPropertyValuationController;
use App\Http\Controllers\AiPropertyDescriptionController;
use App\Http\Controllers\AiPropertyMatchingController;
use App\Http\Controllers\AiPricePredictorController;
use App\Http\Controllers\AiImageRecognitionController;
use App\Http\Controllers\AiVirtualStagingController;
use App\Http\Controllers\AiChatbotController;
use App\Http\Controllers\AiLeadScoringController;
use App\Http\Controllers\AiMarketAnalysisController;
use App\Http\Controllers\AiInvestmentAdvisorController;
use App\Http\Controllers\AiFraudDetectionController;
use App\Http\Controllers\AiRentalPriceOptimizationController;
use App\Http\Controllers\AiPropertyController;
use Illuminate\Support\Facades\Route;

// Real Estate AI System Routes
Route::middleware(['auth', 'verified', 'banned', 'email.verified', 'device.fingerprint', 'track.activity'])
    ->prefix('ai')
    ->name('ai.')
    ->group(function () {

        // AI Property Valuation Routes - Premium feature
        Route::prefix('valuation')->name('valuation.')->middleware(['premium', 'kyc', 'permission:ai.valuation.read'])->group(function () {
            Route::get('/', [AiPropertyValuationController::class, 'index'])->name('index');
            Route::get('/{valuation}', [AiPropertyValuationController::class, 'show'])->name('show');
            Route::get('/{valuation}/report', [AiPropertyValuationController::class, 'report'])->name('report');
            Route::get('/{valuation}/comparison', [AiPropertyValuationController::class, 'comparison'])->name('comparison');
            Route::get('/history', [AiPropertyValuationController::class, 'history'])->name('history');
            Route::get('/statistics', [AiPropertyValuationController::class, 'statistics'])->name('statistics');

            // Create and modify operations
            Route::middleware('permission:ai.valuation.create')->group(function () {
                Route::get('/create', [AiPropertyValuationController::class, 'create'])->name('create');
                Route::post('/', [AiPropertyValuationController::class, 'store'])->name('store');
                Route::post('/{valuation}/analyze', [AiPropertyValuationController::class, 'analyze'])->name('analyze');
                Route::get('/batch', [AiPropertyValuationController::class, 'batch'])->name('batch');
                Route::post('/batch', [AiPropertyValuationController::class, 'batchProcess'])->name('batchProcess');
            });

            // Update and delete operations
            Route::middleware('permission:ai.valuation.update')->group(function () {
                Route::get('/{valuation}/edit', [AiPropertyValuationController::class, 'edit'])->name('edit');
                Route::put('/{valuation}', [AiPropertyValuationController::class, 'update'])->name('update');
            });

            Route::delete('/{valuation}', [AiPropertyValuationController::class, 'destroy'])->name('destroy')->middleware('permission:ai.valuation.delete');
        });

        // AI Property Description Routes - Premium feature
        Route::prefix('description')->name('description.')->middleware(['premium', 'kyc', 'permission:ai.description.read'])->group(function () {
            Route::get('/', [AiPropertyDescriptionController::class, 'index'])->name('index');
            Route::get('/{description}', [AiPropertyDescriptionController::class, 'show'])->name('show');
            Route::get('/{description}/variants', [AiPropertyDescriptionController::class, 'variants'])->name('variants');
            Route::get('/{description}/seo', [AiPropertyDescriptionController::class, 'seo'])->name('seo');
            Route::get('/templates', [AiPropertyDescriptionController::class, 'templates'])->name('templates');

            // Create and modify operations
            Route::middleware('permission:ai.description.create')->group(function () {
                Route::get('/create', [AiPropertyDescriptionController::class, 'create'])->name('create');
                Route::post('/', [AiPropertyDescriptionController::class, 'store'])->name('store');
                Route::post('/{description}/regenerate', [AiPropertyDescriptionController::class, 'regenerate'])->name('regenerate');
                Route::post('/{description}/optimize', [AiPropertyDescriptionController::class, 'optimize'])->name('optimize');
                Route::post('/{description}/translate', [AiPropertyDescriptionController::class, 'translate'])->name('translate');
            });

            // Update and delete operations
            Route::middleware('permission:ai.description.update')->group(function () {
                Route::get('/{description}/edit', [AiPropertyDescriptionController::class, 'edit'])->name('edit');
                Route::put('/{description}', [AiPropertyDescriptionController::class, 'update'])->name('update');
            });

            Route::delete('/{description}', [AiPropertyDescriptionController::class, 'destroy'])->name('destroy')->middleware('permission:ai.description.delete');
        });

        // AI Property Matching Routes - Premium feature
        Route::prefix('matching')->name('matching.')->middleware(['premium', 'kyc', 'permission:ai.matching.read'])->group(function () {
            Route::get('/', [AiPropertyMatchingController::class, 'index'])->name('index');
            Route::get('/results', [AiPropertyMatchingController::class, 'results'])->name('results');
            Route::get('/{match}', [AiPropertyMatchingController::class, 'show'])->name('show');
            Route::get('/{match}/similar', [AiPropertyMatchingController::class, 'similar'])->name('similar');
            Route::get('/history', [AiPropertyMatchingController::class, 'history'])->name('history');
            Route::get('/statistics', [AiPropertyMatchingController::class, 'statistics'])->name('statistics');

            Route::middleware('permission:ai.matching.create')->group(function () {
                Route::post('/find', [AiPropertyMatchingController::class, 'find'])->name('find');
                Route::post('/{match}/contact', [AiPropertyMatchingController::class, 'contact'])->name('contact');
                Route::post('/{match}/save', [AiPropertyController::class, 'save'])->name('save');
                Route::post('/{match}/feedback', [AiPropertyMatchingController::class, 'feedback'])->name('feedback');
            });
        });

        // AI Price Prediction Routes - Premium feature
        Route::prefix('prediction')->name('prediction.')->middleware(['premium', 'kyc', 'permission:ai.prediction.read'])->group(function () {
            Route::get('/', [AiPricePredictorController::class, 'index'])->name('index');
            Route::get('/{prediction}', [AiPricePredictorController::class, 'show'])->name('show');
            Route::get('/{prediction}/details', [AiPricePredictorController::class, 'details'])->name('details');
            Route::get('/{prediction}/factors', [AiPricePredictorController::class, 'factors'])->name('factors');
            Route::get('/{prediction}/confidence', [AiPricePredictorController::class, 'confidence'])->name('confidence');
            Route::get('/{prediction}/timeline', [AiPricePredictorController::class, 'timeline'])->name('timeline');
            Route::get('/market-trends', [AiPricePredictorController::class, 'marketTrends'])->name('marketTrends');
            Route::get('/comparison', [AiPricePredictorController::class, 'comparison'])->name('comparison');
            Route::get('/accuracy', [AiPricePredictorController::class, 'accuracy'])->name('accuracy');

            Route::middleware('permission:ai.prediction.create')->group(function () {
                Route::get('/create', [AiPricePredictorController::class, 'create'])->name('create');
                Route::post('/', [AiPricePredictorController::class, 'predict'])->name('predict');
                Route::post('/{prediction}/update', [AiPricePredictorController::class, 'update'])->name('update');
            });
        });

        // AI Image Recognition Routes - Premium feature
        Route::prefix('image')->name('image.')->middleware(['premium', 'kyc', 'permission:ai.image.read'])->group(function () {
            Route::get('/', [AiImageRecognitionController::class, 'index'])->name('index');
            Route::get('/{analysis}', [AiImageRecognitionController::class, 'show'])->name('show');
            Route::get('/{analysis}/objects', [AiImageRecognitionController::class, 'objects'])->name('objects');
            Route::get('/{analysis}/features', [AiImageRecognitionController::class, 'features'])->name('features');
            Route::get('/{analysis}/metadata', [AiImageRecognitionController::class, 'metadata'])->name('metadata');
            Route::get('/{analysis}/quality', [AiImageRecognitionController::class, 'quality'])->name('quality');
            Route::get('/{analysis}/tags', [AiImageRecognitionController::class, 'tags'])->name('tags');
            Route::get('/history', [AiImageRecognitionController::class, 'history'])->name('history');

            Route::middleware('permission:ai.image.create')->group(function () {
                Route::post('/analyze', [AiImageRecognitionController::class, 'analyze'])->name('analyze');
                Route::post('/{analysis}/enhance', [AiImageRecognitionController::class, 'enhance'])->name('enhance');
                Route::post('/{analysis}/classify', [AiImageRecognitionController::class, 'classify'])->name('classify');
                Route::get('/batch', [AiImageRecognitionController::class, 'batch'])->name('batch');
                Route::post('/batch', [AiImageRecognitionController::class, 'batchProcess'])->name('batchProcess');
            });
        });

        // AI Virtual Staging Routes - Premium feature
        Route::prefix('staging')->name('staging.')->middleware(['premium', 'kyc', 'permission:ai.staging.read'])->group(function () {
            Route::get('/', [AiVirtualStagingController::class, 'index'])->name('index');
            Route::get('/{staging}', [AiVirtualStagingController::class, 'show'])->name('show');
            Route::get('/{staging}/variants', [AiVirtualStagingController::class, 'variants'])->name('variants');
            Route::get('/{staging}/render', [AiVirtualStagingController::class, 'render'])->name('render');
            Route::get('/{staging}/download', [AiVirtualStagingController::class, 'download'])->name('download');
            Route::get('/templates', [AiVirtualStagingController::class, 'templates'])->name('templates');

            Route::middleware('permission:ai.staging.create')->group(function () {
                Route::get('/create', [AiVirtualStagingController::class, 'create'])->name('create');
                Route::post('/', [AiVirtualStagingController::class, 'stage'])->name('stage');
                Route::get('/{staging}/edit', [AiVirtualStagingController::class, 'edit'])->name('edit');
                Route::put('/{staging}', [AiVirtualStagingController::class, 'update'])->name('update');
                Route::post('/{staging}/regenerate', [AiVirtualStagingController::class, 'regenerate'])->name('regenerate');
                Route::post('/{staging}/furniture', [AiVirtualStagingController::class, 'addFurniture'])->name('addFurniture');
                Route::post('/{staging}/lighting', [AiVirtualStagingController::class, 'adjustLighting'])->name('adjustLighting');
                Route::post('/{staging}/materials', [AiVirtualStagingController::class, 'changeMaterials'])->name('changeMaterials');
            });
        });

        // AI Chatbot Routes - Premium feature
        Route::prefix('chatbot')->name('chatbot.')->middleware(['premium', 'kyc', 'permission:ai.chatbot.read'])->group(function () {
            Route::get('/', [AiChatbotController::class, 'index'])->name('index');
            Route::get('/conversation/{id}', [AiChatbotController::class, 'conversation'])->name('conversation');
            Route::get('/conversations', [AiChatbotController::class, 'conversations'])->name('conversations');
            Route::get('/conversation/{id}/export', [AiChatbotController::class, 'export'])->name('export');
            Route::get('/analytics', [AiChatbotController::class, 'analytics'])->name('analytics');
            Route::get('/settings', [AiChatbotController::class, 'settings'])->name('settings');

            Route::middleware('permission:ai.chatbot.create')->group(function () {
                Route::post('/chat', [AiChatbotController::class, 'chat'])->name('chat');
                Route::post('/conversation/{id}/message', [AiChatbotController::class, 'message'])->name('message');
                Route::post('/conversation/{id}/rate', [AiChatbotController::class, 'rate'])->name('rate');
                Route::post('/training/upload', [AiChatbotController::class, 'uploadData'])->name('uploadData');
                Route::post('/training/start', [AiChatbotController::class, 'startTraining'])->name('startTraining');
                Route::put('/settings', [AiChatbotController::class, 'updateSettings'])->name('updateSettings');
            });

            Route::middleware('permission:ai.chatbot.admin')->group(function () {
                Route::get('/training', [AiChatbotController::class, 'training'])->name('training');
            });
        });

        // AI Lead Scoring Routes - Premium feature
        Route::prefix('lead-scoring')->name('leadScoring.')->middleware(['premium', 'kyc', 'permission:ai.lead_scoring.read'])->group(function () {
            Route::get('/', [AiLeadScoringController::class, 'index'])->name('index');
            Route::get('/{score}', [AiLeadScoringController::class, 'show'])->name('show');
            Route::get('/{score}/details', [AiLeadScoringController::class, 'details'])->name('details');
            Route::get('/{score}/factors', [AiLeadScoringController::class, 'factors'])->name('factors');
            Route::get('/{score}/recommendations', [AiLeadScoringController::class, 'recommendations'])->name('recommendations');
            Route::get('/{score}/timeline', [AiLeadScoringController::class, 'timeline'])->name('timeline');
            Route::get('/history', [AiLeadScoringController::class, 'history'])->name('history');
            Route::get('/statistics', [AiLeadScoringController::class, 'statistics'])->name('statistics');
            Route::get('/settings', [AiLeadScoringController::class, 'settings'])->name('settings');

            Route::middleware('permission:ai.lead_scoring.create')->group(function () {
                Route::post('/score', [AiLeadScoringController::class, 'score'])->name('score');
                Route::post('/{score}/update', [AiLeadScoringController::class, 'update'])->name('update');
                Route::get('/batch', [AiLeadScoringController::class, 'batch'])->name('batch');
                Route::post('/batch', [AiLeadScoringController::class, 'batchScore'])->name('batchScore');
                Route::put('/settings', [AiLeadScoringController::class, 'updateSettings'])->name('updateSettings');
            });
        });

        // AI Market Analysis Routes - Premium feature
        Route::prefix('market-analysis')->name('marketAnalysis.')->middleware(['premium', 'kyc', 'permission:ai.market_analysis.read'])->group(function () {
            Route::get('/', [AiMarketAnalysisController::class, 'index'])->name('index');
            Route::get('/{analysis}', [AiMarketAnalysisController::class, 'show'])->name('show');
            Route::get('/{analysis}/trends', [AiMarketAnalysisController::class, 'trends'])->name('trends');
            Route::get('/{analysis}/insights', [AiMarketAnalysisController::class, 'insights'])->name('insights');
            Route::get('/{analysis}/forecast', [AiMarketAnalysisController::class, 'forecast'])->name('forecast');
            Route::get('/{analysis}/comparison', [AiMarketAnalysisController::class, 'comparison'])->name('comparison');
            Route::get('/history', [AiMarketAnalysisController::class, 'history'])->name('history');
            Route::get('/statistics', [AiMarketAnalysisController::class, 'statistics'])->name('statistics');

            Route::middleware('permission:ai.market_analysis.create')->group(function () {
                Route::get('/create', [AiMarketAnalysisController::class, 'create'])->name('create');
                Route::post('/', [AiMarketAnalysisController::class, 'analyze'])->name('analyze');
                Route::post('/{analysis}/update', [AiMarketAnalysisController::class, 'update'])->name('update');
            });
        });

        // AI Investment Advisor Routes - Premium feature
        Route::prefix('investment-advisor')->name('investmentAdvisor.')->middleware(['premium', 'kyc', 'permission:ai.investment_advisor.read'])->group(function () {
            Route::get('/', [AiInvestmentAdvisorController::class, 'index'])->name('index');
            Route::get('/{advice}', [AiInvestmentAdvisorController::class, 'show'])->name('show');
            Route::get('/{advice}/details', [AiInvestmentAdvisorController::class, 'details'])->name('details');
            Route::get('/{advice}/risks', [AiInvestmentAdvisorController::class, 'risks'])->name('risks');
            Route::get('/{advice}/returns', [AiInvestmentAdvisorController::class, 'returns'])->name('returns');
            Route::get('/{advice}/portfolio', [AiInvestmentAdvisorController::class, 'portfolio'])->name('portfolio');
            Route::get('/history', [AiInvestmentAdvisorController::class, 'history'])->name('history');

            Route::middleware('permission:ai.investment_advisor.create')->group(function () {
                Route::get('/create', [AiInvestmentAdvisorController::class, 'create'])->name('create');
                Route::post('/', [AiInvestmentAdvisorController::class, 'advise'])->name('advise');
                Route::post('/{advice}/update', [AiInvestmentAdvisorController::class, 'update'])->name('update');
            });
        });

        // AI Fraud Detection Routes - Premium feature
        Route::prefix('fraud-detection')->name('fraudDetection.')->middleware(['premium', 'kyc', 'permission:ai.fraud_detection.read'])->group(function () {
            Route::get('/', [AiFraudDetectionController::class, 'index'])->name('index');
            Route::get('/{detection}', [AiFraudDetectionController::class, 'show'])->name('show');
            Route::get('/{detection}/details', [AiFraudDetectionController::class, 'details'])->name('details');
            Route::get('/{detection}/risk-score', [AiFraudDetectionController::class, 'riskScore'])->name('riskScore');
            Route::get('/{detection}/factors', [AiFraudDetectionController::class, 'factors'])->name('factors');
            Route::get('/alerts', [AiFraudDetectionController::class, 'alerts'])->name('alerts');
            Route::get('/history', [AiFraudDetectionController::class, 'history'])->name('history');
            Route::get('/statistics', [AiFraudDetectionController::class, 'statistics'])->name('statistics');

            Route::middleware('permission:ai.fraud_detection.create')->group(function () {
                Route::post('/scan', [AiFraudDetectionController::class, 'scan'])->name('scan');
                Route::post('/{detection}/investigate', [AiFraudDetectionController::class, 'investigate'])->name('investigate');
                Route::post('/{detection}/resolve', [AiFraudDetectionController::class, 'resolve'])->name('resolve');
            });
        });

        // AI Rental Price Optimization Routes - Premium feature
        Route::prefix('rental-optimization')->name('rentalOptimization.')->middleware(['premium', 'kyc', 'permission:ai.rental_optimization.read'])->group(function () {
            Route::get('/', [AiRentalPriceOptimizationController::class, 'index'])->name('index');
            Route::get('/{optimization}', [AiRentalPriceOptimizationController::class, 'show'])->name('show');
            Route::get('/{optimization}/analysis', [AiRentalPriceOptimizationController::class, 'analysis'])->name('analysis');
            Route::get('/{optimization}/recommendations', [AiRentalPriceOptimizationController::class, 'recommendations'])->name('recommendations');
            Route::get('/{optimization}/market-comparison', [AiRentalPriceOptimizationController::class, 'marketComparison'])->name('marketComparison');
            Route::get('/history', [AiRentalPriceOptimizationController::class, 'history'])->name('history');
            Route::get('/statistics', [AiRentalPriceOptimizationController::class, 'statistics'])->name('statistics');

            Route::middleware('permission:ai.rental_optimization.create')->group(function () {
                Route::get('/create', [AiRentalPriceOptimizationController::class, 'create'])->name('create');
                Route::post('/', [AiRentalPriceOptimizationController::class, 'optimize'])->name('optimize');
                Route::post('/{optimization}/update', [AiRentalPriceOptimizationController::class, 'update'])->name('update');
            });
        });

        // AI API Routes - For AJAX requests
        Route::prefix('api')->name('api.')->middleware(['log.activity'])->group(function () {
            // Valuation API
            Route::get('/valuation-stats', [AiPropertyValuationController::class, 'getStats'])->name('valuationStats');
            Route::get('/recent-valuations', [AiPropertyValuationController::class, 'getRecentValuations'])->name('recentValuations');
            Route::post('/quick-valuation', [AiPropertyValuationController::class, 'quickValuation'])->name('quickValuation');

            // Description API
            Route::get('/description-stats', [AiPropertyDescriptionController::class, 'getStats'])->name('descriptionStats');
            Route::post('/quick-description', [AiPropertyDescriptionController::class, 'quickDescription'])->name('quickDescription');

            // Matching API
            Route::get('/matching-stats', [AiPropertyMatchingController::class, 'getStats'])->name('matchingStats');
            Route::post('/quick-match', [AiPropertyMatchingController::class, 'quickMatch'])->name('quickMatch');

            // Prediction API
            Route::get('/prediction-stats', [AiPricePredictorController::class, 'getStats'])->name('predictionStats');
            Route::get('/recent-predictions', [AiPricePredictorController::class, 'getRecentPredictions'])->name('recentPredictions');

            // Image Recognition API
            Route::get('/image-stats', [AiImageRecognitionController::class, 'getStats'])->name('imageStats');
            Route::post('/quick-analyze', [AiImageRecognitionController::class, 'quickAnalyze'])->name('quickAnalyze');

            // Staging API
            Route::get('/staging-stats', [AiVirtualStagingController::class, 'getStats'])->name('stagingStats');
            Route::post('/quick-stage', [AiVirtualStagingController::class, 'quickStage'])->name('quickStage');

            // Chatbot API
            Route::get('/chatbot-stats', [AiChatbotController::class, 'getStats'])->name('chatbotStats');
            Route::post('/quick-chat', [AiChatbotController::class, 'quickChat'])->name('quickChat');

            // Lead Scoring API
            Route::get('/lead-scoring-stats', [AiLeadScoringController::class, 'getStats'])->name('leadScoringStats');
            Route::post('/quick-score', [AiLeadScoringController::class, 'quickScore'])->name('quickScore');

            // Market Analysis API
            Route::get('/market-analysis-stats', [AiMarketAnalysisController::class, 'getStats'])->name('marketAnalysisStats');
            Route::get('/recent-insights', [AiMarketAnalysisController::class, 'getRecentInsights'])->name('recentInsights');

            // Investment Advisor API
            Route::get('/investment-advisor-stats', [AiInvestmentAdvisorController::class, 'getStats'])->name('investmentAdvisorStats');
            Route::post('/quick-advice', [AiInvestmentAdvisorController::class, 'quickAdvice'])->name('quickAdvice');

            // Fraud Detection API
            Route::get('/fraud-detection-stats', [AiFraudDetectionController::class, 'getStats'])->name('fraudDetectionStats');
            Route::get('/recent-alerts', [AiFraudDetectionController::class, 'getRecentAlerts'])->name('recentAlerts');

            // Rental Optimization API
            Route::get('/rental-optimization-stats', [AiRentalPriceOptimizationController::class, 'getStats'])->name('rentalOptimizationStats');
            Route::post('/quick-optimize', [AiRentalPriceOptimizationController::class, 'quickOptimize'])->name('quickOptimize');
        });
    });
