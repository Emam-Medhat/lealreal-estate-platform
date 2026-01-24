<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Neighborhood\NeighborhoodController;
use App\Http\Controllers\Neighborhood\CommunityController;
use App\Http\Controllers\Neighborhood\NeighborhoodGuideController;
use App\Http\Controllers\Neighborhood\LocalBusinessController;
use App\Http\Controllers\Neighborhood\AmenityMapController;
use App\Http\Controllers\Neighborhood\CommunityEventController;
use App\Http\Controllers\Neighborhood\NeighborhoodReviewController;
use App\Http\Controllers\Neighborhood\ResidentForumController;
use App\Http\Controllers\Neighborhood\CommunityNewsController;
use App\Http\Controllers\Neighborhood\NeighborhoodStatisticsController;

/*
|--------------------------------------------------------------------------
| Neighborhood & Community Management Routes
|--------------------------------------------------------------------------
|
| Routes for managing neighborhoods, communities, guides, businesses,
| amenities, events, reviews, forum posts, news, and statistics.
|
*/

// Neighborhood Routes
Route::prefix('neighborhoods')->name('neighborhoods.')->group(function () {
    Route::get('/', [NeighborhoodController::class, 'index'])->name('index');
    Route::get('/create', [NeighborhoodController::class, 'create'])->name('create');
    Route::post('/', [NeighborhoodController::class, 'store'])->name('store');
    Route::get('/{neighborhood}', [NeighborhoodController::class, 'show'])->name('show');
    Route::get('/{neighborhood}/edit', [NeighborhoodController::class, 'edit'])->name('edit');
    Route::put('/{neighborhood}', [NeighborhoodController::class, 'update'])->name('update');
    Route::delete('/{neighborhood}', [NeighborhoodController::class, 'destroy'])->name('destroy');
    
    // Neighborhood Statistics
    Route::get('/{neighborhood}/statistics', [NeighborhoodController::class, 'getStatistics'])->name('statistics');
    Route::get('/{neighborhood}/comparison', [NeighborhoodController::class, 'getComparisonData'])->name('comparison');
    Route::get('/{neighborhood}/export', [NeighborhoodController::class, 'export'])->name('export');
    
    // Search and Filter
    Route::get('/search', [NeighborhoodController::class, 'search'])->name('search');
    Route::get('/filter', [NeighborhoodController::class, 'filter'])->name('filter');
});

// Community Routes
Route::prefix('communities')->name('communities.')->group(function () {
    Route::get('/', [CommunityController::class, 'index'])->name('index');
    Route::get('/create', [CommunityController::class, 'create'])->name('create');
    Route::post('/', [CommunityController::class, 'store'])->name('store');
    Route::get('/{community}', [CommunityController::class, 'show'])->name('show');
    Route::get('/{community}/edit', [CommunityController::class, 'edit'])->name('edit');
    Route::put('/{community}', [CommunityController::class, 'update'])->name('update');
    Route::delete('/{community}', [CommunityController::class, 'destroy'])->name('destroy');
    
    // Community Management
    Route::post('/{community}/join', [CommunityController::class, 'join'])->name('join');
    Route::post('/{community}/leave', [CommunityController::class, 'leave'])->name('leave');
    Route::get('/{community}/statistics', [CommunityController::class, 'getStatistics'])->name('statistics');
    Route::get('/{community}/activity', [CommunityController::class, 'getActivityFeed'])->name('activity');
    Route::get('/{community}/members', [CommunityController::class, 'getMembers'])->name('members');
    Route::get('/{community}/export', [CommunityController::class, 'export'])->name('export');
    
    // Search and Filter
    Route::get('/search', [CommunityController::class, 'search'])->name('search');
    Route::get('/filter', [CommunityController::class, 'filter'])->name('filter');
});

