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
Route::get('/property-types', [PropertyTypeController::class, 'index'])->name('property-types.index');
Route::get('/property-types/{propertyType}', [PropertyTypeController::class, 'show'])->name('property-types.show');
Route::get('/property-types/{propertyType}/properties', [PropertyTypeController::class, 'getProperties'])->name('property-types.properties');
Route::get('/property-types/{propertyType}/stats', [PropertyTypeController::class, 'getStats'])->name('property-types.stats');
Route::post('/property-types', [PropertyTypeController::class, 'store'])->name('property-types.store');
Route::put('/property-types/{propertyType}', [PropertyTypeController::class, 'update'])->name('property-types.update');
Route::delete('/property-types/{propertyType}', [PropertyTypeController::class, 'destroy'])->name('property-types.destroy');
Route::post('/property-types/reorder', [PropertyTypeController::class, 'reorder'])->name('property-types.reorder');
Route::post('/property-types/{propertyType}/toggle', [PropertyTypeController::class, 'toggleStatus'])->name('property-types.toggle');
Route::get('/property-types/search', [PropertyTypeController::class, 'search'])->name('property-types.search');
Route::get('/property-types/{propertyType}/export', [PropertyTypeController::class, 'export'])->name('property-types.export');

// Main Property Routes
Route::get('/', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create')->middleware('auth');
Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store')->middleware('auth');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit')->middleware('auth');
Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update')->middleware('auth');
Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy')->middleware('auth');
Route::post('/properties/{property}/duplicate', [PropertyController::class, 'duplicate'])->name('properties.duplicate');
Route::post('/properties/{property}/publish', [PropertyController::class, 'publish'])->name('properties.publish');
Route::post('/properties/{property}/archive', [PropertyController::class, 'archive'])->name('properties.archive');
Route::post('/properties/{property}/view', [PropertyController::class, 'recordView'])->name('properties.view');

// Property Search Routes
Route::get('/properties/search', [PropertySearchController::class, 'search'])->name('properties.search');
Route::get('/properties/search/web', [PropertySearchController::class, 'webSearch'])->name('properties.search.web');
Route::get('/properties/search/api', [PropertySearchController::class, 'apiSearch'])->name('properties.search.api');
Route::get('/properties/search/autocomplete', [PropertySearchController::class, 'autocomplete'])->name('properties.search.autocomplete');
Route::get('/properties/search/map', [PropertySearchController::class, 'mapSearch'])->name('properties.search.map');
Route::post('/properties/search/advanced', [PropertySearchController::class, 'advancedSearch'])->name('properties.search.advanced');
Route::get('/properties/search/suggestions', [PropertySearchController::class, 'getSuggestions'])->name('properties.search.suggestions');
Route::get('/properties/search/popular', [PropertySearchController::class, 'getPopularSearches'])->name('properties.search.popular');
Route::post('/properties/search/save', [PropertySearchController::class, 'saveSearch'])->name('properties.search.save');
Route::get('/properties/search/saved', [PropertySearchController::class, 'getSavedSearches'])->name('properties.search.saved');

// Property Filter Routes
Route::get('/properties/filter', [PropertyFilterController::class, 'filter'])->name('properties.filter');
Route::get('/properties/filter/options', [PropertyFilterController::class, 'getFilterOptions'])->name('properties.filter.options');
Route::post('/properties/filter/apply', [PropertyFilterController::class, 'applyFilters'])->name('properties.filter.apply');
Route::get('/properties/filter/quick', [PropertyFilterController::class, 'quickFilters'])->name('properties.filter.quick');
Route::get('/properties/filter/by-type/{type}', [PropertyFilterController::class, 'filterByType'])->name('properties.filter.by-type');
Route::get('/properties/filter/by-location/{location}', [PropertyFilterController::class, 'filterByLocation'])->name('properties.filter.by-location');
Route::get('/properties/filter/by-price/{min}/{max}', [PropertyFilterController::class, 'filterByPrice'])->name('properties.filter.by-price');
Route::post('/properties/filter/save', [PropertyFilterController::class, 'saveFilter'])->name('properties.filter.save');
Route::get('/properties/filter/saved', [PropertyFilterController::class, 'getSavedFilters'])->name('properties.filter.saved');
Route::delete('/properties/filter/saved/{filter}', [PropertyFilterController::class, 'deleteSavedFilter'])->name('properties.filter.saved.delete');

