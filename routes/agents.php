<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\Agent\AgentCommissionController;
use App\Http\Controllers\Agent\AgentPerformanceController;
use App\Http\Controllers\AppointmentController;

/*
|--------------------------------------------------------------------------
| Agent Management Routes
|--------------------------------------------------------------------------
|
| Routes for agent management, performance, commissions, and appointments
|
*/

Route::get('/agents/directory', [AgentController::class, 'directory'])->name('agents.directory');

Route::middleware(['auth', 'verified'])->prefix('agents')->group(function () {
    
    // Agent Dashboard
    Route::get('/', [AgentController::class, 'dashboard'])->name('agents.dashboard');
    Route::get('/profile', [AgentController::class, 'profile'])->name('agents.profile');
    Route::get('/performance', [AgentController::class, 'performance'])->name('agents.performance');
    Route::get('/ranking', [AgentController::class, 'ranking'])->name('agents.ranking');
    Route::get('/goals', [AgentController::class, 'goals'])->name('agents.goals');
    
    // Agent Management
    Route::get('/', [AgentController::class, 'index'])->name('agents.index');
    Route::get('/create', [AgentController::class, 'create'])->name('agents.create');
    Route::post('/', [AgentController::class, 'store'])->name('agents.store');
    Route::get('/{agent}', [AgentController::class, 'show'])->name('agents.show');
    Route::get('/{agent}/edit', [AgentController::class, 'edit'])->name('agents.edit');
    Route::put('/{agent}', [AgentController::class, 'update'])->name('agents.update');
    Route::delete('/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');
    
    // Agent Leads
    Route::prefix('/{agent}/leads')->group(function () {
        Route::get('/', [AgentController::class, 'leads'])->name('agents.leads.index');
        Route::get('/{lead}', [AgentController::class, 'showLead'])->name('agents.leads.show');
        Route::post('/{lead}/assign', [AgentController::class, 'assignLead'])->name('agents.leads.assign');
        Route::post('/{lead}/convert', [AgentController::class, 'convertLead'])->name('agents.leads.convert');
        Route::post('/{lead}/schedule-appointment', [AgentController::class, 'scheduleAppointment'])->name('agents.leads.schedule-appointment');
    });
    
    // Agent Appointments
    Route::prefix('/{agent}/appointments')->group(function () {
        Route::get('/', [AppointmentController::class, 'index'])->name('agents.appointments.index');
        Route::get('/create', [AppointmentController::class, 'create'])->name('agents.appointments.create');
        Route::post('/', [AppointmentController::class, 'store'])->name('agents.appointments.store');
        Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('agents.appointments.show');
        Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('agents.appointments.edit');
        Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('agents.appointments.update');
        Route::delete('/{appointment}', [AppointmentController::class, 'destroy'])->name('agents.appointments.destroy');
        Route::post('/{appointment}/complete', [AppointmentController::class, 'complete'])->name('agents.appointments.complete');
        Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('agents.appointments.cancel');
        Route::post('/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('agents.appointments.reschedule');
    });
    
    // Agent Commissions
    Route::prefix('/{agent}/commissions')->group(function () {
        Route::get('/', [AgentCommissionController::class, 'index'])->name('agents.commissions.index');
        Route::get('/{commission}', [AgentCommissionController::class, 'show'])->name('agents.commissions.show');
        Route::get('/history', [AgentCommissionController::class, 'history'])->name('agents.commissions.history');
        Route::get('/summary', [AgentCommissionController::class, 'summary'])->name('agents.commissions.summary');
        Route::get('/calculate', [AgentCommissionController::class, 'calculate'])->name('agents.commissions.calculate');
        Route::post('/{commission}/pay', [AgentCommissionController::class, 'pay'])->name('agents.commissions.pay');
    });
    
    // Agent Performance
    Route::prefix('/{agent}/performance')->group(function () {
        Route::get('/', [AgentPerformanceController::class, 'index'])->name('agents.performance.index');
        Route::get('/metrics', [AgentPerformanceController::class, 'metrics'])->name('agents.performance.metrics');
        Route::get('/monthly', [AgentPerformanceController::class, 'monthly'])->name('agents.performance.monthly');
        Route::get('/ranking', [AgentPerformanceController::class, 'ranking'])->name('agents.performance.ranking');
        Route::get('/goals', [AgentPerformanceController::class, 'goals'])->name('agents.performance.goals');
        Route::get('/reports', [AgentPerformanceController::class, 'reports'])->name('agents.performance.reports');
    });
    
    // Agent Settings
    Route::prefix('/{agent}/settings')->group(function () {
        Route::get('/profile', [AgentController::class, 'settings'])->name('agents.settings.profile');
        Route::get('/notifications', [AgentController::class, 'settings'])->name('agents.settings.notifications');
        Route::get('/privacy', [AgentController::class, 'settings'])->name('agents.settings.privacy');
        Route::get('/commission', [AgentController::class, 'settings'])->name('agents.settings.commission');
        Route::put('/{setting}', [AgentController::class, 'updateSetting'])->name('agents.settings.update');
    });
    
    // Agent Reports
    Route::prefix('/{agent}/reports')->group(function () {
        Route::get('/', [AgentController::class, 'reports'])->name('agents.reports.index');
        Route::post('/generate', [AgentController::class, 'generateReport'])->name('agents.reports.generate');
        Route::get('/{report}', [AgentController::class, 'showReport'])->name('agents.reports.show');
        Route::get('/{report}/download', [AgentController::class, 'downloadReport'])->name('agents.reports.download');
    });
});

// API Routes for Agent Management
Route::prefix('api/agents')->middleware(['auth:api', 'throttle:60,1'])->group(function () {
    
    // Agent API
    Route::get('/', [AgentController::class, 'apiIndex'])->name('api.agents.index');
    Route::post('/', [AgentController::class, 'apiStore'])->name('api.agents.store');
    Route::get('/{agent}', [AgentController::class, 'apiShow'])->name('api.agents.show');
    Route::put('/{agent}', [AgentController::class, 'apiUpdate'])->name('api.agents.update');
    Route::delete('/{agent}', [AgentController::class, 'apiDestroy'])->name('api.agents.destroy');
    
    // Agent Performance API
    Route::prefix('/{agent}/performance')->group(function () {
        Route::get('/metrics', [AgentPerformanceController::class, 'apiMetrics'])->name('api.agents.performance.metrics');
        Route::get('/monthly', [AgentPerformanceController::class, 'apiMonthly'])->name('api.agents.performance.monthly');
        Route::get('/ranking', [AgentPerformanceController::class, 'apiRanking'])->name('api.agents.performance.ranking');
    });
    
    // Agent Commissions API
    Route::prefix('/{agent}/commissions')->group(function () {
        Route::get('/', [AgentCommissionController::class, 'apiIndex'])->name('api.agents.commissions.index');
        Route::get('/{commission}', [AgentCommissionController::class, 'apiShow'])->name('api.agents.commissions.show');
        Route::get('/history', [AgentCommissionController::class, 'apiHistory'])->name('api.agents.commissions.history');
        Route::get('/summary', [AgentCommissionController::class, 'apiSummary'])->name('api.agents.commissions.summary');
        Route::post('/calculate', [AgentCommissionController::class, 'apiCalculate'])->name('api.agents.commissions.calculate');
        Route::post('/{commission}/pay', [AgentCommissionController::class, 'apiPay'])->name('api.agents.commissions.pay');
    });
});
