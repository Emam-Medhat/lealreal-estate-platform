<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;

/*
|--------------------------------------------------------------------------
| Inventory Routes
|--------------------------------------------------------------------------
|
| Routes for inventory management, parts, supplies, and equipment
|
*/

Route::middleware(['auth'])->prefix('inventory')->name('inventory.')->group(function () {
    
    // Test route
    Route::get('/test', function() {
        return 'Inventory routes are working!';
    })->name('test');
    
    // Inventory Dashboard
    Route::get('/', [InventoryController::class, 'index'])->name('index');
    
    // Simple Inventory Routes (for compatibility)
    Route::get('/create', [InventoryController::class, 'create'])->name('create');
    Route::post('/', [InventoryController::class, 'store'])->name('store');
    Route::get('/{item}', [InventoryController::class, 'show'])->name('show');
    Route::get('/{item}/edit', [InventoryController::class, 'edit'])->name('edit');
    Route::put('/{item}', [InventoryController::class, 'update'])->name('update');
    Route::delete('/{item}', [InventoryController::class, 'destroy'])->name('destroy');
    
    // Items Management
    Route::get('/items', [InventoryController::class, 'itemsIndex'])->name('items.index');
    Route::get('/items/create', [InventoryController::class, 'itemsCreate'])->name('items.create');
    Route::post('/items', [InventoryController::class, 'itemsStore'])->name('items.store');
    Route::get('/items/{item}', [InventoryController::class, 'itemsShow'])->name('items.show');
    Route::get('/items/{item}/edit', [InventoryController::class, 'itemsEdit'])->name('items.edit');
    Route::put('/items/{item}', [InventoryController::class, 'itemsUpdate'])->name('items.update');
    Route::delete('/items/{item}', [InventoryController::class, 'itemsDestroy'])->name('items.destroy');
    
    // Item Actions
    Route::post('/items/{item}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('items.adjust-stock');
    Route::post('/items/{item}/reorder', [InventoryController::class, 'reorderItem'])->name('items.reorder');
    Route::post('/items/{item}/toggle-status', [InventoryController::class, 'toggleItemStatus'])->name('items.toggle-status');
    
    // Categories Management
    Route::get('/categories', [InventoryController::class, 'categoriesIndex'])->name('categories.index');
    Route::get('/categories/create', [InventoryController::class, 'categoriesCreate'])->name('categories.create');
    Route::post('/categories', [InventoryController::class, 'categoriesStore'])->name('categories.store');
    Route::get('/categories/{category}', [InventoryController::class, 'categoriesShow'])->name('categories.show');
    Route::get('/categories/{category}/edit', [InventoryController::class, 'categoriesEdit'])->name('categories.edit');
    Route::put('/categories/{category}', [InventoryController::class, 'categoriesUpdate'])->name('categories.update');
    Route::delete('/categories/{category}', [InventoryController::class, 'categoriesDestroy'])->name('categories.destroy');
    
    // Suppliers Management
    Route::get('/suppliers', [InventoryController::class, 'suppliersIndex'])->name('suppliers.index');
    Route::get('/suppliers/create', [InventoryController::class, 'suppliersCreate'])->name('suppliers.create');
    Route::post('/suppliers', [InventoryController::class, 'suppliersStore'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}', [InventoryController::class, 'suppliersShow'])->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit', [InventoryController::class, 'suppliersEdit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [InventoryController::class, 'suppliersUpdate'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [InventoryController::class, 'suppliersDestroy'])->name('suppliers.destroy');
    
    // Stock Movements
    Route::get('/movements', [InventoryController::class, 'movementsIndex'])->name('movements.index');
    Route::get('/movements/create', [InventoryController::class, 'movementsCreate'])->name('movements.create');
    Route::post('/movements', [InventoryController::class, 'movementsStore'])->name('movements.store');
    Route::get('/movements/{movement}', [InventoryController::class, 'movementsShow'])->name('movements.show');
    
    // Reports and Analytics
    Route::get('/reports', [InventoryController::class, 'reports'])->name('reports');
    Route::get('/reports/stock-levels', [InventoryController::class, 'stockLevelsReport'])->name('reports.stock-levels');
    Route::get('/reports/movements', [InventoryController::class, 'movementsReport'])->name('reports.movements');
    Route::get('/reports/valuation', [InventoryController::class, 'valuationReport'])->name('reports.valuation');
    Route::get('/export', [InventoryController::class, 'export'])->name('export');
    
    // Low Stock Alerts
    Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
    Route::post('/low-stock/send-alerts', [InventoryController::class, 'sendLowStockAlerts'])->name('low-stock.send-alerts');
});
