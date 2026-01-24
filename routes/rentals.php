<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\LeaseRenewalController;
use App\Http\Controllers\RentPaymentController;
use App\Http\Controllers\SecurityDepositController;
use App\Http\Controllers\RentCollectionController;
use App\Http\Controllers\EvictionController;
use App\Http\Controllers\TenantScreeningController;
use App\Http\Controllers\RentalApplicationController;
use App\Http\Controllers\RentalInspectionController;
use App\Http\Controllers\RentAdjustmentController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Rental Dashboard
    Route::get('/rentals', [RentalController::class, 'dashboard'])->name('rentals.dashboard');
    Route::get('/rentals/index', [RentalController::class, 'index'])->name('rentals.index');
    
    // Rentals Management
    Route::get('/rentals/properties', [RentalController::class, 'properties'])->name('rentals.properties');
    Route::get('/rentals/properties/create', [RentalController::class, 'createProperty'])->name('rentals.properties.create');
    Route::post('/rentals/properties', [RentalController::class, 'storeProperty'])->name('rentals.properties.store');
    Route::get('/rentals/properties/{property}', [RentalController::class, 'showProperty'])->name('rentals.properties.show');
    Route::get('/rentals/properties/{property}/edit', [RentalController::class, 'editProperty'])->name('rentals.properties.edit');
    Route::put('/rentals/properties/{property}', [RentalController::class, 'updateProperty'])->name('rentals.properties.update');
    Route::delete('/rentals/properties/{property}', [RentalController::class, 'destroyProperty'])->name('rentals.properties.destroy');
    
    // Property Actions
    Route::post('/rentals/properties/{property}/toggle-availability', [RentalController::class, 'toggleAvailability'])->name('rentals.properties.toggle-availability');
    Route::post('/rentals/properties/{property}/update-rent', [RentalController::class, 'updateRent'])->name('rentals.properties.update-rent');
    Route::get('/rentals/properties/{property}/analytics', [RentalController::class, 'propertyAnalytics'])->name('rentals.properties.analytics');
    Route::get('/rentals/properties/export', [RentalController::class, 'exportProperties'])->name('rentals.properties.export');
    
    // Tenants Management
    Route::get('/rentals/tenants', [TenantController::class, 'index'])->name('rentals.tenants.index');
    Route::get('/rentals/tenants/create', [TenantController::class, 'create'])->name('rentals.tenants.create');
    Route::post('/rentals/tenants', [TenantController::class, 'store'])->name('rentals.tenants.store');
    Route::get('/rentals/tenants/{tenant}', [TenantController::class, 'show'])->name('rentals.tenants.show');
    Route::get('/rentals/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('rentals.tenants.edit');
    Route::put('/rentals/tenants/{tenant}', [TenantController::class, 'update'])->name('rentals.tenants.update');
    Route::delete('/rentals/tenants/{tenant}', [TenantController::class, 'destroy'])->name('rentals.tenants.destroy');
    
    // Tenant Actions
    Route::post('/rentals/tenants/{tenant}/screen', [TenantController::class, 'screen'])->name('rentals.tenants.screen');
    Route::post('/rentals/tenants/{tenant}/verify', [TenantController::class, 'verify'])->name('rentals.tenants.verify');
    Route::post('/rentals/tenants/{tenant}/blacklist', [TenantController::class, 'blacklist'])->name('rentals.tenants.blacklist');
    Route::get('/rentals/tenants/{tenant}/history', [TenantController::class, 'history'])->name('rentals.tenants.history');
    Route::get('/rentals/tenants/{tenant}/documents', [TenantController::class, 'documents'])->name('rentals.tenants.documents');
    Route::post('/rentals/tenants/{tenant}/upload-document', [TenantController::class, 'uploadDocument'])->name('rentals.tenants.upload-document');
    Route::get('/rentals/tenants/export', [TenantController::class, 'export'])->name('rentals.tenants.export');
    
    // Leases Management
    Route::get('/rentals/leases', [LeaseController::class, 'index'])->name('rentals.leases.index');
    Route::get('/rentals/leases/create', [LeaseController::class, 'create'])->name('rentals.leases.create');
    Route::post('/rentals/leases', [LeaseController::class, 'store'])->name('rentals.leases.store');
    Route::get('/rentals/leases/{lease}', [LeaseController::class, 'show'])->name('rentals.leases.show');
    Route::get('/rentals/leases/{lease}/edit', [LeaseController::class, 'edit'])->name('rentals.leases.edit');
    Route::put('/rentals/leases/{lease}', [LeaseController::class, 'update'])->name('rentals.leases.update');
    Route::delete('/rentals/leases/{lease}', [LeaseController::class, 'destroy'])->name('rentals.leases.destroy');
    
    // Lease Actions
    Route::post('/rentals/leases/{lease}/activate', [LeaseController::class, 'activate'])->name('rentals.leases.activate');
    Route::post('/rentals/leases/{lease}/terminate', [LeaseController::class, 'terminate'])->name('rentals.leases.terminate');
    Route::post('/rentals/leases/{lease}/suspend', [LeaseController::class, 'suspend'])->name('rentals.leases.suspend');
    Route::post('/rentals/leases/{lease}/resume', [LeaseController::class, 'resume'])->name('rentals.leases.resume');
    Route::get('/rentals/leases/{lease}/download', [LeaseController::class, 'download'])->name('rentals.leases.download');
    Route::post('/rentals/leases/{lease}/send-reminder', [LeaseController::class, 'sendReminder'])->name('rentals.leases.send-reminder');
    Route::get('/rentals/leases/{lease}/payments', [LeaseController::class, 'payments'])->name('rentals.leases.payments');
    Route::get('/rentals/leases/export', [LeaseController::class, 'export'])->name('rentals.leases.export');
    
    // Lease Renewals
    Route::get('/rentals/lease-renewals', [LeaseRenewalController::class, 'index'])->name('rentals.lease-renewals.index');
    Route::get('/rentals/lease-renewals/create', [LeaseRenewalController::class, 'create'])->name('rentals.lease-renewals.create');
    Route::post('/rentals/lease-renewals', [LeaseRenewalController::class, 'store'])->name('rentals.lease-renewals.store');
    Route::get('/rentals/lease-renewals/{renewal}', [LeaseRenewalController::class, 'show'])->name('rentals.lease-renewals.show');
    Route::get('/rentals/lease-renewals/{renewal}/edit', [LeaseRenewalController::class, 'edit'])->name('rentals.lease-renewals.edit');
    Route::put('/rentals/lease-renewals/{renewal}', [LeaseRenewalController::class, 'update'])->name('rentals.lease-renewals.update');
    Route::delete('/rentals/lease-renewals/{renewal}', [LeaseRenewalController::class, 'destroy'])->name('rentals.lease-renewals.destroy');
    
    // Renewal Actions
    Route::post('/rentals/lease-renewals/{renewal}/approve', [LeaseRenewalController::class, 'approve'])->name('rentals.lease-renewals.approve');
    Route::post('/rentals/lease-renewals/{renewal}/reject', [LeaseRenewalController::class, 'reject'])->name('rentals.lease-renewals.reject');
    Route::post('/rentals/lease-renewals/{renewal}/send-offer', [LeaseRenewalController::class, 'sendOffer'])->name('rentals.lease-renewals.send-offer');
    Route::get('/rentals/lease-renewals/upcoming', [LeaseRenewalController::class, 'upcoming'])->name('rentals.lease-renewals.upcoming');
    Route::get('/rentals/lease-renewals/export', [LeaseRenewalController::class, 'export'])->name('rentals.lease-renewals.export');
    
    // Rent Payments
    Route::get('/rentals/payments', [RentPaymentController::class, 'index'])->name('rentals.payments.index');
    Route::get('/rentals/payments/create', [RentPaymentController::class, 'create'])->name('rentals.payments.create');
    Route::post('/rentals/payments', [RentPaymentController::class, 'store'])->name('rentals.payments.store');
    Route::get('/rentals/payments/{payment}', [RentPaymentController::class, 'show'])->name('rentals.payments.show');
    Route::get('/rentals/payments/{payment}/edit', [RentPaymentController::class, 'edit'])->name('rentals.payments.edit');
    Route::put('/rentals/payments/{payment}', [RentPaymentController::class, 'update'])->name('rentals.payments.update');
    Route::delete('/rentals/payments/{payment}', [RentPaymentController::class, 'destroy'])->name('rentals.payments.destroy');
    
    // Payment Actions
    Route::post('/rentals/payments/{payment}/process', [RentPaymentController::class, 'process'])->name('rentals.payments.process');
    Route::post('/rentals/payments/{payment}/confirm', [RentPaymentController::class, 'confirm'])->name('rentals.payments.confirm');
    Route::post('/rentals/payments/{payment}/refund', [RentPaymentController::class, 'refund'])->name('rentals.payments.refund');
    Route::post('/rentals/payments/{payment}/mark-late', [RentPaymentController::class, 'markLate'])->name('rentals.payments.mark-late');
    Route::get('/rentals/payments/{payment}/receipt', [RentPaymentController::class, 'receipt'])->name('rentals.payments.receipt');
    Route::post('/rentals/payments/{payment}/send-reminder', [RentPaymentController::class, 'sendReminder'])->name('rentals.payments.send-reminder');
    Route::get('/rentals/payments/overdue', [RentPaymentController::class, 'overdue'])->name('rentals.payments.overdue');
    Route::get('/rentals/payments/export', [RentPaymentController::class, 'export'])->name('rentals.payments.export');
    
    // Security Deposits
    Route::get('/rentals/deposits', [SecurityDepositController::class, 'index'])->name('rentals.deposits.index');
    Route::get('/rentals/deposits/create', [SecurityDepositController::class, 'create'])->name('rentals.deposits.create');
    Route::post('/rentals/deposits', [SecurityDepositController::class, 'store'])->name('rentals.deposits.store');
    Route::get('/rentals/deposits/{deposit}', [SecurityDepositController::class, 'show'])->name('rentals.deposits.show');
    Route::get('/rentals/deposits/{deposit}/edit', [SecurityDepositController::class, 'edit'])->name('rentals.deposits.edit');
    Route::put('/rentals/deposits/{deposit}', [SecurityDepositController::class, 'update'])->name('rentals.deposits.update');
    Route::delete('/rentals/deposits/{deposit}', [SecurityDepositController::class, 'destroy'])->name('rentals.deposits.destroy');
    
    // Deposit Actions
    Route::post('/rentals/deposits/{deposit}/receive', [SecurityDepositController::class, 'receive'])->name('rentals.deposits.receive');
    Route::post('/rentals/deposits/{deposit}/hold', [SecurityDepositController::class, 'hold'])->name('rentals.deposits.hold');
    Route::post('/rentals/deposits/{deposit}/release', [SecurityDepositController::class, 'release'])->name('rentals.deposits.release');
    Route::post('/rentals/deposits/{deposit}/deduct', [SecurityDepositController::class, 'deduct'])->name('rentals.deposits.deduct');
    Route::post('/rentals/deposits/{deposit}/refund', [SecurityDepositController::class, 'refund'])->name('rentals.deposits.refund');
    Route::get('/rentals/deposits/{deposit}/statement', [SecurityDepositController::class, 'statement'])->name('rentals.deposits.statement');
    Route::get('/rentals/deposits/export', [SecurityDepositController::class, 'export'])->name('rentals.deposits.export');
    
    // Rent Collection
    Route::get('/rentals/collection', [RentCollectionController::class, 'index'])->name('rentals.collection.index');
    Route::get('/rentals/collection/dashboard', [RentCollectionController::class, 'dashboard'])->name('rentals.collection.dashboard');
    Route::post('/rentals/collection/generate-invoices', [RentCollectionController::class, 'generateInvoices'])->name('rentals.collection.generate-invoices');
    Route::post('/rentals/collection/send-reminders', [RentCollectionController::class, 'sendReminders'])->name('rentals.collection.send-reminders');
    Route::post('/rentals/collection/process-payments', [RentCollectionController::class, 'processPayments'])->name('rentals.collection.process-payments');
    Route::get('/rentals/collection/report', [RentCollectionController::class, 'report'])->name('rentals.collection.report');
    Route::get('/rentals/collection/analytics', [RentCollectionController::class, 'analytics'])->name('rentals.collection.analytics');
    Route::get('/rentals/collection/export', [RentCollectionController::class, 'export'])->name('rentals.collection.export');
    
    // Evictions
    Route::get('/rentals/evictions', [EvictionController::class, 'index'])->name('rentals.evictions.index');
    Route::get('/rentals/evictions/create', [EvictionController::class, 'create'])->name('rentals.evictions.create');
    Route::post('/rentals/evictions', [EvictionController::class, 'store'])->name('rentals.evictions.store');
    Route::get('/rentals/evictions/{eviction}', [EvictionController::class, 'show'])->name('rentals.evictions.show');
    Route::get('/rentals/evictions/{eviction}/edit', [EvictionController::class, 'edit'])->name('rentals.evictions.edit');
    Route::put('/rentals/evictions/{eviction}', [EvictionController::class, 'update'])->name('rentals.evictions.update');
    Route::delete('/rentals/evictions/{eviction}', [EvictionController::class, 'destroy'])->name('rentals.evictions.destroy');
    
    // Eviction Actions
    Route::post('/rentals/evictions/{eviction}/initiate', [EvictionController::class, 'initiate'])->name('rentals.evictions.initiate');
    Route::post('/rentals/evictions/{eviction}/serve-notice', [EvictionController::class, 'serveNotice'])->name('rentals.evictions.serve-notice');
    Route::post('/rentals/evictions/{eviction}/file-court', [EvictionController::class, 'fileCourt'])->name('rentals.evictions.file-court');
    Route::post('/rentals/evictions/{eviction}/complete', [EvictionController::class, 'complete'])->name('rentals.evictions.complete');
    Route::post('/rentals/evictions/{eviction}/cancel', [EvictionController::class, 'cancel'])->name('rentals.evictions.cancel');
    Route::get('/rentals/evictions/{eviction}/documents', [EvictionController::class, 'documents'])->name('rentals.evictions.documents');
    Route::get('/rentals/evictions/export', [EvictionController::class, 'export'])->name('rentals.evictions.export');
    
    // Tenant Screening
    Route::get('/rentals/screening', [TenantScreeningController::class, 'index'])->name('rentals.screening.index');
    Route::get('/rentals/screening/create', [TenantScreeningController::class, 'create'])->name('rentals.screening.create');
    Route::post('/rentals/screening', [TenantScreeningController::class, 'store'])->name('rentals.screening.store');
    Route::get('/rentals/screening/{screening}', [TenantScreeningController::class, 'show'])->name('rentals.screening.show');
    Route::get('/rentals/screening/{screening}/edit', [TenantScreeningController::class, 'edit'])->name('rentals.screening.edit');
    Route::put('/rentals/screening/{screening}', [TenantScreeningController::class, 'update'])->name('rentals.screening.update');
    Route::delete('/rentals/screening/{screening}', [TenantScreeningController::class, 'destroy'])->name('rentals.screening.destroy');
    
    // Screening Actions
    Route::post('/rentals/screening/{screening}/run-check', [TenantScreeningController::class, 'runCheck'])->name('rentals.screening.run-check');
    Route::post('/rentals/screening/{screening}/approve', [TenantScreeningController::class, 'approve'])->name('rentals.screening.approve');
    Route::post('/rentals/screening/{screening}/reject', [TenantScreeningController::class, 'reject'])->name('rentals.screening.reject');
    Route::post('/rentals/screening/{screening}/request-documents', [TenantScreeningController::class, 'requestDocuments'])->name('rentals.screening.request-documents');
    Route::get('/rentals/screening/{screening}/report', [TenantScreeningController::class, 'report'])->name('rentals.screening.report');
    Route::get('/rentals/screening/export', [TenantScreeningController::class, 'export'])->name('rentals.screening.export');
    
    // Rental Applications
    Route::get('/rentals/applications', [RentalApplicationController::class, 'index'])->name('rentals.applications.index');
    Route::get('/rentals/applications/create', [RentalApplicationController::class, 'create'])->name('rentals.applications.create');
    Route::post('/rentals/applications', [RentalApplicationController::class, 'store'])->name('rentals.applications.store');
    Route::get('/rentals/applications/{application}', [RentalApplicationController::class, 'show'])->name('rentals.applications.show');
    Route::get('/rentals/applications/{application}/edit', [RentalApplicationController::class, 'edit'])->name('rentals.applications.edit');
    Route::put('/rentals/applications/{application}', [RentalApplicationController::class, 'update'])->name('rentals.applications.update');
    Route::delete('/rentals/applications/{application}', [RentalApplicationController::class, 'destroy'])->name('rentals.applications.destroy');
    
    // Application Actions
    Route::post('/rentals/applications/{application}/submit', [RentalApplicationController::class, 'submit'])->name('rentals.applications.submit');
    Route::post('/rentals/applications/{application}/approve', [RentalApplicationController::class, 'approve'])->name('rentals.applications.approve');
    Route::post('/rentals/applications/{application}/reject', [RentalApplicationController::class, 'reject'])->name('rentals.applications.reject');
    Route::post('/rentals/applications/{application}/waitlist', [RentalApplicationController::class, 'waitlist'])->name('rentals.applications.waitlist');
    Route::post('/rentals/applications/{application}/create-lease', [RentalApplicationController::class, 'createLease'])->name('rentals.applications.create-lease');
    Route::get('/rentals/applications/{application}/documents', [RentalApplicationController::class, 'documents'])->name('rentals.applications.documents');
    Route::get('/rentals/applications/export', [RentalApplicationController::class, 'export'])->name('rentals.applications.export');
    
    // Rental Inspections
    Route::get('/rentals/inspections', [RentalInspectionController::class, 'index'])->name('rentals.inspections.index');
    Route::get('/rentals/inspections/create', [RentalInspectionController::class, 'create'])->name('rentals.inspections.create');
    Route::post('/rentals/inspections', [RentalInspectionController::class, 'store'])->name('rentals.inspections.store');
    Route::get('/rentals/inspections/{inspection}', [RentalInspectionController::class, 'show'])->name('rentals.inspections.show');
    Route::get('/rentals/inspections/{inspection}/edit', [RentalInspectionController::class, 'edit'])->name('rentals.inspections.edit');
    Route::put('/rentals/inspections/{inspection}', [RentalInspectionController::class, 'update'])->name('rentals.inspections.update');
    Route::delete('/rentals/inspections/{inspection}', [RentalInspectionController::class, 'destroy'])->name('rentals.inspections.destroy');
    
    // Inspection Actions
    Route::post('/rentals/inspections/{inspection}/conduct', [RentalInspectionController::class, 'conduct'])->name('rentals.inspections.conduct');
    Route::post('/rentals/inspections/{inspection}/complete', [RentalInspectionController::class, 'complete'])->name('rentals.inspections.complete');
    Route::post('/rentals/inspections/{inspection}/schedule', [RentalInspectionController::class, 'schedule'])->name('rentals.inspections.schedule');
    Route::post('/rentals/inspections/{inspection}/add-photos', [RentalInspectionController::class, 'addPhotos'])->name('rentals.inspections.add-photos');
    Route::get('/rentals/inspections/{inspection}/report', [RentalInspectionController::class, 'report'])->name('rentals.inspections.report');
    Route::get('/rentals/inspections/calendar', [RentalInspectionController::class, 'calendar'])->name('rentals.inspections.calendar');
    Route::get('/rentals/inspections/export', [RentalInspectionController::class, 'export'])->name('rentals.inspections.export');
    
    // Rent Adjustments
    Route::get('/rentals/adjustments', [RentAdjustmentController::class, 'index'])->name('rentals.adjustments.index');
    Route::get('/rentals/adjustments/create', [RentAdjustmentController::class, 'create'])->name('rentals.adjustments.create');
    Route::post('/rentals/adjustments', [RentAdjustmentController::class, 'store'])->name('rentals.adjustments.store');
    Route::get('/rentals/adjustments/{adjustment}', [RentAdjustmentController::class, 'show'])->name('rentals.adjustments.show');
    Route::get('/rentals/adjustments/{adjustment}/edit', [RentAdjustmentController::class, 'edit'])->name('rentals.adjustments.edit');
    Route::put('/rentals/adjustments/{adjustment}', [RentAdjustmentController::class, 'update'])->name('rentals.adjustments.update');
    Route::delete('/rentals/adjustments/{adjustment}', [RentAdjustmentController::class, 'destroy'])->name('rentals.adjustments.destroy');
    
    // Adjustment Actions
    Route::post('/rentals/adjustments/{adjustment}/approve', [RentAdjustmentController::class, 'approve'])->name('rentals.adjustments.approve');
    Route::post('/rentals/adjustments/{adjustment}/apply', [RentAdjustmentController::class, 'apply'])->name('rentals.adjustments.apply');
    Route::post('/rentals/adjustments/{adjustment}/cancel', [RentAdjustmentController::class, 'cancel'])->name('rentals.adjustments.cancel');
    Route::get('/rentals/adjustments/{adjustment}/notice', [RentAdjustmentController::class, 'notice'])->name('rentals.adjustments.notice');
    Route::get('/rentals/adjustments/export', [RentAdjustmentController::class, 'export'])->name('rentals.adjustments.export');
    
    // Rental Reports and Analytics
    Route::get('/rentals/reports', [RentalController::class, 'reports'])->name('rentals.reports');
    Route::get('/rentals/analytics', [RentalController::class, 'analytics'])->name('rentals.analytics');
    Route::get('/rentals/performance', [RentalController::class, 'performance'])->name('rentals.performance');
    Route::get('/rentals/occupancy', [RentalController::class, 'occupancy'])->name('rentals.occupancy');
    Route::get('/rentals/revenue', [RentalController::class, 'revenue'])->name('rentals.revenue');
    
    // Rental Search and API
    Route::get('/rentals/search', [RentalController::class, 'search'])->name('rentals.search');
    Route::get('/rentals/api/dashboard-stats', [RentalController::class, 'dashboardStats'])->name('rentals.api.dashboard-stats');
    Route::get('/rentals/api/calendar-events', [RentalController::class, 'calendarEvents'])->name('rentals.api.calendar-events');
    
    // Rental Settings
    Route::get('/rentals/settings', [RentalController::class, 'settings'])->name('rentals.settings');
    Route::post('/rentals/settings', [RentalController::class, 'saveSettings'])->name('rentals.settings.save');
});
