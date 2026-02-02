<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Metaverse\MetaversePropertyController;
use App\Http\Controllers\Metaverse\MetaverseAvatarController;
use App\Http\Controllers\Metaverse\MetaversePropertyMarketplaceController;
use App\Http\Controllers\Metaverse\MetaversePropertyNftController;
use App\Http\Controllers\Metaverse\MetaverseShowroomController;
use App\Http\Controllers\Metaverse\MetaverseTransactionController;
use App\Http\Controllers\Metaverse\MetaversePropertyBuilderController;
use App\Http\Controllers\MetaverseController;

Route::middleware(['auth'])->prefix('metaverse')->name('metaverse.')->group(function () {
    Route::get('/', [MetaversePropertyController::class, 'index'])->name('index');
    Route::resource('/properties', MetaversePropertyController::class);
    
    // Marketplace
    Route::get('/marketplace', [MetaversePropertyMarketplaceController::class, 'index'])->name('marketplace.index');
    
    // NFTs
    Route::get('/nfts', [MetaversePropertyNftController::class, 'index'])->name('nfts.index');
    Route::post('/nfts/{property}/mint', [MetaversePropertyNftController::class, 'mint'])->name('nfts.mint');
    
    // Showroom & Builder
    Route::get('/showroom', [MetaverseShowroomController::class, 'index'])->name('showroom.index');
    Route::get('/builder', [MetaversePropertyBuilderController::class, 'index'])->name('builder.index');
    
    // Avatar
    Route::resource('/avatars', MetaverseAvatarController::class);
    
    // Transactions
    Route::get('/transactions', [MetaverseTransactionController::class, 'index'])->name('transactions.index');
});

// Blockchain Metaverse Routes
Route::middleware(['auth'])->prefix('blockchain/metaverse')->name('blockchain.metaverse.')->group(function () {
    Route::get('/properties', [MetaverseController::class, 'properties'])->name('properties');
    Route::get('/marketplace', [MetaverseController::class, 'marketplace'])->name('marketplace');
    Route::get('/nft', [MetaverseController::class, 'nft'])->name('nft');
});
