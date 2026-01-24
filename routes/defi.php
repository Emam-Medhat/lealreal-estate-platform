<?php

use App\Http\Controllers\Defi\DefiPropertyLoanController;
use App\Http\Controllers\Defi\PropertyTokenizationController;
use App\Http\Controllers\Defi\DefiPropertyStakingController;
use App\Http\Controllers\Defi\PropertyFractionalOwnershipController;
use App\Http\Controllers\Defi\DefiPropertyInvestmentController;
use App\Http\Controllers\Defi\PropertyLiquidityPoolController;
use App\Http\Controllers\Defi\DefiPropertyYieldController;
use App\Http\Controllers\Defi\PropertyDaoController;
use App\Http\Controllers\Defi\CryptoPropertyPaymentController;

/*
|--------------------------------------------------------------------------
| DeFi Real Estate System Routes
|--------------------------------------------------------------------------
|
| Routes for the DeFi Real Estate System including property loans,
| tokenization, staking, fractional ownership, investments, liquidity pools,
| yields, DAO, and crypto payments.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Main DeFi Dashboard
    Route::get('/defi', function () {
        return view('defi.index', [
            'stats' => [
                'active_loans' => \App\Models\Defi\DefiPropertyLoan::active()->count(),
                'total_tokens' => \App\Models\Defi\PropertyToken::count(),
                'liquidity_pools' => \App\Models\Defi\PropertyLiquidityPool::active()->count(),
                'total_investments' => \App\Models\Defi\DefiPropertyInvestment::active()->count(),
            ],
            'recentActivity' => collect([]) // This would be populated with actual activity data
        ]);
    })->name('defi.index');

    // Property Loans Routes
    Route::prefix('defi/loans')->name('defi.loans.')->group(function () {
        Route::get('/', [DefiPropertyLoanController::class, 'index'])->name('index');
        Route::get('/create', [DefiPropertyLoanController::class, 'create'])->name('create');
        Route::post('/', [DefiPropertyLoanController::class, 'store'])->name('store');
        Route::get('/{loan}', [DefiPropertyLoanController::class, 'show'])->name('show');
        Route::get('/{loan}/edit', [DefiPropertyLoanController::class, 'edit'])->name('edit');
        Route::put('/{loan}', [DefiPropertyLoanController::class, 'update'])->name('update');
        Route::delete('/{loan}', [DefiPropertyLoanController::class, 'destroy'])->name('destroy');
        
        // Loan Actions
        Route::post('/{loan}/approve', [DefiPropertyLoanController::class, 'approve'])->name('approve');
        Route::post('/{loan}/reject', [DefiPropertyLoanController::class, 'reject'])->name('reject');
        Route::post('/{loan}/repay', [DefiPropertyLoanController::class, 'repay'])->name('repay');
        Route::get('/analytics', [DefiPropertyLoanController::class, 'analytics'])->name('analytics');
        Route::get('/marketplace', [DefiPropertyLoanController::class, 'marketplace'])->name('marketplace');
    });

    // Property Tokenization Routes
    Route::prefix('defi/tokens')->name('defi.tokens.')->group(function () {
        Route::get('/', [PropertyTokenizationController::class, 'index'])->name('index');
        Route::get('/create', [PropertyTokenizationController::class, 'create'])->name('create');
        Route::post('/', [PropertyTokenizationController::class, 'store'])->name('store');
        Route::get('/{token}', [PropertyTokenizationController::class, 'show'])->name('show');
        Route::get('/{token}/edit', [PropertyTokenizationController::class, 'edit'])->name('edit');
        Route::put('/{token}', [PropertyTokenizationController::class, 'update'])->name('update');
        Route::delete('/{token}', [PropertyTokenizationController::class, 'destroy'])->name('destroy');
        
        // Token Actions
        Route::post('/{token}/mint', [PropertyTokenizationController::class, 'mint'])->name('mint');
        Route::post('/{token}/transfer', [PropertyTokenizationController::class, 'transfer'])->name('transfer');
        Route::post('/{token}/burn', [PropertyTokenizationController::class, 'burn'])->name('burn');
        Route::get('/analytics', [PropertyTokenizationController::class, 'analytics'])->name('analytics');
        Route::get('/marketplace', [PropertyTokenizationController::class, 'marketplace'])->name('marketplace');
    });

    // Property Staking Routes
    Route::prefix('defi/staking')->name('defi.staking.')->group(function () {
        Route::get('/', [DefiPropertyStakingController::class, 'index'])->name('index');
        Route::get('/create', [DefiPropertyStakingController::class, 'create'])->name('create');
        Route::post('/', [DefiPropertyStakingController::class, 'store'])->name('store');
        Route::get('/{staking}', [DefiPropertyStakingController::class, 'show'])->name('show');
        Route::get('/{staking}/edit', [DefiPropertyStakingController::class, 'edit'])->name('edit');
        Route::put('/{staking}', [DefiPropertyStakingController::class, 'update'])->name('update');
        Route::delete('/{staking}', [DefiPropertyStakingController::class, 'destroy'])->name('destroy');
        
        // Staking Actions
        Route::post('/{staking}/unstake', [DefiPropertyStakingController::class, 'unstake'])->name('unstake');
        Route::post('/{staking}/compound', [DefiPropertyStakingController::class, 'compound'])->name('compound');
        Route::get('/analytics', [DefiPropertyStakingController::class, 'analytics'])->name('analytics');
        Route::get('/marketplace', [DefiPropertyStakingController::class, 'marketplace'])->name('marketplace');
    });

    // Fractional Ownership Routes
    Route::prefix('defi/fractional')->name('defi.fractional.')->group(function () {
        Route::get('/', [PropertyFractionalOwnershipController::class, 'index'])->name('index');
        Route::get('/create', [PropertyFractionalOwnershipController::class, 'create'])->name('create');
        Route::post('/', [PropertyFractionalOwnershipController::class, 'store'])->name('store');
        Route::get('/{ownership}', [PropertyFractionalOwnershipController::class, 'show'])->name('show');
        Route::get('/{ownership}/edit', [PropertyFractionalOwnershipController::class, 'edit'])->name('edit');
        Route::put('/{ownership}', [PropertyFractionalOwnershipController::class, 'update'])->name('update');
        Route::delete('/{ownership}', [PropertyFractionalOwnershipController::class, 'destroy'])->name('destroy');
        
        // Ownership Actions
        Route::post('/{ownership}/buy', [PropertyFractionalOwnershipController::class, 'buy'])->name('buy');
        Route::post('/{ownership}/sell', [PropertyFractionalOwnershipController::class, 'sell'])->name('sell');
        Route::post('/{ownership}/claim-dividends', [PropertyFractionalOwnershipController::class, 'claimDividends'])->name('claim-dividends');
        Route::get('/analytics', [PropertyFractionalOwnershipController::class, 'analytics'])->name('analytics');
        Route::get('/marketplace', [PropertyFractionalOwnershipController::class, 'marketplace'])->name('marketplace');
    });

    // Property Investments Routes
    Route::prefix('defi/investments')->name('defi.investments.')->group(function () {
        Route::get('/', [DefiPropertyInvestmentController::class, 'index'])->name('index');
        Route::get('/create', [DefiPropertyInvestmentController::class, 'create'])->name('create');
        Route::post('/', [DefiPropertyInvestmentController::class, 'store'])->name('store');
        Route::get('/{investment}', [DefiPropertyInvestmentController::class, 'show'])->name('show');
        Route::get('/{investment}/edit', [DefiPropertyInvestmentController::class, 'edit'])->name('edit');
        Route::put('/{investment}', [DefiPropertyInvestmentController::class, 'update'])->name('update');
        Route::delete('/{investment}', [DefiPropertyInvestmentController::class, 'destroy'])->name('destroy');
        
        // Investment Actions
        Route::post('/{investment}/withdraw', [DefiPropertyInvestmentController::class, 'withdraw'])->name('withdraw');
        Route::post('/{investment}/reinvest', [DefiPropertyInvestmentController::class, 'reinvest'])->name('reinvest');
        Route::get('/analytics', [DefiPropertyInvestmentController::class, 'analytics'])->name('analytics');
        Route::get('/marketplace', [DefiPropertyInvestmentController::class, 'marketplace'])->name('marketplace');
    });

    // Liquidity Pools Routes
    Route::prefix('defi/pools')->name('defi.pools.')->group(function () {
        Route::get('/', [PropertyLiquidityPoolController::class, 'index'])->name('index');
        Route::get('/create', [PropertyLiquidityPoolController::class, 'create'])->name('create');
        Route::post('/', [PropertyLiquidityPoolController::class, 'store'])->name('store');
        Route::get('/{pool}', [PropertyLiquidityPoolController::class, 'show'])->name('show');
        Route::get('/{pool}/edit', [PropertyLiquidityPoolController::class, 'edit'])->name('edit');
        Route::put('/{pool}', [PropertyLiquidityPoolController::class, 'update'])->name('update');
        Route::delete('/{pool}', [PropertyLiquidityPoolController::class, 'destroy'])->name('destroy');
        
        // Pool Actions
        Route::post('/{pool}/deploy', [PropertyLiquidityPoolController::class, 'deploy'])->name('deploy');
        Route::post('/{pool}/add-liquidity', [PropertyLiquidityPoolController::class, 'addLiquidity'])->name('add-liquidity');
        Route::post('/{pool}/remove-liquidity', [PropertyLiquidityPoolController::class, 'removeLiquidity'])->name('remove-liquidity');
        Route::post('/{pool}/compound', [PropertyLiquidityPoolController::class, 'compound'])->name('compound');
        Route::post('/{pool}/rebalance', [PropertyLiquidityPoolController::class, 'rebalance'])->name('rebalance');
        Route::get('/{pool}/positions', [PropertyLiquidityPoolController::class, 'userPositions'])->name('positions');
        Route::get('/analytics', [PropertyLiquidityPoolController::class, 'analytics'])->name('analytics');
    });

    // Property Yields Routes
    Route::prefix('defi/yields')->name('defi.yields.')->group(function () {
        Route::get('/', [DefiPropertyYieldController::class, 'index'])->name('index');
        Route::get('/create', [DefiPropertyYieldController::class, 'create'])->name('create');
        Route::post('/', [DefiPropertyYieldController::class, 'store'])->name('store');
        Route::get('/{yield}', [DefiPropertyYieldController::class, 'show'])->name('show');
        Route::get('/{yield}/edit', [DefiPropertyYieldController::class, 'edit'])->name('edit');
        Route::put('/{yield}', [DefiPropertyYieldController::class, 'update'])->name('update');
        Route::delete('/{yield}', [DefiPropertyYieldController::class, 'destroy'])->name('destroy');
        
        // Yield Actions
        Route::post('/{yield}/claim', [DefiPropertyYieldController::class, 'claim'])->name('claim');
        Route::post('/{yield}/compound', [DefiPropertyYieldController::class, 'compound'])->name('compound');
        Route::get('/analytics', [DefiPropertyYieldController::class, 'analytics'])->name('analytics');
        Route::get('/marketplace', [DefiPropertyYieldController::class, 'marketplace'])->name('marketplace');
    });

    // Property DAO Routes
    Route::prefix('defi/dao')->name('defi.dao.')->group(function () {
        Route::get('/', [PropertyDaoController::class, 'index'])->name('index');
        Route::get('/create', [PropertyDaoController::class, 'create'])->name('create');
        Route::post('/', [PropertyDaoController::class, 'store'])->name('store');
        Route::get('/{dao}', [PropertyDaoController::class, 'show'])->name('show');
        Route::get('/{dao}/edit', [PropertyDaoController::class, 'edit'])->name('edit');
        Route::put('/{dao}', [PropertyDaoController::class, 'update'])->name('update');
        Route::delete('/{dao}', [PropertyDaoController::class, 'destroy'])->name('destroy');
        
        // DAO Actions
        Route::post('/{dao}/deploy', [PropertyDaoController::class, 'deploy'])->name('deploy');
        Route::post('/{dao}/join', [PropertyDaoController::class, 'join'])->name('join');
        Route::post('/{dao}/leave', [PropertyDaoController::class, 'leave'])->name('leave');
        Route::post('/{dao}/stake', [PropertyDaoController::class, 'stake'])->name('stake');
        Route::post('/{dao}/unstake', [PropertyDaoController::class, 'unstake'])->name('unstake');
        Route::post('/{dao}/proposals', [PropertyDaoController::class, 'createProposal'])->name('proposals.create');
        Route::post('/{dao}/proposals/{proposal}/vote', [PropertyDaoController::class, 'vote'])->name('proposals.vote');
        Route::post('/{dao}/proposals/{proposal}/execute', [PropertyDaoController::class, 'executeProposal'])->name('proposals.execute');
        Route::get('/analytics', [PropertyDaoController::class, 'analytics'])->name('analytics');
    });

    // Crypto Property Payments Routes
    Route::prefix('defi/payments')->name('defi.payments.')->group(function () {
        Route::get('/', [CryptoPropertyPaymentController::class, 'index'])->name('index');
        Route::get('/create', [CryptoPropertyPaymentController::class, 'create'])->name('create');
        Route::post('/', [CryptoPropertyPaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [CryptoPropertyPaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/edit', [CryptoPropertyPaymentController::class, 'edit'])->name('edit');
        Route::put('/{payment}', [CryptoPropertyPaymentController::class, 'update'])->name('update');
        Route::delete('/{payment}', [CryptoPropertyPaymentController::class, 'destroy'])->name('destroy');
        
        // Payment Actions
        Route::post('/{payment}/retry', [CryptoPropertyPaymentController::class, 'retry'])->name('retry');
        Route::post('/{payment}/confirm', [CryptoPropertyPaymentController::class, 'confirm'])->name('confirm');
        Route::post('/{payment}/cancel', [CryptoPropertyPaymentController::class, 'cancel'])->name('cancel');
        Route::get('/analytics', [CryptoPropertyPaymentController::class, 'analytics'])->name('analytics');
        Route::get('/history', [CryptoPropertyPaymentController::class, 'history'])->name('history');
    });

});

// API Routes for AJAX requests
Route::middleware(['auth', 'verified'])->prefix('api/defi')->name('api.defi.')->group(function () {
    
    // Property Loans API
    Route::get('/loans/stats', [DefiPropertyLoanController::class, 'getStats'])->name('loans.stats');
    Route::get('/loans/{loan}/risk-assessment', [DefiPropertyLoanController::class, 'getRiskAssessment'])->name('loans.risk-assessment');
    Route::get('/loans/{loan}/credit-score', [DefiPropertyLoanController::class, 'getCreditScore'])->name('loans.credit-score');
    
    // Property Tokens API
    Route::get('/tokens/stats', [PropertyTokenizationController::class, 'getStats'])->name('tokens.stats');
    Route::get('/tokens/{token}/price-history', [PropertyTokenizationController::class, 'getPriceHistory'])->name('tokens.price-history');
    Route::get('/tokens/{token}/balance', [PropertyTokenizationController::class, 'getTokenBalance'])->name('tokens.balance');
    
    // Property Staking API
    Route::get('/staking/stats', [DefiPropertyStakingController::class, 'getStats'])->name('staking.stats');
    Route::get('/staking/{staking}/rewards', [DefiPropertyStakingController::class, 'getRewards'])->name('staking.rewards');
    Route::get('/staking/{staking}/progress', [DefiPropertyStakingController::class, 'getProgress'])->name('staking.progress');
    
    // Fractional Ownership API
    Route::get('/fractional/stats', [PropertyFractionalOwnershipController::class, 'getStats'])->name('fractional.stats');
    Route::get('/fractional/{ownership}/profit-loss', [PropertyFractionalOwnershipController::class, 'getProfitLoss'])->name('fractional.profit-loss');
    Route::get('/fractional/{ownership}/dividends', [PropertyFractionalOwnershipController::class, 'getDividends'])->name('fractional.dividends');
    
    // Property Investments API
    Route::get('/investments/stats', [DefiPropertyInvestmentController::class, 'getStats'])->name('investments.stats');
    Route::get('/investments/{investment}/returns', [DefiPropertyInvestmentController::class, 'getReturns'])->name('investments.returns');
    Route::get('/investments/{investment}/risk-assessment', [DefiPropertyInvestmentController::class, 'getRiskAssessment'])->name('investments.risk-assessment');
    
    // Liquidity Pools API
    Route::get('/pools/stats', [PropertyLiquidityPoolController::class, 'getStats'])->name('pools.stats');
    Route::get('/pools/{pool}/utilization', [PropertyLiquidityPoolController::class, 'getUtilizationRate'])->name('pools.utilization');
    Route::get('/pools/{pool}/impermanent-loss', [PropertyLiquidityPoolController::class, 'getImpermanentLoss'])->name('pools.impermanent-loss');
    Route::get('/pools/{pool}/user-earnings', [PropertyLiquidityPoolController::class, 'getUserEarnings'])->name('pools.user-earnings');
    
    // Property Yields API
    Route::get('/yields/stats', [DefiPropertyYieldController::class, 'getStats'])->name('yields.stats');
    Route::get('/yields/{yield}/daily-earnings', [DefiPropertyYieldController::class, 'getDailyEarnings'])->name('yields.daily-earnings');
    Route::get('/yields/{yield}/next-payout', [DefiPropertyYieldController::class, 'getNextPayoutDate'])->name('yields.next-payout');
    Route::get('/yields/{yield}/diversification', [DefiPropertyYieldController::class, 'getDiversificationScore'])->name('yields.diversification');
    
    // Property DAO API
    Route::get('/dao/stats', [PropertyDaoController::class, 'getStats'])->name('dao.stats');
    Route::get('/dao/{dao}/voting-power', [PropertyDaoController::class, 'getVotingPower'])->name('dao.voting-power');
    Route::get('/dao/{dao}/participation-rate', [PropertyDaoController::class, 'getParticipationRate'])->name('dao.participation-rate');
    Route::get('/dao/{dao}/proposal-success-rate', [PropertyDaoController::class, 'getProposalSuccessRate'])->name('dao.proposal-success-rate');
    
    // Crypto Payments API
    Route::get('/payments/stats', [CryptoPropertyPaymentController::class, 'getStats'])->name('payments.stats');
    Route::get('/payments/{payment}/progress', [CryptoPropertyPaymentController::class, 'getProgress'])->name('payments.progress');
    Route::get('/payments/{payment}/block-explorer', [CryptoPropertyPaymentController::class, 'getBlockExplorerUrl'])->name('payments.block-explorer');
    
});
