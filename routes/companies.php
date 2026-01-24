<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyMemberController;
use App\Http\Controllers\CompanyBranchController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\CompanyReportController;
use App\Http\Controllers\CompanySubscriptionController;
use App\Http\Controllers\CompanyInvitationController;

/*
|--------------------------------------------------------------------------
| Company Management Routes
|--------------------------------------------------------------------------
|
| Routes for company management, members, branches, and analytics
|
*/

Route::middleware(['auth', 'verified'])->prefix('companies')->group(function () {
    
    // Company Management
    Route::get('/', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/{company}', [CompanyController::class, 'show'])->name('companies.show');
    Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    
    // Company Members
    Route::prefix('/{company}/members')->group(function () {
        Route::get('/', [CompanyMemberController::class, 'index'])->name('companies.members.index');
        Route::get('/create', [CompanyMemberController::class, 'create'])->name('companies.members.create');
        Route::post('/', [CompanyMemberController::class, 'store'])->name('companies.members.store');
        Route::get('/{member}', [CompanyMemberController::class, 'show'])->name('companies.members.show');
        Route::get('/{member}/edit', [CompanyMemberController::class, 'edit'])->name('companies.members.edit');
        Route::put('/{member}', [CompanyMemberController::class, 'update'])->name('companies.members.update');
        Route::put('/{member}/role', [CompanyMemberController::class, 'updateRole'])->name('companies.members.update.role');
        Route::delete('/{member}', [CompanyMemberController::class, 'destroy'])->name('companies.members.destroy');
        Route::post('/{member}/resend-invitation', [CompanyMemberController::class, 'resendInvitation'])->name('companies.members.resend.invitation');
    });
    
    // Company Branches
    Route::prefix('/{company}/branches')->group(function () {
        Route::get('/', [CompanyBranchController::class, 'index'])->name('companies.branches.index');
        Route::get('/create', [CompanyBranchController::class, 'create'])->name('companies.branches.create');
        Route::post('/', [CompanyBranchController::class, 'store'])->name('companies.branches.store');
        Route::get('/{branch}', [CompanyBranchController::class, 'show'])->name('companies.branches.show');
        Route::get('/{branch}/edit', [CompanyBranchController::class, 'edit'])->name('companies.branches.edit');
        Route::put('/{branch}', [CompanyBranchController::class, 'update'])->name('companies.branches.update');
        Route::delete('/{branch}', [CompanyBranchController::class, 'destroy'])->name('companies.branches.destroy');
    });
    
    // Company Settings
    Route::prefix('/{company}/settings')->group(function () {
        Route::get('/', [CompanySettingsController::class, 'index'])->name('companies.settings.index');
        Route::get('/profile', [CompanySettingsController::class, 'profile'])->name('companies.settings.profile');
        Route::get('/notifications', [CompanySettingsController::class, 'notifications'])->name('companies.settings.notifications');
        Route::get('/privacy', [CompanySettingsController::class, 'privacy'])->name('companies.settings.privacy');
        Route::get('/features', [CompanySettingsController::class, 'features'])->name('companies.settings.features');
        Route::get('/branding', [CompanySettingsController::class, 'branding'])->name('companies.settings.branding');
        Route::put('/{setting}', [CompanySettingsController::class, 'update'])->name('companies.settings.update');
    });
    
    // Company Reports
    Route::prefix('/{company}/reports')->group(function () {
        Route::get('/', [CompanyReportController::class, 'index'])->name('companies.reports.index');
        Route::post('/generate', [CompanyReportController::class, 'generate'])->name('companies.reports.generate');
        Route::get('/{report}', [CompanyReportController::class, 'show'])->name('companies.reports.show');
        Route::get('/{report}/download', [CompanyReportController::class, 'download'])->name('companies.reports.download');
        Route::get('/{report}/preview', [CompanyReportController::class, 'preview'])->name('companies.reports.preview');
        Route::delete('/{report}', [CompanyReportController::class, 'destroy'])->name('companies.reports.destroy');
    });
    
    // Company Subscriptions
    Route::prefix('/{company}/subscription')->group(function () {
        Route::get('/', [CompanySubscriptionController::class, 'index'])->name('companies.subscription.index');
        Route::get('/plans', [CompanySubscriptionController::class, 'plans'])->name('companies.subscription.plans');
        Route::post('/subscribe', [CompanySubscriptionController::class, 'subscribe'])->name('companies.subscription.subscribe');
        Route::post('/renew', [CompanySubscriptionController::class, 'renew'])->name('companies.subscription.renew');
        Route::post('/upgrade', [CompanySubscriptionController::class, 'upgrade'])->name('companies.subscription.upgrade');
        Route::post('/cancel', [CompanySubscriptionController::class, 'cancel'])->name('companies.subscription.cancel');
        Route::get('/history', [CompanySubscriptionController::class, 'history'])->name('companies.subscription.history');
    });
    
    // Company Invitations
    Route::prefix('/invitations')->group(function () {
        Route::get('/', [CompanyInvitationController::class, 'index'])->name('companies.invitations.index');
        Route::post('/', [CompanyInvitationController::class, 'store'])->name('companies.invitations.store');
        Route::get('/{invitation}', [CompanyInvitationController::class, 'show'])->name('companies.invitations.show');
        Route::post('/{invitation}/accept', [CompanyInvitationController::class, 'accept'])->name('companies.invitations.accept');
        Route::post('/{invitation}/decline', [CompanyInvitationController::class, 'decline'])->name('companies.invitations.decline');
        Route::post('/{invitation}/resend', [CompanyInvitationController::class, 'resend'])->name('companies.invitations.resend');
    });
    
    // Company Dashboard
    Route::get('/{company}/dashboard', [CompanyController::class, 'dashboard'])->name('companies.dashboard');
    
    // Company Analytics
    Route::get('/{company}/analytics', [CompanyController::class, 'analytics'])->name('companies.analytics');
    Route::get('/{company}/analytics/performance', [CompanyController::class, 'analyticsPerformance'])->name('companies.analytics.performance');
    Route::get('/{company}/analytics/team', [CompanyController::class, 'analyticsTeam'])->name('companies.analytics.team');
    Route::get('/{company}/analytics/properties', [CompanyController::class, 'analyticsProperties'])->name('companies.analytics.properties');
    Route::get('/{company}/analytics/revenue', [CompanyController::class, 'analyticsRevenue'])->name('companies.analytics.revenue');
});

// API Routes for Company Management
Route::prefix('api/companies')->middleware(['auth:api', 'throttle:60,1'])->group(function () {
    
    // Company API
    Route::get('/', [CompanyController::class, 'apiIndex'])->name('api.companies.index');
    Route::post('/', [CompanyController::class, 'apiStore'])->name('api.companies.store');
    Route::get('/{company}', [CompanyController::class, 'apiShow'])->name('api.companies.show');
    Route::put('/{company}', [CompanyController::class, 'apiUpdate'])->name('api.companies.update');
    Route::delete('/{company}', [CompanyController::class, 'apiDestroy'])->name('api.companies.destroy');
    
    // Members API
    Route::prefix('/{company}/members')->group(function () {
        Route::get('/', [CompanyMemberController::class, 'apiIndex'])->name('api.companies.members.index');
        Route::post('/', [CompanyMemberController::class, 'apiStore'])->name('api.companies.members.store');
        Route::get('/{member}', [CompanyMemberController::class, 'apiShow'])->name('api.companies.members.show');
        Route::put('/{member}', [CompanyMemberController::class, 'apiUpdate'])->name('api.companies.members.update');
        Route::delete('/{member}', [CompanyMemberController::class, 'apiDestroy'])->name('api.companies.members.destroy');
    });
    
    // Analytics API
    Route::prefix('/{company}/analytics')->group(function () {
        Route::get('/performance', [CompanyController::class, 'apiAnalyticsPerformance'])->name('api.companies.analytics.performance');
        Route::get('/team', [CompanyController::class, 'apiAnalyticsTeam'])->name('api.companies.analytics.team');
        Route::get('/properties', [CompanyController::class, 'apiAnalyticsProperties'])->name('api.companies.analytics.properties');
        Route::get('/revenue', [CompanyController::class, 'apiAnalyticsRevenue'])->name('api.companies.analytics.revenue');
    });
    
    // Reports API
    Route::prefix('/{company}/reports')->group(function () {
        Route::get('/', [CompanyReportController::class, 'apiIndex'])->name('api.companies.reports.index');
        Route::post('/generate', [CompanyReportController::class, 'apiGenerate'])->name('api.companies.reports.generate');
        Route::get('/{report}', [CompanyReportController::class, 'apiShow'])->name('api.companies.reports.show');
        Route::get('/{report}/download', [CompanyReportController::class, 'apiDownload'])->name('api.companies.reports.download');
    });
});
