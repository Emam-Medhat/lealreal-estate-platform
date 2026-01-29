<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaintenanceController;

/*
|--------------------------------------------------------------------------
| Maintenance Routes
|--------------------------------------------------------------------------
|
| Routes for maintenance management, scheduling, and operations
|
*/

Route::middleware(['auth'])->group(function () {
    
    // Maintenance Dashboard & Main Index
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    
    // Maintenance Management
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        // Create Maintenance
        Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store');
        
        // View/Edit Maintenance
        Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('show');
        Route::get('/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('edit');
        Route::put('/{maintenance}', [MaintenanceController::class, 'update'])->name('update');
        Route::delete('/{maintenance}', [MaintenanceController::class, 'destroy'])->name('destroy');
        
        // Maintenance Actions
        Route::post('/{maintenance}/start', [MaintenanceController::class, 'startWork'])->name('start');
        Route::post('/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('complete');
        Route::post('/{maintenance}/cancel', [MaintenanceController::class, 'cancel'])->name('cancel');
        Route::post('/{maintenance}/schedule', [MaintenanceController::class, 'schedule'])->name('schedule');
        Route::post('/{maintenance}/assign', [MaintenanceController::class, 'assign'])->name('assign');

        // Maintenance Schedule Management
        Route::prefix('schedule')->name('schedule.')->group(function () {
            Route::get('/', [MaintenanceController::class, 'scheduleIndex'])->name('index');
            Route::get('/create', [MaintenanceController::class, 'scheduleCreate'])->name('create');
            Route::post('/', [MaintenanceController::class, 'scheduleStore'])->name('store');
            Route::get('/{schedule}', [MaintenanceController::class, 'scheduleShow'])->name('show');
            Route::get('/{schedule}/edit', [MaintenanceController::class, 'scheduleEdit'])->name('edit');
            Route::put('/{schedule}', [MaintenanceController::class, 'scheduleUpdate'])->name('update');
            Route::delete('/{schedule}', [MaintenanceController::class, 'scheduleDestroy'])->name('destroy');
        });

        // Maintenance Calendar (Available to all auth users)
        Route::get('/calendar', [MaintenanceController::class, 'calendar'])->name('calendar');
        Route::get('/calendar/events', [MaintenanceController::class, 'calendarEvents'])->name('calendar.events');
    });
});

// Admin-only maintenance routes
Route::middleware(['auth', 'admin'])->prefix('maintenance')->name('maintenance.')->group(function () {
    
    // Work Orders Management
    Route::prefix('workorders')->name('workorders.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'workOrderIndex'])->name('index');
        Route::get('/create', [MaintenanceController::class, 'workOrderCreate'])->name('create');
        Route::post('/', [MaintenanceController::class, 'workOrderStore'])->name('store');
        Route::get('/{workOrder}', [MaintenanceController::class, 'workOrderShow'])->name('show');
        Route::get('/{workOrder}/edit', [MaintenanceController::class, 'workOrderEdit'])->name('edit');
        Route::put('/{workOrder}', [MaintenanceController::class, 'workOrderUpdate'])->name('update');
        Route::delete('/{workOrder}', [MaintenanceController::class, 'workOrderDestroy'])->name('destroy');
        
        // Work Order Actions
        Route::post('/{workOrder}/assign', [MaintenanceController::class, 'workOrderAssign'])->name('assign');
        Route::post('/{workOrder}/start', [MaintenanceController::class, 'workOrderStart'])->name('start');
        Route::post('/{workOrder}/complete', [MaintenanceController::class, 'workOrderComplete'])->name('complete');
        Route::post('/{workOrder}/cancel', [MaintenanceController::class, 'workOrderCancel'])->name('cancel');
        Route::post('/{workOrder}/approve', [MaintenanceController::class, 'workOrderApprove'])->name('approve');
        Route::post('/{workOrder}/reject', [MaintenanceController::class, 'workOrderReject'])->name('reject');
    });
    
    // Maintenance Teams Management
    Route::prefix('teams')->name('teams.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'teamIndex'])->name('index');
        Route::get('/create', [MaintenanceController::class, 'teamCreate'])->name('create');
        Route::post('/', [MaintenanceController::class, 'teamStore'])->name('store');
        Route::get('/{team}', [MaintenanceController::class, 'teamShow'])->name('show');
        Route::get('/{team}/edit', [MaintenanceController::class, 'teamEdit'])->name('edit');
        Route::put('/{team}', [MaintenanceController::class, 'teamUpdate'])->name('update');
        Route::delete('/{team}', [MaintenanceController::class, 'teamDestroy'])->name('destroy');
        
        // Team Actions
        Route::post('/{team}/add-member', [MaintenanceController::class, 'teamAddMember'])->name('add-member');
        Route::delete('/{team}/remove-member/{user}', [MaintenanceController::class, 'teamRemoveMember'])->name('remove-member');
        Route::post('/{team}/toggle-status', [MaintenanceController::class, 'teamToggleStatus'])->name('toggle-status');
        Route::get('/{team}/workload', [MaintenanceController::class, 'teamWorkload'])->name('workload');
    });
    
    // Maintenance Reports & Export
    Route::get('/reports', [MaintenanceController::class, 'reports'])->name('reports');
    Route::get('/export', [MaintenanceController::class, 'export'])->name('export');
});
