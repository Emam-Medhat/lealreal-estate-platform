<?php

use App\Http\Controllers\Payment\CheckoutController;
use App\Http\Controllers\Payment\WebhookController;
use Illuminate\Support\Facades\Route;

// Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/properties/{property}/checkout', [CheckoutController::class, 'show'])->name('payments.checkout');
    Route::post('/properties/{property}/checkout', [CheckoutController::class, 'process'])->name('payments.process');
    Route::get('/payment/callback', [CheckoutController::class, 'callback'])->name('payments.callback');
});

// Webhooks (Exclude from CSRF)
Route::post('/webhooks/payment/{gateway}', [WebhookController::class, 'handle'])->name('webhooks.payment');
