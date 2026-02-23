<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuctionResultController;

Route::middleware(['auth'])->prefix('auctions')->name('auctions.')->group(function () {
    Route::get('/', [AuctionController::class, 'index'])->name('index');
    Route::get('/create', [AuctionController::class, 'create'])->name('create');
    Route::post('/', [AuctionController::class, 'store'])->name('store');
    Route::get('/my-auctions', [AuctionController::class, 'myAuctions'])->name('my');

    // Results routes
    Route::get('/results', [AuctionResultController::class, 'index'])->name('results');
    Route::get('/my-results', [AuctionResultController::class, 'myResults'])->name('my-results');
    Route::get('/results/{id}', [AuctionResultController::class, 'show'])->name('results.show');
    Route::post('/{auctionId}/results', [AuctionResultController::class, 'create'])->name('results.create');
    Route::post('/results/{id}/confirm', [AuctionResultController::class, 'confirmWinner'])->name('results.confirm');
    Route::post('/results/{id}/reject', [AuctionResultController::class, 'rejectWinner'])->name('results.reject');
    Route::get('/results/{id}/download', [AuctionResultController::class, 'downloadReport'])->name('results.download');

    // Single auction routes
    Route::get('/{id}', [AuctionController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [AuctionController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AuctionController::class, 'update'])->name('update');

    // Actions
    Route::post('/{id}/start', [AuctionController::class, 'start'])->name('start');
    Route::post('/{id}/end', [AuctionController::class, 'end'])->name('end');
    Route::post('/{id}/cancel', [AuctionController::class, 'cancel'])->name('cancel');
});
