<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\InsurancePolicyController;
use App\Http\Controllers\InsuranceProviderController;
use App\Http\Controllers\InsuranceClaimController;
use App\Http\Controllers\InsuranceQuoteController;
use App\Http\Controllers\InsuranceCoverageController;
use App\Http\Controllers\InsuranceRenewalController;
use App\Http\Controllers\RiskAssessmentController;
use App\Http\Controllers\InsuranceInspectionController;
use App\Http\Controllers\InsurancePaymentController;
use App\Http\Controllers\InsuranceDocumentController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Insurance Dashboard
    Route::get('/insurance', [InsuranceController::class, 'dashboard'])->name('insurance.dashboard');
    Route::get('/insurance/index', [InsuranceController::class, 'index'])->name('insurance.index');
    
    // Insurance Policies
    Route::get('/insurance/policies', [InsurancePolicyController::class, 'index'])->name('insurance.policies.index');
    Route::get('/insurance/policies/create', [InsurancePolicyController::class, 'create'])->name('insurance.policies.create');
    Route::post('/insurance/policies', [InsurancePolicyController::class, 'store'])->name('insurance.policies.store');
    Route::get('/insurance/policies/{policy}', [InsurancePolicyController::class, 'show'])->name('insurance.policies.show');
    Route::get('/insurance/policies/{policy}/edit', [InsurancePolicyController::class, 'edit'])->name('insurance.policies.edit');
    Route::put('/insurance/policies/{policy}', [InsurancePolicyController::class, 'update'])->name('insurance.policies.update');
    Route::delete('/insurance/policies/{policy}', [InsurancePolicyController::class, 'destroy'])->name('insurance.policies.destroy');
    
    // Policy Actions
    Route::post('/insurance/policies/{policy}/activate', [InsurancePolicyController::class, 'activate'])->name('insurance.policies.activate');
    Route::post('/insurance/policies/{policy}/suspend', [InsurancePolicyController::class, 'suspend'])->name('insurance.policies.suspend');
    Route::post('/insurance/policies/{policy}/cancel', [InsurancePolicyController::class, 'cancel'])->name('insurance.policies.cancel');
    Route::post('/insurance/policies/{policy}/renew', [InsurancePolicyController::class, 'renew'])->name('insurance.policies.renew');
    Route::get('/insurance/policies/{policy}/download', [InsurancePolicyController::class, 'download'])->name('insurance.policies.download');
    Route::post('/insurance/policies/{policy}/send-reminder', [InsurancePolicyController::class, 'sendReminder'])->name('insurance.policies.send-reminder');
    Route::get('/insurance/policies/{policy}/coverage', [InsurancePolicyController::class, 'coverage'])->name('insurance.policies.coverage');
    Route::get('/insurance/policies/{policy}/claims', [InsurancePolicyController::class, 'claims'])->name('insurance.policies.claims');
    Route::get('/insurance/policies/{policy}/payments', [InsurancePolicyController::class, 'payments'])->name('insurance.policies.payments');
    Route::get('/insurance/policies/export', [InsurancePolicyController::class, 'export'])->name('insurance.policies.export');
    
    // Insurance Providers
    Route::get('/insurance/providers', [InsuranceProviderController::class, 'index'])->name('insurance.providers.index');
    Route::get('/insurance/providers/create', [InsuranceProviderController::class, 'create'])->name('insurance.providers.create');
    Route::post('/insurance/providers', [InsuranceProviderController::class, 'store'])->name('insurance.providers.store');
    Route::get('/insurance/providers/{provider}', [InsuranceProviderController::class, 'show'])->name('insurance.providers.show');
    Route::get('/insurance/providers/{provider}/edit', [InsuranceProviderController::class, 'edit'])->name('insurance.providers.edit');
    Route::put('/insurance/providers/{provider}', [InsuranceProviderController::class, 'update'])->name('insurance.providers.update');
    Route::delete('/insurance/providers/{provider}', [InsuranceProviderController::class, 'destroy'])->name('insurance.providers.destroy');
    
    // Provider Actions
    Route::post('/insurance/providers/{provider}/toggle-status', [InsuranceProviderController::class, 'toggleStatus'])->name('insurance.providers.toggle-status');
    Route::post('/insurance/providers/{provider}/verify', [InsuranceProviderController::class, 'verify'])->name('insurance.providers.verify');
    Route::post('/insurance/providers/{provider}/rate', [InsuranceProviderController::class, 'rate'])->name('insurance.providers.rate');
    Route::get('/insurance/providers/{provider}/policies', [InsuranceProviderController::class, 'policies'])->name('insurance.providers.policies');
    Route::get('/insurance/providers/{provider}/claims', [InsuranceProviderController::class, 'claims'])->name('insurance.providers.claims');
    Route::get('/insurance/providers/{provider}/performance', [InsuranceProviderController::class, 'performance'])->name('insurance.providers.performance');
    Route::get('/insurance/providers/export', [InsuranceProviderController::class, 'export'])->name('insurance.providers.export');
    
    // Insurance Claims
    Route::get('/insurance/claims', [InsuranceClaimController::class, 'index'])->name('insurance.claims.index');
    Route::get('/insurance/claims/create', [InsuranceClaimController::class, 'create'])->name('insurance.claims.create');
    Route::post('/insurance/claims', [InsuranceClaimController::class, 'store'])->name('insurance.claims.store');
    Route::get('/insurance/claims/{claim}', [InsuranceClaimController::class, 'show'])->name('insurance.claims.show');
    Route::get('/insurance/claims/{claim}/edit', [InsuranceClaimController::class, 'edit'])->name('insurance.claims.edit');
    Route::put('/insurance/claims/{claim}', [InsuranceClaimController::class, 'update'])->name('insurance.claims.update');
    Route::delete('/insurance/claims/{claim}', [InsuranceClaimController::class, 'destroy'])->name('insurance.claims.destroy');
    
    // Claim Actions
    Route::post('/insurance/claims/{claim}/submit', [InsuranceClaimController::class, 'submit'])->name('insurance.claims.submit');
    Route::post('/insurance/claims/{claim}/approve', [InsuranceClaimController::class, 'approve'])->name('insurance.claims.approve');
    Route::post('/insurance/claims/{claim}/reject', [InsuranceClaimController::class, 'reject'])->name('insurance.claims.reject');
    Route::post('/insurance/claims/{claim}/process', [InsuranceClaimController::class, 'process'])->name('insurance.claims.process');
    Route::post('/insurance/claims/{claim}/settle', [InsuranceClaimController::class, 'settle'])->name('insurance.claims.settle');
    Route::post('/insurance/claims/{claim}/deny', [InsuranceClaimController::class, 'deny'])->name('insurance.claims.deny');
    Route::post('/insurance/claims/{claim}/reopen', [InsuranceClaimController::class, 'reopen'])->name('insurance.claims.reopen');
    Route::post('/insurance/claims/{claim}/add-document', [InsuranceClaimController::class, 'addDocument'])->name('insurance.claims.add-document');
    Route::post('/insurance/claims/{claim}/add-note', [InsuranceClaimController::class, 'addNote'])->name('insurance.claims.add-note');
    Route::get('/insurance/claims/{claim}/timeline', [InsuranceClaimController::class, 'timeline'])->name('insurance.claims.timeline');
    Route::get('/insurance/claims/{claim}/report', [InsuranceClaimController::class, 'report'])->name('insurance.claims.report');
    Route::get('/insurance/claims/export', [InsuranceClaimController::class, 'export'])->name('insurance.claims.export');
    
    // Insurance Quotes
    Route::get('/insurance/quotes', [InsuranceQuoteController::class, 'index'])->name('insurance.quotes.index');
    Route::get('/insurance/quotes/create', [InsuranceQuoteController::class, 'create'])->name('insurance.quotes.create');
    Route::post('/insurance/quotes', [InsuranceQuoteController::class, 'store'])->name('insurance.quotes.store');
    Route::get('/insurance/quotes/{quote}', [InsuranceQuoteController::class, 'show'])->name('insurance.quotes.show');
    Route::get('/insurance/quotes/{quote}/edit', [InsuranceQuoteController::class, 'edit'])->name('insurance.quotes.edit');
    Route::put('/insurance/quotes/{quote}', [InsuranceQuoteController::class, 'update'])->name('insurance.quotes.update');
    Route::delete('/insurance/quotes/{quote}', [InsuranceQuoteController::class, 'destroy'])->name('insurance.quotes.destroy');
    
    // Quote Actions
    Route::post('/insurance/quotes/{quote}/send', [InsuranceQuoteController::class, 'send'])->name('insurance.quotes.send');
    Route::post('/insurance/quotes/{quote}/accept', [InsuranceQuoteController::class, 'accept'])->name('insurance.quotes.accept');
    Route::post('/insurance/quotes/{quote}/reject', [InsuranceQuoteController::class, 'reject'])->name('insurance.quotes.reject');
    Route::post('/insurance/quotes/{quote}/convert', [InsuranceQuoteController::class, 'convert'])->name('insurance.quotes.convert');
    Route::post('/insurance/quotes/{quote}/expire', [InsuranceQuoteController::class, 'expire'])->name('insurance.quotes.expire');
    Route::post('/insurance/quotes/{quote}/compare', [InsuranceQuoteController::class, 'compare'])->name('insurance.quotes.compare');
    Route::get('/insurance/quotes/{quote}/download', [InsuranceQuoteController::class, 'download'])->name('insurance.quotes.download');
    Route::get('/insurance/quotes/export', [InsuranceQuoteController::class, 'export'])->name('insurance.quotes.export');
    
    // Insurance Coverage
    Route::get('/insurance/coverage', [InsuranceCoverageController::class, 'index'])->name('insurance.coverage.index');
    Route::get('/insurance/coverage/create', [InsuranceCoverageController::class, 'create'])->name('insurance.coverage.create');
    Route::post('/insurance/coverage', [InsuranceCoverageController::class, 'store'])->name('insurance.coverage.store');
    Route::get('/insurance/coverage/{coverage}', [InsuranceCoverageController::class, 'show'])->name('insurance.coverage.show');
    Route::get('/insurance/coverage/{coverage}/edit', [InsuranceCoverageController::class, 'edit'])->name('insurance.coverage.edit');
    Route::put('/insurance/coverage/{coverage}', [InsuranceCoverageController::class, 'update'])->name('insurance.coverage.update');
    Route::delete('/insurance/coverage/{coverage}', [InsuranceCoverageController::class, 'destroy'])->name('insurance.coverage.destroy');
    
    // Coverage Actions
    Route::post('/insurance/coverage/{coverage}/activate', [InsuranceCoverageController::class, 'activate'])->name('insurance.coverage.activate');
    Route::post('/insurance/coverage/{coverage}/deactivate', [InsuranceCoverageController::class, 'deactivate'])->name('insurance.coverage.deactivate');
    Route::post('/insurance/coverage/{coverage}/adjust', [InsuranceCoverageController::class, 'adjust'])->name('insurance.coverage.adjust');
    Route::get('/insurance/coverage/{coverage}/details', [InsuranceCoverageController::class, 'details'])->name('insurance.coverage.details');
    Route::get('/insurance/coverage/{coverage}/history', [InsuranceCoverageController::class, 'history'])->name('insurance.coverage.history');
    Route::get('/insurance/coverage/export', [InsuranceCoverageController::class, 'export'])->name('insurance.coverage.export');
    
    // Insurance Renewals
    Route::get('/insurance/renewals', [InsuranceRenewalController::class, 'index'])->name('insurance.renewals.index');
    Route::get('/insurance/renewals/create', [InsuranceRenewalController::class, 'create'])->name('insurance.renewals.create');
    Route::post('/insurance/renewals', [InsuranceRenewalController::class, 'store'])->name('insurance.renewals.store');
    Route::get('/insurance/renewals/{renewal}', [InsuranceRenewalController::class, 'show'])->name('insurance.renewals.show');
    Route::get('/insurance/renewals/{renewal}/edit', [InsuranceRenewalController::class, 'edit'])->name('insurance.renewals.edit');
    Route::put('/insurance/renewals/{renewal}', [InsuranceRenewalController::class, 'update'])->name('insurance.renewals.update');
    Route::delete('/insurance/renewals/{renewal}', [InsuranceRenewalController::class, 'destroy'])->name('insurance.renewals.destroy');
    
    // Renewal Actions
    Route::post('/insurance/renewals/{renewal}/process', [InsuranceRenewalController::class, 'process'])->name('insurance.renewals.process');
    Route::post('/insurance/renewals/{renewal}/approve', [InsuranceRenewalController::class, 'approve'])->name('insurance.renewals.approve');
    Route::post('/insurance/renewals/{renewal}/reject', [InsuranceRenewalController::class, 'reject'])->name('insurance.renewals.reject');
    Route::post('/insurance/renewals/{renewal}/send-notice', [InsuranceRenewalController::class, 'sendNotice'])->name('insurance.renewals.send-notice');
    Route::get('/insurance/renewals/{renewal}/preview', [InsuranceRenewalController::class, 'preview'])->name('insurance.renewals.preview');
    Route::get('/insurance/renewals/upcoming', [InsuranceRenewalController::class, 'upcoming'])->name('insurance.renewals.upcoming');
    Route::get('/insurance/renewals/expired', [InsuranceRenewalController::class, 'expired'])->name('insurance.renewals.expired');
    Route::get('/insurance/renewals/export', [InsuranceRenewalController::class, 'export'])->name('insurance.renewals.export');
    
    // Risk Assessment
    Route::get('/insurance/risk-assessment', [RiskAssessmentController::class, 'index'])->name('insurance.risk-assessment.index');
    Route::get('/insurance/risk-assessment/create', [RiskAssessmentController::class, 'create'])->name('insurance.risk-assessment.create');
    Route::post('/insurance/risk-assessment', [RiskAssessmentController::class, 'store'])->name('insurance.risk-assessment.store');
    Route::get('/insurance/risk-assessment/{assessment}', [RiskAssessmentController::class, 'show'])->name('insurance.risk-assessment.show');
    Route::get('/insurance/risk-assessment/{assessment}/edit', [RiskAssessmentController::class, 'edit'])->name('insurance.risk-assessment.edit');
    Route::put('/insurance/risk-assessment/{assessment}', [RiskAssessmentController::class, 'update'])->name('insurance.risk-assessment.update');
    Route::delete('/insurance/risk-assessment/{assessment}', [RiskAssessmentController::class, 'destroy'])->name('insurance.risk-assessment.destroy');
    
    // Assessment Actions
    Route::post('/insurance/risk-assessment/{assessment}/evaluate', [RiskAssessmentController::class, 'evaluate'])->name('insurance.risk-assessment.evaluate');
    Route::post('/insurance/risk-assessment/{assessment}/calculate-score', [RiskAssessmentController::class, 'calculateScore'])->name('insurance.risk-assessment.calculate-score');
    Route::post('/insurance/risk-assessment/{assessment}/generate-report', [RiskAssessmentController::class, 'generateReport'])->name('insurance.risk-assessment.generate-report');
    Route::get('/insurance/risk-assessment/{assessment}/report', [RiskAssessmentController::class, 'report'])->name('insurance.risk-assessment.report');
    Route::get('/insurance/risk-assessment/{assessment}/recommendations', [RiskAssessmentController::class, 'recommendations'])->name('insurance.risk-assessment.recommendations');
    Route::get('/insurance/risk-assessment/export', [RiskAssessmentController::class, 'export'])->name('insurance.risk-assessment.export');
    
    // Insurance Inspections
    Route::get('/insurance/inspections', [InsuranceInspectionController::class, 'index'])->name('insurance.inspections.index');
    Route::get('/insurance/inspections/create', [InsuranceInspectionController::class, 'create'])->name('insurance.inspections.create');
    Route::post('/insurance/inspections', [InsuranceInspectionController::class, 'store'])->name('insurance.inspections.store');
    Route::get('/insurance/inspections/{inspection}', [InsuranceInspectionController::class, 'show'])->name('insurance.inspections.show');
    Route::get('/insurance/inspections/{inspection}/edit', [InsuranceInspectionController::class, 'edit'])->name('insurance.inspections.edit');
    Route::put('/insurance/inspections/{inspection}', [InsuranceInspectionController::class, 'update'])->name('insurance.inspections.update');
    Route::delete('/insurance/inspections/{inspection}', [InsuranceInspectionController::class, 'destroy'])->name('insurance.inspections.destroy');
    
    // Inspection Actions
    Route::post('/insurance/inspections/{inspection}/conduct', [InsuranceInspectionController::class, 'conduct'])->name('insurance.inspections.conduct');
    Route::post('/insurance/inspections/{inspection}/complete', [InsuranceInspectionController::class, 'complete'])->name('insurance.inspections.complete');
    Route::post('/insurance/inspections/{inspection}/schedule', [InsuranceInspectionController::class, 'schedule'])->name('insurance.inspections.schedule');
    Route::post('/insurance/inspections/{inspection}/add-photos', [InsuranceInspectionController::class, 'addPhotos'])->name('insurance.inspections.add-photos');
    Route::post('/insurance/inspections/{inspection}/add-findings', [InsuranceInspectionController::class, 'addFindings'])->name('insurance.inspections.add-findings');
    Route::get('/insurance/inspections/{inspection}/report', [InsuranceInspectionController::class, 'report'])->name('insurance.inspections.report');
    Route::get('/insurance/inspections/calendar', [InsuranceInspectionController::class, 'calendar'])->name('insurance.inspections.calendar');
    Route::get('/insurance/inspections/export', [InsuranceInspectionController::class, 'export'])->name('insurance.inspections.export');
    
    // Insurance Payments
    Route::get('/insurance/payments', [InsurancePaymentController::class, 'index'])->name('insurance.payments.index');
    Route::get('/insurance/payments/create', [InsurancePaymentController::class, 'create'])->name('insurance.payments.create');
    Route::post('/insurance/payments', [InsurancePaymentController::class, 'store'])->name('insurance.payments.store');
    Route::get('/insurance/payments/{payment}', [InsurancePaymentController::class, 'show'])->name('insurance.payments.show');
    Route::get('/insurance/payments/{payment}/edit', [InsurancePaymentController::class, 'edit'])->name('insurance.payments.edit');
    Route::put('/insurance/payments/{payment}', [InsurancePaymentController::class, 'update'])->name('insurance.payments.update');
    Route::delete('/insurance/payments/{payment}', [InsurancePaymentController::class, 'destroy'])->name('insurance.payments.destroy');
    
    // Payment Actions
    Route::post('/insurance/payments/{payment}/process', [InsurancePaymentController::class, 'process'])->name('insurance.payments.process');
    Route::post('/insurance/payments/{payment}/confirm', [InsurancePaymentController::class, 'confirm'])->name('insurance.payments.confirm');
    Route::post('/insurance/payments/{payment}/refund', [InsurancePaymentController::class, 'refund'])->name('insurance.payments.refund');
    Route::post('/insurance/payments/{payment}/mark-late', [InsurancePaymentController::class, 'markLate'])->name('insurance.payments.mark-late');
    Route::get('/insurance/payments/{payment}/receipt', [InsurancePaymentController::class, 'receipt'])->name('insurance.payments.receipt');
    Route::post('/insurance/payments/{payment}/send-reminder', [InsurancePaymentController::class, 'sendReminder'])->name('insurance.payments.send-reminder');
    Route::get('/insurance/payments/overdue', [InsurancePaymentController::class, 'overdue'])->name('insurance.payments.overdue');
    Route::get('/insurance/payments/export', [InsurancePaymentController::class, 'export'])->name('insurance.payments.export');
    
    // Insurance Documents
    Route::get('/insurance/documents', [InsuranceDocumentController::class, 'index'])->name('insurance.documents.index');
    Route::get('/insurance/documents/create', [InsuranceDocumentController::class, 'create'])->name('insurance.documents.create');
    Route::post('/insurance/documents', [InsuranceDocumentController::class, 'store'])->name('insurance.documents.store');
    Route::get('/insurance/documents/{document}', [InsuranceDocumentController::class, 'show'])->name('insurance.documents.show');
    Route::get('/insurance/documents/{document}/edit', [InsuranceDocumentController::class, 'edit'])->name('insurance.documents.edit');
    Route::put('/insurance/documents/{document}', [InsuranceDocumentController::class, 'update'])->name('insurance.documents.update');
    Route::delete('/insurance/documents/{document}', [InsuranceDocumentController::class, 'destroy'])->name('insurance.documents.destroy');
    
    // Document Actions
    Route::post('/insurance/documents/{document}/upload', [InsuranceDocumentController::class, 'upload'])->name('insurance.documents.upload');
    Route::get('/insurance/documents/{document}/download', [InsuranceDocumentController::class, 'download'])->name('insurance.documents.download');
    Route::post('/insurance/documents/{document}/share', [InsuranceDocumentController::class, 'share'])->name('insurance.documents.share');
    Route::post('/insurance/documents/{document}/sign', [InsuranceDocumentController::class, 'sign'])->name('insurance.documents.sign');
    Route::post('/insurance/documents/{document}/verify', [InsuranceDocumentController::class, 'verify'])->name('insurance.documents.verify');
    Route::post('/insurance/documents/{document}/archive', [InsuranceDocumentController::class, 'archive'])->name('insurance.documents.archive');
    Route::get('/insurance/documents/{document}/preview', [InsuranceDocumentController::class, 'preview'])->name('insurance.documents.preview');
    Route::get('/insurance/documents/export', [InsuranceDocumentController::class, 'export'])->name('insurance.documents.export');
    
    // Insurance Reports and Analytics
    Route::get('/insurance/reports', [InsuranceController::class, 'reports'])->name('insurance.reports');
    Route::get('/insurance/analytics', [InsuranceController::class, 'analytics'])->name('insurance.analytics');
    Route::get('/insurance/performance', [InsuranceController::class, 'performance'])->name('insurance.performance');
    Route::get('/insurance/claims-analysis', [InsuranceController::class, 'claimsAnalysis'])->name('insurance.claims-analysis');
    Route::get('/insurance/premium-analysis', [InsuranceController::class, 'premiumAnalysis'])->name('insurance.premium-analysis');
    Route::get('/insurance/risk-analysis', [InsuranceController::class, 'riskAnalysis'])->name('insurance.risk-analysis');
    
    // Insurance Search and API
    Route::get('/insurance/search', [InsuranceController::class, 'search'])->name('insurance.search');
    Route::get('/insurance/api/dashboard-stats', [InsuranceController::class, 'dashboardStats'])->name('insurance.api.dashboard-stats');
    Route::get('/insurance/api/calendar-events', [InsuranceController::class, 'calendarEvents'])->name('insurance.api.calendar-events');
    Route::get('/insurance/api/claim-status', [InsuranceController::class, 'claimStatus'])->name('insurance.api.claim-status');
    
    // Insurance Settings
    Route::get('/insurance/settings', [InsuranceController::class, 'settings'])->name('insurance.settings');
    Route::post('/insurance/settings', [InsuranceController::class, 'saveSettings'])->name('insurance.settings.save');
});
