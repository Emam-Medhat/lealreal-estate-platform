<?php

use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\LeadScoringController;
use App\Http\Controllers\LeadNurturingController;
use App\Http\Controllers\LeadAssignmentController;
use App\Http\Controllers\LeadConversionController;
use App\Http\Controllers\LeadCampaignController;
use App\Http\Controllers\LeadAutomationController;
use App\Http\Controllers\LeadAnalyticsController;
use App\Http\Controllers\LeadImportController;
use App\Http\Controllers\LeadExportController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Main Lead Management Routes
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/dashboard', [LeadController::class, 'dashboard'])->name('leads.dashboard');
    Route::get('/leads/pipeline', [LeadController::class, 'pipeline'])->name('leads.pipeline');
    Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    Route::post('/leads/{lead}/score', [LeadController::class, 'score'])->name('leads.score');

    // Lead Sources
    Route::get('/lead-sources', [LeadSourceController::class, 'index'])->name('lead-sources.index');
    Route::get('/lead-sources/create', [LeadSourceController::class, 'create'])->name('lead-sources.create');
    Route::post('/lead-sources', [LeadSourceController::class, 'store'])->name('lead-sources.store');
    Route::get('/lead-sources/{leadSource}', [LeadSourceController::class, 'show'])->name('lead-sources.show');
    Route::get('/lead-sources/{leadSource}/edit', [LeadSourceController::class, 'edit'])->name('lead-sources.edit');
    Route::put('/lead-sources/{leadSource}', [LeadSourceController::class, 'update'])->name('lead-sources.update');
    Route::delete('/lead-sources/{leadSource}', [LeadSourceController::class, 'destroy'])->name('lead-sources.destroy');
    Route::post('/lead-sources/{leadSource}/toggle', [LeadSourceController::class, 'toggleStatus'])->name('lead-sources.toggle');

    // Lead Statuses
    Route::get('/lead-statuses', [LeadStatusController::class, 'index'])->name('lead-statuses.index');
    Route::get('/lead-statuses/create', [LeadStatusController::class, 'create'])->name('lead-statuses.create');
    Route::post('/lead-statuses', [LeadStatusController::class, 'store'])->name('lead-statuses.store');
    Route::get('/lead-statuses/{leadStatus}', [LeadStatusController::class, 'show'])->name('lead-statuses.show');
    Route::get('/lead-statuses/{leadStatus}/edit', [LeadStatusController::class, 'edit'])->name('lead-statuses.edit');
    Route::put('/lead-statuses/{leadStatus}', [LeadStatusController::class, 'update'])->name('lead-statuses.update');
    Route::delete('/lead-statuses/{leadStatus}', [LeadStatusController::class, 'destroy'])->name('lead-statuses.destroy');
    Route::post('/lead-statuses/{leadStatus}/toggle', [LeadStatusController::class, 'toggleStatus'])->name('lead-statuses.toggle');
    Route::post('/lead-statuses/reorder', [LeadStatusController::class, 'reorder'])->name('lead-statuses.reorder');

    // Lead Scoring
    Route::get('/lead-scoring', [LeadScoringController::class, 'index'])->name('lead-scoring.index');
    Route::get('/lead-scoring/create', [LeadScoringController::class, 'create'])->name('lead-scoring.create');
    Route::post('/lead-scoring', [LeadScoringController::class, 'store'])->name('lead-scoring.store');
    Route::get('/lead-scoring/{leadScore}', [LeadScoringController::class, 'show'])->name('lead-scoring.show');
    Route::get('/lead-scoring/{leadScore}/edit', [LeadScoringController::class, 'edit'])->name('lead-scoring.edit');
    Route::put('/lead-scoring/{leadScore}', [LeadScoringController::class, 'update'])->name('lead-scoring.update');
    Route::delete('/lead-scoring/{leadScore}', [LeadScoringController::class, 'destroy'])->name('lead-scoring.destroy');
    Route::post('/lead-scoring/bulk', [LeadScoringController::class, 'bulkScoring'])->name('lead-scoring.bulk');
    Route::get('/lead-scoring/rules', [LeadScoringController::class, 'rules'])->name('lead-scoring.rules');
    Route::post('/lead-scoring/rules', [LeadScoringController::class, 'storeRules'])->name('lead-scoring.rules.store');

    // Lead Nurturing
    Route::get('/lead-nurturing', [LeadNurturingController::class, 'index'])->name('lead-nurturing.index');
    Route::get('/lead-nurturing/create', [LeadNurturingController::class, 'create'])->name('lead-nurturing.create');
    Route::post('/lead-nurturing', [LeadNurturingController::class, 'store'])->name('lead-nurturing.store');
    Route::get('/lead-nurturing/{lead}', [LeadNurturingController::class, 'show'])->name('lead-nurturing.show');
    Route::post('/lead-nurturing/bulk', [LeadNurturingController::class, 'bulkNurturing'])->name('lead-nurturing.bulk');
    Route::get('/lead-nurturing/automation', [LeadNurturingController::class, 'automation'])->name('lead-nurturing.automation');
    Route::get('/lead-nurturing/automation/create', [LeadNurturingController::class, 'createAutomation'])->name('lead-nurturing.automation.create');
    Route::post('/lead-nurturing/automation', [LeadNurturingController::class, 'storeAutomation'])->name('lead-nurturing.automation.store');
    Route::get('/lead-nurturing/reminders', [LeadNurturingController::class, 'followUpReminders'])->name('lead-nurturing.reminders');

    // Lead Assignment
    Route::get('/lead-assignment', [LeadAssignmentController::class, 'index'])->name('lead-assignment.index');
    Route::post('/lead-assignment/assign', [LeadAssignmentController::class, 'assign'])->name('lead-assignment.assign');
    Route::post('/lead-assignment/bulk', [LeadAssignmentController::class, 'bulkAssign'])->name('lead-assignment.bulk');
    Route::post('/lead-assignment/{lead}/reassign', [LeadAssignmentController::class, 'reassign'])->name('lead-assignment.reassign');
    Route::post('/lead-assignment/{lead}/unassign', [LeadAssignmentController::class, 'unassign'])->name('lead-assignment.unassign');
    Route::get('/lead-assignment/rules', [LeadAssignmentController::class, 'assignmentRules'])->name('lead-assignment.rules');
    Route::post('/lead-assignment/rules', [LeadAssignmentController::class, 'storeAssignmentRules'])->name('lead-assignment.rules.store');
    Route::get('/lead-assignment/workload', [LeadAssignmentController::class, 'workload'])->name('lead-assignment.workload');

    // Lead Conversions
    Route::get('/lead-conversions', [LeadConversionController::class, 'index'])->name('lead-conversions.index');
    Route::get('/lead-conversions/analytics', [LeadConversionController::class, 'analytics'])->name('lead-conversions.analytics');
    Route::get('/lead-conversions/funnel', [LeadConversionController::class, 'conversionFunnel'])->name('lead-conversions.funnel');
    Route::get('/lead-conversions/report', [LeadConversionController::class, 'conversionReport'])->name('lead-conversions.report');
    Route::get('/lead-conversions/create/{lead}', [LeadConversionController::class, 'create'])->name('lead-conversions.create');
    Route::post('/lead-conversions', [LeadConversionController::class, 'store'])->name('lead-conversions.store');
    Route::get('/lead-conversions/{conversion}', [LeadConversionController::class, 'show'])->name('lead-conversions.show');
    Route::get('/lead-conversions/{conversion}/edit', [LeadConversionController::class, 'edit'])->name('lead-conversions.edit');
    Route::put('/lead-conversions/{conversion}', [LeadConversionController::class, 'update'])->name('lead-conversions.update');
    Route::delete('/lead-conversions/{conversion}', [LeadConversionController::class, 'destroy'])->name('lead-conversions.destroy');

    // Lead Campaigns
    Route::get('/lead-campaigns', [LeadCampaignController::class, 'index'])->name('lead-campaigns.index');
    Route::get('/lead-campaigns/create', [LeadCampaignController::class, 'create'])->name('lead-campaigns.create');
    Route::post('/lead-campaigns', [LeadCampaignController::class, 'store'])->name('lead-campaigns.store');
    Route::get('/lead-campaigns/{campaign}', [LeadCampaignController::class, 'show'])->name('lead-campaigns.show');
    Route::get('/lead-campaigns/{campaign}/edit', [LeadCampaignController::class, 'edit'])->name('lead-campaigns.edit');
    Route::put('/lead-campaigns/{campaign}', [LeadCampaignController::class, 'update'])->name('lead-campaigns.update');
    Route::delete('/lead-campaigns/{campaign}', [LeadCampaignController::class, 'destroy'])->name('lead-campaigns.destroy');
    Route::post('/lead-campaigns/{campaign}/toggle', [LeadCampaignController::class, 'toggleStatus'])->name('lead-campaigns.toggle');
    Route::post('/lead-campaigns/{campaign}/leads', [LeadCampaignController::class, 'addLeads'])->name('lead-campaigns.leads.add');
    Route::delete('/lead-campaigns/{campaign}/leads/{lead}', [LeadCampaignController::class, 'removeLead'])->name('lead-campaigns.leads.remove');
    Route::get('/lead-campaigns/{campaign}/analytics', [LeadCampaignController::class, 'analytics'])->name('lead-campaigns.analytics');
    Route::post('/lead-campaigns/{campaign}/duplicate', [LeadCampaignController::class, 'duplicate'])->name('lead-campaigns.duplicate');

    // Lead Automation
    Route::get('/lead-automation', [LeadAutomationController::class, 'index'])->name('lead-automation.index');
    Route::get('/lead-automation/create', [LeadAutomationController::class, 'create'])->name('lead-automation.create');
    Route::post('/lead-automation', [LeadAutomationController::class, 'store'])->name('lead-automation.store');
    Route::get('/lead-automation/{id}', [LeadAutomationController::class, 'show'])->name('lead-automation.show');
    Route::get('/lead-automation/{id}/edit', [LeadAutomationController::class, 'edit'])->name('lead-automation.edit');
    Route::put('/lead-automation/{id}', [LeadAutomationController::class, 'update'])->name('lead-automation.update');
    Route::delete('/lead-automation/{id}', [LeadAutomationController::class, 'destroy'])->name('lead-automation.destroy');
    Route::post('/lead-automation/{id}/toggle', [LeadAutomationController::class, 'toggleStatus'])->name('lead-automation.toggle');
    Route::post('/lead-automation/test', [LeadAutomationController::class, 'test'])->name('lead-automation.test');
    Route::post('/lead-automation/run', [LeadAutomationController::class, 'runAutomations'])->name('lead-automation.run');

    // Lead Analytics
    Route::get('/lead-analytics/dashboard', [LeadAnalyticsController::class, 'dashboard'])->name('lead-analytics.dashboard');
    Route::get('/lead-analytics/conversions', [LeadAnalyticsController::class, 'conversionAnalytics'])->name('lead-analytics.conversions');
    Route::get('/lead-analytics/sources', [LeadAnalyticsController::class, 'sourceAnalytics'])->name('lead-analytics.sources');
    Route::get('/lead-analytics/activities', [LeadAnalyticsController::class, 'activityAnalytics'])->name('lead-analytics.activities');
    Route::get('/lead-analytics/performance', [LeadAnalyticsController::class, 'performanceAnalytics'])->name('lead-analytics.performance');
    Route::post('/lead-analytics/custom', [LeadAnalyticsController::class, 'customReport'])->name('lead-analytics.custom');
    Route::post('/lead-analytics/export', [LeadAnalyticsController::class, 'export'])->name('lead-analytics.export');

    // Lead Import/Export
    Route::get('/lead-import', [LeadImportController::class, 'index'])->name('lead-import.index');
    Route::get('/lead-import/create', [LeadImportController::class, 'create'])->name('lead-import.create');
    Route::post('/lead-import', [LeadImportController::class, 'store'])->name('lead-import.store');
    Route::get('/lead-import/{importId}', [LeadImportController::class, 'show'])->name('lead-import.show');
    Route::get('/lead-import/template', [LeadImportController::class, 'downloadTemplate'])->name('lead-import.template');
    Route::post('/lead-import/preview', [LeadImportController::class, 'preview'])->name('lead-import.preview');
    Route::post('/lead-import/validate', [LeadImportController::class, 'validateMapping'])->name('lead-import.validate');

    Route::get('/lead-export', [LeadExportController::class, 'index'])->name('lead-export.index');
    Route::get('/lead-export/create', [LeadExportController::class, 'create'])->name('lead-export.create');
    Route::post('/lead-export', [LeadExportController::class, 'store'])->name('lead-export.store');
    Route::get('/lead-export/{exportId}', [LeadExportController::class, 'show'])->name('lead-export.show');
    Route::get('/lead-export/{exportId}/download', [LeadExportController::class, 'download'])->name('lead-export.download');
    Route::post('/lead-export/quick', [LeadExportController::class, 'quickExport'])->name('lead-export.quick');
    Route::delete('/lead-export/{exportId}', [LeadExportController::class, 'destroy'])->name('lead-export.destroy');

});