// Property Comparison Routes
Route::get('/properties/compare', [PropertyComparisonController::class, 'index'])->name('properties.compare');
Route::post('/properties/compare/add', [PropertyComparisonController::class, 'add'])->name('properties.compare.add');
Route::post('/properties/compare/remove', [PropertyComparisonController::class, 'remove'])->name('properties.compare.remove');
Route::post('/properties/compare/clear', [PropertyComparisonController::class, 'clear'])->name('properties.compare.clear');
Route::get('/properties/compare/data', [PropertyComparisonController::class, 'getData'])->name('properties.compare.data');
Route::post('/properties/compare/export', [PropertyComparisonController::class, 'export'])->name('properties.compare.export');
Route::get('/properties/compare/similar/{property}', [PropertyComparisonController::class, 'getSimilarProperties'])->name('properties.compare.similar');

// Property Favorite Routes
Route::get('/properties/favorites', [PropertyFavoriteController::class, 'index'])->name('properties.favorites');
Route::post('/properties/{property}/favorite', [PropertyFavoriteController::class, 'add'])->name('properties.favorite.add');
Route::delete('/properties/{property}/favorite', [PropertyFavoriteController::class, 'remove'])->name('properties.favorite.remove');
Route::post('/properties/{property}/favorite/toggle', [PropertyFavoriteController::class, 'toggle'])->name('properties.favorite.toggle');
Route::get('/properties/{property}/favorite/status', [PropertyFavoriteController::class, 'getStatus'])->name('properties.favorite.status');
Route::get('/properties/favorites/stats', [PropertyFavoriteController::class, 'getStats'])->name('properties.favorites.stats');
Route::post('/properties/favorites/bulk', [PropertyFavoriteController::class, 'bulkAction'])->name('properties.favorites.bulk');
Route::get('/properties/favorites/share', [PropertyFavoriteController::class, 'share'])->name('properties.favorites.share');
Route::get('/properties/favorites/alerts', [PropertyFavoriteController::class, 'getAlerts'])->name('properties.favorites.alerts');
Route::post('/properties/favorites/alerts', [PropertyFavoriteController::class, 'createAlert'])->name('properties.favorites.alerts.create');
Route::get('/properties/favorites/similar/{property}', [PropertyFavoriteController::class, 'getSimilarProperties'])->name('properties.favorites.similar');

// Property View Routes
Route::get('/properties/{property}/views', [PropertyViewController::class, 'index'])->name('properties.views');
Route::post('/properties/{property}/views/record', [PropertyViewController::class, 'record'])->name('properties.views.record');
Route::get('/properties/{property}/views/stats', [PropertyViewController::class, 'getStats'])->name('properties.views.stats');
Route::get('/properties/{property}/views/analytics', [PropertyViewController::class, 'getAnalytics'])->name('properties.views.analytics');
Route::get('/properties/{property}/views/engagement', [PropertyViewController::class, 'getEngagement'])->name('properties.views.engagement');
Route::get('/properties/{property}/views/heatmap', [PropertyViewController::class, 'getHeatmapData'])->name('properties.views.heatmap');
Route::get('/properties/{property}/views/performance', [PropertyViewController::class, 'getPerformance'])->name('properties.views.performance');
Route::post('/properties/{property}/views/export', [PropertyViewController::class, 'export'])->name('properties.views.export');
Route::get('/properties/{property}/views/recent', [PropertyViewController::class, 'getRecentViews'])->name('properties.views.recent');

