<?php

use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertySearchController;
use App\Http\Controllers\PropertyFilterController;
use App\Http\Controllers\PropertyComparisonController;
use App\Http\Controllers\PropertyFavoriteController;
use App\Http\Controllers\PropertyViewController;
use App\Http\Controllers\PropertyShareController;
use App\Http\Controllers\PropertyMediaController;
use App\Http\Controllers\PropertyDocumentController;
use App\Http\Controllers\PropertyPriceController;
use App\Http\Controllers\PropertyLocationController;
use App\Http\Controllers\PropertyAmenityController;
use App\Http\Controllers\PropertyFeatureController;
use App\Http\Controllers\PropertyTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Property Routes
|--------------------------------------------------------------------------
|
| These routes are for the property management system including
| property listings, search, filtering, comparison, favorites, etc.
|
*/

// Property Type Routes
Route::prefix('property-types')->name('property-types.')->group(function () {
    Route::get('/', [PropertyTypeController::class, 'index'])->name('index');
    Route::get('/create', [PropertyTypeController::class, 'create'])->name('create');
    Route::get('/search', [PropertyTypeController::class, 'search'])->name('search');
    Route::post('/', [PropertyTypeController::class, 'store'])->name('store');
    Route::post('/reorder', [PropertyTypeController::class, 'reorder'])->name('reorder');
    Route::get('/{propertyType}', [PropertyTypeController::class, 'show'])->name('show');
    Route::get('/{propertyType}/edit', [PropertyTypeController::class, 'edit'])->name('edit');
    Route::get('/{propertyType}/properties', [PropertyTypeController::class, 'getProperties'])->name('properties');
    Route::get('/{propertyType}/stats', [PropertyTypeController::class, 'getStats'])->name('stats');
    Route::put('/{propertyType}', [PropertyTypeController::class, 'update'])->name('update');
    Route::delete('/{propertyType}', [PropertyTypeController::class, 'destroy'])->name('destroy');
    Route::post('/{propertyType}/toggle', [PropertyTypeController::class, 'toggleStatus'])->name('toggle');
    Route::get('/{propertyType}/export', [PropertyTypeController::class, 'export'])->name('export');
});

// Main Property Routes
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/recommendations', [PropertyController::class, 'recommendations'])->name('properties.recommendations');
Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create')->middleware('auth');
Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store')->middleware('auth');

// Property Search Routes
Route::prefix('properties/search')->name('properties.search.')->group(function () {
    Route::get('/', [PropertySearchController::class, 'search'])->name('index');
    Route::get('/web', [PropertySearchController::class, 'webSearch'])->name('web');
    Route::get('/api', [PropertySearchController::class, 'apiSearch'])->name('api');
    Route::get('/autocomplete', [PropertySearchController::class, 'autocomplete'])->name('autocomplete');
    Route::get('/map', [PropertySearchController::class, 'mapSearch'])->name('map');
    Route::post('/advanced', [PropertySearchController::class, 'advancedSearch'])->name('advanced');
    Route::get('/suggestions', [PropertySearchController::class, 'getSuggestions'])->name('suggestions');
    Route::get('/popular', [PropertySearchController::class, 'getPopularSearches'])->name('popular');
    Route::post('/save', [PropertySearchController::class, 'saveSearch'])->name('save');
    Route::get('/saved', [PropertySearchController::class, 'getSavedSearches'])->name('saved');
});

// Property Filter Routes
Route::get('/properties/filter', [PropertyFilterController::class, 'filter'])->name('properties.filter');
Route::prefix('properties/filter')->name('properties.filter.')->group(function () {
    Route::get('/options', [PropertyFilterController::class, 'getFilterOptions'])->name('options');
    Route::post('/apply', [PropertyFilterController::class, 'applyFilters'])->name('apply');
    Route::get('/quick', [PropertyFilterController::class, 'quickFilters'])->name('quick');
    Route::get('/by-type/{type}', [PropertyFilterController::class, 'filterByType'])->name('by-type');
    Route::get('/by-location/{location}', [PropertyFilterController::class, 'filterByLocation'])->name('by-location');
    Route::get('/by-price/{min}/{max}', [PropertyFilterController::class, 'filterByPrice'])->name('by-price');
    Route::post('/save', [PropertyFilterController::class, 'saveFilter'])->name('save');
    Route::get('/saved', [PropertyFilterController::class, 'getSavedFilters'])->name('saved');
    Route::delete('/saved/{filter}', [PropertyFilterController::class, 'deleteSavedFilter'])->name('saved.delete');
});

// Property Favorite Routes
Route::get('/properties/favorites', [PropertyFavoriteController::class, 'index'])->name('properties.favorites');
Route::prefix('properties/favorites')->name('properties.favorites.')->group(function () {
    Route::get('/stats', [PropertyFavoriteController::class, 'getFavoritesStats'])->name('stats');
    Route::post('/bulk', [PropertyFavoriteController::class, 'bulkAction'])->name('bulk');
    Route::get('/share', [PropertyFavoriteController::class, 'shareFavorites'])->name('share');
    Route::get('/alerts', [PropertyFavoriteController::class, 'getAlerts'])->name('alerts');
    Route::post('/alerts', [PropertyFavoriteController::class, 'createAlert'])->name('alerts.create');
    Route::get('/similar/{property}', [PropertyFavoriteController::class, 'getSimilarProperties'])->name('similar');
});

