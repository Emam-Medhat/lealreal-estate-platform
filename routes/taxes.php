<?php

use App\Http\Controllers\TaxController;
use App\Http\Controllers\PropertyTaxController;
use App\Http\Controllers\TaxCalculatorController;
use App\Http\Controllers\TaxFilingController;
use App\Http\Controllers\TaxPaymentController;
use App\Http\Controllers\TaxExemptionController;
use App\Http\Controllers\TaxAssessmentController;
use App\Http\Controllers\TaxDocumentController;
use App\Http\Controllers\TaxReportController;
use App\Http\Controllers\CapitalGainsTaxController;
use App\Http\Controllers\VatController;
use Illuminate\Support\Facades\Route;

// Tax System Routes
Route::middleware(['auth'])->prefix('taxes')->name('taxes.')->group(function () {
    
    // Main Tax Routes
    Route::get('/', [TaxController::class, 'index'])->name('index');
    Route::get('/dashboard', [TaxController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [TaxController::class, 'analytics'])->name('analytics');
    Route::get('/overview', [TaxController::class, 'overview'])->name('overview');
    Route::get('/statistics', [TaxController::class, 'statistics'])->name('statistics');
    Route::get('/reports', [TaxController::class, 'reports'])->name('reports');
    Route::get('/export', [TaxController::class, 'export'])->name('export');
    
    // Property Tax Routes
    Route::prefix('property')->name('property.')->group(function () {
        Route::get('/', [PropertyTaxController::class, 'index'])->name('index');
        Route::get('/create', [PropertyTaxController::class, 'create'])->name('create');
        Route::post('/', [PropertyTaxController::class, 'store'])->name('store');
        Route::get('/{propertyTax}', [PropertyTaxController::class, 'show'])->name('show');
        Route::get('/{propertyTax}/edit', [PropertyTaxController::class, 'edit'])->name('edit');
        Route::put('/{propertyTax}', [PropertyTaxController::class, 'update'])->name('update');
        Route::delete('/{propertyTax}', [PropertyTaxController::class, 'destroy'])->name('destroy');
        Route::get('/{propertyTax}/calculate', [PropertyTaxController::class, 'calculate'])->name('calculate');
        Route::post('/{propertyTax}/calculate', [PropertyTaxController::class, 'calculateTax'])->name('calculateTax');
        Route::get('/{propertyTax}/assessment', [PropertyTaxController::class, 'assessment'])->name('assessment');
        Route::post('/{propertyTax}/assessment', [PropertyTaxController::class, 'createAssessment'])->name('createAssessment');
        Route::get('/{propertyTax}/filing', [PropertyTaxController::class, 'filing'])->name('filing');
        Route::post('/{propertyTax}/filing', [PropertyTaxController::class, 'createFiling'])->name('createFiling');
        Route::get('/{propertyTax}/payment', [PropertyTaxController::class, 'payment'])->name('payment');
        Route::post('/{propertyTax}/payment', [PropertyTaxController::class, 'createPayment'])->name('createPayment');
        Route::get('/{propertyTax}/exemption', [PropertyTaxController::class, 'exemption'])->name('exemption');
        Route::post('/{propertyTax}/exemption', [PropertyTaxController::class, 'createExemption'])->name('createExemption');
        Route::get('/{propertyTax}/documents', [PropertyTaxController::class, 'documents'])->name('documents');
        Route::post('/{propertyTax}/documents', [PropertyTaxController::class, 'uploadDocument'])->name('uploadDocument');
        Route::get('/{propertyTax}/history', [PropertyTaxController::class, 'history'])->name('history');
        Route::get('/{propertyTax}/appeal', [PropertyTaxController::class, 'appeal'])->name('appeal');
        Route::post('/{propertyTax}/appeal', [PropertyTaxController::class, 'submitAppeal'])->name('submitAppeal');
        Route::get('/{propertyTax}/certificate', [PropertyTaxController::class, 'certificate'])->name('certificate');
        Route::get('/{propertyTax}/clearance', [PropertyTaxController::class, 'clearance'])->name('clearance');
    });
    
    // Tax Calculator Routes
    Route::prefix('calculator')->name('calculator.')->group(function () {
        Route::get('/', [TaxCalculatorController::class, 'index'])->name('index');
        Route::post('/calculate', [TaxCalculatorController::class, 'calculate'])->name('calculate');
        Route::post('/property-tax', [TaxCalculatorController::class, 'calculatePropertyTax'])->name('propertyTax');
        Route::post('/capital-gains', [TaxCalculatorController::class, 'calculateCapitalGains'])->name('capitalGains');
        Route::post('/vat', [TaxCalculatorController::class, 'calculateVat'])->name('vat');
        Route::post('/stamp-duty', [TaxCalculatorController::class, 'calculateStampDuty'])->name('stampDuty');
        Route::get('/estimate/{property}', [TaxCalculatorController::class, 'estimate'])->name('estimate');
        Route::get('/comparison', [TaxCalculatorController::class, 'comparison'])->name('comparison');
        Route::post('/comparison', [TaxCalculatorController::class, 'compareTaxes'])->name('compareTaxes');
        Route::get('/scenarios', [TaxCalculatorController::class, 'scenarios'])->name('scenarios');
        Route::post('/scenarios', [TaxCalculatorController::class, 'runScenarios'])->name('runScenarios');
        Route::get('/projections', [TaxCalculatorController::class, 'projections'])->name('projections');
        Route::post('/projections', [TaxCalculatorController::class, 'calculateProjections'])->name('calculateProjections');
    });
    
    // Tax Filing Routes
    Route::prefix('filings')->name('filings.')->group(function () {
        Route::get('/', [TaxFilingController::class, 'index'])->name('index');
        Route::get('/create', [TaxFilingController::class, 'create'])->name('create');
        Route::post('/', [TaxFilingController::class, 'store'])->name('store');
        Route::get('/{taxFiling}', [TaxFilingController::class, 'show'])->name('show');
        Route::get('/{taxFiling}/edit', [TaxFilingController::class, 'edit'])->name('edit');
        Route::put('/{taxFiling}', [TaxFilingController::class, 'update'])->name('update');
        Route::delete('/{taxFiling}', [TaxFilingController::class, 'destroy'])->name('destroy');
        Route::post('/{taxFiling}/submit', [TaxFilingController::class, 'submit'])->name('submit');
        Route::post('/{taxFiling}/approve', [TaxFilingController::class, 'approve'])->name('approve');
        Route::post('/{taxFiling}/reject', [TaxFilingController::class, 'reject'])->name('reject');
        Route::get('/{taxFiling}/download', [TaxFilingController::class, 'download'])->name('download');
        Route::get('/{taxFiling}/attachments/{attachment}', [TaxFilingController::class, 'downloadAttachment'])->name('downloadAttachment');
        Route::post('/{taxFiling}/attachments', [TaxFilingController::class, 'uploadAttachment'])->name('uploadAttachment');
        Route::delete('/{taxFiling}/attachments/{attachment}', [TaxFilingController::class, 'deleteAttachment'])->name('deleteAttachment');
        Route::get('/{taxFiling}/history', [TaxFilingController::class, 'history'])->name('history');
        Route::get('/{taxFiling}/status', [TaxFilingController::class, 'status'])->name('status');
        Route::get('/pending', [TaxFilingController::class, 'pending'])->name('pending');
        Route::get('/approved', [TaxFilingController::class, 'approved'])->name('approved');
        Route::get('/rejected', [TaxFilingController::class, 'rejected'])->name('rejected');
        Route::get('/overdue', [TaxFilingController::class, 'overdue'])->name('overdue');
    });
    
    // Tax Payment Routes
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [TaxPaymentController::class, 'index'])->name('index');
        Route::get('/create', [TaxPaymentController::class, 'create'])->name('create');
        Route::post('/', [TaxPaymentController::class, 'store'])->name('store');
        Route::get('/{taxPayment}', [TaxPaymentController::class, 'show'])->name('show');
        Route::get('/{taxPayment}/edit', [TaxPaymentController::class, 'edit'])->name('edit');
        Route::put('/{taxPayment}', [TaxPaymentController::class, 'update'])->name('update');
        Route::delete('/{taxPayment}', [TaxPaymentController::class, 'destroy'])->name('destroy');
        Route::post('/{taxPayment}/process', [TaxPaymentController::class, 'process'])->name('process');
        Route::post('/{taxPayment}/confirm', [TaxPaymentController::class, 'confirm'])->name('confirm');
        Route::post('/{taxPayment}/cancel', [TaxPaymentController::class, 'cancel'])->name('cancel');
        Route::post('/{taxPayment}/refund', [TaxPaymentController::class, 'refund'])->name('refund');
        Route::get('/{taxPayment}/receipt', [TaxPaymentController::class, 'receipt'])->name('receipt');
        Route::get('/{taxPayment}/download-receipt', [TaxPaymentController::class, 'downloadReceipt'])->name('downloadReceipt');
        Route::get('/{taxPayment}/history', [TaxPaymentController::class, 'history'])->name('history');
        Route::get('/{taxPayment}/schedule', [TaxPaymentController::class, 'schedule'])->name('schedule');
        Route::post('/{taxPayment}/schedule', [TaxPaymentController::class, 'schedulePayment'])->name('schedulePayment');
        Route::get('/pending', [TaxPaymentController::class, 'pending'])->name('pending');
        Route::get('/overdue', [TaxPaymentController::class, 'overdue'])->name('overdue');
        Route::get('/paid', [TaxPaymentController::class, 'paid'])->name('paid');
        Route::get('/refunded', [TaxPaymentController::class, 'refunded'])->name('refunded');
        Route::get('/recurring', [TaxPaymentController::class, 'recurring'])->name('recurring');
        Route::post('/batch', [TaxPaymentController::class, 'batchProcess'])->name('batchProcess');
    });
    
    // Tax Exemption Routes
    Route::prefix('exemptions')->name('exemptions.')->group(function () {
        Route::get('/', [TaxExemptionController::class, 'index'])->name('index');
        Route::get('/create', [TaxExemptionController::class, 'create'])->name('create');
        Route::post('/', [TaxExemptionController::class, 'store'])->name('store');
        Route::get('/{taxExemption}', [TaxExemptionController::class, 'show'])->name('show');
        Route::get('/{taxExemption}/edit', [TaxExemptionController::class, 'edit'])->name('edit');
        Route::put('/{taxExemption}', [TaxExemptionController::class, 'update'])->name('update');
        Route::delete('/{taxExemption}', [TaxExemptionController::class, 'destroy'])->name('destroy');
        Route::post('/{taxExemption}/approve', [TaxExemptionController::class, 'approve'])->name('approve');
        Route::post('/{taxExemption}/reject', [TaxExemptionController::class, 'reject'])->name('reject');
        Route::post('/{taxExemption}/renew', [TaxExemptionController::class, 'renew'])->name('renew');
        Route::post('/{taxExemption}/revoke', [TaxExemptionController::class, 'revoke'])->name('revoke');
        Route::get('/{taxExemption}/certificate', [TaxExemptionController::class, 'certificate'])->name('certificate');
        Route::get('/{taxExemption}/download-certificate', [TaxExemptionController::class, 'downloadCertificate'])->name('downloadCertificate');
        Route::get('/{taxExemption}/documents', [TaxExemptionController::class, 'documents'])->name('documents');
        Route::post('/{taxExemption}/documents', [TaxExemptionController::class, 'uploadDocument'])->name('uploadDocument');
        Route::get('/{taxExemption}/history', [TaxExemptionController::class, 'history'])->name('history');
        Route::get('/pending', [TaxExemptionController::class, 'pending'])->name('pending');
        Route::get('/approved', [TaxExemptionController::class, 'approved'])->name('approved');
        Route::get('/rejected', [TaxExemptionController::class, 'rejected'])->name('rejected');
        Route::get('/expired', [TaxExemptionController::class, 'expired'])->name('expired');
    });
    
    // Tax Assessment Routes
    Route::prefix('assessments')->name('assessments.')->group(function () {
        Route::get('/', [TaxAssessmentController::class, 'index'])->name('index');
        Route::get('/create', [TaxAssessmentController::class, 'create'])->name('create');
        Route::post('/', [TaxAssessmentController::class, 'store'])->name('store');
        Route::get('/{taxAssessment}', [TaxAssessmentController::class, 'show'])->name('show');
        Route::get('/{taxAssessment}/edit', [TaxAssessmentController::class, 'edit'])->name('edit');
        Route::put('/{taxAssessment}', [TaxAssessmentController::class, 'update'])->name('update');
        Route::delete('/{taxAssessment}', [TaxAssessmentController::class, 'destroy'])->name('destroy');
        Route::post('/{taxAssessment}/approve', [TaxAssessmentController::class, 'approve'])->name('approve');
        Route::post('/{taxAssessment}/reject', [TaxAssessmentController::class, 'reject'])->name('reject');
        Route::post('/{taxAssessment}/appeal', [TaxAssessmentController::class, 'appeal'])->name('appeal');
        Route::post('/{taxAssessment}/correct', [TaxAssessmentController::class, 'correct'])->name('correct');
        Route::post('/{taxAssessment}/certify', [TaxAssessmentController::class, 'certify'])->name('certify');
        Route::get('/{taxAssessment}/report', [TaxAssessmentController::class, 'report'])->name('report');
        Route::get('/{taxAssessment}/download-report', [TaxAssessmentController::class, 'downloadReport'])->name('downloadReport');
        Route::get('/{taxAssessment}/documents', [TaxAssessmentController::class, 'documents'])->name('documents');
        Route::post('/{taxAssessment}/documents', [TaxAssessmentController::class, 'uploadDocument'])->name('uploadDocument');
        Route::get('/{taxAssessment}/history', [TaxAssessmentController::class, 'history'])->name('history');
        Route::get('/pending', [TaxAssessmentController::class, 'pending'])->name('pending');
        Route::get('/approved', [TaxAssessmentController::class, 'approved'])->name('approved');
        Route::get('/rejected', [TaxAssessmentController::class, 'rejected'])->name('rejected');
        Route::get('/appealed', [TaxAssessmentController::class, 'appealed'])->name('appealed');
    });
    
    // Tax Document Routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [TaxDocumentController::class, 'index'])->name('index');
        Route::get('/create', [TaxDocumentController::class, 'create'])->name('create');
        Route::post('/', [TaxDocumentController::class, 'store'])->name('store');
        Route::get('/{taxDocument}', [TaxDocumentController::class, 'show'])->name('show');
        Route::get('/{taxDocument}/edit', [TaxDocumentController::class, 'edit'])->name('edit');
        Route::put('/{taxDocument}', [TaxDocumentController::class, 'update'])->name('update');
        Route::delete('/{taxDocument}', [TaxDocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{taxDocument}/download', [TaxDocumentController::class, 'download'])->name('download');
        Route::post('/{taxDocument}/approve', [TaxDocumentController::class, 'approve'])->name('approve');
        Route::post('/{taxDocument}/reject', [TaxDocumentController::class, 'reject'])->name('reject');
        Route::post('/{taxDocument}/archive', [TaxDocumentController::class, 'archive'])->name('archive');
        Route::post('/{taxDocument}/restore', [TaxDocumentController::class, 'restore'])->name('restore');
        Route::get('/{taxDocument}/preview', [TaxDocumentController::class, 'preview'])->name('preview');
        Route::get('/{taxDocument}/versions', [TaxDocumentController::class, 'versions'])->name('versions');
        Route::post('/{taxDocument}/sign', [TaxDocumentController::class, 'sign'])->name('sign');
        Route::get('/{taxDocument}/verify', [TaxDocumentController::class, 'verify'])->name('verify');
        Route::get('/search', [TaxDocumentController::class, 'search'])->name('search');
        Route::post('/batch', [TaxDocumentController::class, 'batchProcess'])->name('batchProcess');
        Route::get('/categories', [TaxDocumentController::class, 'categories'])->name('categories');
        Route::get('/tags', [TaxDocumentController::class, 'tags'])->name('tags');
    });
    
    // Tax Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [TaxReportController::class, 'index'])->name('index');
        Route::get('/create', [TaxReportController::class, 'create'])->name('create');
        Route::post('/', [TaxReportController::class, 'store'])->name('store');
        Route::get('/{taxReport}', [TaxReportController::class, 'show'])->name('show');
        Route::get('/{taxReport}/edit', [TaxReportController::class, 'edit'])->name('edit');
        Route::put('/{taxReport}', [TaxReportController::class, 'update'])->name('update');
        Route::delete('/{taxReport}', [TaxReportController::class, 'destroy'])->name('destroy');
        Route::get('/{taxReport}/download', [TaxReportController::class, 'download'])->name('download');
        Route::get('/{taxReport}/export', [TaxReportController::class, 'export'])->name('export');
        Route::post('/{taxReport}/generate', [TaxReportController::class, 'generate'])->name('generate');
        Route::post('/{taxReport}/schedule', [TaxReportController::class, 'schedule'])->name('schedule');
        Route::get('/{taxReport}/preview', [TaxReportController::class, 'preview'])->name('preview');
        Route::get('/summary', [TaxReportController::class, 'summary'])->name('summary');
        Route::get('/analytics', [TaxReportController::class, 'analytics'])->name('analytics');
        Route::get('/compliance', [TaxReportController::class, 'compliance'])->name('compliance');
        Route::get('/audit', [TaxReportController::class, 'audit'])->name('audit');
        Route::get('/trends', [TaxReportController::class, 'trends'])->name('trends');
        Route::get('/forecasts', [TaxReportController::class, 'forecasts'])->name('forecasts');
        Route::post('/custom', [TaxReportController::class, 'custom'])->name('custom');
        Route::get('/templates', [TaxReportController::class, 'templates'])->name('templates');
    });
    
    // Capital Gains Tax Routes
    Route::prefix('capital-gains')->name('capitalGains.')->group(function () {
        Route::get('/', [CapitalGainsTaxController::class, 'index'])->name('index');
        Route::get('/create', [CapitalGainsTaxController::class, 'create'])->name('create');
        Route::post('/', [CapitalGainsTaxController::class, 'store'])->name('store');
        Route::get('/{capitalGainsTax}', [CapitalGainsTaxController::class, 'show'])->name('show');
        Route::get('/{capitalGainsTax}/edit', [CapitalGainsTaxController::class, 'edit'])->name('edit');
        Route::put('/{capitalGainsTax}', [CapitalGainsTaxController::class, 'update'])->name('update');
        Route::delete('/{capitalGainsTax}', [CapitalGainsTaxController::class, 'destroy'])->name('destroy');
        Route::post('/{capitalGainsTax}/calculate', [CapitalGainsTaxController::class, 'calculate'])->name('calculate');
        Route::post('/{capitalGainsTax}/file', [CapitalGainsTaxController::class, 'file'])->name('file');
        Route::post('/{capitalGainsTax}/pay', [CapitalGainsTaxController::class, 'pay'])->name('pay');
        Route::post('/{capitalGainsTax}/defer', [CapitalGainsTaxController::class, 'defer'])->name('defer');
        Route::get('/{capitalGainsTax}/exclusion', [CapitalGainsTaxController::class, 'exclusion'])->name('exclusion');
        Route::post('/{capitalGainsTax}/exclusion', [CapitalGainsTaxController::class, 'applyExclusion'])->name('applyExclusion');
        Route::get('/{capitalGainsTax}/exchange', [CapitalGainsTaxController::class, 'exchange'])->name('exchange');
        Route::post('/{capitalGainsTax}/exchange', [CapitalGainsTaxController::class, 'likeKindExchange'])->name('likeKindExchange');
        Route::get('/{capitalGainsTax}/installment', [CapitalGainsTaxController::class, 'installment'])->name('installment');
        Route::post('/{capitalGainsTax}/installment', [CapitalGainsTaxController::class, 'installmentSale'])->name('installmentSale');
        Route::get('/{capitalGainsTax}/history', [CapitalGainsTaxController::class, 'history'])->name('history');
        Route::get('/pending', [CapitalGainsTaxController::class, 'pending'])->name('pending');
        Route::get('/calculated', [CapitalGainsTaxController::class, 'calculated'])->name('calculated');
        Route::get('/filed', [CapitalGainsTaxController::class, 'filed'])->name('filed');
        Route::get('/paid', [CapitalGainsTaxController::class, 'paid'])->name('paid');
    });
    
    // VAT Routes
    Route::prefix('vat')->name('vat.')->group(function () {
        Route::get('/', [VatController::class, 'index'])->name('index');
        Route::get('/create', [VatController::class, 'create'])->name('create');
        Route::post('/', [VatController::class, 'store'])->name('store');
        Route::get('/{vatRecord}', [VatController::class, 'show'])->name('show');
        Route::get('/{vatRecord}/edit', [VatController::class, 'edit'])->name('edit');
        Route::put('/{vatRecord}', [VatController::class, 'update'])->name('update');
        Route::delete('/{vatRecord}', [VatController::class, 'destroy'])->name('destroy');
        Route::post('/{vatRecord}/calculate', [VatController::class, 'calculate'])->name('calculate');
        Route::post('/{vatRecord}/submit', [VatController::class, 'submit'])->name('submit');
        Route::post('/{vatRecord}/approve', [VatController::class, 'approve'])->name('approve');
        Route::post('/{vatRecord}/reject', [VatController::class, 'reject'])->name('reject');
        Route::post('/{vatRecord}/pay', [VatController::class, 'pay'])->name('pay');
        Route::post('/{vatRecord}/refund', [VatController::class, 'refund'])->name('refund');
        Route::get('/{vatRecord}/return', [VatController::class, 'return'])->name('return');
        Route::post('/{vatRecord}/return', [VatController::class, 'fileReturn'])->name('fileReturn');
        Route::get('/{vatRecord}/adjustment', [VatController::class, 'adjustment'])->name('adjustment');
        Route::post('/{vatRecord}/adjustment', [VatController::class, 'makeAdjustment'])->name('makeAdjustment');
        Route::get('/{vatRecord}/reconciliation', [VatController::class, 'reconciliation'])->name('reconciliation');
        Route::post('/{vatRecord}/reconciliation', [VatController::class, 'reconcile'])->name('reconcile');
        Route::get('/{vatRecord}/history', [VatController::class, 'history'])->name('history');
        Route::get('/pending', [VatController::class, 'pending'])->name('pending');
        Route::get('/submitted', [VatController::class, 'submitted'])->name('submitted');
        Route::get('/approved', [VatController::class, 'approved'])->name('approved');
        Route::get('/rejected', [VatController::class, 'rejected'])->name('rejected');
        Route::get('/paid', [VatController::class, 'paid'])->name('paid');
        Route::get('/refunded', [VatController::class, 'refunded'])->name('refunded');
        Route::get('/periods', [VatController::class, 'periods'])->name('periods');
        Route::get('/liability', [VatController::class, 'liability'])->name('liability');
        Route::get('/compliance', [VatController::class, 'compliance'])->name('compliance');
    });
    
    // API Routes for AJAX requests
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/search', [TaxController::class, 'search'])->name('search');
        Route::get('/stats', [TaxController::class, 'stats'])->name('stats');
        Route::get('/charts', [TaxController::class, 'charts'])->name('charts');
        Route::get('/notifications', [TaxController::class, 'notifications'])->name('notifications');
        Route::get('/alerts', [TaxController::class, 'alerts'])->name('alerts');
        Route::get('/reminders', [TaxController::class, 'reminders'])->name('reminders');
        Route::get('/deadlines', [TaxController::class, 'deadlines'])->name('deadlines');
        Route::get('/quick-calculate', [TaxCalculatorController::class, 'quickCalculate'])->name('quickCalculate');
        Route::get('/property-taxes', [PropertyTaxController::class, 'apiIndex'])->name('propertyTaxes');
        Route::get('/filings-list', [TaxFilingController::class, 'apiIndex'])->name('filingsList');
        Route::get('/payments-list', [TaxPaymentController::class, 'apiIndex'])->name('paymentsList');
        Route::get('/exemptions-list', [TaxExemptionController::class, 'apiIndex'])->name('exemptionsList');
        Route::get('/assessments-list', [TaxAssessmentController::class, 'apiIndex'])->name('assessmentsList');
        Route::get('/documents-list', [TaxDocumentController::class, 'apiIndex'])->name('documentsList');
        Route::get('/reports-list', [TaxReportController::class, 'apiIndex'])->name('reportsList');
        Route::get('/capital-gains-list', [CapitalGainsTaxController::class, 'apiIndex'])->name('capitalGainsList');
        Route::get('/vat-records-list', [VatController::class, 'apiIndex'])->name('vatRecordsList');
    });
});