// Property Share Routes
Route::post('/properties/{property}/share', [PropertyShareController::class, 'share'])->name('properties.share');
Route::get('/properties/{property}/share/link', [PropertyShareController::class, 'getShareLink'])->name('properties.share.link');
Route::get('/properties/shared/{token}', [PropertyShareController::class, 'sharedView'])->name('properties.shared');
Route::get('/properties/{property}/share/stats', [PropertyShareController::class, 'getShareStats'])->name('properties.share.stats');
Route::post('/properties/share/campaign', [PropertyShareController::class, 'createCampaign'])->name('properties.share.campaign');
Route::get('/properties/{property}/share/qrcode', [PropertyShareController::class, 'generateQRCode'])->name('properties.share.qrcode');
Route::post('/properties/share/bulk', [PropertyShareController::class, 'bulkShare'])->name('properties.share.bulk');

// Property Media Routes
Route::get('/properties/{property}/media', [PropertyMediaController::class, 'index'])->name('properties.media.index');
Route::post('/properties/{property}/media/upload', [PropertyMediaController::class, 'upload'])->name('properties.media.upload');
Route::put('/properties/{property}/media/{media}', [PropertyMediaController::class, 'update'])->name('properties.media.update');
Route::delete('/properties/{property}/media/{media}', [PropertyMediaController::class, 'destroy'])->name('properties.media.destroy');
Route::post('/properties/{property}/media/reorder', [PropertyMediaController::class, 'reorder'])->name('properties.media.reorder');
Route::post('/properties/{property}/media/{media}/set-primary', [PropertyMediaController::class, 'setPrimary'])->name('properties.media.set-primary');
Route::post('/properties/{property}/media/bulk-delete', [PropertyMediaController::class, 'bulkDelete'])->name('properties.media.bulk-delete');
Route::get('/properties/{property}/media/gallery', [PropertyMediaController::class, 'gallery'])->name('properties.media.gallery');
Route::get('/properties/{property}/media/{media}/download', [PropertyMediaController::class, 'download'])->name('properties.media.download');
Route::get('/properties/{property}/media/stats', [PropertyMediaController::class, 'getMediaStats'])->name('properties.media.stats');

// Property Document Routes
Route::get('/properties/{property}/documents', [PropertyDocumentController::class, 'index'])->name('properties.documents.index');
Route::post('/properties/{property}/documents/upload', [PropertyDocumentController::class, 'upload'])->name('properties.documents.upload');
Route::get('/properties/{property}/documents/{document}', [PropertyDocumentController::class, 'show'])->name('properties.documents.show');
Route::put('/properties/{property}/documents/{document}', [PropertyDocumentController::class, 'update'])->name('properties.documents.update');
Route::delete('/properties/{property}/documents/{document}', [PropertyDocumentController::class, 'destroy'])->name('properties.documents.destroy');
Route::get('/properties/{property}/documents/{document}/download', [PropertyDocumentController::class, 'download'])->name('properties.documents.download');
Route::post('/properties/{property}/documents/{document}/approve', [PropertyDocumentController::class, 'approve'])->name('properties.documents.approve');
Route::post('/properties/{property}/documents/{document}/reject', [PropertyDocumentController::class, 'reject'])->name('properties.documents.reject');
Route::post('/properties/{property}/documents/bulk-approve', [PropertyDocumentController::class, 'bulkApprove'])->name('properties.documents.bulk-approve');
Route::post('/properties/{property}/documents/bulk-delete', [PropertyDocumentController::class, 'bulkDelete'])->name('properties.documents.bulk-delete');
Route::get('/properties/{property}/documents/stats', [PropertyDocumentController::class, 'getDocumentStats'])->name('properties.documents.stats');
Route::post('/properties/{property}/documents/package', [PropertyDocumentController::class, 'generateDocumentPackage'])->name('properties.documents.package');