// Neighborhood Guides Routes
Route::prefix('neighborhood-guides')->name('neighborhood-guides.')->group(function () {
    Route::get('/', [NeighborhoodGuideController::class, 'index'])->name('index');
    Route::get('/create', [NeighborhoodGuideController::class, 'create'])->name('create');
    Route::post('/', [NeighborhoodGuideController::class, 'store'])->name('store');
    Route::get('/{guide}', [NeighborhoodGuideController::class, 'show'])->name('show');
    Route::get('/{guide}/edit', [NeighborhoodGuideController::class, 'edit'])->name('edit');
    Route::put('/{guide}', [NeighborhoodGuideController::class, 'update'])->name('update');
    Route::delete('/{guide}', [NeighborhoodGuideController::class, 'destroy'])->name('destroy');
    
    // Guide Features
    Route::get('/{guide}/statistics', [NeighborhoodGuideController::class, 'getStatistics'])->name('statistics');
    Route::get('/{guide}/search', [NeighborhoodGuideController::class, 'search'])->name('search');
    Route::post('/{guide}/rate', [NeighborhoodGuideController::class, 'rate'])->name('rate');
    Route::get('/{guide}/export', [NeighborhoodGuideController::class, 'export'])->name('export');
    
    // Search and Filter
    Route::get('/search', [NeighborhoodGuideController::class, 'search'])->name('search');
    Route::get('/filter', [NeighborhoodGuideController::class, 'filter'])->name('filter');
});

// Local Business Routes
Route::prefix('local-businesses')->name('local-businesses.')->group(function () {
    Route::get('/', [LocalBusinessController::class, 'index'])->name('index');
    Route::get('/create', [LocalBusinessController::class, 'create'])->name('create');
    Route::post('/', [LocalBusinessController::class, 'store'])->name('store');
    Route::get('/{business}', [LocalBusinessController::class, 'show'])->name('show');
    Route::get('/{business}/edit', [LocalBusinessController::class, 'edit'])->name('edit');
    Route::put('/{business}', [LocalBusinessController::class, 'update'])->name('update');
    Route::delete('/{business}', [LocalBusinessController::class, 'destroy'])->name('destroy');
    
    // Business Features
    Route::get('/{business}/statistics', [LocalBusinessController::class, 'getStatistics'])->name('statistics');
    Route::get('/{business}/search', [LocalBusinessController::class, 'search'])->name('search');
    Route::post('/{business}/rate', [LocalBusinessController::class, 'rate'])->name('rate');
    Route::get('/{business}/export', [LocalBusinessController::class, 'export'])->name('export');
    Route::get('/{business}/nearby', [LocalBusinessController::class, 'findNearby'])->name('nearby');
    Route::get('/{business}/category', [LocalBusinessController::class, 'getByCategory'])->name('category');
    
    // Search and Filter
    Route::get('/search', [LocalBusinessController::class, 'search'])->name('search');
    Route::get('/filter', [LocalBusinessController::class, 'filter'])->name('filter');
});

// Community Amenities Routes
Route::prefix('amenity-maps')->name('amenity-maps.')->group(function () {
    Route::get('/', [AmenityMapController::class, 'index'])->name('index');
    Route::get('/create', [AmenityMapController::class, 'create'])->name('create');
    Route::post('/', [AmenityMapController::class, 'store'])->name('store');
    Route::get('/{amenity}', [AmenityMapController::class, 'show'])->name('show');
    Route::get('/{amenity}/edit', [AmenityMapController::class, 'edit'])->name('edit');
    Route::put('/{amenity}', [AmenityMapController::class, 'update'])->name('update');
    Route::delete('/{amenity}', [AmenityMapController::class, 'destroy'])->name('destroy');
    
    // Amenity Features
    Route::get('/{amenity}/statistics', [AmenityMapController::class, 'getStatistics'])->name('statistics');
    Route::get('/{amenity}/search', [AmenityMapController::class, 'search'])->name('search');
    Route::post('/{amenity}/rate', [AmenityMapController::class, 'rate'])->name('rate');
    Route::get('/{amenity}/export', [AmenityMapController::class, 'export'])->name('export');
    Route::get('/{amenity}/map-data', [AmenityMapController::class, 'getMapData'])->name('map-data');
    Route::get('/{amenity}/nearby', [AmenityMapController::class, 'findNearby'])->name('nearby');
    
    // Search and Filter
    Route::get('/search', [AmenityMapController::class, 'search'])->name('search');
    Route::get('/filter', [AmenityMapController::class, 'filter'])->name('filter');
    Route::get('/map', [AmenityMapController::class, 'getMapData'])->name('map');
});

