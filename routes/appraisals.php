<?php

use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\AppraisalReportController;
use App\Http\Controllers\AppraiserController;
use App\Http\Controllers\PropertyConditionController;

// Appraisals Management Routes
Route::middleware(['auth', 'verified', 'banned', 'email.verified', 'device.fingerprint', 'track.activity'])->group(function () {
    
    // Appraisal Routes
    Route::prefix('appraisals')->name('appraisals.')->group(function () {
        // Public and Read Routes
        Route::middleware('permission:appraisals.read')->group(function () {
            Route::get('/', [AppraisalController::class, 'index'])->name('index');
            Route::get('/{appraisal}', [AppraisalController::class, 'show'])->name('show');
            Route::get('/calendar', [AppraisalController::class, 'calendar'])->name('calendar');
            Route::get('/schedule', [AppraisalController::class, 'schedule'])->name('schedule');
            Route::get('/dashboard', [AppraisalController::class, 'dashboard'])->name('dashboard');
        });
        
        // Create Routes
        Route::middleware('permission:appraisals.create')->group(function () {
            Route::get('/create', [AppraisalController::class, 'create'])->name('create');
            Route::post('/', [AppraisalController::class, 'store'])->name('store');
            Route::post('/{appraisal}/start', [AppraisalController::class, 'start'])->name('start');
            Route::post('/{appraisal}/reschedule', [AppraisalController::class, 'reschedule'])->name('reschedule');
        });
        
        // Update Routes
        Route::middleware('permission:appraisals.update')->group(function () {
            Route::get('/{appraisal}/edit', [AppraisalController::class, 'edit'])->name('edit');
            Route::put('/{appraisal}', [AppraisalController::class, 'update'])->name('update');
        });
        
        // Delete Routes
        Route::delete('/{appraisal}', [AppraisalController::class, 'destroy'])->name('destroy')->middleware('permission:appraisals.delete');
        
        // Action Routes - Premium feature
        Route::middleware(['premium', 'kyc'])->group(function () {
            Route::middleware('permission:appraisals.complete')->group(function () {
                Route::post('/{appraisal}/complete', [AppraisalController::class, 'complete'])->name('complete');
            });
            Route::middleware('permission:appraisals.cancel')->group(function () {
                Route::post('/{appraisal}/cancel', [AppraisalController::class, 'cancel'])->name('cancel');
            });
        });
        
        // Export Routes - Premium feature
        Route::middleware(['premium', 'kyc', 'permission:appraisals.export'])->group(function () {
            Route::get('/export', [AppraisalController::class, 'export'])->name('export');
        });
    });
    
    // Appraisal Report Routes
    Route::prefix('appraisal-reports')->name('appraisal-reports.')->group(function () {
        // Read Routes
        Route::middleware('permission:appraisal_reports.read')->group(function () {
            Route::get('/', [AppraisalReportController::class, 'index'])->name('index');
            Route::get('/{report}', [AppraisalReportController::class, 'show'])->name('show');
        });
        
        // Create Routes
        Route::middleware('permission:appraisal_reports.create')->group(function () {
            Route::get('/create/{appraisal}', [AppraisalReportController::class, 'create'])->name('create');
            Route::post('/', [AppraisalReportController::class, 'store'])->name('store');
        });
        
        // Update Routes
        Route::middleware('permission:appraisal_reports.update')->group(function () {
            Route::get('/{report}/edit', [AppraisalReportController::class, 'edit'])->name('edit');
            Route::put('/{report}', [AppraisalReportController::class, 'update'])->name('update');
        });
        
        // Delete Routes
        Route::delete('/{report}', [AppraisalReportController::class, 'destroy'])->name('destroy')->middleware('permission:appraisal_reports.delete');
        
        // Report Actions - Premium feature
        Route::middleware(['premium', 'kyc'])->group(function () {
            Route::middleware('permission:appraisal_reports.download')->group(function () {
                Route::get('/{report}/download', [AppraisalReportController::class, 'download'])->name('download');
            });
            Route::middleware('permission:appraisal_reports.email')->group(function () {
                Route::post('/{report}/email', [AppraisalReportController::class, 'email'])->name('email');
            });
        });
        
        // Report Management Routes - Premium feature
        Route::middleware(['premium', 'kyc'])->group(function () {
            Route::middleware('permission:appraisal_reports.approve')->group(function () {
                Route::post('/{report}/approve', [AppraisalReportController::class, 'approve'])->name('approve');
            });
            Route::middleware('permission:appraisal_reports.reject')->group(function () {
                Route::post('/{report}/reject', [AppraisalReportController::class, 'reject'])->name('reject');
            });
            Route::middleware('permission:appraisal_reports.manage')->group(function () {
                Route::get('/{report}/certificate', [AppraisalReportController::class, 'certificate'])->name('certificate');
                Route::post('/{report}/add-photo', [AppraisalReportController::class, 'addPhoto'])->name('add-photo');
                Route::delete('/{report}/remove-photo/{photo}', [AppraisalReportController::class, 'removePhoto'])->name('remove-photo');
                Route::post('/{report}/add-attachment', [AppraisalReportController::class, 'addAttachment'])->name('add-attachment');
                Route::delete('/{report}/remove-attachment/{attachment}', [AppraisalReportController::class, 'removeAttachment'])->name('remove-attachment');
            });
        });
        
        // Dashboard and Export
        Route::middleware('permission:appraisal_reports.read')->group(function () {
            Route::get('/dashboard', [AppraisalReportController::class, 'dashboard'])->name('dashboard');
        });
        
        Route::middleware(['premium', 'kyc', 'permission:appraisal_reports.export'])->group(function () {
            Route::get('/export', [AppraisalReportController::class, 'export'])->name('export');
        });
    });
    
    // Appraiser Routes - Premium feature
    Route::prefix('appraisers')->name('appraisers.')->middleware(['premium', 'kyc'])->group(function () {
        // Read Routes
        Route::middleware('permission:appraisers.read')->group(function () {
            Route::get('/', [AppraiserController::class, 'index'])->name('index');
            Route::get('/{appraiser}', [AppraiserController::class, 'show'])->name('show');
            Route::get('/{appraiser}/schedule', [AppraiserController::class, 'schedule'])->name('schedule');
            Route::get('/{appraiser}/performance', [AppraiserController::class, 'performance'])->name('performance');
            Route::get('/{appraiser}/certifications', [AppraiserController::class, 'certifications'])->name('certifications');
            Route::get('/dashboard', [AppraiserController::class, 'dashboard'])->name('dashboard');
        });
        
        // Create Routes
        Route::middleware('permission:appraisers.create')->group(function () {
            Route::get('/create', [AppraiserController::class, 'create'])->name('create');
            Route::post('/', [AppraiserController::class, 'store'])->name('store');
            Route::post('/{appraiser}/add-certification', [AppraiserController::class, 'addCertification'])->name('add-certification');
        });
        
        // Update Routes
        Route::middleware('permission:appraisers.update')->group(function () {
            Route::get('/{appraiser}/edit', [AppraiserController::class, 'edit'])->name('edit');
            Route::put('/{appraiser}', [AppraiserController::class, 'update'])->name('update');
        });
        
        // Delete Routes
        Route::middleware('permission:appraisers.delete')->group(function () {
            Route::delete('/{appraiser}', [AppraiserController::class, 'destroy'])->name('destroy');
            Route::delete('/{appraiser}/remove-certification/{certification}', [AppraiserController::class, 'removeCertification'])->name('remove-certification');
        });
        
        // Export Routes
        Route::middleware('permission:appraisers.export')->group(function () {
            Route::get('/export', [AppraiserController::class, 'export'])->name('export');
        });
    });
    
    // Property Condition Routes - Premium feature
    Route::prefix('property-conditions')->name('property-conditions.')->middleware(['premium', 'kyc'])->group(function () {
        // Read Routes
        Route::middleware('permission:property_conditions.read')->group(function () {
            Route::get('/', [PropertyConditionController::class, 'index'])->name('index');
            Route::get('/{condition}', [PropertyConditionController::class, 'show'])->name('show');
        });
        
        // Create Routes
        Route::middleware('permission:property_conditions.create')->group(function () {
            Route::get('/create', [PropertyConditionController::class, 'create'])->name('create');
            Route::post('/', [PropertyConditionController::class, 'store'])->name('store');
        });
        
        // Update Routes
        Route::middleware('permission:property_conditions.update')->group(function () {
            Route::get('/{condition}/edit', [PropertyConditionController::class, 'edit'])->name('edit');
            Route::put('/{condition}', [PropertyConditionController::class, 'update'])->name('update');
        });
        
        // Delete Routes
        Route::delete('/{condition}', [PropertyConditionController::class, 'destroy'])->name('destroy')->middleware('permission:property_conditions.delete');
        
        // Condition Actions - Premium feature
        Route::middleware('permission:property_conditions.read')->group(function () {
            Route::get('/{condition}/report', [PropertyConditionController::class, 'report'])->name('report');
            Route::get('/dashboard', [PropertyConditionController::class, 'dashboard'])->name('dashboard');
        });
        
        Route::middleware(['premium', 'kyc', 'permission:property_conditions.download'])->group(function () {
            Route::get('/{condition}/download', [PropertyConditionController::class, 'download'])->name('download');
            Route::get('/export', [PropertyConditionController::class, 'export'])->name('export');
        });
    });
});
