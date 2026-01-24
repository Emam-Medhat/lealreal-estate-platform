<?php

use App\Http\Controllers\MaintenanceTeamController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Maintenance Teams Routes
    Route::prefix('maintenance-teams')->name('maintenance-teams.')->group(function () {
        Route::get('/', [MaintenanceTeamController::class, 'index'])->name('index');
        Route::get('/create', [MaintenanceTeamController::class, 'create'])->name('create');
        Route::post('/', [MaintenanceTeamController::class, 'store'])->name('store');
        Route::get('/{team}', [MaintenanceTeamController::class, 'show'])->name('show');
        Route::get('/{team}/edit', [MaintenanceTeamController::class, 'edit'])->name('edit');
        Route::put('/{team}', [MaintenanceTeamController::class, 'update'])->name('update');
        Route::delete('/{team}', [MaintenanceTeamController::class, 'destroy'])->name('destroy');
        
        // Team Actions
        Route::post('/{team}/add-member', [MaintenanceTeamController::class, 'addMember'])->name('add-member');
        Route::delete('/{team}/remove-member/{member}', [MaintenanceTeamController::class, 'removeMember'])->name('remove-member');
        Route::post('/{team}/set-availability', [MaintenanceTeamController::class, 'setAvailability'])->name('set-availability');
        Route::get('/{team}/performance', [MaintenanceTeamController::class, 'performance'])->name('performance');
        Route::get('/{team}/schedule', [MaintenanceTeamController::class, 'schedule'])->name('schedule');
        Route::get('/{team}/work-history', [MaintenanceTeamController::class, 'workHistory'])->name('work-history');
        
        // Dashboard
        Route::get('/dashboard', [MaintenanceTeamController::class, 'dashboard'])->name('dashboard');
        
        // Export
        Route::get('/export', [MaintenanceTeamController::class, 'export'])->name('export');
    });
});
