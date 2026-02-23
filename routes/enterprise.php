<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Enterprise\EnterpriseController;

/*
|--------------------------------------------------------------------------
| Enterprise Routes
|--------------------------------------------------------------------------
|
| Routes for enterprise management, accounts, and subscriptions
|
*/

Route::middleware(['auth', 'admin'])->prefix('enterprise')->name('enterprise.')->group(function () {
    
    // Dashboard
    Route::get('/', [EnterpriseController::class, 'dashboard'])->name('index');
    Route::get('/dashboard', [EnterpriseController::class, 'dashboard'])->name('dashboard');
    
    // Accounts (Tenants)
    Route::get('/accounts', [EnterpriseController::class, 'accounts'])->name('accounts');
    Route::get('/accounts/create', [EnterpriseController::class, 'createAccount'])->name('accounts.create');
    Route::post('/accounts', [EnterpriseController::class, 'storeAccount'])->name('accounts.store');
    Route::get('/accounts/{id}', [EnterpriseController::class, 'viewAccount'])->name('accounts.show');
    Route::get('/accounts/{id}/view', [EnterpriseController::class, 'viewAccount'])->name('accounts.view');
    Route::get('/accounts/{id}/edit', [EnterpriseController::class, 'editAccount'])->name('accounts.edit');
    Route::put('/accounts/{id}', [EnterpriseController::class, 'updateAccount'])->name('accounts.update');
    Route::post('/accounts/{id}/toggle-status', [EnterpriseController::class, 'toggleAccountStatus'])->name('accounts.toggle-status');
    
    // Tenants (Alias for Accounts)
    Route::get('/tenants', [EnterpriseController::class, 'accounts'])->name('tenants');

    // Reports & Billing
    Route::get('/reports', [EnterpriseController::class, 'reports'])->name('reports');
    Route::get('/billing', [EnterpriseController::class, 'billing'])->name('billing');
    
    // Subscriptions
    Route::get('/subscriptions', [EnterpriseController::class, 'subscriptions'])->name('subscriptions');
    Route::get('/subscriptions/create', [EnterpriseController::class, 'createSubscription'])->name('subscriptions.create');
    Route::post('/subscriptions', [EnterpriseController::class, 'storeSubscription'])->name('subscriptions.store');
    Route::get('/subscriptions/{id}', [EnterpriseController::class, 'viewSubscription'])->name('subscriptions.show');
    Route::get('/subscriptions/{id}/edit', [EnterpriseController::class, 'editSubscription'])->name('subscriptions.edit');
    Route::put('/subscriptions/{id}', [EnterpriseController::class, 'updateSubscription'])->name('subscriptions.update');
    
    // Reports
    Route::get('/reports', [EnterpriseController::class, 'reports'])->name('reports');
});
