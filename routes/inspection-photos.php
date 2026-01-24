<?php

use App\Http\Controllers\InspectionPhotoController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Inspection Photos Routes
    Route::prefix('inspection-photos')->name('inspection-photos.')->group(function () {
        Route::get('/', [InspectionPhotoController::class, 'index'])->name('index');
        Route::get('/create', [InspectionPhotoController::class, 'create'])->name('create');
        Route::post('/', [InspectionPhotoController::class, 'store'])->name('store');
        Route::get('/{photo}', [InspectionPhotoController::class, 'show'])->name('show');
        Route::get('/{photo}/edit', [InspectionPhotoController::class, 'edit'])->name('edit');
        Route::put('/{photo}', [InspectionPhotoController::class, 'update'])->name('update');
        Route::delete('/{photo}', [InspectionPhotoController::class, 'destroy'])->name('destroy');
        
        // Photo Actions
        Route::post('/{photo}/set-primary', [InspectionPhotoController::class, 'setPrimary'])->name('set-primary');
        Route::post('/{photo}/toggle-public', [InspectionPhotoController::class, 'togglePublic'])->name('toggle-public');
        Route::get('/{photo}/download', [InspectionPhotoController::class, 'download'])->name('download');
        Route::post('/{photo}/add-tag', [InspectionPhotoController::class, 'addTag'])->name('add-tag');
        Route::delete('/{photo}/remove-tag/{tag}', [InspectionPhotoController::class, 'removeTag'])->name('remove-tag');
        
        // Bulk Actions
        Route::post('/bulk-upload', [InspectionPhotoController::class, 'bulkUpload'])->name('bulk-upload');
        Route::post('/bulk-delete', [InspectionPhotoController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-update-tags', [InspectionPhotoController::class, 'bulkUpdateTags'])->name('bulk-update-tags');
        
        // Gallery Views
        Route::get('/inspection/{inspection}', [InspectionPhotoController::class, 'inspectionGallery'])->name('inspection-gallery');
        Route::get('/property/{property}', [InspectionPhotoController::class, 'propertyGallery'])->name('property-gallery');
        
        // Export
        Route::get('/export', [InspectionPhotoController::class, 'export'])->name('export');
    });
});
