<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\DocumentGenerationController;
use App\Http\Controllers\DocumentSignatureController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractTemplateController;
use App\Http\Controllers\ContractNegotiationController;
use App\Http\Controllers\ContractApprovalController;
use App\Http\Controllers\EsignatureController;
use App\Http\Controllers\NotaryController;
use App\Http\Controllers\DocumentVersionController;
use App\Http\Controllers\DocumentComplianceController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Document Management
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/search', [DocumentController::class, 'search'])->name('documents.search');

    // Document Templates
    Route::get('/document-templates', [DocumentTemplateController::class, 'index'])->name('document-templates.index');
    Route::get('/documents/templates', [DocumentTemplateController::class, 'index'])->name('documents.templates.index');
    Route::get('/document-templates/create', [DocumentTemplateController::class, 'create'])->name('document-templates.create');
    Route::post('/document-templates', [DocumentTemplateController::class, 'store'])->name('document-templates.store');
    Route::get('/document-templates/{template}', [DocumentTemplateController::class, 'show'])->name('document-templates.show');
    Route::get('/document-templates/{template}/edit', [DocumentTemplateController::class, 'edit'])->name('document-templates.edit');
    Route::put('/document-templates/{template}', [DocumentTemplateController::class, 'update'])->name('document-templates.update');
    Route::delete('/document-templates/{template}', [DocumentTemplateController::class, 'destroy'])->name('document-templates.destroy');
    Route::post('/document-templates/{template}/duplicate', [DocumentTemplateController::class, 'duplicate'])->name('document-templates.duplicate');
    Route::get('/document-templates/{template}/preview', [DocumentTemplateController::class, 'preview'])->name('document-templates.preview');
    Route::post('/document-templates/{template}/generate', [DocumentTemplateController::class, 'generate'])->name('document-templates.generate');
    Route::post('/document-templates/{template}/toggle', [DocumentTemplateController::class, 'toggle'])->name('document-templates.toggle');
    Route::get('/document-templates/{template}/create-contract', [DocumentTemplateController::class, 'createContract'])->name('document-templates.create-contract');
    Route::get('/document-templates/export', [DocumentTemplateController::class, 'export'])->name('document-templates.export');
    Route::get('/document-templates/analytics', [DocumentTemplateController::class, 'analytics'])->name('document-templates.analytics');
    Route::get('/document-templates/search', [DocumentTemplateController::class, 'search'])->name('document-templates.search');
    Route::get('/document-templates/{template}/variables', [DocumentTemplateController::class, 'variables'])->name('document-templates.variables');
    Route::post('/document-templates/{template}/validate', [DocumentTemplateController::class, 'validate'])->name('document-templates.validate');

    // Document Generation
    Route::get('/document-generation', [DocumentGenerationController::class, 'index'])->name('document-generation.index');
    Route::get('/document-generation/create', [DocumentGenerationController::class, 'create'])->name('document-generation.create');
    Route::post('/document-generation', [DocumentGenerationController::class, 'store'])->name('document-generation.store');
    Route::get('/document-generation/{document}', [DocumentGenerationController::class, 'show'])->name('document-generation.show');
    Route::post('/document-generation/bulk', [DocumentGenerationController::class, 'bulkGenerate'])->name('document-generation.bulk');
    Route::get('/document-generation/history', [DocumentGenerationController::class, 'history'])->name('document-generation.history');

    // Document Signatures
    Route::get('/document-signatures', [DocumentSignatureController::class, 'index'])->name('document-signatures.index');
    Route::get('/document-signatures/create/{document}', [DocumentSignatureController::class, 'create'])->name('document-signatures.create');
    Route::post('/document-signatures', [DocumentSignatureController::class, 'store'])->name('document-signatures.store');
    Route::get('/document-signatures/{signature}', [DocumentSignatureController::class, 'show'])->name('document-signatures.show');
    Route::post('/document-signatures/{signature}/verify', [DocumentSignatureController::class, 'verify'])->name('document-signatures.verify');
    Route::get('/document-signatures/{signature}/download', [DocumentSignatureController::class, 'download'])->name('document-signatures.download');
    Route::post('/document-signatures/bulk', [DocumentSignatureController::class, 'bulkSign'])->name('document-signatures.bulk');
    Route::post('/document-signatures/{signature}/revoke', [DocumentSignatureController::class, 'revoke'])->name('document-signatures.revoke');
    Route::get('/document-signatures/{signature}/history', [DocumentSignatureController::class, 'history'])->name('document-signatures.history');

    // Contract Management
    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
    Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
    Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
    Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
    Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
    Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');
    Route::get('/contracts/{contract}/sign', [ContractController::class, 'sign'])->name('contracts.sign');
    Route::post('/contracts/{contract}/sign', [ContractController::class, 'submitSignature'])->name('contracts.sign.submit');
    Route::get('/contracts/{contract}/download', [ContractController::class, 'download'])->name('contracts.download');

    // Contract Templates
    Route::get('/contract-templates', [ContractTemplateController::class, 'index'])->name('contract-templates.index');
    Route::get('/contract-templates/create', [ContractTemplateController::class, 'create'])->name('contract-templates.create');
    Route::post('/contract-templates', [ContractTemplateController::class, 'store'])->name('contract-templates.store');
    Route::get('/contract-templates/{template}', [ContractTemplateController::class, 'show'])->name('contract-templates.show');
    Route::get('/contract-templates/{template}/edit', [ContractTemplateController::class, 'edit'])->name('contract-templates.edit');
    Route::put('/contract-templates/{template}', [ContractTemplateController::class, 'update'])->name('contract-templates.update');
    Route::delete('/contract-templates/{template}', [ContractTemplateController::class, 'destroy'])->name('contract-templates.destroy');
    Route::post('/contract-templates/{template}/duplicate', [ContractTemplateController::class, 'duplicate'])->name('contract-templates.duplicate');

    // Contract Negotiation
    Route::get('/contract-negotiations', [ContractNegotiationController::class, 'index'])->name('contract-negotiations.index');
    Route::get('/contract-negotiations/{contract}', [ContractNegotiationController::class, 'show'])->name('contract-negotiations.show');
    Route::post('/contract-negotiations/{contract}/propose', [ContractNegotiationController::class, 'propose'])->name('contract-negotiations.propose');
    Route::post('/contract-negotiations/{contract}/respond', [ContractNegotiationController::class, 'respond'])->name('contract-negotiations.respond');
    Route::post('/contract-negotiations/{contract}/accept', [ContractNegotiationController::class, 'accept'])->name('contract-negotiations.accept');
    Route::post('/contract-negotiations/{contract}/reject', [ContractNegotiationController::class, 'reject'])->name('contract-negotiations.reject');
    Route::post('/contract-negotiations/{contract}/finalize', [ContractNegotiationController::class, 'finalize'])->name('contract-negotiations.finalize');
    Route::post('/contract-negotiations/{contract}/cancel', [ContractNegotiationController::class, 'cancel'])->name('contract-negotiations.cancel');
    Route::get('/contract-negotiations/{contract}/history', [ContractNegotiationController::class, 'history'])->name('contract-negotiations.history');
    Route::get('/contract-negotiations/{contract}/compare', [ContractNegotiationController::class, 'compare'])->name('contract-negotiations.compare');

    // Contract Approval
    Route::get('/contract-approvals', [ContractApprovalController::class, 'index'])->name('contract-approvals.index');
    Route::get('/contract-approvals/create/{contract}', [ContractApprovalController::class, 'create'])->name('contract-approvals.create');
    Route::post('/contract-approvals', [ContractApprovalController::class, 'store'])->name('contract-approvals.store');
    Route::get('/contract-approvals/{approval}', [ContractApprovalController::class, 'show'])->name('contract-approvals.show');
    Route::post('/contract-approvals/{approval}/approve', [ContractApprovalController::class, 'approve'])->name('contract-approvals.approve');
    Route::post('/contract-approvals/{approval}/reject', [ContractApprovalController::class, 'reject'])->name('contract-approvals.reject');
    Route::post('/contract-approvals/{approval}/delegate', [ContractApprovalController::class, 'delegate'])->name('contract-approvals.delegate');
    Route::post('/contract-approvals/{approval}/remind', [ContractApprovalController::class, 'remind'])->name('contract-approvals.remind');
    Route::post('/contract-approvals/{approval}/escalate', [ContractApprovalController::class, 'escalate'])->name('contract-approvals.escalate');
    Route::get('/contract-approvals/{contract}/workflow', [ContractApprovalController::class, 'workflow'])->name('contract-approvals.workflow');
    Route::get('/contract-approvals/{contract}/history', [ContractApprovalController::class, 'history'])->name('contract-approvals.history');
    Route::get('/contract-approvals/dashboard', [ContractApprovalController::class, 'dashboard'])->name('contract-approvals.dashboard');

    // Electronic Signatures
    Route::get('/esignatures', [EsignatureController::class, 'index'])->name('esignatures.index');
    Route::get('/esignatures/create/{document}', [EsignatureController::class, 'create'])->name('esignatures.create');
    Route::post('/esignatures', [EsignatureController::class, 'store'])->name('esignatures.store');
    Route::get('/esignatures/{signature}', [EsignatureController::class, 'show'])->name('esignatures.show');
    Route::get('/esignatures/sign/{token}', [EsignatureController::class, 'sign'])->name('esignatures.sign');
    Route::post('/esignatures/sign/{token}', [EsignatureController::class, 'submitSignature'])->name('esignatures.sign.submit');
    Route::post('/esignatures/{signature}/remind', [EsignatureController::class, 'remind'])->name('esignatures.remind');
    Route::post('/esignatures/{signature}/cancel', [EsignatureController::class, 'cancel'])->name('esignatures.cancel');
    Route::get('/esignatures/verify/{token}', [EsignatureController::class, 'verify'])->name('esignatures.verify');
    Route::get('/esignatures/{signature}/download', [EsignatureController::class, 'download'])->name('esignatures.download');

    // Notary Services
    Route::get('/notary', [NotaryController::class, 'index'])->name('notary.index');
    Route::get('/notary/create', [NotaryController::class, 'create'])->name('notary.create');
    Route::post('/notary', [NotaryController::class, 'store'])->name('notary.store');
    Route::get('/notary/{verification}', [NotaryController::class, 'show'])->name('notary.show');
    Route::post('/notary/{verification}/verify', [NotaryController::class, 'verify'])->name('notary.verify');
    Route::get('/notary/{verification}/info', [NotaryController::class, 'info'])->name('notary.info');
    Route::post('/notary/{verification}/info', [NotaryController::class, 'provideInfo'])->name('notary.provide-info');
    Route::get('/notary/{verification}/certificate', [NotaryController::class, 'certificate'])->name('notary.certificate');
    Route::get('/notary/{verification}/download-certificate', [NotaryController::class, 'downloadCertificate'])->name('notary.download-certificate');
    Route::get('/notary/verify-certificate', [NotaryController::class, 'verifyCertificate'])->name('notary.verify-certificate');
    Route::get('/notary/dashboard', [NotaryController::class, 'dashboard'])->name('notary.dashboard');

    // Document Versions
    Route::get('/document-versions/{document}', [DocumentVersionController::class, 'index'])->name('document-versions.index');
    Route::get('/documents/versions', [DocumentVersionController::class, 'dashboard'])->name('documents.versions.index');
    Route::get('/document-versions/{document}/create', [DocumentVersionController::class, 'create'])->name('document-versions.create');
    Route::post('/document-versions/{document}', [DocumentVersionController::class, 'store'])->name('document-versions.store');
    Route::get('/document-versions/{version}', [DocumentVersionController::class, 'show'])->name('document-versions.show');
    Route::get('/document-versions/{version}/edit', [DocumentVersionController::class, 'edit'])->name('document-versions.edit');
    Route::put('/document-versions/{version}', [DocumentVersionController::class, 'update'])->name('document-versions.update');
    Route::get('/document-versions/{version}/compare/{compare}', [DocumentVersionController::class, 'compare'])->name('document-versions.compare');
    Route::post('/document-versions/{version}/restore', [DocumentVersionController::class, 'restore'])->name('document-versions.restore');
    Route::get('/document-versions/{version}/download', [DocumentVersionController::class, 'download'])->name('document-versions.download');
    Route::post('/document-versions/{version}/publish', [DocumentVersionController::class, 'publish'])->name('document-versions.publish');
    Route::post('/document-versions/{version}/archive', [DocumentVersionController::class, 'archive'])->name('document-versions.archive');
    Route::delete('/document-versions/{version}', [DocumentVersionController::class, 'destroy'])->name('document-versions.destroy');
    Route::get('/document-versions/{version}/history', [DocumentVersionController::class, 'history'])->name('document-versions.history');

    // Document Compliance
    Route::get('/documents/compliance', [DocumentComplianceController::class, 'index'])->name('documents.compliance.index');
    Route::get('/documents/compliance/{document}/create', [DocumentComplianceController::class, 'create'])->name('documents.compliance.create');
    Route::post('/documents/compliance/{document}', [DocumentComplianceController::class, 'store'])->name('documents.compliance.store');
    Route::get('/documents/compliance/{compliance}', [DocumentComplianceController::class, 'show'])->name('documents.compliance.show');
    Route::get('/documents/compliance/{compliance}/edit', [DocumentComplianceController::class, 'edit'])->name('documents.compliance.edit');
    Route::put('/documents/compliance/{compliance}', [DocumentComplianceController::class, 'update'])->name('documents.compliance.update');
    Route::post('/documents/compliance/bulk-check', [DocumentComplianceController::class, 'bulkCheck'])->name('documents.compliance.bulk-check');
    Route::get('/documents/compliance/report', [DocumentComplianceController::class, 'report'])->name('documents.compliance.report');
    Route::get('/documents/compliance/export', [DocumentComplianceController::class, 'export'])->name('documents.compliance.export');
    Route::get('/documents/compliance/reminders', [DocumentComplianceController::class, 'reminders'])->name('documents.compliance.reminders');
    Route::post('/documents/compliance/send-reminders', [DocumentComplianceController::class, 'sendReminders'])->name('documents.compliance.send-reminders');

});
