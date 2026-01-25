<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;

/*
|--------------------------------------------------------------------------
| Company Routes
|--------------------------------------------------------------------------
|
| Routes for company management, directory, and profile
|
*/

Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/{company}', [CompanyController::class, 'show'])->name('show');
    
    // Protected company routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/create', [CompanyController::class, 'create'])->name('create');
        Route::post('/', [CompanyController::class, 'store'])->name('store');
        Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
        Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
        Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
        
        // Company members
        Route::get('/{company}/members', [CompanyController::class, 'members'])->name('members');
        Route::post('/{company}/members', [CompanyController::class, 'addMember'])->name('members.add');
        Route::delete('/{company}/members/{user}', [CompanyController::class, 'removeMember'])->name('members.remove');
    });
});
