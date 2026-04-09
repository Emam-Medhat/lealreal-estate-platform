<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Currency\CurrencyController;
use App\Http\Controllers\Language\LanguageController;
use App\Http\Controllers\Gamification\ExtendedGamificationController;
use App\Http\Controllers\Blockchain\BlockchainController;
use App\Http\Controllers\Enterprise\EnterpriseController;
use App\Http\Controllers\AI\AIController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Auth::routes();

// Home Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Include all service routes
require __DIR__.'/web_services.php';
require __DIR__.'/global_services.php';

// Currency Routes
Route::prefix('currency')->name('currency.')->group(function () {
    Route::get('/', [CurrencyController::class, 'index'])->name('index');
    Route::get('/converter', [CurrencyController::class, 'converter'])->name('converter');
    Route::get('/rates', [CurrencyController::class, 'rates'])->name('rates');
    Route::get('/history', [CurrencyController::class, 'history'])->name('history');
    Route::get('/statistics', [CurrencyController::class, 'statistics'])->name('statistics');
});

// Language Routes
Route::prefix('language')->name('language.')->group(function () {
    Route::get('/', [LanguageController::class, 'index'])->name('index');
    Route::get('/translations', [LanguageController::class, 'translations'])->name('translations');
    Route::get('/import', [LanguageController::class, 'import'])->name('import');
    Route::get('/export', [LanguageController::class, 'export'])->name('export');
    Route::get('/statistics', [LanguageController::class, 'statistics'])->name('statistics');
});

// Gamification Routes
Route::prefix('gamification')->name('gamification.')->group(function () {
    Route::get('/', [ExtendedGamificationController::class, 'index'])->name('index');
    Route::get('/dashboard', [ExtendedGamificationController::class, 'dashboard'])->name('dashboard');
    Route::get('/achievements', [ExtendedGamificationController::class, 'achievements'])->name('achievements');
    Route::get('/badges', [ExtendedGamificationController::class, 'badges'])->name('badges');
    Route::get('/rewards', [ExtendedGamificationController::class, 'rewards'])->name('rewards');
    Route::get('/challenges', [ExtendedGamificationController::class, 'challenges'])->name('challenges');
    Route::get('/leaderboard', [ExtendedGamificationController::class, 'leaderboard'])->name('leaderboard');
});

// Blockchain Routes
Route::prefix('blockchain')->name('blockchain.')->group(function () {
    Route::get('/', [BlockchainController::class, 'index'])->name('index');
    Route::get('/dashboard', [BlockchainController::class, 'dashboard'])->name('dashboard');
    Route::get('/contracts', [BlockchainController::class, 'contracts'])->name('contracts');
    Route::get('/contracts/create', [BlockchainController::class, 'createContract'])->name('contracts.create');
    Route::get('/contracts/{address}', [BlockchainController::class, 'showContract'])->name('contracts.show');
    Route::get('/nfts', [BlockchainController::class, 'nfts'])->name('nfts');
    Route::get('/nfts/create', [BlockchainController::class, 'createNFT'])->name('nfts.create');
    Route::get('/transactions', [BlockchainController::class, 'transactions'])->name('transactions');
    Route::get('/daos', [BlockchainController::class, 'daos'])->name('daos');
    Route::get('/statistics', [BlockchainController::class, 'statistics'])->name('statistics');
});

// Enterprise Routes
Route::prefix('enterprise')->name('enterprise.')->group(function () {
    Route::get('/', [EnterpriseController::class, 'index'])->name('index');
    Route::get('/dashboard', [EnterpriseController::class, 'dashboard'])->name('dashboard');
    Route::get('/accounts', [EnterpriseController::class, 'accounts'])->name('accounts');
    Route::get('/accounts/create', [EnterpriseController::class, 'createAccount'])->name('accounts.create');
    Route::get('/accounts/{id}', [EnterpriseController::class, 'showAccount'])->name('accounts.show');
    Route::get('/subscriptions', [EnterpriseController::class, 'subscriptions'])->name('subscriptions');
    Route::get('/tenants', [EnterpriseController::class, 'tenants'])->name('tenants');
    Route::get('/reports', [EnterpriseController::class, 'reports'])->name('reports');
    Route::get('/integrations', [EnterpriseController::class, 'integrations'])->name('integrations');
});

// AI Routes
Route::prefix('ai')->name('ai.')->group(function () {
    Route::get('/', [AIController::class, 'index'])->name('index');
    Route::get('/dashboard', [AIController::class, 'dashboard'])->name('dashboard');
    Route::get('/descriptions', [AIController::class, 'descriptions'])->name('descriptions');
    Route::get('/images', [AIController::class, 'images'])->name('images');
    Route::get('/analysis', [AIController::class, 'analysis'])->name('analysis');
    Route::get('/predictions', [AIController::class, 'predictions'])->name('predictions');
    Route::get('/recommendations', [AIController::class, 'recommendations'])->name('recommendations');
    Route::get('/pricing', [AIController::class, 'pricing'])->name('pricing');
    Route::get('/reports', [AIController::class, 'reports'])->name('reports');
    Route::get('/chat', [AIController::class, 'chat'])->name('chat');
});

// Admin Routes (protected)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Include existing admin routes
    require __DIR__.'/admin.php';
});

// Agent Routes (protected)
Route::middleware(['auth', 'agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', function () {
        return view('agent.dashboard');
    })->name('dashboard');
    
    // Include existing agent routes
    require __DIR__.'/agents.php';
});

// User Routes (protected)
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');
});

// Fallback Route
Route::fallback(function () {
    return view('errors.404');
});
