<?php

use App\Http\Controllers\Metaverse\MetaversePropertyController;
use App\Http\Controllers\Metaverse\VirtualLandController;
use App\Http\Controllers\Metaverse\MetaverseShowroomController;
use App\Http\Controllers\Metaverse\VirtualPropertyEventController;
use App\Http\Controllers\Metaverse\MetaverseAvatarController;
use App\Http\Controllers\Metaverse\VirtualWorldController;
use App\Http\Controllers\Metaverse\MetaversePropertyNftController;
use App\Http\Controllers\Metaverse\MetaversePropertyBuilderController;
use App\Http\Controllers\Metaverse\VirtualPropertyTourController;
use App\Http\Controllers\Metaverse\MetaversePropertyMarketplaceController;
use App\Http\Controllers\Metaverse\MetaverseTransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Metaverse Routes
|--------------------------------------------------------------------------
|
| Routes for managing metaverse real estate system
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Metaverse Properties Routes
    Route::prefix('metaverse/properties')->name('metaverse.properties')->group(function () {
        Route::get('/', [MetaversePropertyController::class, 'index'])->name('index');
        Route::get('/create', [MetaversePropertyController::class, 'create'])->name('create');
        Route::post('/', [MetaversePropertyController::class, 'store'])->name('store');
        Route::get('/{property}', [MetaversePropertyController::class, 'show'])->name('show');
        Route::get('/{property}/edit', [MetaversePropertyController::class, 'edit'])->name('edit');
        Route::put('/{property}', [MetaversePropertyController::class, 'update'])->name('update');
        Route::delete('/{property}', [MetaversePropertyController::class, 'destroy'])->name('destroy');
        
        // Property Actions
        Route::post('/{property}/toggle-status', [MetaversePropertyController::class, 'toggleStatus'])->name('toggle.status');
        Route::post('/{property}/log-visit', [MetaversePropertyController::class, 'logVisit'])->name('log.visit');
        Route::get('/{property}/analytics', [MetaversePropertyController::class, 'analytics'])->name('analytics');
        Route::post('/{property}/create-nft', [MetaversePropertyController::class, 'createNft'])->name('create.nft');
        Route::get('/{property}/download', [MetaversePropertyController::class, 'download'])->name('download');
        Route::get('/{property}/share', [MetaversePropertyController::class, 'share'])->name('share');
    });
    
    // Virtual Lands Routes
    Route::prefix('metaverse/lands')->name('metaverse.lands')->group(function () {
        Route::get('/', [VirtualLandController::class, 'index'])->name('index');
        Route::get('/create', [VirtualLandController::class, 'create'])->name('create');
        Route::post('/', [VirtualLandController::class, 'store'])->name('store');
        Route::get('/{land}', [VirtualLandController::class, 'show'])->name('show');
        Route::get('/{land}/edit', [VirtualLandController::class, 'edit'])->name('edit');
        Route::put('/{land}', [VirtualLandController::class, 'update'])->name('update');
        Route::delete('/{land}', [VirtualLandController::class, 'destroy'])->name('destroy');
        
        // Land Actions
        Route::post('/{land}/purchase', [VirtualLandController::class, 'purchase'])->name('purchase');
        Route::post('/{land}/transfer', [VirtualLandController::class, 'transfer'])->name('transfer');
        Route::post('/{land}/develop', [VirtualLandController::class, 'develop'])->name('develop');
        Route::get('/{land}/valuation', [VirtualLandController::class, 'valuation'])->name('valuation');
        Route::get('/{land}/analytics', [VirtualLandController::class, 'analytics'])->name('analytics');
        Route::get('/{land}/neighbors', [VirtualLandController::class, 'neighbors'])->name('neighbors');
        Route::get('/{land}/download', [VirtualLandController::class, 'download'])->name('download');
        Route::get('/{land}/share', [VirtualLandController::class, 'share'])->name('share');
    });
    
    // Metaverse Showrooms Routes
    Route::prefix('metaverse/showrooms')->name('metaverse.showrooms')->group(function () {
        Route::get('/', [MetaverseShowroomController::class, 'index'])->name('index');
        Route::get('/create', [MetaverseShowroomController::class, 'create'])->name('create');
        Route::post('/', [MetaverseShowroomController::class, 'store'])->name('store');
        Route::get('/{showroom}', [MetaverseShowroomController::class, 'show'])->name('show');
        Route::get('/{showroom}/edit', [MetaverseShowroomController::class, 'edit'])->name('edit');
        Route::put('/{showroom}', [MetaverseShowroomController::class, 'update'])->name('update');
        Route::delete('/{showroom}', [MetaverseShowroomController::class, 'destroy'])->name('destroy');
        
        // Showroom Actions
        Route::post('/{showroom}/enter', [MetaverseShowroomController::class, 'enter'])->name('enter');
        Route::post('/{showroom}/exit', [MetaverseShowroomController::class, 'exit'])->name('exit');
        Route::post('/{showroom}/configure', [MetaverseShowroomController::class, 'configure'])->name('configure');
        Route::get('/{showroom}/analytics', [MetaverseShowroomController::class, 'analytics'])->name('analytics');
        Route::post('/{showroom}/upload-images', [MetaverseShowroomController::class, 'uploadImages'])->name('upload.images');
        Route::post('/{showroom}/upload-models', [MetaverseShowroomController::class, 'uploadModels'])->name('upload.models');
        Route::get('/{showroom}/download', [MetaverseShowroomController::class, 'download'])->name('download');
        Route::get('/{showroom}/share', [MetaverseShowroomController::class, 'share'])->name('share');
    });
    
    // Virtual Property Events Routes
    Route::prefix('metaverse/events')->name('metaverse.events')->group(function () {
        Route::get('/', [VirtualPropertyEventController::class, 'index'])->name('index');
        Route::get('/create', [VirtualPropertyEventController::class, 'create'])->name('create');
        Route::post('/', [VirtualPropertyEventController::class, 'store'])->name('store');
        Route::get('/{event}', [VirtualPropertyEventController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [VirtualPropertyEventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [VirtualPropertyEventController::class, 'update'])->name('update');
        Route::delete('/{event}', [VirtualPropertyEventController::class, 'destroy'])->name('destroy');
        
        // Event Actions
        Route::post('/{event}/register', [VirtualPropertyEventController::class, 'register'])->name('register');
        Route::post('/{event}/cancel', [VirtualPropertyEventController::class, 'cancel'])->name('cancel');
        Route::post('/{event}/join', [VirtualPropertyEventController::class, 'join'])->name('join');
        Route::post('/{event}/leave', [VirtualPropertyEventController::class, 'leave'])->name('leave');
        Route::get('/{event}/analytics', [VirtualPropertyEventController::class, 'analytics'])->name('analytics');
        Route::post('/{event}/upload-media', [VirtualPropertyEventController::class, 'uploadMedia'])->name('upload.media');
        Route::get('/{event}/download', [VirtualPropertyEventController::class, 'download'])->name('download');
        Route::get('/{event}/share', [VirtualPropertyEventController::class, 'share'])->name('share');
    });
    
    // Metaverse Avatars Routes
    Route::prefix('metaverse/avatars')->name('metaverse.avatars')->group(function () {
        Route::get('/', [MetaverseAvatarController::class, 'index'])->name('index');
        Route::get('/create', [MetaverseAvatarController::class, 'create'])->name('create');
        Route::post('/', [MetaverseAvatarController::class, 'store'])->name('store');
        Route::get('/{avatar}', [MetaverseAvatarController::class, 'show'])->name('show');
        Route::get('/{avatar}/edit', [MetaverseAvatarController::class, 'edit'])->name('edit');
        Route::put('/{avatar}', [MetaverseAvatarController::class, 'update'])->name('update');
        Route::delete('/{avatar}', [MetaverseAvatarController::class, 'destroy'])->name('destroy');
        
        // Avatar Actions
        Route::post('/{avatar}/update-online-status', [MetaverseAvatarController::class, 'updateOnlineStatus'])->name('update.online.status');
        Route::post('/{avatar}/update-location', [MetaverseAvatarController::class, 'updateLocation'])->name('update.location');
        Route::post('/{avatar}/add-friend', [MetaverseAvatarController::class, 'addFriend'])->name('add.friend');
        Route::post('/{avatar}/accept-friend/{requestId}', [MetaverseAvatarController::class, 'acceptFriendRequest'])->name('accept.friend.request');
        Route::post('/{avatar}/reject-friend/{requestId}', [MetaverseAvatarController::class, 'rejectFriendRequest'])->name('reject.friend.request');
        Route::post('/{avatar}/remove-friend/{friendId}', [MetaverseAvatarController::class, 'removeFriend'])->name('remove.friend');
        Route::post('/{avatar}/equip-item', [MetaverseAvatarController::class, 'equipItem'])->name('equip.item');
        Route::post('/{avatar}/unequip-item', [MetaverseAvatarController::class, 'unequipItem'])->name('unequip.item');
        Route::get('/{avatar}/inventory', [MetaverseAvatarController::class, 'inventory'])->name('inventory');
        Route::get('/{avatar}/friends', [MetaverseAvatarController::class, 'friends'])->name('friends');
        Route::get('/{avatar}/social-stats', [MetaverseAvatarController::class, 'socialStats'])->name('social.stats');
        Route::get('/{avatar}/activity-stats', [MetaverseAvatarController::class, 'activityStats'])->name('activity.stats');
        Route::post('/{avatar}/upload-image', [MetaverseAvatarController::class, 'uploadImage'])->name('upload.image');
        Route::post('/{avatar}/upload-model', [MetaverseAvatarController::class, 'uploadModel'])->name('upload.model');
        Route::get('/{avatar}/download', [MetaverseAvatarController::class, 'download'])->name('download');
        Route::get('/{avatar}/share', [MetaverseAvatarController::class, 'share'])->name('share');
    });
    
    // Virtual Worlds Routes
    Route::prefix('metaverse/worlds')->name('metaverse.worlds')->group(function () {
        Route::get('/', [VirtualWorldController::class, 'index'])->name('index');
        Route::get('/create', [VirtualWorldController::class, 'create'])->name('create');
        Route::post('/', [VirtualWorldController::class, 'store'])->name('store');
        Route::get('/{world}', [VirtualWorldController::class, 'show'])->name('show');
        Route::get('/{world}/edit', [VirtualWorldController::class, 'edit'])->name('edit');
        Route::put('/{world}', [VirtualWorldController::class, 'update'])->name('update');
        Route::delete('/{world}', [VirtualWorldController::class, 'destroy'])->name('destroy');
        
        // World Actions
        Route::post('/{world}/launch', [VirtualWorldController::class, 'launch'])->name('launch');
        Route::post('/{world}/suspend', [VirtualWorldController::class, 'suspend'])->name('suspend');
        Route::post('/{world}/archive', [VirtualWorldController::class, 'archive'])->name('archive');
        Route::get('/{world}/analytics', [VirtualWorldController::class, 'analytics'])->name('analytics');
        Route::get('/{world}/statistics', [VirtualWorldController::class, 'statistics'])->name('statistics');
        Route::get('/{world}/map-data', [VirtualWorldController::class, 'mapData'])->name('map.data');
        Route::get('/{world}/properties', [VirtualWorldController::class, 'properties'])->name('properties');
        Route::get('/{world}/lands', [VirtualWorldController::class, 'lands'])->name('lands');
        Route::get('/{world}/showrooms', [VirtualWorldController::class, 'showrooms'])->name('showrooms');
        Route::get('/{world}/avatars', [VirtualWorldController::class, 'avatars'])->name('avatars');
        Route::get('/{world}/download', [VirtualWorldController::class, 'download'])->name('download');
        Route::get('/{world}/share', [VirtualWorldController::class, 'share'])->name('share');
    });
    
    // Metaverse Property NFTs Routes
    Route::prefix('metaverse/nfts')->name('metaverse.nfts')->group(function () {
        Route::get('/', [MetaversePropertyNftController::class, 'index'])->name('index');
        Route::get('/create', [MetaversePropertyNftController::class, 'create'])->name('create');
        Route::post('/', [MetaversePropertyNftController::class, 'store'])->name('store');
        Route::get('/{nft}', [MetaversePropertyNftController::class, 'show'])->name('show');
        Route::get('/{nft}/edit', [MetaversePropertyNftController::class, 'edit'])->name('edit');
        Route::put('/{nft}', [MetaversePropertyNftController::class, 'update'])->name('update');
        Route::delete('/{nft}', [MetaversePropertyNftController::class, 'destroy'])->name('destroy');
        
        // NFT Actions
        Route::post('/{nft}/place-bid', [MetaversePropertyNftController::class, 'placeBid'])->name('place.bid');
        Route::post('/{nft}/accept-bid/{bidId}', [MetaversePropertyNftController::class, 'acceptBid'])->name('accept.bid');
        Route::post('/{nft}/start-auction', [MetaversePropertyNftController::class, 'startAuction'])->name('start.auction');
        Route::post('/{nft}/end-auction', [MetaversePropertyNftController::class, 'endAuction'])->name('end.auction');
        Route::post('/{nft}/transfer', [MetaversePropertyNftController::class, 'transfer'])->name('transfer');
        Route::get('/{nft}/metadata', [MetaversePropertyNftController::class, 'getMetadata'])->name('get.metadata');
        Route::get('/{nft}/verify', [MetaversePropertyNftController::class, 'verify'])->name('verify');
        Route::get('/{nft}/flag', [MetaversePropertyNftController::class, 'flag'])->name('flag');
        Route::post('/{nft}/burn', [MetaversePropertyNftController::class, 'burn'])->name('burn');
        Route::get('/{nft}/market-data', [MetaversePropertyNftController::class, 'getMarketData'])->name('get.market.data');
        Route::get('/{nft}/ownership-history', [MetaversePropertyNftController::class, 'getOwnershipHistory'])->name('get.ownership.history');
        Route::get('/{nft}/bid-history', [MetaversePropertyNftController::class, 'getBidHistory'])->name('get.bid.history');
        Route::get('/{nft}/analytics', [MetaversePropertyNftController::class, 'analytics'])->name('analytics');
        Route::get('/{nft}/download', [MetaversePropertyNftController::class, 'download'])->name('download');
        Route::get('/{nft}/share', [MetaversePropertyNftController::class, 'share'])->name('share');
    });
    
    // Virtual Property Builder Routes
    Route::prefix('metaverse/builder')->name('metaverse.builder')->group(function () {
        Route::get('/', [MetaversePropertyBuilderController::class, 'index'])->name('index');
        Route::get('/create', [MetaversePropertyBuilderController::class, 'create'])->name('create');
        Route::post('/', [MetaversePropertyBuilderController::class, 'store'])->name('store');
        Route::get('/{design}', [MetaversePropertyBuilderController::class, 'show'])->name('show');
        Route::get('/{design}/edit', [MetaversePropertyBuilderController::class, 'edit'])->name('edit');
        Route::put('/{design}', [MetaversePropertyBuilderController::class, 'update'])->name('update');
        Route::delete('/{design}', [MetaversePropertyBuilderController::class, 'destroy'])->name('destroy');
        
        // Builder Actions
        Route::post('/{design}/build', [MetaversePropertyBuilderController::class, 'build'])->name('build');
        Route::post('/{design}/clone', [MetaversePropertyBuilderController::class, 'clone'])->name('clone');
        Route::post('/{design}/publish', [MetaversePropertyBuilderController::class, 'publish'])->name('publish');
        Route::post('/{design}/archive', [MetaversePropertyBuilderController::class, 'archive'])->name('archive');
        Route::post('/{design}/download', [MetaversePropertyBuilderController::class, 'download'])->name('download');
        Route::post('/{design}/share', [MetaversePropertyBuilderController::class, 'share'])->name('share');
        Route::get('/{design}/workspace', [MetaversePropertyBuilderController::class, 'workspace'])->name('workspace');
        Route::post('/{design}/save-progress', [MetaversePropertyBuilderController::class, 'saveProgress'])->name('save.progress');
        Route::get('/{design}/analytics', [MetaversePropertyBuilderController::class, 'analytics'])->name('analytics');
        Route::post('/{design}/upload-blueprint', [MetaversePropertyBuilderController::class, 'uploadBlueprint'])->name('upload.blueprint');
        Route::post('/{design}/upload-model', [MetaversePropertyBuilderController::class, 'uploadModel'])->name('upload.model');
        Route::post('/{design}/upload-texture', [MetaversePropertyBuilderController::class, 'uploadTexture'])->name('upload.texture');
        Route::get('/{design}/usage-stats', [MetaversePropertyBuilderController::class, 'usageStats'])->name('usage.stats');
    });
    
    // Virtual Property Tours Routes
    Route::prefix('metaverse/tours')->name('metaverse.tours')->group(function () {
        Route::get('/', [VirtualPropertyTourController::class, 'index'])->name('index');
        Route::get('/create', [VirtualPropertyTourController::class, 'create'])->name('create');
        Route::post('/', [VirtualPropertyTourController::class, 'store'])->name('store');
        Route::get('/{tour}', [VirtualPropertyTourController::class, 'show'])->name('show');
        Route::get('/{tour}/edit', [VirtualPropertyTourController::class, 'edit'])->name('edit');
        Route::put('/{tour}', [VirtualPropertyTourController::class, 'update'])->name('update');
        Route::delete('/{tour}', [VirtualPropertyTourController::class, 'destroy'])->name('destroy');
        
        // Tour Actions
        Route::post('/{tour}/book', [VirtualPropertyTourController::class, 'book'])->name('book');
        Route::post('/{tour}/start', [VirtualPropertyTourController::class, 'start'])->name('start');
        Route::post('/{tour}/update-progress', [VirtualPropertyTourController::class, 'updateProgress'])->name('update.progress');
        Route::post('/{tour}/complete', [VirtualPropertyTourController::class, 'complete'])->name('complete');
        Route::get('/{tour}/analytics', [VirtualPropertyTourController::class, 'analytics'])->name('analytics');
        Route::get('/{tour}/statistics', [VirtualPropertyTourController::class, 'statistics'])->name('statistics');
        Route::get('/{tour}/available-slots', [VirtualPropertyTourController::class, 'getAvailableSlots'])->name('get.available.slots');
        Route::post('/{tour}/upload-images', [VirtualPropertyTourController::class, 'uploadImages'])->name('upload.images');
        Route::post('/{tour}/upload-media', [VirtualPropertyTourController::class, 'uploadMedia'])->name('upload.media');
        Route::get('/{tour}/download', [VirtualPropertyTourController::class, 'download'])->name('download');
        Route::get('/{tour}/share', [VirtualPropertyTourController::class, 'share'])->name('share');
    });
    
    // Metaverse Marketplace Routes
    Route::prefix('metaverse/marketplace')->name('metaverse.marketplace')->group(function () {
        Route::get('/', [MetaversePropertyMarketplaceController::class, 'index'])->name('index');
        Route::get('/create', [MetaversePropertyMarketplaceController::class, 'create'])->name('create');
        Route::get('/property/{property}', [MetaversePropertyMarketplaceController::class, 'showProperty'])->name('show.property');
        Route::get('/land/{land}', [MetaversePropertyMarketplaceController::class, 'showLand'])->name('show.land');
        Route::get('/nft/{nft}', [MetaversePropertyMarketplaceController::class, 'showNft'])->name('show.nft');
        Route::get('/tour/{tour}', [MetaversePropertyMarketplaceController::class, 'showTour'])->name('show.tour');
        
        // Marketplace Actions
        Route::post('/property/{property}/make-offer', [MetaversePropertyMarketplaceController::class, 'makePropertyOffer'])->name('make.property.offer');
        Route::post('/land/{land}/make-offer', [MetaversePropertyMarketplaceController::class, 'makeLandOffer'])->name('make.land.offer');
        Route::post('/property/{property}/purchase', [MetaversePropertyMarketplaceController::class, 'purchaseProperty'])->name('purchase.property');
        Route::post('/land/{land}/purchase', [MetaversePropertyMarketplaceController::class, 'purchaseLand'])->name('purchase.land');
        Route::post('/nft/{nft}/place-bid', [MetaversePropertyMarketplaceController::class, 'placeNftBid'])->name('place.nft.bid');
        Route::post('/tour/{tour}/book', [MetaversePropertyMarketplaceController::class, 'bookTour'])->name('book.tour');
        Route::get('/analytics', [MetaversePropertyMarketplaceController::class, 'analytics'])->name('analytics');
        Route::get('/statistics', [MetaversePropertyMarketplaceController::class, 'statistics'])->name('statistics');
        Route::get('/market-data', [MetaversePropertyMarketplaceController::class, 'getMarketData'])->name('get.market.data');
        Route::get('/price-trends', [MetaversePropertyMarketplaceController::class, 'getPriceTrends'])->name('get.price.trends');
    });
});

