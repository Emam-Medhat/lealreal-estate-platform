<?php

use App\Http\Controllers\WarrantyController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Warranty Routes
    Route::prefix('warranties')->name('warranties.')->group(function () {
        Route::get('/', [WarrantyController::class, 'index'])->name('index');
        Route::get('/create', [WarrantyController::class, 'create'])->name('create');
        Route::post('/', [WarrantyController::class, 'store'])->name('store');
        Route::get('/{warranty}', [WarrantyController::class, 'show'])->name('show');
        Route::get('/{warranty}/edit', [WarrantyController::class, 'edit'])->name('edit');
        Route::put('/{warranty}', [WarrantyController::class, 'update'])->name('update');
        Route::delete('/{warranty}', [WarrantyController::class, 'destroy'])->name('destroy');
        
        // Warranty Actions
        Route::post('/{warranty}/renew', [WarrantyController::class, 'renew'])->name('renew');
        Route::post('/{warranty}/extend', [WarrantyController::class, 'extend'])->name('extend');
        Route::post('/{warranty}/suspend', [WarrantyController::class, 'suspend'])->name('suspend');
        Route::post('/{warranty}/reactivate', [WarrantyController::class, 'reactivate'])->name('reactivate');
        Route::post('/{warranty}/revoke', [WarrantyController::class, 'revoke'])->name('revoke');
        Route::post('/{warranty}/transfer', [WarrantyController::class, 'transfer'])->name('transfer');
        
        // Claims
        Route::get('/{warranty}/claims', [WarrantyController::class, 'claims'])->name('claims');
        Route::get('/{warranty}/create-claim', [WarrantyController::class, 'createClaim'])->name('create-claim');
        Route::post('/{warranty}/submit-claim', [WarrantyController::class, 'submitClaim'])->name('submit-claim');
        Route::get('/{warranty}/claim/{claim}', [WarrantyController::class, 'showClaim'])->name('show-claim');
        Route::put('/{warranty}/claim/{claim}', [WarrantyController::class, 'updateClaim'])->name('update-claim');
        
        // Documents
        Route::get('/{warranty}/certificate', [WarrantyController::class, 'certificate'])->name('certificate');
        Route::get('/{warranty}/download-certificate', [WarrantyController::class, 'downloadCertificate'])->name('download-certificate');
        Route::post('/{warranty}/add-document', [WarrantyController::class, 'addDocument'])->name('add-document');
        Route::delete('/{warranty}/remove-document/{document}', [WarrantyController::class, 'removeDocument'])->name('remove-document');
        
        // Maintenance Records
        Route::get('/{warranty}/maintenance', [WarrantyController::class, 'maintenance'])->name('maintenance');
        Route::post('/{warranty}/add-maintenance', [WarrantyController::class, 'addMaintenance'])->name('add-maintenance');
        Route::put('/{warranty}/maintenance/{record}', [WarrantyController::class, 'updateMaintenance'])->name('update-maintenance');
        
        // Verification
        Route::get('/{warranty}/verify', [WarrantyController::class, 'verify'])->name('verify');
        Route::post('/{warranty}/validate', [WarrantyController::class, 'validate'])->name('validate');
        
        // Dashboard
        Route::get('/dashboard', [WarrantyController::class, 'dashboard'])->name('dashboard');
        Route::get('/expiring', [WarrantyController::class, 'expiring'])->name('expiring');
        Route::get('/expired', [WarrantyController::class, 'expired'])->name('expired');
        
        // Export
        Route::get('/export', [WarrantyController::class, 'export'])->name('export');
        Route::get('/{warranty}/export', [WarrantyController::class, 'exportWarranty'])->name('export-warranty');
        
        // Search and Filter
        Route::get('/search', [WarrantyController::class, 'search'])->name('search');
        Route::get('/by-property/{property}', [WarrantyController::class, 'byProperty'])->name('by-property');
        Route::get('/by-provider/{provider}', [WarrantyController::class, 'byProvider'])->name('by-provider');
    });
});
