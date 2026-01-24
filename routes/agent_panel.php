<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\Agent\AgentPropertyController;
use App\Http\Controllers\Agent\CrmController;
use App\Http\Controllers\Agent\AppointmentController;
use App\Http\Controllers\Agent\AgentOfferController;

/*
|--------------------------------------------------------------------------
| Agent Panel Routes
|--------------------------------------------------------------------------
|
| Routes for agent panel functionality including dashboard, properties, CRM, appointments, and offers
|
*/

Route::middleware(['auth', 'verified'])->prefix('agent')->name('agent.')->group(function () {
    
    // Agent Dashboard
    Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
    
    // Properties
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/', [AgentPropertyController::class, 'index'])->name('index');
        Route::get('/create', [AgentPropertyController::class, 'create'])->name('create');
        Route::post('/', [AgentPropertyController::class, 'store'])->name('store');
        Route::get('/{property}', [AgentPropertyController::class, 'show'])->name('show');
        Route::get('/{property}/edit', [AgentPropertyController::class, 'edit'])->name('edit');
        Route::put('/{property}', [AgentPropertyController::class, 'update'])->name('update');
        Route::delete('/{property}', [AgentPropertyController::class, 'destroy'])->name('destroy');
        Route::get('/{property}/add-photos', [AgentPropertyController::class, 'addPhotos'])->name('add-photos');
        Route::post('/{property}/upload-photos', [AgentPropertyController::class, 'uploadPhotos'])->name('upload-photos');
        Route::delete('/photos/{media}/delete', [AgentPropertyController::class, 'deletePhoto'])->name('delete-photo');
        Route::get('/featured', [AgentPropertyController::class, 'featured'])->name('featured');
        Route::post('/{property}/toggle-featured', [AgentPropertyController::class, 'toggleFeatured'])->name('toggle-featured');
    });
    
    // CRM
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::get('/', [CrmController::class, 'index'])->name('index');
        Route::get('/create', [CrmController::class, 'create'])->name('create');
        Route::post('/', [CrmController::class, 'store'])->name('store');
        Route::get('/{client}', [CrmController::class, 'show'])->name('show');
        Route::get('/{client}/edit', [CrmController::class, 'edit'])->name('edit');
        Route::put('/{client}', [CrmController::class, 'update'])->name('update');
        Route::delete('/{client}', [CrmController::class, 'destroy'])->name('destroy');
        Route::get('/leads', [CrmController::class, 'leads'])->name('leads');
        Route::get('/leads/{lead}', [CrmController::class, 'showLead'])->name('showLead');
        Route::post('/leads/{lead}/assign', [CrmController::class, 'assignLead'])->name('assignLead');
        Route::post('/leads/{lead}/convert', [CrmController::class, 'convertLead'])->name('convertLead');
    });
    
    // Appointments
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [AppointmentController::class, 'index'])->name('index');
        Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');
        Route::get('/create', [AppointmentController::class, 'create'])->name('create');
        Route::post('/', [AppointmentController::class, 'store'])->name('store');
        Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
        Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('edit');
        Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('update');
        Route::delete('/{appointment}', [AppointmentController::class, 'destroy'])->name('destroy');
        Route::post('/{appointment}/complete', [AppointmentController::class, 'complete'])->name('complete');
        Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
        Route::post('/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('reschedule');
    });
    
    // Offers
    Route::prefix('offers')->name('offers.')->group(function () {
        Route::get('/', [AgentOfferController::class, 'index'])->name('index');
        Route::get('/received', [AgentOfferController::class, 'received'])->name('received');
        Route::get('/sent', [AgentOfferController::class, 'sent'])->name('sent');
        Route::get('/create', [AgentOfferController::class, 'create'])->name('create');
        Route::post('/', [AgentOfferController::class, 'store'])->name('store');
        Route::get('/{offer}', [AgentOfferController::class, 'show'])->name('show');
        Route::get('/{offer}/edit', [AgentOfferController::class, 'edit'])->name('edit');
        Route::put('/{offer}', [AgentOfferController::class, 'update'])->name('update');
        Route::delete('/{offer}', [AgentOfferController::class, 'destroy'])->name('destroy');
        Route::post('/{offer}/accept', [AgentOfferController::class, 'accept'])->name('accept');
        Route::post('/{offer}/reject', [AgentOfferController::class, 'reject'])->name('reject');
        Route::post('/{offer}/counter', [AgentOfferController::class, 'counter'])->name('counter');
    });
});