// Public Routes (no authentication required)
Route::prefix('metaverse')->name('metaverse')->group(function () {
    // Public property portal
    Route::get('/properties', [MetaversePropertyController::class, 'publicIndex'])->name('properties.public');
    Route::get('/properties/{property}', [MetaversePropertyController::class, 'publicShow'])->name('properties.public.show');
    
    // Public virtual lands
    Route::get('/lands', [VirtualLandController::class, 'publicIndex'])->name('lands.public');
    Route::get('/lands/{land}', [VirtualLandController::class, 'publicShow'])->name('lands.public.show');
    
    // Public showrooms
    Route::get('/showrooms', [MetaverseShowroomController::class, 'publicIndex'])->name('showrooms.public');
    Route::get('/showrooms/{showroom}', [MetaverseShowroomController::class, 'publicShow'])->name('showrooms.public.show');
    
    // Public tours
    Route::get('/tours', [VirtualPropertyTourController::class, 'publicIndex'])->name('tours.public');
    Route::get('/tours/{tour}', [VirtualPropertyTourController::class, 'publicShow'])->name('tours.public.show');
    
    // Public marketplace
    Route::get('/marketplace', [MetaversePropertyMarketplaceController::class, 'publicIndex'])->name('marketplace.public');
    Route::get('/marketplace/property/{property}', [MetaversePropertyMarketplaceController::class, 'publicShowProperty'])->name('marketplace.public.show.property');
    Route::get('/marketplace/land/{land}', [MetaversePropertyMarketplaceController::class, 'publicShowLand'])->name('marketplace.public.show.land');
    Route::get('/marketplace/nft/{nft}', [MetaversePropertyMarketplaceController::class, 'publicShowNft'])->name('marketplace.public.show.nft');
    
    // Public worlds
    Route::get('/worlds', [VirtualWorldController::class, 'publicIndex'])->name('worlds.public');
    Route::get('/worlds/{world}', [VirtualWorldController::class, 'publicShow'])->name('worlds.public.show');
});

