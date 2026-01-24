<?php

use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\Subscription\SubscriptionPlanController;
use App\Http\Controllers\Subscription\BillingController;
use Illuminate\Support\Facades\Route;

// Subscription Routes
Route::prefix('subscription')->name('subscription.')->group(function () {
    
    // Subscription Plans - Public (accessible without auth for redirect purposes)
    Route::get('/plans', [SubscriptionPlanController::class, 'index'])->name('plans');
    Route::get('/plans/{plan}', [SubscriptionPlanController::class, 'show'])->name('plans.show');
    
    // Subscription Management - Authenticated users only
    Route::middleware(['auth'])->group(function () {
        Route::middleware('permission:subscription.read')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('index');
            Route::get('/current', [SubscriptionController::class, 'current'])->name('current');
            Route::get('/history', [SubscriptionController::class, 'history'])->name('history');
            Route::get('/invoices', [SubscriptionController::class, 'invoices'])->name('invoices');
        });
        
        // Subscription Actions
        Route::middleware('permission:subscription.create')->group(function () {
            Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscribe');
            Route::post('/upgrade', [SubscriptionController::class, 'upgrade'])->name('upgrade');
            Route::post('/downgrade', [SubscriptionController::class, 'downgrade'])->name('downgrade');
            Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
            Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
        });
        
        // Payment Processing
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/process/{subscription}', [BillingController::class, 'process'])->name('process');
            Route::post('/confirm/{subscription}', [BillingController::class, 'confirm'])->name('confirm');
            Route::get('/success/{subscription}', [BillingController::class, 'success'])->name('success');
            Route::get('/failed/{subscription}', [BillingController::class, 'failed'])->name('failed');
            Route::post('/webhook', [BillingController::class, 'webhook'])->name('webhook');
        });
        
        // Admin Routes
        Route::middleware(['admin', 'permission:subscription.admin'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [SubscriptionController::class, 'adminDashboard'])->name('dashboard');
            Route::get('/users', [SubscriptionController::class, 'users'])->name('users');
            Route::get('/revenue', [SubscriptionController::class, 'revenue'])->name('revenue');
            Route::get('/analytics', [SubscriptionController::class, 'analytics'])->name('analytics');
            
            // Plan Management
            Route::resource('plans', SubscriptionPlanController::class)->except(['show']);
            Route::post('/plans/{plan}/toggle', [SubscriptionPlanController::class, 'toggle'])->name('plans.toggle');
            
            // User Subscription Management
            Route::post('/users/{user}/subscribe/{plan}', [SubscriptionController::class, 'adminSubscribe'])->name('users.subscribe');
            Route::post('/users/{user}/cancel', [SubscriptionController::class, 'adminCancel'])->name('users.cancel');
            Route::post('/users/{user}/extend', [SubscriptionController::class, 'extendSubscription'])->name('users.extend');
        });
        
        // API Routes for AJAX
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/stats', [SubscriptionController::class, 'getStats'])->name('stats');
            Route::get('/usage', [SubscriptionController::class, 'getUsage'])->name('usage');
            Route::get('/features/{plan}', [SubscriptionPlanController::class, 'getFeatures'])->name('plans.features');
            Route::post('/validate-coupon', [BillingController::class, 'validateCoupon'])->name('validate-coupon');
        });
    });
});
