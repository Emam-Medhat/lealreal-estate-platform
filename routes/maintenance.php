<?php

use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\MaintenanceScheduleController;
use App\Http\Controllers\MaintenanceTicketController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\MaintenanceTeamController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\PreventiveMaintenanceController;
use App\Http\Controllers\EmergencyRepairController;
use App\Http\Controllers\MaintenanceInvoiceController;
use App\Http\Controllers\WarrantyController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Maintenance Dashboard
    Route::get('/maintenance', [MaintenanceController::class, 'dashboard'])->name('maintenance.dashboard');
    Route::get('/maintenance/index', [MaintenanceController::class, 'index'])->name('maintenance.index');
    
    // Maintenance Create
    Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    
    // Maintenance Requests
    Route::get('/maintenance/requests', [MaintenanceRequestController::class, 'index'])->name('maintenance.requests.index');
    Route::get('/maintenance/requests/create', [MaintenanceRequestController::class, 'create'])->name('maintenance.requests.create');
    Route::post('/maintenance/requests', [MaintenanceRequestController::class, 'store'])->name('maintenance.requests.store');
    Route::get('/maintenance/requests/{request}', [MaintenanceRequestController::class, 'show'])->name('maintenance.requests.show');
    Route::get('/maintenance/requests/{request}/edit', [MaintenanceRequestController::class, 'edit'])->name('maintenance.requests.edit');
    Route::put('/maintenance/requests/{request}', [MaintenanceRequestController::class, 'update'])->name('maintenance.requests.update');
    Route::delete('/maintenance/requests/{request}', [MaintenanceRequestController::class, 'destroy'])->name('maintenance.requests.destroy');
    
    // Maintenance Request Actions
    Route::post('/maintenance/requests/{request}/assign', [MaintenanceRequestController::class, 'assign'])->name('maintenance.requests.assign');
    Route::post('/maintenance/requests/{request}/start', [MaintenanceRequestController::class, 'start'])->name('maintenance.requests.start');
    Route::post('/maintenance/requests/{request}/complete', [MaintenanceRequestController::class, 'complete'])->name('maintenance.requests.complete');
    Route::post('/maintenance/requests/{request}/cancel', [MaintenanceRequestController::class, 'cancel'])->name('maintenance.requests.cancel');
    Route::post('/maintenance/requests/{request}/create-ticket', [MaintenanceRequestController::class, 'createTicket'])->name('maintenance.requests.create-ticket');
    
    // Maintenance Schedules
    Route::get('/maintenance/schedules', [MaintenanceScheduleController::class, 'index'])->name('maintenance.schedules.index');
    Route::get('/maintenance/schedule', [MaintenanceScheduleController::class, 'index'])->name('maintenance.schedule.index');
    Route::get('/maintenance/schedules/create', [MaintenanceScheduleController::class, 'create'])->name('maintenance.schedules.create');
    Route::post('/maintenance/schedules', [MaintenanceScheduleController::class, 'store'])->name('maintenance.schedules.store');
    Route::get('/maintenance/schedules/{schedule}', [MaintenanceScheduleController::class, 'show'])->name('maintenance.schedules.show');
    Route::get('/maintenance/schedules/{schedule}/edit', [MaintenanceScheduleController::class, 'edit'])->name('maintenance.schedules.edit');
    Route::put('/maintenance/schedules/{schedule}', [MaintenanceScheduleController::class, 'update'])->name('maintenance.schedules.update');
    Route::delete('/maintenance/schedules/{schedule}', [MaintenanceScheduleController::class, 'destroy'])->name('maintenance.schedules.destroy');
    
    // Schedule Calendar
    Route::get('/maintenance/schedules/calendar', [MaintenanceScheduleController::class, 'calendar'])->name('maintenance.schedules.calendar');
    Route::get('/maintenance/schedules/{schedule}/details', [MaintenanceScheduleController::class, 'details'])->name('maintenance.schedules.details');
    
    // Schedule Actions
    Route::post('/maintenance/schedules/{schedule}/start', [MaintenanceScheduleController::class, 'start'])->name('maintenance.schedules.start');
    Route::post('/maintenance/schedules/{schedule}/complete', [MaintenanceScheduleController::class, 'complete'])->name('maintenance.schedules.complete');
    Route::post('/maintenance/schedules/{schedule}/reschedule', [MaintenanceScheduleController::class, 'reschedule'])->name('maintenance.schedules.reschedule');
    Route::post('/maintenance/schedules/{schedule}/cancel', [MaintenanceScheduleController::class, 'cancel'])->name('maintenance.schedules.cancel');
    
    // Maintenance Tickets
    Route::get('/maintenance/tickets', [MaintenanceTicketController::class, 'index'])->name('maintenance.tickets.index');
    Route::get('/maintenance/tickets/create', [MaintenanceTicketController::class, 'create'])->name('maintenance.tickets.create');
    Route::post('/maintenance/tickets', [MaintenanceTicketController::class, 'store'])->name('maintenance.tickets.store');
    Route::get('/maintenance/tickets/{ticket}', [MaintenanceTicketController::class, 'show'])->name('maintenance.tickets.show');
    Route::get('/maintenance/tickets/{ticket}/edit', [MaintenanceTicketController::class, 'edit'])->name('maintenance.tickets.edit');
    Route::put('/maintenance/tickets/{ticket}', [MaintenanceTicketController::class, 'update'])->name('maintenance.tickets.update');
    Route::delete('/maintenance/tickets/{ticket}', [MaintenanceTicketController::class, 'destroy'])->name('maintenance.tickets.destroy');
    
    // Ticket Actions
    Route::post('/maintenance/tickets/{ticket}/assign', [MaintenanceTicketController::class, 'assign'])->name('maintenance.tickets.assign');
    Route::post('/maintenance/tickets/{ticket}/start', [MaintenanceTicketController::class, 'start'])->name('maintenance.tickets.start');
    Route::post('/maintenance/tickets/{ticket}/close', [MaintenanceTicketController::class, 'close'])->name('maintenance.tickets.close');
    Route::post('/maintenance/tickets/{ticket}/reopen', [MaintenanceTicketController::class, 'reopen'])->name('maintenance.tickets.reopen');
    
    // Service Providers
    Route::get('/maintenance/providers', [ServiceProviderController::class, 'index'])->name('maintenance.providers.index');
    Route::get('/maintenance/providers/create', [ServiceProviderController::class, 'create'])->name('maintenance.providers.create');
    Route::post('/maintenance/providers', [ServiceProviderController::class, 'store'])->name('maintenance.providers.store');
    Route::get('/maintenance/providers/{provider}', [ServiceProviderController::class, 'show'])->name('maintenance.providers.show');
    Route::get('/maintenance/providers/{provider}/edit', [ServiceProviderController::class, 'edit'])->name('maintenance.providers.edit');
    Route::put('/maintenance/providers/{provider}', [ServiceProviderController::class, 'update'])->name('maintenance.providers.update');
    Route::delete('/maintenance/providers/{provider}', [ServiceProviderController::class, 'destroy'])->name('maintenance.providers.destroy');
    
    // Provider Actions
    Route::post('/maintenance/providers/{provider}/toggle-status', [ServiceProviderController::class, 'toggleStatus'])->name('maintenance.providers.toggle-status');
    Route::post('/maintenance/providers/{provider}/update-rating', [ServiceProviderController::class, 'updateRating'])->name('maintenance.providers.update-rating');
    Route::get('/maintenance/providers/{provider}/performance', [ServiceProviderController::class, 'performance'])->name('maintenance.providers.performance');
    Route::get('/maintenance/providers/export', [ServiceProviderController::class, 'export'])->name('maintenance.providers.export');
    
    // Maintenance Teams
    Route::get('/maintenance/teams', [MaintenanceTeamController::class, 'index'])->name('maintenance.teams.index');
    Route::get('/maintenance/teams/create', [MaintenanceTeamController::class, 'create'])->name('maintenance.teams.create');
    Route::post('/maintenance/teams', [MaintenanceTeamController::class, 'store'])->name('maintenance.teams.store');
    Route::get('/maintenance/teams/{team}', [MaintenanceTeamController::class, 'show'])->name('maintenance.teams.show');
    Route::get('/maintenance/teams/{team}/edit', [MaintenanceTeamController::class, 'edit'])->name('maintenance.teams.edit');
    Route::put('/maintenance/teams/{team}', [MaintenanceTeamController::class, 'update'])->name('maintenance.teams.update');
    Route::delete('/maintenance/teams/{team}', [MaintenanceTeamController::class, 'destroy'])->name('maintenance.teams.destroy');
    
    // Team Actions
    Route::post('/maintenance/teams/{team}/toggle-status', [MaintenanceTeamController::class, 'toggleStatus'])->name('maintenance.teams.toggle-status');
    Route::post('/maintenance/teams/{team}/add-member', [MaintenanceTeamController::class, 'addMember'])->name('maintenance.teams.add-member');
    Route::post('/maintenance/teams/{team}/remove-member', [MaintenanceTeamController::class, 'removeMember'])->name('maintenance.teams.remove-member');
    Route::post('/maintenance/teams/{team}/update-member-role', [MaintenanceTeamController::class, 'updateMemberRole'])->name('maintenance.teams.update-member-role');
    Route::post('/maintenance/teams/{team}/check-availability', [MaintenanceTeamController::class, 'checkAvailability'])->name('maintenance.teams.check-availability');
    Route::get('/maintenance/teams/{team}/performance', [MaintenanceTeamController::class, 'performance'])->name('maintenance.teams.performance');
    Route::get('/maintenance/teams/export', [MaintenanceTeamController::class, 'export'])->name('maintenance.teams.export');
    
    // Inventory Management
    Route::get('/maintenance/inventory', [InventoryController::class, 'index'])->name('maintenance.inventory.index');
    Route::get('/maintenance/inventory/create', [InventoryController::class, 'create'])->name('maintenance.inventory.create');
    Route::post('/maintenance/inventory', [InventoryController::class, 'store'])->name('maintenance.inventory.store');
    Route::get('/maintenance/inventory/{item}', [InventoryController::class, 'show'])->name('maintenance.inventory.show');
    Route::get('/maintenance/inventory/{item}/edit', [InventoryController::class, 'edit'])->name('maintenance.inventory.edit');
    Route::put('/maintenance/inventory/{item}', [InventoryController::class, 'update'])->name('maintenance.inventory.update');
    Route::delete('/maintenance/inventory/{item}', [InventoryController::class, 'destroy'])->name('maintenance.inventory.destroy');
    
    // Inventory Actions
    Route::post('/maintenance/inventory/{item}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('maintenance.inventory.adjust-stock');
    Route::post('/maintenance/inventory/{item}/reserve', [InventoryController::class, 'reserve'])->name('maintenance.inventory.reserve');
    Route::post('/maintenance/inventory/{item}/release', [InventoryController::class, 'release'])->name('maintenance.inventory.release');
    Route::get('/maintenance/inventory/{item}/history', [InventoryController::class, 'history'])->name('maintenance.inventory.history');
    Route::get('/maintenance/inventory/reports/low-stock', [InventoryController::class, 'lowStockReport'])->name('maintenance.inventory.reports.low-stock');
    Route::get('/maintenance/inventory/export', [InventoryController::class, 'export'])->name('maintenance.inventory.export');
    
    // Work Orders
    Route::get('/maintenance/work-orders', [WorkOrderController::class, 'index'])->name('maintenance.work-orders.index');
    Route::get('/maintenance/workorders', [WorkOrderController::class, 'index'])->name('maintenance.workorders.index');
    Route::get('/maintenance/work-orders/create', [WorkOrderController::class, 'create'])->name('maintenance.work-orders.create');
    Route::post('/maintenance/work-orders', [WorkOrderController::class, 'store'])->name('maintenance.work-orders.store');
    Route::get('/maintenance/work-orders/{workOrder}', [WorkOrderController::class, 'show'])->name('maintenance.work-orders.show');
    Route::get('/maintenance/work-orders/{workOrder}/edit', [WorkOrderController::class, 'edit'])->name('maintenance.work-orders.edit');
    Route::put('/maintenance/work-orders/{workOrder}', [WorkOrderController::class, 'update'])->name('maintenance.work-orders.update');
    Route::delete('/maintenance/work-orders/{workOrder}', [WorkOrderController::class, 'destroy'])->name('maintenance.work-orders.destroy');
    
    // Work Order Actions
    Route::post('/maintenance/work-orders/{workOrder}/start', [WorkOrderController::class, 'start'])->name('maintenance.work-orders.start');
    Route::post('/maintenance/work-orders/{workOrder}/pause', [WorkOrderController::class, 'pause'])->name('maintenance.work-orders.pause');
    Route::post('/maintenance/work-orders/{workOrder}/resume', [WorkOrderController::class, 'resume'])->name('maintenance.work-orders.resume');
    Route::post('/maintenance/work-orders/{workOrder}/complete', [WorkOrderController::class, 'complete'])->name('maintenance.work-orders.complete');
    Route::post('/maintenance/work-orders/{workOrder}/cancel', [WorkOrderController::class, 'cancel'])->name('maintenance.work-orders.cancel');
    Route::post('/maintenance/work-orders/{workOrder}/add-time-log', [WorkOrderController::class, 'addTimeLog'])->name('maintenance.work-orders.add-time-log');
    Route::post('/maintenance/work-orders/{workOrder}/add-item', [WorkOrderController::class, 'addItem'])->name('maintenance.work-orders.add-item');
    Route::post('/maintenance/work-orders/{workOrder}/remove-item', [WorkOrderController::class, 'removeItem'])->name('maintenance.work-orders.remove-item');
    Route::get('/maintenance/work-orders/{workOrder}/download', [WorkOrderController::class, 'download'])->name('maintenance.work-orders.download');
    Route::get('/maintenance/work-orders/export', [WorkOrderController::class, 'export'])->name('maintenance.work-orders.export');
    
    // Preventive Maintenance
    Route::get('/maintenance/preventive', [PreventiveMaintenanceController::class, 'index'])->name('maintenance.preventive.index');
    Route::get('/maintenance/preventive/create', [PreventiveMaintenanceController::class, 'create'])->name('maintenance.preventive.create');
    Route::post('/maintenance/preventive', [PreventiveMaintenanceController::class, 'store'])->name('maintenance.preventive.store');
    Route::get('/maintenance/preventive/{plan}', [PreventiveMaintenanceController::class, 'show'])->name('maintenance.preventive.show');
    Route::get('/maintenance/preventive/{plan}/edit', [PreventiveMaintenanceController::class, 'edit'])->name('maintenance.preventive.edit');
    Route::put('/maintenance/preventive/{plan}', [PreventiveMaintenanceController::class, 'update'])->name('maintenance.preventive.update');
    Route::delete('/maintenance/preventive/{plan}', [PreventiveMaintenanceController::class, 'destroy'])->name('maintenance.preventive.destroy');
    
    // Preventive Maintenance Actions
    Route::post('/maintenance/preventive/{plan}/activate', [PreventiveMaintenanceController::class, 'activate'])->name('maintenance.preventive.activate');
    Route::post('/maintenance/preventive/{plan}/deactivate', [PreventiveMaintenanceController::class, 'deactivate'])->name('maintenance.preventive.deactivate');
    Route::post('/maintenance/preventive/{plan}/complete', [PreventiveMaintenanceController::class, 'complete'])->name('maintenance.preventive.complete');
    Route::post('/maintenance/preventive/{plan}/generate-schedules', [PreventiveMaintenanceController::class, 'generateSchedules'])->name('maintenance.preventive.generate-schedules');
    Route::get('/maintenance/preventive/{plan}/calendar', [PreventiveMaintenanceController::class, 'calendar'])->name('maintenance.preventive.calendar');
    Route::get('/maintenance/preventive/reports', [PreventiveMaintenanceController::class, 'reports'])->name('maintenance.preventive.reports');
    Route::get('/maintenance/preventive/export', [PreventiveMaintenanceController::class, 'export'])->name('maintenance.preventive.export');
    
    // Emergency Repairs
    Route::get('/maintenance/emergency', [EmergencyRepairController::class, 'index'])->name('maintenance.emergency.index');
    Route::get('/maintenance/emergency/create', [EmergencyRepairController::class, 'create'])->name('maintenance.emergency.create');
    Route::post('/maintenance/emergency', [EmergencyRepairController::class, 'store'])->name('maintenance.emergency.store');
    Route::get('/maintenance/emergency/{repair}', [EmergencyRepairController::class, 'show'])->name('maintenance.emergency.show');
    Route::get('/maintenance/emergency/{repair}/edit', [EmergencyRepairController::class, 'edit'])->name('maintenance.emergency.edit');
    Route::put('/maintenance/emergency/{repair}', [EmergencyRepairController::class, 'update'])->name('maintenance.emergency.update');
    Route::delete('/maintenance/emergency/{repair}', [EmergencyRepairController::class, 'destroy'])->name('maintenance.emergency.destroy');
    
    // Emergency Repair Actions
    Route::post('/maintenance/emergency/{repair}/assign', [EmergencyRepairController::class, 'assign'])->name('maintenance.emergency.assign');
    Route::post('/maintenance/emergency/{repair}/start', [EmergencyRepairController::class, 'start'])->name('maintenance.emergency.start');
    Route::post('/maintenance/emergency/{repair}/pause', [EmergencyRepairController::class, 'pause'])->name('maintenance.emergency.pause');
    Route::post('/maintenance/emergency/{repair}/resume', [EmergencyRepairController::class, 'resume'])->name('maintenance.emergency.resume');
    Route::post('/maintenance/emergency/{repair}/complete', [EmergencyRepairController::class, 'complete'])->name('maintenance.emergency.complete');
    Route::post('/maintenance/emergency/{repair}/add-time-log', [EmergencyRepairController::class, 'addTimeLog'])->name('maintenance.emergency.add-time-log');
    Route::get('/maintenance/emergency/{repair}/report', [EmergencyRepairController::class, 'report'])->name('maintenance.emergency.report');
    Route::get('/maintenance/emergency/dashboard', [EmergencyRepairController::class, 'dashboard'])->name('maintenance.emergency.dashboard');
    Route::get('/maintenance/emergency/export', [EmergencyRepairController::class, 'export'])->name('maintenance.emergency.export');
    
    // Maintenance Invoices
    Route::get('/maintenance/invoices', [MaintenanceInvoiceController::class, 'index'])->name('maintenance.invoices.index');
    Route::get('/maintenance/invoices/create', [MaintenanceInvoiceController::class, 'create'])->name('maintenance.invoices.create');
    Route::post('/maintenance/invoices', [MaintenanceInvoiceController::class, 'store'])->name('maintenance.invoices.store');
    Route::get('/maintenance/invoices/{invoice}', [MaintenanceInvoiceController::class, 'show'])->name('maintenance.invoices.show');
    Route::get('/maintenance/invoices/{invoice}/edit', [MaintenanceInvoiceController::class, 'edit'])->name('maintenance.invoices.edit');
    Route::put('/maintenance/invoices/{invoice}', [MaintenanceInvoiceController::class, 'update'])->name('maintenance.invoices.update');
    Route::delete('/maintenance/invoices/{invoice}', [MaintenanceInvoiceController::class, 'destroy'])->name('maintenance.invoices.destroy');
    
    // Invoice Actions
    Route::post('/maintenance/invoices/{invoice}/send', [MaintenanceInvoiceController::class, 'send'])->name('maintenance.invoices.send');
    Route::post('/maintenance/invoices/{invoice}/mark-paid', [MaintenanceInvoiceController::class, 'markPaid'])->name('maintenance.invoices.mark-paid');
    Route::post('/maintenance/invoices/{invoice}/mark-overdue', [MaintenanceInvoiceController::class, 'markOverdue'])->name('maintenance.invoices.mark-overdue');
    Route::post('/maintenance/invoices/{invoice}/cancel', [MaintenanceInvoiceController::class, 'cancel'])->name('maintenance.invoices.cancel');
    Route::post('/maintenance/invoices/{invoice}/duplicate', [MaintenanceInvoiceController::class, 'duplicate'])->name('maintenance.invoices.duplicate');
    Route::get('/maintenance/invoices/{invoice}/download', [MaintenanceInvoiceController::class, 'download'])->name('maintenance.invoices.download');
    Route::get('/maintenance/invoices/reports', [MaintenanceInvoiceController::class, 'reports'])->name('maintenance.invoices.reports');
    Route::get('/maintenance/invoices/export', [MaintenanceInvoiceController::class, 'export'])->name('maintenance.invoices.export');
    
    // Warranties
    Route::get('/maintenance/warranties', [WarrantyController::class, 'index'])->name('maintenance.warranties.index');
    Route::get('/maintenance/warranties/create', [WarrantyController::class, 'create'])->name('maintenance.warranties.create');
    Route::post('/maintenance/warranties', [WarrantyController::class, 'store'])->name('maintenance.warranties.store');
    Route::get('/maintenance/warranties/{warranty}', [WarrantyController::class, 'show'])->name('maintenance.warranties.show');
    Route::get('/maintenance/warranties/{warranty}/edit', [WarrantyController::class, 'edit'])->name('maintenance.warranties.edit');
    Route::put('/maintenance/warranties/{warranty}', [WarrantyController::class, 'update'])->name('maintenance.warranties.update');
    Route::delete('/maintenance/warranties/{warranty}', [WarrantyController::class, 'destroy'])->name('maintenance.warranties.destroy');
    
    // Warranty Actions
    Route::post('/maintenance/warranties/{warranty}/extend', [WarrantyController::class, 'extend'])->name('maintenance.warranties.extend');
    Route::post('/maintenance/warranties/{warranty}/suspend', [WarrantyController::class, 'suspend'])->name('maintenance.warranties.suspend');
    Route::post('/maintenance/warranties/{warranty}/reactivate', [WarrantyController::class, 'reactivate'])->name('maintenance.warranties.reactivate');
    Route::post('/maintenance/warranties/{warranty}/expire', [WarrantyController::class, 'expire'])->name('maintenance.warranties.expire');
    Route::post('/maintenance/warranties/{warranty}/create-claim', [WarrantyController::class, 'createClaim'])->name('maintenance.warranties.create-claim');
    Route::get('/maintenance/warranties/{warranty}/claims', [WarrantyController::class, 'claims'])->name('maintenance.warranties.claims');
    Route::get('/maintenance/warranties/{warranty}/history', [WarrantyController::class, 'history'])->name('maintenance.warranties.history');
    Route::get('/maintenance/warranties/reports', [WarrantyController::class, 'reports'])->name('maintenance.warranties.reports');
    Route::get('/maintenance/warranties/export', [WarrantyController::class, 'export'])->name('maintenance.warranties.export');
    
    // Maintenance Reports and Analytics
    Route::get('/maintenance/reports', [MaintenanceController::class, 'reports'])->name('maintenance.reports');
    Route::get('/maintenance/analytics', [MaintenanceController::class, 'analytics'])->name('maintenance.analytics');
    Route::get('/maintenance/performance', [MaintenanceController::class, 'performance'])->name('maintenance.performance');
    
    // Maintenance Search and API
    Route::get('/maintenance/search', [MaintenanceController::class, 'search'])->name('maintenance.search');
    Route::get('/maintenance/api/dashboard-stats', [MaintenanceController::class, 'dashboardStats'])->name('maintenance.api.dashboard-stats');
    Route::get('/maintenance/api/calendar-events', [MaintenanceController::class, 'calendarEvents'])->name('maintenance.api.calendar-events');
    
    // Maintenance Settings
    Route::get('/maintenance/settings', [MaintenanceController::class, 'settings'])->name('maintenance.settings');
    Route::post('/maintenance/settings', [MaintenanceController::class, 'saveSettings'])->name('maintenance.settings.save');
});
