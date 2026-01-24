<?php

use App\Http\Controllers\InventoryController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Inventory Routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{item}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{item}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{item}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{item}', [InventoryController::class, 'destroy'])->name('destroy');
        
        // Inventory Actions
        Route::post('/{item}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('adjust-stock');
        Route::post('/{item}/reserve', [InventoryController::class, 'reserve'])->name('reserve');
        Route::post('/{item}/release', [InventoryController::class, 'release'])->name('release');
        Route::post('/{item}/reorder', [InventoryController::class, 'reorder'])->name('reorder');
        Route::get('/{item}/history', [InventoryController::class, 'history'])->name('history');
        Route::get('/{item}/usage', [InventoryController::class, 'usage'])->name('usage');
        Route::post('/{item}/add-photo', [InventoryController::class, 'addPhoto'])->name('add-photo');
        Route::delete('/{item}/remove-photo/{photo}', [InventoryController::class, 'removePhoto'])->name('remove-photo');
        
        // Stock Management
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
        Route::get('/out-of-stock', [InventoryController::class, 'outOfStock'])->name('out-of-stock');
        Route::get('/reorder-alerts', [InventoryController::class, 'reorderAlerts'])->name('reorder-alerts');
        Route::post('/bulk-reorder', [InventoryController::class, 'bulkReorder'])->name('bulk-reorder');
        
        // Reports
        Route::get('/valuation', [InventoryController::class, 'valuation'])->name('valuation');
        Route::get('/usage-report', [InventoryController::class, 'usageReport'])->name('usage-report');
        Route::get('/movement-report', [InventoryController::class, 'movementReport'])->name('movement-report');
        
        // Dashboard
        Route::get('/dashboard', [InventoryController::class, 'dashboard'])->name('dashboard');
        
        // Export
        Route::get('/export', [InventoryController::class, 'export'])->name('export');
        Route::post('/import', [InventoryController::class, 'import'])->name('import');
        
        // Barcode/QR Code
        Route::get('/{item}/barcode', [InventoryController::class, 'barcode'])->name('barcode');
        Route::get('/{item}/qrcode', [InventoryController::class, 'qrcode'])->name('qrcode');
        Route::post('/{item}/generate-barcode', [InventoryController::class, 'generateBarcode'])->name('generate-barcode');
    });
});
