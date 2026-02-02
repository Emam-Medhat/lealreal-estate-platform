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
    
    // Risk Assessment
    Route::get('/risk-assessment', [DefiRiskAssessmentController::class, 'index'])->name('risk-assessment.index');
    Route::get('/risk-assessment/property/{id}', [DefiRiskAssessmentController::class, 'property'])->name('risk-assessment.property');
    Route::post('/risk-assessment/evaluate', [DefiRiskAssessmentController::class, 'evaluate'])->name('risk-assessment.evaluate');
    
    // Dashboard
    Route::get('/dashboard', [DefiDashboardController::class, 'index'])->name('dashboard.index');
});
