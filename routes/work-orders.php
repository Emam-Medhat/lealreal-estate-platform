<?php

use App\Http\Controllers\WorkOrderController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Work Orders Routes
    Route::prefix('work-orders')->name('work-orders.')->group(function () {
        Route::get('/', [WorkOrderController::class, 'index'])->name('index');
        Route::get('/create', [WorkOrderController::class, 'create'])->name('create');
        Route::post('/', [WorkOrderController::class, 'store'])->name('store');
        Route::get('/{order}', [WorkOrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [WorkOrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [WorkOrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [WorkOrderController::class, 'destroy'])->name('destroy');
        
        // Work Order Actions
        Route::post('/{order}/start', [WorkOrderController::class, 'start'])->name('start');
        Route::post('/{order}/pause', [WorkOrderController::class, 'pause'])->name('pause');
        Route::post('/{order}/resume', [WorkOrderController::class, 'resume'])->name('resume');
        Route::post('/{order}/complete', [WorkOrderController::class, 'complete'])->name('complete');
        Route::post('/{order}/cancel', [WorkOrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/approve', [WorkOrderController::class, 'approve'])->name('approve');
        Route::post('/{order}/reject', [WorkOrderController::class, 'reject'])->name('reject');
        
        // Time Tracking
        Route::post('/{order}/start-time', [WorkOrderController::class, 'startTime'])->name('start-time');
        Route::post('/{order}/stop-time', [WorkOrderController::class, 'stopTime'])->name('stop-time');
        Route::get('/{order}/time-logs', [WorkOrderController::class, 'timeLogs'])->name('time-logs');
        Route::post('/{order}/add-time-log', [WorkOrderController::class, 'addTimeLog'])->name('add-time-log');
        
        // Items and Materials
        Route::post('/{order}/add-item', [WorkOrderController::class, 'addItem'])->name('add-item');
        Route::put('/{order}/update-item/{item}', [WorkOrderController::class, 'updateItem'])->name('update-item');
        Route::delete('/{order}/remove-item/{item}', [WorkOrderController::class, 'removeItem'])->name('remove-item');
        Route::get('/{order}/items', [WorkOrderController::class, 'items'])->name('items');
        
        // Photos and Documents
        Route::post('/{order}/add-photo', [WorkOrderController::class, 'addPhoto'])->name('add-photo');
        Route::delete('/{order}/remove-photo/{photo}', [WorkOrderController::class, 'removePhoto'])->name('remove-photo');
        Route::post('/{order}/add-document', [WorkOrderController::class, 'addDocument'])->name('add-document');
        Route::delete('/{order}/remove-document/{document}', [WorkOrderController::class, 'removeDocument'])->name('remove-document');
        
        // Checklist
        Route::get('/{order}/checklist', [WorkOrderController::class, 'checklist'])->name('checklist');
        Route::post('/{order}/update-checklist', [WorkOrderController::class, 'updateChecklist'])->name('update-checklist');
        
        // Customer Approval
        Route::post('/{order}/request-approval', [WorkOrderController::class, 'requestApproval'])->name('request-approval');
        Route::post('/{order}/customer-approve', [WorkOrderController::class, 'customerApprove'])->name('customer-approve');
        
        // Reports
        Route::get('/{order}/download', [WorkOrderController::class, 'download'])->name('download');
        Route::get('/{order}/pdf', [WorkOrderController::class, 'pdf'])->name('pdf');
        Route::get('/{order}/report', [WorkOrderController::class, 'report'])->name('report');
        
        // Dashboard
        Route::get('/dashboard', [WorkOrderController::class, 'dashboard'])->name('dashboard');
        
        // Export
        Route::get('/export', [WorkOrderController::class, 'export'])->name('export');
    });
});