// API Routes
Route::prefix('api/metaverse')->name('api.metaverse')->group(function () {
    Route::get('/stats', [MetaversePropertyController::class, 'getStats'])->name('stats');
    Route::get('/marketplace/stats', [MetaversePropertyMarketplaceController::class, 'getStats'])->name('marketplace.stats');
    Route::get('/builder/stats', [MetaversePropertyBuilderController::class, 'getStats'])->name('builder.stats');
    Route::get('/tours/available-slots/{tourId}', [VirtualPropertyTourController::class, 'getAvailableSlots'])->name('tours.available.slots');
    
    // Property API
    Route::get('/properties/search', [MetaversePropertyController::class, 'search'])->name('properties.search');
    Route::get('/properties/{property}/similar', [MetaversePropertyController::class, 'getSimilar'])->name('properties.similar');
    Route::get('/properties/{property}/market-data', [MetaversePropertyController::class, 'getMarketData'])->name('properties.market.data');
    
    // Land API
    Route::get('/lands/search', [VirtualLandController::class, 'search'])->name('lands.search');
    Route::get('/lands/{land}/similar', [VirtualLandController::class, 'getSimilar'])->name('lands.similar');
    Route::get('/lands/{land}/market-data', [VirtualLandController::class, 'getMarketData'])->name('lands.market.data');
    
    // NFT API
    Route::get('/nfts/search', [MetaversePropertyNftController::class, 'search'])->name('nfts.search');
    Route::get('/nfts/{nft}/market-data', [MetaversePropertyNftController::class, 'getMarketData'])->name('nfts.market.data');
    
    // Tour API
    Route::get('/tours/search', [VirtualPropertyTourController::class, 'search'])->name('tours.search');
    Route::get('/tours/{tour}/statistics', [VirtualPropertyTourController::class, 'statistics'])->name('tours.statistics');
    
    // Avatar API
    Route::get('/avatars/online', [MetaverseAvatarController::class, 'online'])->name('avatars.online');
    Route::get('/avatars/{avatar}/friends', [MetaverseAvatarController::class, 'getFriends'])->name('avatars.friends');
    
    // World API
    Route::get('/worlds/active', [VirtualWorldController::class, 'active'])->name('worlds.active');
    Route::get('/worlds/{world}/map-data', [VirtualWorldController::class, 'getMapData'])->name('worlds.map.data');
    
    // Transaction API
    Route::get('/transactions/recent', [MetaverseTransactionController::class, 'recent'])->name('transactions.recent');
    Route::get('/transactions/volume', [MetaverseTransactionController::class, 'getVolume'])->name('transactions.volume');
});
