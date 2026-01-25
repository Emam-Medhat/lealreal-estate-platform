<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlockchainController;
use App\Http\Controllers\BlockchainVerificationController;
use App\Http\Controllers\CryptoTransactionController;
use App\Http\Controllers\CryptoWalletController;
use App\Http\Controllers\DaoController;
use App\Http\Controllers\DefiController;
use App\Http\Controllers\LiquidityPoolController;

Route::middleware(['auth'])->prefix('blockchain')->name('blockchain.')->group(function () {
    Route::get('/', [BlockchainController::class, 'index'])->name('index');
    Route::post('/records', [BlockchainController::class, 'createRecord'])->name('records.create');
    Route::get('/records', [BlockchainController::class, 'getRecords'])->name('records.index');
    Route::get('/block', [BlockchainController::class, 'getBlock'])->name('block.show');
    Route::get('/latest-block', [BlockchainController::class, 'getLatestBlock'])->name('block.latest');

    // Verification
    Route::resource('/verification', BlockchainVerificationController::class);
    
    // Crypto Wallet & Transactions
    Route::resource('/wallets', CryptoWalletController::class);
    Route::resource('/transactions', CryptoTransactionController::class);
    
    // DAO & DeFi
    Route::get('/dao', [DaoController::class, 'index'])->name('dao.index');
    Route::get('/defi', [DefiController::class, 'index'])->name('defi.index');
    Route::get('/liquidity-pools', [LiquidityPoolController::class, 'index'])->name('liquidity-pools.index');
});