// Community Events Routes
Route::prefix('community-events')->name('community-events.')->group(function () {
    Route::get('/', [CommunityEventController::class, 'index'])->name('index');
    Route::get('/create', [CommunityEventController::class, 'create'])->name('create');
    Route::post('/', [CommunityEventController::class, 'store'])->name('store');
    Route::get('/{event}', [CommunityEventController::class, 'show'])->name('show');
    Route::get('/{event}/edit', [CommunityEventController::class, 'edit'])->name('edit');
    Route::put('/{event}', [CommunityEventController::class, 'update'])->name('update');
    Route::delete('/{event}', [CommunityEventController::class, 'destroy'])->name('destroy');
    
    // Event Management
    Route::post('/{event}/join', [CommunityEventController::class, 'join'])->name('join');
    Route::post('/{event}/leave', [CommunityEventController::class, 'leave'])->name('leave');
    Route::get('/{event}/statistics', [CommunityEventController::class, 'getStatistics'])->name('statistics');
    Route::get('/{event}/calendar', [CommunityEventController::class, 'getCalendarEvents'])->name('calendar');
    Route::get('/{event}/search', [CommunityEventController::class, 'search'])->name('search');
    Route::post('/{event}/rate', [CommunityEventController::class, 'rate'])->name('rate');
    Route::get('/{event}/export', [CommunityEventController::class, 'export'])->name('export');
    Route::get('/{event}/trending', [CommunityEventController::class, 'getTrending'])->name('trending');
    
    // Search and Filter
    Route::get('/search', [CommunityEventController::class, 'search'])->name('search');
    Route::get('/filter', [CommunityEventController::class, 'filter'])->name('filter');
    Route::get('/calendar', [CommunityEventController::class, 'getCalendarEvents'])->name('calendar');
});

// Neighborhood Reviews Routes
Route::prefix('neighborhood-reviews')->name('neighborhood-reviews.')->group(function () {
    Route::get('/', [NeighborhoodReviewController::class, 'index'])->name('index');
    Route::get('/create', [NeighborhoodReviewController::class, 'create'])->name('create');
    Route::post('/', [NeighborhoodReviewController::class, 'store'])->name('store');
    Route::get('/{review}', [NeighborhoodReviewController::class, 'show'])->name('show');
    Route::get('/{review}/edit', [NeighborhoodReviewController::class, 'edit'])->name('edit');
    Route::put('/{review}', [NeighborhoodReviewController::class, 'update'])->name('update');
    Route::delete('/{review}', [NeighborhoodReviewController::class, 'destroy'])->name('destroy');
    
    // Review Features
    Route::post('/{review}/helpful', [NeighborhoodReviewController::class, 'markHelpful'])->name('helpful');
    Route::post('/{review}/report', [NeighborhoodReviewController::class, 'report'])->name('report');
    Route::get('/{review}/statistics', [NeighborhoodReviewController::class, 'getStatistics'])->name('statistics');
    Route::get('/{review}/search', [NeighborhoodReviewController::class, 'search'])->name('search');
    Route::get('/{review}/export', [NeighborhoodReviewController::class, 'export'])->name('export');
    
    // Search and Filter
    Route::get('/search', [NeighborhoodReviewController::class, 'search'])->name('search');
    Route::get('/filter', [NeighborhoodReviewController::class, 'filter'])->name('filter');
});

// Resident Forum Routes
Route::prefix('resident-forum')->name('resident-forum.')->group(function () {
    Route::get('/', [ResidentForumController::class, 'index'])->name('index');
    Route::get('/create', [ResidentForumController::class, 'create'])->name('create');
    Route::post('/', [ResidentForumController::class, 'store'])->name('store');
    Route::get('/{post}', [ResidentForumController::class, 'show'])->name('show');
    Route::get('/{post}/edit', [ResidentForumController::class, 'edit'])->name('edit');
    Route::put('/{post}', [ResidentForumController::class, 'update'])->name('update');
    Route::delete('/{post}', [ResidentForumController::class, 'destroy'])->name('destroy');
    
    // Forum Features
    Route::post('/{post}/like', [ResidentForumController::class, 'like'])->name('like');
    Route::post('/{post}/comment', [ResidentForumController::class, 'comment'])->name('comment');
    Route::post('/{post}/share', [ResidentForumController::class, 'share'])->name('share');
    Route::post('/{post}/pin', [ResidentForumController::class, 'pin'])->name('pin');
    Route::post('/{post}/feature', [ResidentForumController::class, 'feature'])->name('feature');
    Route::get('/{post}/statistics', [ResidentForumController::class, 'getStatistics'])->name('statistics');
    Route::get('/{post}/search', [ResidentForumController::class, 'search'])->name('search');
    Route::get('/{post}/trending', [ResidentForumController::class, 'getTrending'])->name('trending');
    Route::get('/{post}/export', [ResidentForumController::class, 'export'])->name('export');
    
    // Search and Filter
    Route::get('/search', [ResidentForumController::class, 'search'])->name('search');
    Route::get('/filter', [ResidentForumController::class, 'filter'])->name('filter');
    Route::get('/trending', [ResidentForumController::class, 'getTrending'])->name('trending');
});

