<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Defi\CryptoPropertyPaymentController;
use App\Http\Controllers\Defi\DefiPropertyInvestmentController;
use App\Http\Controllers\Defi\DefiPropertyLoanController;
use App\Http\Controllers\Defi\DefiPropertyStakingController;
use App\Http\Controllers\Defi\DefiPropertyYieldController;
use App\Http\Controllers\Defi\PropertyDaoController;
use App\Http\Controllers\Defi\PropertyFractionalOwnershipController;
use App\Http\Controllers\Defi\PropertyLiquidityPoolController;
use App\Http\Controllers\Defi\PropertyTokenizationController;

Route::middleware(['auth'])->prefix('defi')->name('defi.')->group(function () {
    Route::get('/payments', [CryptoPropertyPaymentController::class, 'index'])->name('payments.index');
    Route::get('/investments', [DefiPropertyInvestmentController::class, 'index'])->name('investments.index');
    Route::get('/loans', [DefiPropertyLoanController::class, 'index'])->name('loans.index');
    Route::get('/staking', [DefiPropertyStakingController::class, 'index'])->name('staking.index');
    Route::get('/yield', [DefiPropertyYieldController::class, 'index'])->name('yield.index');
    Route::get('/dao', [PropertyDaoController::class, 'index'])->name('dao.index');
    Route::get('/fractional', [PropertyFractionalOwnershipController::class, 'index'])->name('fractional.index');
    Route::get('/liquidity', [PropertyLiquidityPoolController::class, 'index'])->name('liquidity.index');
    Route::get('/tokenization', [PropertyTokenizationController::class, 'index'])->name('tokenization.index');
});
