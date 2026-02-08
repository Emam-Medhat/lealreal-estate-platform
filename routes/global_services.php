<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Currency\CurrencyController;
use App\Http\Controllers\Language\LanguageController;
use App\Http\Controllers\Gamification\GamificationController;
use App\Http\Controllers\Blockchain\BlockchainController;
use App\Http\Controllers\Enterprise\EnterpriseController;
use App\Http\Controllers\AI\AIController;

// Currency Routes
Route::prefix('api/currency')->name('currency.')->group(function () {
    Route::get('/rates', [CurrencyController::class, 'getRates'])->name('rates');
    Route::post('/convert', [CurrencyController::class, 'convert'])->name('convert');
    Route::post('/update-rates', [CurrencyController::class, 'updateRates'])->name('update_rates');
    Route::get('/history', [CurrencyController::class, 'getHistory'])->name('history');
    Route::get('/statistics', [CurrencyController::class, 'getStatistics'])->name('statistics');
    Route::post('/set-preferred', [CurrencyController::class, 'setPreferredCurrency'])->name('set_preferred');
    Route::get('/supported', [CurrencyController::class, 'getSupported'])->name('supported');
});

// Language Routes
Route::prefix('api/language')->name('language.')->group(function () {
    Route::post('/translate', [LanguageController::class, 'translate'])->name('translate');
    Route::post('/set', [LanguageController::class, 'setLanguage'])->name('set');
    Route::get('/current', [LanguageController::class, 'getCurrent'])->name('current');
    Route::get('/supported', [LanguageController::class, 'getSupported'])->name('supported');
    Route::post('/add-translation', [LanguageController::class, 'addTranslation'])->name('add_translation');
    Route::get('/translations', [LanguageController::class, 'getTranslations'])->name('translations');
    Route::post('/import', [LanguageController::class, 'importTranslations'])->name('import');
    Route::get('/export', [LanguageController::class, 'exportTranslations'])->name('export');
    Route::get('/statistics', [LanguageController::class, 'getStatistics'])->name('statistics');
    Route::post('/generate-files', [LanguageController::class, 'generateFiles'])->name('generate_files');
});

// Gamification Routes
Route::prefix('api/gamification')->name('gamification.')->group(function () {
    Route::post('/track-activity', [GamificationController::class, 'trackActivity'])->name('track_activity');
    Route::get('/profile', [GamificationController::class, 'getProfile'])->name('profile');
    Route::get('/leaderboard', [GamificationController::class, 'getLeaderboard'])->name('leaderboard');
    Route::get('/statistics', [GamificationController::class, 'getStatistics'])->name('statistics');
});

// Blockchain Routes
Route::prefix('api/blockchain')->name('blockchain.')->group(function () {
    Route::post('/deploy-contract', [BlockchainController::class, 'deployContract'])->name('deploy_contract');
    Route::post('/mint-nft', [BlockchainController::class, 'mintNFT'])->name('mint_nft');
    Route::post('/execute-function', [BlockchainController::class, 'executeFunction'])->name('execute_function');
    Route::get('/balance', [BlockchainController::class, 'getBalance'])->name('balance');
    Route::get('/transaction-status', [BlockchainController::class, 'getTransactionStatus'])->name('transaction_status');
    Route::get('/property-nfts', [BlockchainController::class, 'getPropertyNFTs'])->name('property_nfts');
    Route::get('/statistics', [BlockchainController::class, 'getStatistics'])->name('statistics');
    Route::get('/contracts', [BlockchainController::class, 'getContracts'])->name('contracts');
    Route::get('/contracts/{address}', [BlockchainController::class, 'getContract'])->name('get_contract');
    Route::post('/create-dao', [BlockchainController::class, 'createDAO'])->name('create_dao');
});

// Enterprise Routes
Route::prefix('api/enterprise')->name('enterprise.')->group(function () {
    Route::post('/create-account', [EnterpriseController::class, 'createAccount'])->name('create_account');
    Route::post('/upgrade-subscription', [EnterpriseController::class, 'upgradeSubscription'])->name('upgrade_subscription');
    Route::post('/configure-tenant', [EnterpriseController::class, 'configureTenant'])->name('configure_tenant');
    Route::get('/dashboard', [EnterpriseController::class, 'getDashboard'])->name('dashboard');
    Route::post('/generate-report', [EnterpriseController::class, 'generateReport'])->name('generate_report');
    Route::post('/manage-users', [EnterpriseController::class, 'manageUsers'])->name('manage_users');
    Route::post('/integrate-system', [EnterpriseController::class, 'integrateSystem'])->name('integrate_system');
    Route::get('/plans', [EnterpriseController::class, 'getPlans'])->name('plans');
});

// AI Routes
Route::prefix('api/ai')->name('ai.')->group(function () {
    Route::post('/generate-description', [AIController::class, 'generatePropertyDescription'])->name('generate_description');
    Route::post('/generate-images', [AIController::class, 'generatePropertyImages'])->name('generate_images');
    Route::post('/analyze-investment', [AIController::class, 'analyzeInvestment'])->name('analyze_investment');
    Route::post('/predict-trends', [AIController::class, 'predictMarketTrends'])->name('predict_trends');
    Route::post('/get-recommendations', [AIController::class, 'getRecommendations'])->name('get_recommendations');
    Route::post('/analyze-behavior', [AIController::class, 'analyzeBehavior'])->name('analyze_behavior');
    Route::post('/optimize-pricing', [AIController::class, 'optimizePricing'])->name('optimize_pricing');
    Route::post('/generate-report', [AIController::class, 'generateMarketReport'])->name('generate_report');
    Route::post('/chat', [AIController::class, 'chat'])->name('chat');
});

// Web Routes for Views
Route::middleware(['auth'])->group(function () {
    // Currency
    Route::get('/currency', function () {
        $currencyService = app(\App\Services\CurrencyService::class);
        return view('currency.index', [
            'currencies' => $currencyService->getSupportedCurrencies()
        ]);
    })->name('currency.index');

    // Gamification
    Route::get('/gamification', function () {
        $gamificationService = app(\App\Services\GamificationService::class);
        $profile = $gamificationService->getUserGamificationProfile(auth()->id());
        return view('gamification.dashboard', ['profile' => $profile['profile'] ?? []]);
    })->name('gamification.dashboard');

    // Blockchain
    Route::get('/blockchain', function () {
        $blockchainService = app(\App\Services\BlockchainService::class);
        $statistics = $blockchainService->getBlockchainStatistics();
        $contracts = \App\Models\SmartContract::with(['deployer'])->get();
        $nfts = \App\Models\NFT::with(['property'])->get();
        $properties = \App\Models\Property::all();
        
        return view('blockchain.dashboard', [
            'statistics' => $statistics['statistics'] ?? [],
            'contracts' => $contracts,
            'nfts' => $nfts,
            'properties' => $properties
        ]);
    })->name('blockchain.dashboard');

    // Enterprise
    Route::get('/enterprise', function () {
        return view('enterprise.dashboard');
    })->name('enterprise.dashboard');

    // AI
    Route::get('/ai', function () {
        return view('ai.dashboard');
    })->name('ai.dashboard');
});