// Community News Routes
Route::prefix('community-news')->name('community-news.')->group(function () {
    Route::get('/', [CommunityNewsController::class, 'index'])->name('index');
    Route::get('/create', [CommunityNewsController::class, 'create'])->name('create');
    Route::post('/', [CommunityNewsController::class, 'store'])->name('store');
    Route::get('/{news}', [CommunityNewsController::class, 'show'])->name('show');
    Route::get('/{news}/edit', [CommunityNewsController::class, 'edit'])->name('edit');
    Route::put('/{news}', [CommunityNewsController::class, 'update'])->name('update');
    Route::delete('/{news}', [CommunityNewsController::class, 'destroy'])->name('destroy');
    
    // News Features
    Route::post('/{news}/like', [CommunityNewsController::class, 'like'])->name('like');
    Route::post('/{news}/comment', [CommunityNewsController::class, 'comment'])->name('comment');
    Route::post('/{news}/share', [CommunityNewsController::class, 'share'])->name('share');
    Route::post('/{news}/pin', [CommunityNewsController::class, 'pin'])->name('pin');
    Route::post('/{news}/feature', [CommunityNewsController::class, 'feature'])->name('feature');
    Route::get('/{news}/statistics', [CommunityNewsController::class, 'getStatistics'])->name('statistics');
    Route::get('/{news}/search', [CommunityNewsController::class, 'search'])->name('search');
    Route::get('/{news}/trending', [CommunityNewsController::class, 'getTrending'])->name('trending');
    Route::get('/{news}/export', [CommunityNewsController::class, 'export'])->name('export');
    Route::get('/{news}/community', [CommunityNewsController::class, 'getByCommunity'])->name('community');
    
    // Search and Filter
    Route::get('/search', [CommunityNewsController::class, 'search'])->name('search');
    Route::get('/filter', [CommunityNewsController::class, 'filter'])->name('filter');
    Route::get('/trending', [CommunityNewsController::class, 'getTrending'])->name('trending');
});

// Neighborhood Statistics Routes
Route::prefix('neighborhood-statistics')->name('neighborhood-statistics.')->group(function () {
    Route::get('/', [NeighborhoodStatisticsController::class, 'index'])->name('index');
    Route::get('/create', [NeighborhoodStatisticsController::class, 'create'])->name('create');
    Route::post('/', [NeighborhoodStatisticsController::class, 'store'])->name('store');
    Route::get('/{statistic}', [NeighborhoodStatisticsController::class, 'show'])->name('show');
    Route::get('/{statistic}/edit', [NeighborhoodStatisticsController::class, 'edit'])->name('edit');
    Route::put('/{statistic}', [NeighborhoodStatisticsController::class, 'update'])->name('update');
    Route::delete('/{statistic}', [NeighborhoodStatisticsController::class, 'destroy'])->name('destroy');
    
    // Statistics Features
    Route::get('/overview', [NeighborhoodStatisticsController::class, 'getOverview'])->name('overview');
    Route::get('/type/{type}', [NeighborhoodStatisticsController::class, 'getByType'])->name('type');
    Route::get('/neighborhood/{neighborhood}', [NeighborhoodStatisticsController::class, 'getByNeighborhood'])->name('neighborhood');
    Route::get('/trend-analysis', [NeighborhoodStatisticsController::class, 'getTrendAnalysis'])->name('trend-analysis');
    Route::get('/comparative-analysis', [NeighborhoodStatisticsController::class, 'getComparativeAnalysis'])->name('comparative-analysis');
    Route::get('/export', [NeighborhoodStatisticsController::class, 'export'])->name('export');
    Route::post('/generate-report', [NeighborhoodStatisticsController::class, 'generateReport'])->name('generate-report');
    Route::get('/recent', [NeighborhoodStatisticsController::class, 'getRecent'])->name('recent');
    
    // Search and Filter
    Route::get('/search', [NeighborhoodStatisticsController::class, 'search'])->name('search');
    Route::get('/filter', [NeighborhoodStatisticsController::class, 'filter'])->name('filter');
});

