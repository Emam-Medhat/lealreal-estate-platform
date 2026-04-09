<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Defi\DefiCrowdfundingController;
use App\Http\Controllers\Defi\DefiLoanController;
use App\Http\Controllers\Defi\DefiRiskAssessmentController;
use App\Http\Controllers\Defi\DefiDashboardController;

Route::middleware(['auth', 'admin'])->prefix('defi')->name('defi.')->group(function () {
    // Crowdfunding
    Route::get('/crowdfunding', [DefiCrowdfundingController::class, 'index'])->name('crowdfunding.index');
    Route::get('/crowdfunding/create', [DefiCrowdfundingController::class, 'create'])->name('crowdfunding.create');
    Route::post('/crowdfunding', [DefiCrowdfundingController::class, 'store'])->name('crowdfunding.store');
    Route::get('/crowdfunding/{id}', [DefiCrowdfundingController::class, 'show'])->name('crowdfunding.show');
    Route::post('/crowdfunding/{id}/invest', [DefiCrowdfundingController::class, 'invest'])->name('crowdfunding.invest');
    
    // DeFi Loans
    Route::get('/loans', [DefiLoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/create', [DefiLoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [DefiLoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/{id}', [DefiLoanController::class, 'show'])->name('loans.show');
    Route::post('/loans/{id}/approve', [DefiLoanController::class, 'approve'])->name('loans.approve');
    Route::get('/loans/{id}/approve', function($id) {
        return redirect()->route('defi.loans.show', $id)
            ->with('error', 'يرجى استخدام أزرار الموافقة والرفض في صفحة تفاصيل القرض');
    })->name('loans.approve.get');
    Route::post('/loans/{id}/reject', [DefiLoanController::class, 'reject'])->name('loans.reject');
    Route::get('/loans/{id}/reject', function($id) {
        return redirect()->route('defi.loans.show', $id)
            ->with('error', 'يرجى استخدام أزرار الموافقة والرفض في صفحة تفاصيل القرض');
    })->name('loans.reject.get');
    Route::get('/loans/{id}/repay', [DefiLoanController::class, 'repayForm'])->name('loans.repay.form');
    Route::post('/loans/{id}/repay', [DefiLoanController::class, 'repay'])->name('loans.repay');
    
    // Risk Assessment
    Route::get('/risk-assessment', [DefiRiskAssessmentController::class, 'index'])->name('risk-assessment.index');
    Route::get('/risk-assessment/property/{id}', [DefiRiskAssessmentController::class, 'property'])->name('risk-assessment.property');
    Route::post('/risk-assessment/evaluate', [DefiRiskAssessmentController::class, 'evaluate'])->name('risk-assessment.evaluate');
    
    // Dashboard
    Route::get('/dashboard', [DefiDashboardController::class, 'index'])->name('dashboard.index');
});
