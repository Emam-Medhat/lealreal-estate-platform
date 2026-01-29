<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarrantyController;

/*
|--------------------------------------------------------------------------
| Warranty Routes
|--------------------------------------------------------------------------
|
| Routes for warranty management, claims, and coverage tracking
|
*/

Route::middleware(['auth', 'admin'])->group(function () {
    
    // Warranty Dashboard
    Route::get('/warranties', [WarrantyController::class, 'index'])->name('warranties.index');
    
    // Warranty Management
    Route::prefix('warranties')->name('warranties.')->group(function () {
        // Warranty Policies
        Route::get('/policies', [WarrantyController::class, 'policiesIndex'])->name('policies.index');
        Route::get('/policies/create', [WarrantyController::class, 'policiesCreate'])->name('policies.create');
        Route::post('/policies', [WarrantyController::class, 'policiesStore'])->name('policies.store');
        Route::get('/policies/{policy}', [WarrantyController::class, 'policiesShow'])->name('policies.show');
        Route::get('/policies/{policy}/edit', [WarrantyController::class, 'policiesEdit'])->name('policies.edit');
        Route::put('/policies/{policy}', [WarrantyController::class, 'policiesUpdate'])->name('policies.update');
        Route::delete('/policies/{policy}', [WarrantyController::class, 'policiesDestroy'])->name('policies.destroy');
        
        // Warranty Claims
        Route::get('/claims', [WarrantyController::class, 'claimsIndex'])->name('claims.index');
        Route::get('/claims/create', [WarrantyController::class, 'claimsCreate'])->name('claims.create');
        Route::post('/claims', [WarrantyController::class, 'claimsStore'])->name('claims.store');
        Route::get('/claims/{claim}', [WarrantyController::class, 'claimsShow'])->name('claims.show');
        Route::get('/claims/{claim}/edit', [WarrantyController::class, 'claimsEdit'])->name('claims.edit');
        Route::put('/claims/{claim}', [WarrantyController::class, 'claimsUpdate'])->name('claims.update');
        Route::delete('/claims/{claim}', [WarrantyController::class, 'claimsDestroy'])->name('claims.destroy');
        
        // Claim Actions
        Route::post('/claims/{claim}/approve', [WarrantyController::class, 'claimsApprove'])->name('claims.approve');
        Route::post('/claims/{claim}/reject', [WarrantyController::class, 'claimsReject'])->name('claims.reject');
        Route::post('/claims/{claim}/process', [WarrantyController::class, 'claimsProcess'])->name('claims.process');
        Route::post('/claims/{claim}/complete', [WarrantyController::class, 'claimsComplete'])->name('claims.complete');
        Route::post('/claims/{claim}/assign', [WarrantyController::class, 'claimsAssign'])->name('claims.assign');
        
        // Warranty Providers
        Route::get('/providers', [WarrantyController::class, 'providersIndex'])->name('providers.index');
        Route::get('/providers/create', [WarrantyController::class, 'providersCreate'])->name('providers.create');
        Route::post('/providers', [WarrantyController::class, 'providersStore'])->name('providers.store');
        Route::get('/providers/{provider}', [WarrantyController::class, 'providersShow'])->name('providers.show');
        Route::get('/providers/{provider}/edit', [WarrantyController::class, 'providersEdit'])->name('providers.edit');
        Route::put('/providers/{provider}', [WarrantyController::class, 'providersUpdate'])->name('providers.update');
        Route::delete('/providers/{provider}', [WarrantyController::class, 'providersDestroy'])->name('providers.destroy');
        
        // Warranty Coverage
        Route::get('/coverage', [WarrantyController::class, 'coverageIndex'])->name('coverage.index');
        Route::get('/coverage/check', [WarrantyController::class, 'coverageCheck'])->name('coverage.check');
        Route::post('/coverage/check', [WarrantyController::class, 'coverageCheckSubmit'])->name('coverage.check.submit');
        
        // Reports and Analytics
        Route::get('/reports', [WarrantyController::class, 'reports'])->name('reports');
        Route::get('/reports/claims', [WarrantyController::class, 'claimsReport'])->name('reports.claims');
        Route::get('/reports/expiring', [WarrantyController::class, 'expiringReport'])->name('reports.expiring');
        Route::get('/reports/providers', [WarrantyController::class, 'providersReport'])->name('reports.providers');
        Route::get('/export', [WarrantyController::class, 'export'])->name('export');
        
        // Notifications
        Route::get('/notifications', [WarrantyController::class, 'notificationsIndex'])->name('notifications.index');
        Route::post('/notifications/send-expiry', [WarrantyController::class, 'sendExpiryNotifications'])->name('notifications.send-expiry');
    });
});