// Property Comparison Routes
Route::get('/properties/compare', [PropertyComparisonController::class, 'index'])->name('properties.compare');
Route::prefix('properties/compare')->name('properties.compare.')->group(function () {
    Route::post('/add', [PropertyComparisonController::class, 'add'])->name('add');
    Route::post('/remove', [PropertyComparisonController::class, 'remove'])->name('remove');
    Route::post('/clear', [PropertyComparisonController::class, 'clear'])->name('clear');
    Route::get('/data', [PropertyComparisonController::class, 'getData'])->name('data');
    Route::post('/export', [PropertyComparisonController::class, 'export'])->name('export');
    Route::get('/similar/{property}', [PropertyComparisonController::class, 'getSimilarProperties'])->name('similar');
});

// Property Wildcard Routes - MUST BE LAST
Route::prefix('properties/{property}')->group(function () {
    Route::get('/', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('/edit', [PropertyController::class, 'edit'])->name('properties.edit')->middleware('auth');
    Route::put('/', [PropertyController::class, 'update'])->name('properties.update')->middleware('auth');
    Route::delete('/', [PropertyController::class, 'destroy'])->name('properties.destroy')->middleware('auth');
    
    Route::post('/duplicate', [PropertyController::class, 'duplicate'])->name('properties.duplicate');
    Route::post('/publish', [PropertyController::class, 'publish'])->name('properties.publish');
    Route::post('/archive', [PropertyController::class, 'archive'])->name('properties.archive');
    Route::post('/view', [PropertyController::class, 'recordView'])->name('properties.view');

    // Favorite Actions
    Route::post('/favorite', [PropertyFavoriteController::class, 'add'])->name('properties.favorite.add');
    Route::delete('/favorite', [PropertyFavoriteController::class, 'remove'])->name('properties.favorite.remove');
    Route::post('/favorite/toggle', [PropertyFavoriteController::class, 'toggle'])->name('properties.favorite.toggle');
    Route::get('/favorite/status', [PropertyFavoriteController::class, 'getStatus'])->name('properties.favorite.status');

    // View Actions
    Route::get('/views', [PropertyViewController::class, 'index'])->name('properties.views');
    Route::post('/views/record', [PropertyViewController::class, 'record'])->name('properties.views.record');
    Route::get('/views/stats', [PropertyViewController::class, 'getStats'])->name('properties.views.stats');
    Route::get('/views/analytics', [PropertyViewController::class, 'getAnalytics'])->name('properties.views.analytics');
    Route::get('/views/engagement', [PropertyViewController::class, 'getEngagement'])->name('properties.views.engagement');
    Route::get('/views/heatmap', [PropertyViewController::class, 'getHeatmapData'])->name('properties.views.heatmap');
    Route::get('/views/performance', [PropertyViewController::class, 'getPerformance'])->name('properties.views.performance');
    Route::post('/views/export', [PropertyViewController::class, 'export'])->name('properties.views.export');
    Route::get('/views/recent', [PropertyViewController::class, 'getRecentViews'])->name('properties.views.recent');

    // Share Actions
    Route::post('/share', [PropertyShareController::class, 'share'])->name('properties.share');
    Route::get('/share/link', [PropertyShareController::class, 'getShareLink'])->name('properties.share.link');
    Route::get('/share/stats', [PropertyShareController::class, 'getShareStats'])->name('properties.share.stats');
    Route::get('/share/qrcode', [PropertyShareController::class, 'generateQRCode'])->name('properties.share.qrcode');

    // Media Actions
    Route::prefix('media')->name('properties.media.')->group(function () {
        Route::get('/', [PropertyMediaController::class, 'index'])->name('index');
        Route::post('/upload', [PropertyMediaController::class, 'upload'])->name('upload');
        Route::put('/{media}', [PropertyMediaController::class, 'update'])->name('update');
        Route::delete('/{media}', [PropertyMediaController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [PropertyMediaController::class, 'reorder'])->name('reorder');
        Route::post('/{media}/set-primary', [PropertyMediaController::class, 'setPrimary'])->name('set-primary');
        Route::post('/bulk-delete', [PropertyMediaController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/gallery', [PropertyMediaController::class, 'gallery'])->name('gallery');
        Route::get('/{media}/download', [PropertyMediaController::class, 'download'])->name('download');
        Route::get('/stats', [PropertyMediaController::class, 'getMediaStats'])->name('stats');
    });

    // Document Actions
    Route::prefix('documents')->name('properties.documents.')->group(function () {
        Route::get('/', [PropertyDocumentController::class, 'index'])->name('index');
        Route::post('/upload', [PropertyDocumentController::class, 'upload'])->name('upload');
        Route::get('/{document}', [PropertyDocumentController::class, 'show'])->name('show');
        Route::put('/{document}', [PropertyDocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [PropertyDocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download', [PropertyDocumentController::class, 'download'])->name('download');
        Route::post('/{document}/approve', [PropertyDocumentController::class, 'approve'])->name('approve');
        Route::post('/{document}/reject', [PropertyDocumentController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [PropertyDocumentController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-delete', [PropertyDocumentController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/stats', [PropertyDocumentController::class, 'getDocumentStats'])->name('stats');
        Route::post('/package', [PropertyDocumentController::class, 'generateDocumentPackage'])->name('package');
    });

    // Price Actions
    Route::prefix('prices')->name('properties.prices.')->group(function () {
        Route::get('/', [PropertyPriceController::class, 'index'])->name('index');
        Route::post('/', [PropertyPriceController::class, 'store'])->name('store');
        Route::get('/history', [PropertyPriceController::class, 'history'])->name('history');
        Route::get('/analysis', [PropertyPriceController::class, 'getPriceAnalysis'])->name('analysis');
        Route::get('/suggestions', [PropertyPriceController::class, 'getPriceSuggestions'])->name('suggestions');
        Route::get('/{price}', [PropertyPriceController::class, 'show'])->name('show');
        Route::put('/{price}', [PropertyPriceController::class, 'update'])->name('update');
        Route::delete('/{price}', [PropertyPriceController::class, 'destroy'])->name('destroy');
        Route::post('/{price}/activate', [PropertyPriceController::class, 'activate'])->name('activate');
    });

    // Location Actions
    Route::prefix('location')->name('properties.location.')->group(function () {
        Route::get('/', [PropertyLocationController::class, 'index'])->name('index');
        Route::post('/', [PropertyLocationController::class, 'store'])->name('store');
        Route::get('/nearby', [PropertyLocationController::class, 'nearby'])->name('nearby');
        Route::get('/map', [PropertyLocationController::class, 'map'])->name('map');
        Route::get('/stats', [PropertyLocationController::class, 'getLocationStats'])->name('stats');
        Route::get('/{location}', [PropertyLocationController::class, 'show'])->name('show');
        Route::put('/{location}', [PropertyLocationController::class, 'update'])->name('update');
    });

    // Amenity Actions
    Route::prefix('amenities')->name('properties.amenities.')->group(function () {
        Route::get('/', [PropertyAmenityController::class, 'index'])->name('index');
        Route::post('/', [PropertyAmenityController::class, 'attach'])->name('attach');
        Route::post('/bulk-attach', [PropertyAmenityController::class, 'bulkAttach'])->name('bulk-attach');
        Route::post('/bulk-detach', [PropertyAmenityController::class, 'bulkDetach'])->name('bulk-detach');
        Route::get('/suggest', [PropertyAmenityController::class, 'suggestAmenities'])->name('suggest');
        Route::get('/stats', [PropertyAmenityController::class, 'getAmenityStats'])->name('stats');
        Route::get('/export', [PropertyAmenityController::class, 'exportAmenities'])->name('export');
        Route::get('/{amenity}', [PropertyAmenityController::class, 'show'])->name('show');
        Route::delete('/{amenity}', [PropertyAmenityController::class, 'detach'])->name('detach');
        Route::put('/{amenity}', [PropertyAmenityController::class, 'updatePivot'])->name('update-pivot');
    });

    // Feature Actions
    Route::prefix('features')->name('properties.features.')->group(function () {
        Route::get('/', [PropertyFeatureController::class, 'index'])->name('index');
        Route::post('/', [PropertyFeatureController::class, 'attach'])->name('attach');
        Route::post('/bulk-attach', [PropertyFeatureController::class, 'bulkAttach'])->name('bulk-attach');
        Route::post('/bulk-detach', [PropertyFeatureController::class, 'bulkDetach'])->name('bulk-detach');
        Route::get('/{feature}', [PropertyFeatureController::class, 'show'])->name('show');
        Route::delete('/{feature}', [PropertyFeatureController::class, 'detach'])->name('detach');
    });
});

// Shared View (outside prefix)
Route::get('/properties/shared/{token}', [PropertyShareController::class, 'sharedView'])->name('properties.shared');

// Global Utility Routes
Route::post('/geocode', [PropertyLocationController::class, 'geocode'])->name('geocode');
Route::post('/reverse-geocode', [PropertyLocationController::class, 'reverseGeocode'])->name('reverse-geocode');
Route::post('/calculate-distance', [PropertyLocationController::class, 'calculateDistance'])->name('calculate-distance');
Route::post('/properties/prices/bulk-update', [PropertyPriceController::class, 'bulkUpdate'])->name('properties.prices.bulk-update');
Route::get('/amenities/by-category', [PropertyAmenityController::class, 'getAmenitiesByCategory'])->name('amenities.by-category');
Route::get('/amenities/search', [PropertyAmenityController::class, 'searchAmenities'])->name('amenities.search');
Route::get('/amenities/popular', [PropertyAmenityController::class, 'getPopularAmenities'])->name('amenities.popular');
Route::get('/properties/amenities/compare', [PropertyAmenityController::class, 'compareAmenities'])->name('properties.amenities.compare');
