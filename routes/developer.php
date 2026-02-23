<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Developer\DeveloperController;
use App\Http\Controllers\Developer\DeveloperPermitController;

Route::middleware(['auth'])->prefix('developer')->name('developer.')->group(function () {
    Route::get('/', [DeveloperController::class, 'index'])->name('index');
    Route::get('/bim-models', [DeveloperController::class, 'bimModels'])->name('bim.models');
    Route::post('/bim-models/upload', [DeveloperController::class, 'uploadBimModel'])->name('bim.models.upload');
    Route::get('/bim-models/export', [DeveloperController::class, 'exportBimModels'])->name('bim.models.export');
    Route::get('/construction', [DeveloperController::class, 'construction'])->name('construction');
});
