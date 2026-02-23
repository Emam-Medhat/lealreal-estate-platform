<?php

use App\Http\Controllers\Subscription\SubscriptionPlanController;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\Subscription\SubscriptionFeatureController;

// Subscription Plans (Admin)
Route::prefix('/admin-plans')->name('plans.')->group(function () {
    Route::get('/', [SubscriptionPlanController::class, 'index'])->name('index');
    Route::get('/create', [SubscriptionPlanController::class, 'create'])->name('create');
    Route::post('/', [SubscriptionPlanController::class, 'store'])->name('store');
    Route::get('/{plan}', [SubscriptionPlanController::class, 'show'])->name('show');
    Route::get('/{plan}/edit', [SubscriptionPlanController::class, 'edit'])->name('edit');
    Route::put('/{plan}', [SubscriptionPlanController::class, 'update'])->name('update');
    Route::delete('/{plan}', [SubscriptionPlanController::class, 'destroy'])->name('destroy');
    Route::post('/{plan}/duplicate', [SubscriptionPlanController::class, 'duplicate'])->name('duplicate');
    Route::post('/{plan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/stats', [SubscriptionPlanController::class, 'getPlanStats'])->name('stats');
    Route::get('/compare', [SubscriptionPlanController::class, 'comparePlans'])->name('admin-compare');
});

// Subscription Management
Route::get('/', [SubscriptionController::class, 'index'])->name('index');
Route::get('/create', [SubscriptionController::class, 'create'])->name('create');
Route::post('/', [SubscriptionController::class, 'store'])->name('store');
Route::get('/{subscription}', [SubscriptionController::class, 'show'])->name('show');
Route::get('/{subscription}/edit', [SubscriptionController::class, 'edit'])->name('edit');
Route::put('/{subscription}', [SubscriptionController::class, 'update'])->name('update');
Route::delete('/{subscription}', [SubscriptionController::class, 'destroy'])->name('destroy');
Route::get('/{subscription}/payment', [SubscriptionController::class, 'payment'])->name('payment');
Route::post('/{subscription}/process-payment', [SubscriptionController::class, 'processPayment'])->name('process-payment');
Route::post('/{subscription}/renew', [SubscriptionController::class, 'renew'])->name('renew');
Route::get('/{subscription}/usage', [SubscriptionController::class, 'usage'])->name('usage');
Route::get('/{subscription}/invoices', [SubscriptionController::class, 'invoices'])->name('invoices');
Route::get('/stats', [SubscriptionController::class, 'getSubscriptionStats'])->name('stats');

// Subscription Features
Route::prefix('/features')->name('features.')->group(function () {
    Route::get('/', [SubscriptionFeatureController::class, 'index'])->name('index');
    Route::get('/create', [SubscriptionFeatureController::class, 'create'])->name('create');
    Route::post('/', [SubscriptionFeatureController::class, 'store'])->name('store');
    Route::get('/{feature}', [SubscriptionFeatureController::class, 'show'])->name('show');
    Route::get('/{feature}/edit', [SubscriptionFeatureController::class, 'edit'])->name('edit');
    Route::put('/{feature}', [SubscriptionFeatureController::class, 'update'])->name('update');
    Route::delete('/{feature}', [SubscriptionFeatureController::class, 'destroy'])->name('destroy');
});