// API Routes for AJAX requests
Route::prefix('api/neighborhood')->name('api.neighborhood.')->group(function () {
    // Neighborhood API
    Route::get('/list', [NeighborhoodController::class, 'apiList'])->name('list');
    Route::get('/{neighborhood}/details', [NeighborhoodController::class, 'apiDetails'])->name('details');
    Route::get('/{neighborhood}/stats', [NeighborhoodController::class, 'apiStats'])->name('stats');
    
    // Community API
    Route::get('/communities/list', [CommunityController::class, 'apiList'])->name('communities.list');
    Route::get('/communities/{community}/details', [CommunityController::class, 'apiDetails'])->name('communities.details');
    Route::get('/communities/{community}/stats', [CommunityController::class, 'apiStats'])->name('communities.stats');
    
    // Guides API
    Route::get('/guides/list', [NeighborhoodGuideController::class, 'apiList'])->name('guides.list');
    Route::get('/guides/{guide}/details', [NeighborhoodGuideController::class, 'apiDetails'])->name('guides.details');
    
    // Businesses API
    Route::get('/businesses/list', [LocalBusinessController::class, 'apiList'])->name('businesses.list');
    Route::get('/businesses/{business}/details', [LocalBusinessController::class, 'apiDetails'])->name('businesses.details');
    Route::get('/businesses/nearby', [LocalBusinessController::class, 'apiNearby'])->name('businesses.nearby');
    
    // Amenities API
    Route::get('/amenities/list', [AmenityMapController::class, 'apiList'])->name('amenities.list');
    Route::get('/amenities/{amenity}/details', [AmenityMapController::class, 'apiDetails'])->name('amenities.details');
    Route::get('/amenities/map-data', [AmenityMapController::class, 'apiMapData'])->name('amenities.map-data');
    
    // Events API
    Route::get('/events/list', [CommunityEventController::class, 'apiList'])->name('events.list');
    Route::get('/events/{event}/details', [CommunityEventController::class, 'apiDetails'])->name('events.details');
    Route::get('/events/calendar', [CommunityEventController::class, 'apiCalendar'])->name('events.calendar');
    
    // Reviews API
    Route::get('/reviews/list', [NeighborhoodReviewController::class, 'apiList'])->name('reviews.list');
    Route::get('/reviews/{review}/details', [NeighborhoodReviewController::class, 'apiDetails'])->name('reviews.details');
    
    // Forum API
    Route::get('/forum/posts/list', [ResidentForumController::class, 'apiList'])->name('forum.posts.list');
    Route::get('/forum/posts/{post}/details', [ResidentForumController::class, 'apiDetails'])->name('forum.posts.details');
    Route::get('/forum/posts/trending', [ResidentForumController::class, 'apiTrending'])->name('forum.posts.trending');
    
    // News API
    Route::get('/news/list', [CommunityNewsController::class, 'apiList'])->name('news.list');
    Route::get('/news/{news}/details', [CommunityNewsController::class, 'apiDetails'])->name('news.details');
    Route::get('/news/trending', [CommunityNewsController::class, 'apiTrending'])->name('news.trending');
    
    // Statistics API
    Route::get('/statistics/list', [NeighborhoodStatisticsController::class, 'apiList'])->name('statistics.list');
    Route::get('/statistics/{statistic}/details', [NeighborhoodStatisticsController::class, 'apiDetails'])->name('statistics.details');
    Route::get('/statistics/overview', [NeighborhoodStatisticsController::class, 'apiOverview'])->name('statistics.overview');
});
