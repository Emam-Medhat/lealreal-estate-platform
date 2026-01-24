<?php

use App\Http\Controllers\OptimizedPropertyController;
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
| Optimized Property Routes
|--------------------------------------------------------------------------
|
| These routes are for the optimized property management system with
| caching, microservices, and performance improvements.
|
*/

// Apply caching middleware to public routes
Route::middleware(['cache.response:300'])->group(function () {
    
    // Test Route for Dropdown
    Route::get('/optimized/properties/test-dropdown', function () {
        $propertyTypes = \App\Models\PropertyType::select('id', 'name', 'slug')
            ->where('is_active', true)
            ->orWhere('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
            
        return view('properties.test_dropdown', compact('propertyTypes'));
    })->name('optimized.properties.test-dropdown');
    
    // Main Property Routes (Optimized)
    Route::get('/optimized/properties', [OptimizedPropertyController::class, 'index'])->name('optimized.properties.index');
    Route::get('/optimized/properties/{property}', [OptimizedPropertyController::class, 'show'])->name('optimized.properties.show');
    Route::get('/optimized/properties/search', [OptimizedPropertyController::class, 'search'])->name('optimized.properties.search');
    
    // Featured Properties (Cached)
    Route::get('/optimized/properties/featured', [OptimizedPropertyController::class, 'getFeaturedProperties'])->name('optimized.properties.featured');
    
    // Property Statistics (Cached)
    Route::get('/optimized/properties/stats', [OptimizedPropertyController::class, 'getStats'])->name('optimized.properties.stats');
    
    // Property Types (Cached)
    Route::get('/optimized/property-types', [PropertyTypeController::class, 'index'])->name('optimized.property-types.index');
});

// Authenticated Routes (with less aggressive caching)
Route::middleware(['auth', 'cache.response:60'])->group(function () {
    
    // Property Management
    Route::get('/optimized/properties/create', [OptimizedPropertyController::class, 'create'])->name('optimized.properties.create');
    Route::post('/optimized/properties', [OptimizedPropertyController::class, 'store'])->name('optimized.properties.store');
    Route::get('/optimized/properties/{property}/edit', [OptimizedPropertyController::class, 'edit'])->name('optimized.properties.edit');
    Route::put('/optimized/properties/{property}', [OptimizedPropertyController::class, 'update'])->name('optimized.properties.update');
    Route::delete('/optimized/properties/{property}', [OptimizedPropertyController::class, 'destroy'])->name('optimized.properties.destroy');
    
    // Property Actions
    Route::post('/optimized/properties/{property}/duplicate', [OptimizedPropertyController::class, 'duplicate'])->name('optimized.properties.duplicate');
    Route::post('/optimized/properties/{property}/publish', [OptimizedPropertyController::class, 'publish'])->name('optimized.properties.publish');
    Route::post('/optimized/properties/{property}/archive', [OptimizedPropertyController::class, 'archive'])->name('optimized.properties.archive');
    
    // Cache Management
    Route::post('/optimized/properties/clear-cache', [OptimizedPropertyController::class, 'clearCache'])->name('optimized.properties.clear-cache');
});

// API Routes for AJAX requests (with caching)
Route::prefix('api/properties')->middleware(['cache.response:180'])->group(function () {
    
    // Search and Filter API
    Route::get('/search', [PropertySearchController::class, 'apiSearch'])->name('api.optimized.properties.search');
    Route::get('/filter', [PropertyFilterController::class, 'filter'])->name('api.optimized.properties.filter');
    Route::get('/autocomplete', [PropertySearchController::class, 'autocomplete'])->name('api.optimized.properties.autocomplete');
    
    // Property Data API
    Route::get('/{property}/data', [OptimizedPropertyController::class, 'getPropertyDetails'])->name('api.optimized.properties.data');
    Route::get('/{property}/stats', [OptimizedPropertyController::class, 'getStats'])->name('api.optimized.properties.stats');
    Route::get('/{property}/similar', [OptimizedPropertyController::class, 'getSimilarProperties'])->name('api.optimized.properties.similar');
    
    // Interactive Features API
    Route::post('/{property}/favorite/toggle', [PropertyFavoriteController::class, 'toggle'])->name('api.optimized.properties.favorite.toggle');
    Route::post('/{property}/compare/add', [PropertyComparisonController::class, 'add'])->name('api.optimized.properties.compare.add');
    Route::post('/{property}/share', [PropertyShareController::class, 'share'])->name('api.optimized.properties.share');
    Route::post('/{property}/view', [PropertyViewController::class, 'record'])->name('api.optimized.properties.view');
    
    // Media API
    Route::post('/{property}/media/upload', [PropertyMediaController::class, 'upload'])->name('api.optimized.properties.media.upload');
    Route::post('/{property}/media/reorder', [PropertyMediaController::class, 'reorder'])->name('api.optimized.properties.media.reorder');
    Route::post('/{property}/media/{media}/set-primary', [PropertyMediaController::class, 'setPrimary'])->name('api.optimized.properties.media.set-primary');
    
    // Documents API
    Route::post('/{property}/documents/upload', [PropertyDocumentController::class, 'upload'])->name('api.optimized.properties.documents.upload');
    Route::post('/{property}/documents/{document}/approve', [PropertyDocumentController::class, 'approve'])->name('api.optimized.properties.documents.approve');
    
    // Price API
    Route::post('/{property}/prices', [PropertyPriceController::class, 'store'])->name('api.optimized.properties.prices.store');
    Route::get('/{property}/prices/analysis', [PropertyPriceController::class, 'getPriceAnalysis'])->name('api.optimized.properties.prices.analysis');
    
    // Location API
    Route::get('/{property}/location/nearby', [PropertyLocationController::class, 'nearby'])->name('api.optimized.properties.location.nearby');
    Route::get('/{property}/location/map', [PropertyLocationController::class, 'map'])->name('api.optimized.properties.location.map');
    
    // Amenities and Features API
    Route::post('/{property}/amenities', [PropertyAmenityController::class, 'attach'])->name('api.optimized.properties.amenities.attach');
    Route::post('/{property}/features', [PropertyFeatureController::class, 'attach'])->name('api.optimized.properties.features.attach');
});

// Admin Routes (for property management)
Route::prefix('admin/properties')->middleware(['auth', 'admin', 'cache.response:120'])->group(function () {
    Route::get('/dashboard', [OptimizedPropertyController::class, 'adminDashboard'])->name('admin.optimized.properties.dashboard');
    Route::get('/pending', [OptimizedPropertyController::class, 'pendingProperties'])->name('admin.optimized.properties.pending');
    Route::post('/{property}/approve', [OptimizedPropertyController::class, 'approve'])->name('admin.optimized.properties.approve');
    Route::post('/{property}/reject', [OptimizedPropertyController::class, 'reject'])->name('admin.optimized.properties.reject');
    Route::get('/reports', [OptimizedPropertyController::class, 'reports'])->name('admin.optimized.properties.reports');
    Route::post('/bulk-action', [OptimizedPropertyController::class, 'bulkAction'])->name('admin.optimized.properties.bulk-action');
    Route::get('/export', [OptimizedPropertyController::class, 'export'])->name('admin.optimized.properties.export');
    Route::post('/import', [OptimizedPropertyController::class, 'import'])->name('admin.optimized.properties.import');
});

// Agent Routes (for agent-specific property management)
Route::prefix('agent/properties')->middleware(['auth', 'agent', 'cache.response:120'])->group(function () {
    Route::get('/dashboard', [OptimizedPropertyController::class, 'agentDashboard'])->name('agent.optimized.properties.dashboard');
    Route::get('/my-properties', [OptimizedPropertyController::class, 'myProperties'])->name('agent.optimized.properties.my');
    Route::get('/stats', [OptimizedPropertyController::class, 'agentStats'])->name('agent.optimized.properties.stats');
    Route::get('/leads', [OptimizedPropertyController::class, 'leads'])->name('agent.optimized.properties.leads');
    Route::post('/{property}/mark-sold', [OptimizedPropertyController::class, 'markSold'])->name('agent.optimized.properties.mark-sold');
    Route::post('/{property}/mark-rented', [OptimizedPropertyController::class, 'markRented'])->name('agent.optimized.properties.mark-rented');
});

// Public Routes (no authentication required) with heavy caching
Route::prefix('properties')->middleware(['cache.response:600'])->group(function () {
    Route::get('/featured', [OptimizedPropertyController::class, 'featuredProperties'])->name('optimized.properties.featured');
    Route::get('/latest', [OptimizedPropertyController::class, 'latestProperties'])->name('optimized.properties.latest');
    Route::get('/popular', [OptimizedPropertyController::class, 'popularProperties'])->name('optimized.properties.popular');
    Route::get('/by-type/{type}', [OptimizedPropertyController::class, 'propertiesByType'])->name('optimized.properties.by-type');
    Route::get('/by-location/{location}', [OptimizedPropertyController::class, 'propertiesByLocation'])->name('optimized.properties.by-location');
    Route::get('/by-price-range/{min}/{max}', [OptimizedPropertyController::class, 'propertiesByPriceRange'])->name('optimized.properties.by-price-range');
});

// Map and Location Routes with caching
Route::middleware(['cache.response:300'])->group(function () {
    Route::get('/properties/map', [PropertyLocationController::class, 'mapView'])->name('optimized.properties.map');
    Route::post('/properties/map/data', [PropertyLocationController::class, 'getMapData'])->name('optimized.properties.map.data');
    Route::get('/properties/geo-search', [PropertyLocationController::class, 'geoSearch'])->name('optimized.properties.geo-search');
});

// Comparison and Favorites Routes
Route::middleware(['auth', 'cache.response:60'])->group(function () {
    Route::get('/properties/compare', [PropertyComparisonController::class, 'index'])->name('optimized.properties.compare');
    Route::post('/properties/compare/add', [PropertyComparisonController::class, 'add'])->name('optimized.properties.compare.add');
    Route::post('/properties/compare/remove', [PropertyComparisonController::class, 'remove'])->name('optimized.properties.compare.remove');
    Route::post('/properties/compare/clear', [PropertyComparisonController::class, 'clear'])->name('optimized.properties.compare.clear');
    
    Route::get('/properties/favorites', [PropertyFavoriteController::class, 'index'])->name('optimized.properties.favorites');
    Route::post('/properties/{property}/favorite', [PropertyFavoriteController::class, 'add'])->name('optimized.properties.favorite.add');
    Route::delete('/properties/{property}/favorite', [PropertyFavoriteController::class, 'remove'])->name('optimized.properties.favorite.remove');
    Route::post('/properties/{property}/favorite/toggle', [PropertyFavoriteController::class, 'toggle'])->name('optimized.properties.favorite.toggle');
});

// Property Valuation Routes with caching
Route::prefix('properties/valuation')->middleware(['cache.response:300'])->group(function () {
    Route::get('/calculator', [OptimizedPropertyController::class, 'valuationCalculator'])->name('optimized.properties.valuation.calculator');
    Route::post('/calculate', [OptimizedPropertyController::class, 'calculateValuation'])->name('optimized.properties.valuation.calculate');
    Route::get('/report/{property}', [OptimizedPropertyController::class, 'valuationReport'])->name('optimized.properties.valuation.report');
    Route::post('/bulk-valuation', [OptimizedPropertyController::class, 'bulkValuation'])->name('optimized.properties.valuation.bulk');
});
