<?php

use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InspectionReportController;
use App\Http\Controllers\InspectorController;
use App\Http\Controllers\DefectController;
use App\Http\Controllers\RepairEstimateController;
use App\Http\Controllers\ComplianceCheckController;
use App\Http\Controllers\CertificationController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Inspection Routes
    Route::prefix('inspections')->name('inspections.')->group(function () {
        Route::get('/', [InspectionController::class, 'index'])->name('index');
        Route::get('/create', [InspectionController::class, 'create'])->name('create');
        Route::post('/', [InspectionController::class, 'store'])->name('store');
        Route::get('/{inspection}', [InspectionController::class, 'show'])->name('show');
        Route::get('/{inspection}/edit', [InspectionController::class, 'edit'])->name('edit');
        Route::put('/{inspection}', [InspectionController::class, 'update'])->name('update');
        Route::delete('/{inspection}', [InspectionController::class, 'destroy'])->name('destroy');
        
        // Inspection Actions
        Route::post('/{inspection}/start', [InspectionController::class, 'start'])->name('start');
        Route::post('/{inspection}/complete', [InspectionController::class, 'complete'])->name('complete');
        Route::post('/{inspection}/cancel', [InspectionController::class, 'cancel'])->name('cancel');
        Route::post('/{inspection}/reschedule', [InspectionController::class, 'reschedule'])->name('reschedule');
        
        // Calendar and Dashboard
        Route::get('/calendar', [InspectionController::class, 'calendar'])->name('calendar');
        Route::get('/schedule', [InspectionController::class, 'schedule'])->name('schedule');
        Route::get('/dashboard', [InspectionController::class, 'dashboard'])->name('dashboard');
        
        // Export
        Route::get('/export', [InspectionController::class, 'export'])->name('export');
    });
    
    // Inspection Report Routes
    Route::prefix('inspection-reports')->name('inspection-reports.')->group(function () {
        Route::get('/', [InspectionReportController::class, 'index'])->name('index');
        Route::get('/create/{inspection}', [InspectionReportController::class, 'create'])->name('create');
        Route::post('/', [InspectionReportController::class, 'store'])->name('store');
        Route::get('/{report}', [InspectionReportController::class, 'show'])->name('show');
        Route::get('/{report}/edit', [InspectionReportController::class, 'edit'])->name('edit');
        Route::put('/{report}', [InspectionReportController::class, 'update'])->name('update');
        Route::delete('/{report}', [InspectionReportController::class, 'destroy'])->name('destroy');
        
        // Report Actions
        Route::get('/{report}/download', [InspectionReportController::class, 'download'])->name('download');
        Route::post('/{report}/email', [InspectionReportController::class, 'email'])->name('email');
        Route::post('/{report}/add-photo', [InspectionReportController::class, 'addPhoto'])->name('add-photo');
        Route::delete('/{report}/remove-photo/{photo}', [InspectionReportController::class, 'removePhoto'])->name('remove-photo');
    });
    
    // Inspector Routes
    Route::prefix('inspectors')->name('inspectors.')->group(function () {
        Route::get('/', [InspectorController::class, 'index'])->name('index');
        Route::get('/create', [InspectorController::class, 'create'])->name('create');
        Route::post('/', [InspectorController::class, 'store'])->name('store');
        Route::get('/{inspector}', [InspectorController::class, 'show'])->name('show');
        Route::get('/{inspector}/edit', [InspectorController::class, 'edit'])->name('edit');
        Route::put('/{inspector}', [InspectorController::class, 'update'])->name('update');
        Route::delete('/{inspector}', [InspectorController::class, 'destroy'])->name('destroy');
        
        // Inspector Actions
        Route::get('/{inspector}/schedule', [InspectorController::class, 'schedule'])->name('schedule');
        Route::get('/{inspector}/performance', [InspectorController::class, 'performance'])->name('performance');
        Route::get('/{inspector}/certifications', [InspectorController::class, 'certifications'])->name('certifications');
        Route::post('/{inspector}/add-certification', [InspectorController::class, 'addCertification'])->name('add-certification');
        Route::delete('/{inspector}/remove-certification/{certification}', [InspectorController::class, 'removeCertification'])->name('remove-certification');
        
        // Dashboard
        Route::get('/dashboard', [InspectorController::class, 'dashboard'])->name('dashboard');
        
        // Export
        Route::get('/export', [InspectorController::class, 'export'])->name('export');
    });
    
    // Defect Routes
    Route::prefix('defects')->name('defects.')->group(function () {
        Route::get('/', [DefectController::class, 'index'])->name('index');
        Route::get('/create', [DefectController::class, 'create'])->name('create');
        Route::post('/', [DefectController::class, 'store'])->name('store');
        Route::get('/{defect}', [DefectController::class, 'show'])->name('show');
        Route::get('/{defect}/edit', [DefectController::class, 'edit'])->name('edit');
        Route::put('/{defect}', [DefectController::class, 'update'])->name('update');
        Route::delete('/{defect}', [DefectController::class, 'destroy'])->name('destroy');
        
        // Defect Actions
        Route::post('/{defect}/assign', [DefectController::class, 'assign'])->name('assign');
        Route::post('/{defect}/complete', [DefectController::class, 'complete'])->name('complete');
        Route::post('/{defect}/defer', [DefectController::class, 'defer'])->name('defer');
        
        // Dashboard
        Route::get('/dashboard', [DefectController::class, 'dashboard'])->name('dashboard');
        
        // Export
        Route::get('/export', [DefectController::class, 'export'])->name('export');
    });
    
    // Repair Estimate Routes
    Route::prefix('repair-estimates')->name('repair-estimates.')->group(function () {
        Route::get('/', [RepairEstimateController::class, 'index'])->name('index');
        Route::get('/create', [RepairEstimateController::class, 'create'])->name('create');
        Route::post('/', [RepairEstimateController::class, 'store'])->name('store');
        Route::get('/{estimate}', [RepairEstimateController::class, 'show'])->name('show');
        Route::get('/{estimate}/edit', [RepairEstimateController::class, 'edit'])->name('edit');
        Route::put('/{estimate}', [RepairEstimateController::class, 'update'])->name('update');
        Route::delete('/{estimate}', [RepairEstimateController::class, 'destroy'])->name('destroy');
        
        // Estimate Actions
        Route::post('/{estimate}/approve', [RepairEstimateController::class, 'approve'])->name('approve');
        Route::post('/{estimate}/reject', [RepairEstimateController::class, 'reject'])->name('reject');
        Route::get('/{estimate}/download', [RepairEstimateController::class, 'download'])->name('download');
        Route::post('/{estimate}/add-photo', [RepairEstimateController::class, 'addPhoto'])->name('add-photo');
        Route::delete('/{estimate}/remove-photo/{photo}', [RepairEstimateController::class, 'removePhoto'])->name('remove-photo');
        
        // Dashboard
        Route::get('/dashboard', [RepairEstimateController::class, 'dashboard'])->name('dashboard');
        
        // Export
        Route::get('/export', [RepairEstimateController::class, 'export'])->name('export');
    });
    
    // Compliance Check Routes
    Route::prefix('compliance-checks')->name('compliance-checks.')->group(function () {
        Route::get('/', [ComplianceCheckController::class, 'index'])->name('index');
        Route::get('/create', [ComplianceCheckController::class, 'create'])->name('create');
        Route::post('/', [ComplianceCheckController::class, 'store'])->name('store');
        Route::get('/{check}', [ComplianceCheckController::class, 'show'])->name('show');
        Route::get('/{check}/edit', [ComplianceCheckController::class, 'edit'])->name('edit');
        Route::put('/{check}', [ComplianceCheckController::class, 'update'])->name('update');
        Route::delete('/{check}', [ComplianceCheckController::class, 'destroy'])->name('destroy');
        
        // Compliance Actions
        Route::post('/{check}/start', [ComplianceCheckController::class, 'start'])->name('start');
        Route::post('/{check}/complete', [ComplianceCheckController::class, 'complete'])->name('complete');
        Route::get('/{check}/report', [ComplianceCheckController::class, 'report'])->name('report');
        Route::get('/{check}/certificate', [ComplianceCheckController::class, 'certificate'])->name('certificate');
        Route::post('/{check}/add-photo', [ComplianceCheckController::class, 'addPhoto'])->name('add-photo');
        Route::delete('/{check}/remove-photo/{photo}', [ComplianceCheckController::class, 'removePhoto'])->name('remove-photo');
        
        // Dashboard
        Route::get('/dashboard', [ComplianceCheckController::class, 'dashboard'])->name('dashboard');
        
        // Export
        Route::get('/export', [ComplianceCheckController::class, 'export'])->name('export');
    });
    
    // Certification Routes
    Route::prefix('certifications')->name('certifications.')->group(function () {
        Route::get('/', [CertificationController::class, 'index'])->name('index');
        Route::get('/create', [CertificationController::class, 'create'])->name('create');
        Route::post('/', [CertificationController::class, 'store'])->name('store');
        Route::get('/{certification}', [CertificationController::class, 'show'])->name('show');
        Route::get('/{certification}/edit', [CertificationController::class, 'edit'])->name('edit');
        Route::put('/{certification}', [CertificationController::class, 'update'])->name('update');
        Route::delete('/{certification}', [CertificationController::class, 'destroy'])->name('destroy');
        
        // Certification Actions
        Route::post('/{certification}/renew', [CertificationController::class, 'renew'])->name('renew');
        Route::post('/{certification}/suspend', [CertificationController::class, 'suspend'])->name('suspend');
        Route::post('/{certification}/reactivate', [CertificationController::class, 'reactivate'])->name('reactivate');
        Route::post('/{certification}/revoke', [CertificationController::class, 'revoke'])->name('revoke');
        Route::get('/{certification}/download', [CertificationController::class, 'download'])->name('download');
        Route::get('/{certification}/verify', [CertificationController::class, 'verify'])->name('verify');
        Route::post('/{certification}/add-attachment', [CertificationController::class, 'addAttachment'])->name('add-attachment');
        Route::delete('/{certification}/remove-attachment/{attachment}', [CertificationController::class, 'removeAttachment'])->name('remove-attachment');
        
        // Dashboard
        Route::get('/dashboard', [CertificationController::class, 'dashboard'])->name('dashboard');
        
        // Export
        Route::get('/export', [CertificationController::class, 'export'])->name('export');
    });
});
