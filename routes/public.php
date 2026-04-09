<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Investor\InvestorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
|
| These routes are accessible without authentication.
|
*/

// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public Routes
Route::get('/agents', [AgentController::class, 'directory'])->name('agents.directory');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Public investor stats route
Route::get('/investor/stats', [InvestorController::class, 'getInvestorStats'])->name('investor.stats.public');

// API route for JSON data
Route::get('/api/investor/stats', [InvestorController::class, 'getInvestorStatsApi'])->name('investor.stats.api');

// Public investment opportunities route
Route::get('/investor/opportunities', [InvestorController::class, 'getInvestmentOpportunities'])->name('investor.opportunities.public');

// Public investment funds route
Route::get('/investor/funds', [InvestorController::class, 'getInvestmentFunds'])->name('investor.funds.public');

// API route for investor alerts
Route::post('/api/investor/alerts', [\App\Http\Controllers\Investor\InvestorAlertController::class, 'store'])->name('investor.alerts.store');

// Test routes (public)
Route::get('/test-properties', function () {
    $propertyTypes = \App\Models\PropertyType::select('id', 'name', 'slug')
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('properties.simple_index', ['propertyTypes' => $propertyTypes]);
})->name('test.properties');

Route::get('/test-investor-stats', function() {
    return 'Investor stats test route works!';
});

Route::get('/public-test', function () {
    return 'Public route works! User: ' . (auth()->check() ? auth()->user()->name : 'Not logged in');
});

Route::get('/test-subscriptions', function() {
    return 'Test route works!';
});

Route::get('/test-pricing', function() {
    $plans = \App\Models\SubscriptionPlan::where('is_active', true)->get();
    return 'Found ' . $plans->count() . ' plans';
});

Route::get('/test-notifications', function () {
    return view('test-notifications');
});

Route::get('/clear-cache', function () {
    \Cache::flush();
    return 'Cache cleared!';
});

// Agent Reviews (public)
Route::post('/agent-reviews', [\App\Http\Controllers\Agent\AgentReviewController::class, 'store'])->name('agent.reviews.store');