// Property Price Routes
Route::get('/properties/{property}/prices', [PropertyPriceController::class, 'index'])->name('properties.prices.index');
Route::get('/properties/{property}/prices/{price}', [PropertyPriceController::class, 'show'])->name('properties.prices.show');
Route::post('/properties/{property}/prices', [PropertyPriceController::class, 'store'])->name('properties.prices.store');
Route::put('/properties/{property}/prices/{price}', [PropertyPriceController::class, 'update'])->name('properties.prices.update');
Route::delete('/properties/{property}/prices/{price}', [PropertyPriceController::class, 'destroy'])->name('properties.prices.destroy');
Route::get('/properties/{property}/prices', [PropertyPriceController::class, 'index'])->name('properties.prices.index');
Route::get('/properties/{property}/prices/history', [PropertyPriceController::class, 'history'])->name('properties.prices.history');
Route::post('/properties/{property}/prices/{price}/activate', [PropertyPriceController::class, 'activate'])->name('properties.prices.activate');
Route::get('/properties/{property}/prices/analysis', [PropertyPriceController::class, 'getPriceAnalysis'])->name('properties.prices.analysis');
Route::get('/properties/{property}/prices/suggestions', [PropertyPriceController::class, 'getPriceSuggestions'])->name('properties.prices.suggestions');
Route::post('/properties/prices/bulk-update', [PropertyPriceController::class, 'bulkUpdate'])->name('properties.prices.bulk-update');

// Property Location Routes
Route::get('/properties/{property}/location', [PropertyLocationController::class, 'index'])->name('properties.location.index');
Route::get('/properties/{property}/location/{location}', [PropertyLocationController::class, 'show'])->name('properties.location.show');
Route::post('/properties/{property}/location', [PropertyLocationController::class, 'store'])->name('properties.location.store');
Route::put('/properties/{property}/location/{location}', [PropertyLocationController::class, 'update'])->name('properties.location.update');
Route::get('/properties/{property}/location/nearby', [PropertyLocationController::class, 'nearby'])->name('properties.location.nearby');
Route::get('/properties/{property}/location/map', [PropertyLocationController::class, 'map'])->name('properties.location.map');
Route::post('/geocode', [PropertyLocationController::class, 'geocode'])->name('geocode');
Route::post('/reverse-geocode', [PropertyLocationController::class, 'reverseGeocode'])->name('reverse-geocode');
Route::post('/calculate-distance', [PropertyLocationController::class, 'calculateDistance'])->name('calculate-distance');
Route::get('/properties/{property}/location/stats', [PropertyLocationController::class, 'getLocationStats'])->name('properties.location.stats');

// Property Amenity Routes
Route::get('/properties/{property}/amenities', [PropertyAmenityController::class, 'index'])->name('properties.amenities.index');
Route::get('/properties/{property}/amenities/{amenity}', [PropertyAmenityController::class, 'show'])->name('properties.amenities.show');
Route::post('/properties/{property}/amenities', [PropertyAmenityController::class, 'attach'])->name('properties.amenities.attach');
Route::delete('/properties/{property}/amenities/{amenity}', [PropertyAmenityController::class, 'detach'])->name('properties.amenities.detach');
Route::post('/properties/{property}/amenities/bulk-attach', [PropertyAmenityController::class, 'bulkAttach'])->name('properties.amenities.bulk-attach');
Route::post('/properties/{property}/amenities/bulk-detach', [PropertyAmenityController::class, 'bulkDetach'])->name('properties.amenities.bulk-detach');
Route::put('/properties/{property}/amenities/{amenity}', [PropertyAmenityController::class, 'updatePivot'])->name('properties.amenities.update-pivot');
Route::get('/amenities/by-category', [PropertyAmenityController::class, 'getAmenitiesByCategory'])->name('amenities.by-category');
Route::get('/amenities/search', [PropertyAmenityController::class, 'searchAmenities'])->name('amenities.search');
Route::get('/amenities/popular', [PropertyAmenityController::class, 'getPopularAmenities'])->name('amenities.popular');
Route::get('/properties/{property}/amenities/stats', [PropertyAmenityController::class, 'getAmenityStats'])->name('properties.amenities.stats');
Route::get('/properties/amenities/compare', [PropertyAmenityController::class, 'compareAmenities'])->name('properties.amenities.compare');
Route::get('/properties/{property}/amenities/suggest', [PropertyAmenityController::class, 'suggestAmenities'])->name('properties.amenities.suggest');
Route::get('/properties/{property}/amenities/export', [PropertyAmenityController::class, 'exportAmenities'])->name('properties.amenities.export');

// Property Feature Routes
Route::get('/properties/{property}/features', [PropertyFeatureController::class, 'index'])->name('properties.features.index');
Route::get('/properties/{property}/features/{feature}', [PropertyFeatureController::class, 'show'])->name('properties.features.show');
Route::post('/properties/{property}/features', [PropertyFeatureController::class, 'attach'])->name('properties.features.attach');
Route::delete('/properties/{property}/features/{feature}', [PropertyFeatureController::class, 'detach'])->name('properties.features.detach');
Route::post('/properties/{property}/features/bulk-attach', [PropertyFeatureController::class, 'bulkAttach'])->name('properties.features.bulk-attach');
Route::post('/properties/{property}/features/bulk-detach', [PropertyFeatureController::class, 'bulkDetach'])->name('properties.features.bulk-detach');
Route::put('/properties/{property}/features/{feature}', [PropertyFeatureController::class, 'updatePivot'])->name('properties.features.update-pivot');
Route::get('/features/by-category', [PropertyFeatureController::class, 'getFeaturesByCategory'])->name('features.by-category');
Route::get('/features/search', [PropertyFeatureController::class, 'searchFeatures'])->name('features.search');
Route::get('/features/premium', [PropertyFeatureController::class, 'getPremiumFeatures'])->name('features.premium');
Route::get('/features/popular', [PropertyFeatureController::class, 'getPopularFeatures'])->name('features.popular');
Route::get('/properties/{property}/features/stats', [PropertyFeatureController::class, 'getFeatureStats'])->name('properties.features.stats');
Route::get('/properties/features/compare', [PropertyFeatureController::class, 'compareFeatures'])->name('properties.features.compare');
Route::get('/properties/{property}/features/suggest', [PropertyFeatureController::class, 'suggestFeatures'])->name('properties.features.suggest');
Route::get('/properties/{property}/features/export', [PropertyFeatureController::class, 'exportFeatures'])->name('properties.features.export');

// API Routes for AJAX requests
Route::prefix('api/properties')->group(function () {
    // Search and Filter API
    Route::get('/search', [PropertySearchController::class, 'apiSearch'])->name('api.properties.search');
    Route::get('/filter', [PropertyFilterController::class, 'filter'])->name('api.properties.filter');
    Route::get('/autocomplete', [PropertySearchController::class, 'autocomplete'])->name('api.properties.autocomplete');
    
    // Property Data API
    Route::get('/{property}/data', [PropertyController::class, 'getData'])->name('api.properties.data');
    Route::get('/{property}/stats', [PropertyController::class, 'getStats'])->name('api.properties.stats');
    Route::get('/{property}/similar', [PropertyController::class, 'getSimilarProperties'])->name('api.properties.similar');
    
    // Interactive Features API
    Route::post('/{property}/favorite/toggle', [PropertyFavoriteController::class, 'toggle'])->name('api.properties.favorite.toggle');
    Route::post('/{property}/compare/add', [PropertyComparisonController::class, 'add'])->name('api.properties.compare.add');
    Route::post('/{property}/share', [PropertyShareController::class, 'share'])->name('api.properties.share');
    Route::post('/{property}/view', [PropertyViewController::class, 'record'])->name('api.properties.view');
    
    // Media API
    Route::post('/{property}/media/upload', [PropertyMediaController::class, 'upload'])->name('api.properties.media.upload');
    Route::post('/{property}/media/reorder', [PropertyMediaController::class, 'reorder'])->name('api.properties.media.reorder');
    Route::post('/{property}/media/{media}/set-primary', [PropertyMediaController::class, 'setPrimary'])->name('api.properties.media.set-primary');
    
    // Documents API
    Route::post('/{property}/documents/upload', [PropertyDocumentController::class, 'upload'])->name('api.properties.documents.upload');
    Route::post('/{property}/documents/{document}/approve', [PropertyDocumentController::class, 'approve'])->name('api.properties.documents.approve');
    
    // Price API
    Route::post('/{property}/prices', [PropertyPriceController::class, 'store'])->name('api.properties.prices.store');
    Route::get('/{property}/prices/analysis', [PropertyPriceController::class, 'getPriceAnalysis'])->name('api.properties.prices.analysis');
    
    // Location API
    Route::get('/{property}/location/nearby', [PropertyLocationController::class, 'nearby'])->name('api.properties.location.nearby');
    Route::get('/{property}/location/map', [PropertyLocationController::class, 'map'])->name('api.properties.location.map');
    
    // Amenities and Features API
    Route::post('/{property}/amenities', [PropertyAmenityController::class, 'attach'])->name('api.properties.amenities.attach');
    Route::post('/{property}/features', [PropertyFeatureController::class, 'attach'])->name('api.properties.features.attach');
});

// Admin Routes (for property management)
Route::prefix('admin/properties')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [PropertyController::class, 'adminDashboard'])->name('admin.properties.dashboard');
    Route::get('/pending', [PropertyController::class, 'pendingProperties'])->name('admin.properties.pending');
    Route::post('/{property}/approve', [PropertyController::class, 'approve'])->name('admin.properties.approve');
    Route::post('/{property}/reject', [PropertyController::class, 'reject'])->name('admin.properties.reject');
    Route::get('/reports', [PropertyController::class, 'reports'])->name('admin.properties.reports');
    Route::post('/bulk-action', [PropertyController::class, 'bulkAction'])->name('admin.properties.bulk-action');
    Route::get('/export', [PropertyController::class, 'export'])->name('admin.properties.export');
    Route::post('/import', [PropertyController::class, 'import'])->name('admin.properties.import');
});

// Agent Routes (for agent-specific property management)
Route::prefix('agent/properties')->middleware(['auth', 'agent'])->group(function () {
    Route::get('/dashboard', [PropertyController::class, 'agentDashboard'])->name('agent.properties.dashboard');
    Route::get('/my-properties', [PropertyController::class, 'myProperties'])->name('agent.properties.my');
    Route::get('/stats', [PropertyController::class, 'agentStats'])->name('agent.properties.stats');
    Route::get('/leads', [PropertyController::class, 'leads'])->name('agent.properties.leads');
    Route::post('/{property}/mark-sold', [PropertyController::class, 'markSold'])->name('agent.properties.mark-sold');
    Route::post('/{property}/mark-rented', [PropertyController::class, 'markRented'])->name('agent.properties.mark-rented');
});

// Public Routes (no authentication required)
Route::prefix('properties')->group(function () {
    Route::get('/featured', [PropertyController::class, 'featuredProperties'])->name('properties.featured');
    Route::get('/latest', [PropertyController::class, 'latestProperties'])->name('properties.latest');
    Route::get('/popular', [PropertyController::class, 'popularProperties'])->name('properties.popular');
    Route::get('/by-type/{type}', [PropertyController::class, 'propertiesByType'])->name('properties.by-type');
    Route::get('/by-location/{location}', [PropertyController::class, 'propertiesByLocation'])->name('properties.by-location');
    Route::get('/by-price-range/{min}/{max}', [PropertyController::class, 'propertiesByPriceRange'])->name('properties.by-price-range');
});

// Property Valuation Routes
Route::prefix('properties/valuation')->group(function () {
    Route::get('/calculator', [PropertyController::class, 'valuationCalculator'])->name('properties.valuation.calculator');
    Route::post('/calculate', [PropertyController::class, 'calculateValuation'])->name('properties.valuation.calculate');
    Route::get('/report/{property}', [PropertyController::class, 'valuationReport'])->name('properties.valuation.report');
    Route::post('/bulk-valuation', [PropertyController::class, 'bulkValuation'])->name('properties.valuation.bulk');
});
