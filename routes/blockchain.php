<?php

// Blockchain Module Routes - Complete Blockchain & Crypto System

use App\Http\Controllers\BlockchainController;
use App\Http\Controllers\SmartContractController;
use App\Http\Controllers\NftController;
use App\Http\Controllers\CryptoWalletController;
use App\Http\Controllers\CryptoTransactionController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\DaoController;
use App\Http\Controllers\DefiController;
use App\Http\Controllers\StakingController;
use App\Http\Controllers\YieldFarmingController;
use App\Http\Controllers\LiquidityPoolController;
use App\Http\Controllers\PropertyTokenizationController;
use Illuminate\Support\Facades\Route;

// Blockchain Module Routes
Route::prefix('blockchain')->name('blockchain.')->middleware(['auth'])->group(function () {
    
    // Main Blockchain Dashboard
    Route::get('/', [BlockchainController::class, 'index'])->name('dashboard');
    Route::get('/overview', [BlockchainController::class, 'index'])->name('overview');
    Route::post('/create-record', [BlockchainController::class, 'createRecord'])->name('create-record');
    Route::get('/records', [BlockchainController::class, 'getRecords'])->name('records');
    Route::get('/block/{hash}', [BlockchainController::class, 'getBlock'])->name('block');
    Route::get('/latest-block', [BlockchainController::class, 'getLatestBlock'])->name('latest-block');
    Route::get('/stats', [BlockchainController::class, 'getBlockchainStats'])->name('stats');
    Route::post('/validate-chain', [BlockchainController::class, 'validateChain'])->name('validate-chain');
    Route::post('/sync-chain', [BlockchainController::class, 'syncChain'])->name('sync-chain');
    Route::get('/network-info', [BlockchainController::class, 'getNetworkInfo'])->name('network-info');
    Route::get('/transaction/{hash}', [BlockchainController::class, 'getTransaction'])->name('transaction');
    Route::get('/block-transactions/{blockHash}', [BlockchainController::class, 'getBlockTransactions'])->name('block-transactions');
    Route::post('/search-transactions', [BlockchainController::class, 'searchTransactions'])->name('search-transactions');
    Route::get('/transaction-stats', [BlockchainController::class, 'getTransactionStats'])->name('transaction-stats');
    Route::get('/export', [BlockchainController::class, 'exportRecords'])->name('export');
    
    // Smart Contracts Routes
    Route::prefix('smartcontracts')->name('smartcontracts.')->group(function () {
        Route::get('/', [SmartContractController::class, 'index'])->name('index');
        Route::post('/deploy', [SmartContractController::class, 'deployContract'])->name('deploy');
        Route::get('/list', [SmartContractController::class, 'getContracts'])->name('list');
        Route::get('/{address}', [SmartContractController::class, 'getContract'])->name('show');
        Route::put('/{address}', [SmartContractController::class, 'updateContract'])->name('update');
        Route::post('/execute', [SmartContractController::class, 'executeContract'])->name('execute');
        Route::post('/call', [SmartContractController::class, 'callContract'])->name('call');
        Route::get('/{address}/code', [SmartContractController::class, 'getContractCode'])->name('code');
        Route::get('/{address}/transactions', [SmartContractController::class, 'getContractTransactions'])->name('transactions');
        Route::get('/stats', [SmartContractController::class, 'getContractStats'])->name('stats');
        Route::post('/{address}/verify', [SmartContractController::class, 'verifyContract'])->name('verify');
        Route::post('/{address}/deprecate', [SmartContractController::class, 'deprecateContract'])->name('deprecate');
        Route::get('/export', [SmartContractController::class, 'exportContracts'])->name('export');
    });
    
    // NFTs Routes
    Route::prefix('nfts')->name('nfts.')->group(function () {
        Route::get('/', [NftController::class, 'index'])->name('index');
        Route::get('/create', [NftController::class, 'create'])->name('create');
        Route::post('/mint', [NftController::class, 'mintNft'])->name('mint');
        Route::get('/list', [NftController::class, 'getNfts'])->name('list');
        Route::get('/{id}', [NftController::class, 'getNft'])->name('show');
        Route::post('/transfer', [NftController::class, 'transferNft'])->name('transfer');
        Route::post('/list', [NftController::class, 'listNft'])->name('list-for-sale');
        Route::post('/buy', [NftController::class, 'buyNft'])->name('buy');
        Route::post('/burn', [NftController::class, 'burnNft'])->name('burn');
        Route::get('/stats', [NftController::class, 'getNftStats'])->name('stats');
        Route::get('/{id}/history', [NftController::class, 'getNftHistory'])->name('history');
        Route::post('/search', [NftController::class, 'searchNfts'])->name('search');
        Route::get('/owner/{address}', [NftController::class, 'getOwnerNfts'])->name('owner-nfts');
        Route::get('/creator/{address}', [NftController::class, 'getCreatorNfts'])->name('creator-nfts');
        Route::get('/export', [NftController::class, 'exportNfts'])->name('export');
    });
    
    // Crypto Wallets Routes
    Route::prefix('wallets')->name('wallets.')->group(function () {
        Route::get('/', [CryptoWalletController::class, 'index'])->name('index');
        Route::post('/create', [CryptoWalletController::class, 'createWallet'])->name('create');
        Route::get('/list', [CryptoWalletController::class, 'getWallets'])->name('list');
        Route::get('/{id}', [CryptoWalletController::class, 'getWallet'])->name('show');
        Route::put('/{id}', [CryptoWalletController::class, 'updateWallet'])->name('update');
        Route::delete('/{id}', [CryptoWalletController::class, 'deleteWallet'])->name('delete');
        Route::get('/{address}/balance', [CryptoWalletController::class, 'getBalance'])->name('balance');
        Route::put('/{address}/balance', [CryptoWalletController::class, 'updateBalance'])->name('update-balance');
        Route::post('/send', [CryptoWalletController::class, 'sendTransaction'])->name('send');
        Route::get('/{address}/transactions', [CryptoWalletController::class, 'getTransactions'])->name('transactions');
        Route::get('/stats', [CryptoWalletController::class, 'getWalletStats'])->name('stats');
        Route::post('/import', [CryptoWalletController::class, 'importWallet'])->name('import');
        Route::get('/{id}/export', [CryptoWalletController::class, 'exportWallet'])->name('export');
    });
    
    // Crypto Transactions Routes
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [CryptoTransactionController::class, 'index'])->name('index');
        Route::post('/create', [CryptoTransactionController::class, 'createTransaction'])->name('create');
        Route::get('/list', [CryptoTransactionController::class, 'getTransactions'])->name('list');
        Route::get('/{id}', [CryptoTransactionController::class, 'getTransaction'])->name('show');
        Route::put('/{id}/status', [CryptoTransactionController::class, 'updateTransactionStatus'])->name('update-status');
        Route::get('/stats', [CryptoTransactionController::class, 'getTransactionStats'])->name('stats');
        Route::get('/pending', [CryptoTransactionController::class, 'getPendingTransactions'])->name('pending');
        Route::get('/confirmed', [CryptoTransactionController::class, 'getConfirmedTransactions'])->name('confirmed');
        Route::get('/failed', [CryptoTransactionController::class, 'getFailedTransactions'])->name('failed');
        Route::get('/wallet/{address}', [CryptoTransactionController::class, 'getWalletTransactions'])->name('wallet-transactions');
        Route::get('/contract/{address}', [CryptoTransactionController::class, 'getContractTransactions'])->name('contract-transactions');
        Route::get('/{address}/history', [CryptoTransactionController::class, 'getTransactionHistory'])->name('history');
        Route::post('/search', [CryptoTransactionController::class, 'searchTransactions'])->name('search');
        Route::get('/export', [CryptoTransactionController::class, 'exportTransactions'])->name('export');
        Route::get('/{hash}/receipt', [CryptoTransactionController::class, 'getTransactionReceipt'])->name('receipt');
        Route::get('/{hash}/trace', [CryptoTransactionController::class, 'getTransactionTrace'])->name('trace');
    });
    
    // Tokens Routes
    Route::prefix('tokens')->name('tokens.')->group(function () {
        Route::get('/', [TokenController::class, 'index'])->name('index');
        Route::post('/create', [TokenController::class, 'createToken'])->name('create');
        Route::get('/list', [TokenController::class, 'getTokens'])->name('list');
        Route::get('/{id}', [TokenController::class, 'getToken'])->name('show');
        Route::put('/{id}', [TokenController::class, 'updateToken'])->name('update');
        Route::get('/{address}/balance/{walletAddress}', [TokenController::class, 'getTokenBalance'])->name('balance');
        Route::get('/{address}/stats', [TokenController::class, 'getTokenStats'])->name('stats');
        Route::get('/{address}/holders', [TokenController::class, 'getTokenHolders'])->name('holders');
        Route::get('/{address}/transactions', [TokenController::class, 'getTokenTransactions'])->name('transactions');
        Route::get('/search', [TokenController::class, 'searchTokens'])->name('search');
        Route::get('/top', [TokenController::class, 'getTopTokens'])->name('top');
        Route::get('/verified', [TokenController::class, 'getVerifiedTokens'])->name('verified');
        Route::get('/{address}/price', [TokenController::class, 'getTokenPrice'])->name('price');
        Route::get('/{address}/chart', [TokenController::class, 'getTokenChart'])->name('chart');
        Route::get('/export', [TokenController::class, 'exportTokens'])->name('export');
    });
    
    // DAOs Routes
    Route::prefix('daos')->name('daos.')->group(function () {
        Route::get('/', [DaoController::class, 'index'])->name('index');
        Route::post('/create', [DaoController::class, 'createDao'])->name('create');
        Route::get('/list', [DaoController::class, 'getDaos'])->name('list');
        Route::get('/{id}', [DaoController::class, 'getDao'])->name('show');
        Route::put('/{id}', [DaoController::class, 'updateDao'])->name('update');
        Route::post('/{id}/proposals', [DaoController::class, 'createProposal'])->name('create-proposal');
        Route::get('/{id}/proposals', [DaoController::class, 'getProposals'])->name('proposals');
        Route::get('/proposals/{id}', [DaoController::class, 'getProposal'])->name('proposal');
        Route::post('/proposals/{id}/vote', [DaoController::class, 'voteOnProposal'])->name('vote');
        Route::post('/proposals/{id}/execute', [DaoController::class, 'executeProposal'])->name('execute');
        Route::get('/{id}/stats', [DaoController::class, 'getDaoStats'])->name('stats');
        Route::get('/{id}/members', [DaoController::class, 'getDaoMembers'])->name('members');
        Route::post('/{id}/join', [DaoController::class, 'joinDao'])->name('join');
        Route::get('/{id}/treasury', [DaoController::class, 'getDaoTreasury'])->name('treasury');
        Route::get('/export', [DaoController::class, 'exportDaos'])->name('export');
    });
    
    // DeFi Routes
    Route::prefix('defi')->name('defi.')->group(function () {
        Route::get('/', [DefiController::class, 'index'])->name('index');
        Route::post('/create-loan', [DefiController::class, 'createLoan'])->name('create-loan');
        Route::get('/loans', [DefiController::class, 'getLoans'])->name('loans');
        Route::get('/loans/{id}', [DefiController::class, 'getLoan'])->name('loan');
        Route::put('/loans/{id}', [DefiController::class, 'updateLoan'])->name('update-loan');
        Route::post('/loans/{id}/repay', [DefiController::class, 'repayLoan'])->name('repay');
        Route::post('/loans/{id}/liquidate', [DefiController::class, 'liquidateLoan'])->name('liquidate');
        Route::get('/stats', [DefiController::class, 'getDefiStats'])->name('stats');
        Route::get('/protocols', [DefiController::class, 'getProtocolStats'])->name('protocols');
        Route::get('/borrower/{address}/loans', [DefiController::class, 'getBorrowerLoans'])->name('borrower-loans');
        Route::get('/lender/{address}/loans', [DefiController::class, 'getLenderLoans'])->name('lender-loans');
        Route::get('/collateral/stats', [DefiController::class, 'getCollateralStats'])->name('collateral-stats');
        Route::get('/interest-rates', [DefiController::class, 'getInterestRates'])->name('interest-rates');
        Route::post('/search', [DefiController::class, 'searchLoans'])->name('search');
        Route::get('/export', [DefiController::class, 'exportLoans'])->name('export');
    });
    
    // Staking Routes
    Route::prefix('staking')->name('staking.')->group(function () {
        Route::get('/', [StakingController::class, 'index'])->name('index');
        Route::post('/create-pool', [StakingController::class, 'createStakingPool'])->name('create-pool');
        Route::get('/pools', [StakingController::class, 'getPools'])->name('pools');
        Route::get('/pools/{id}', [StakingController::class, 'getPool'])->name('pool');
        Route::put('/pools/{id}', [StakingController::class, 'updatePool'])->name('update-pool');
        Route::post('/stake', [StakingController::class, 'stake'])->name('stake');
        Route::post('/unstake', [StakingController::class, 'unstake'])->name('unstake');
        Route::get('/pools/{id}/stats', [StakingController::class, 'getPoolStats'])->name('pool-stats');
        Route::get('/staker/{address}/stats', [StakingController::class, 'getStakerStats'])->name('staker-stats');
        Route::get('/pools/{id}/stakers', [StakingController::class, 'getPoolStakers'])->name('pool-stakers');
        Route::get('/pools/{id}/rewards', [StakingController::class, 'getRewards'])->name('rewards');
        Route::get('/stats', [StakingController::class, 'getStakingStats'])->name('stats');
        Route::post('/search', [StakingController::class, 'searchPools'])->name('search');
        Route::get('/export', [StakingController::class, 'exportPools'])->name('export');
    });
    
    // Yield Farming Routes
    Route::prefix('yield')->name('yield.')->group(function () {
        Route::get('/', [YieldFarmingController::class, 'index'])->name('index');
        Route::post('/create-pool', [YieldFarmingController::class, 'createLiquidityPool'])->name('create-pool');
        Route::get('/pools', [YieldFarmingController::class, 'getPools'])->name('pools');
        Route::get('/pools/{id}', [YieldFarmingController::class, 'getPool'])->name('pool');
        Route::put('/pools/{id}', [YieldFarmingController::class, 'updatePool'])->name('update-pool');
        Route::post('/add-liquidity', [YieldFarmingController::class, 'addLiquidity'])->name('add-liquidity');
        Route::post('/remove-liquidity', [YieldFarmingController::class, 'removeLiquidity'])->name('remove-liquidity');
        Route::post('/swap', [YieldFarmingController::class, 'swapTokens'])->name('swap');
        Route::get('/pools/{id}/stats', [YieldFarmingController::class, 'getPoolStats'])->name('pool-stats');
        Route::get('/provider/{address}/stats', [YieldFarmingController::class, 'getProviderStats'])->name('provider-stats');
        Route::get('/stats', [YieldFarmingController::class, 'getDefiStats'])->name('stats');
        Route::post('/search', [YieldFarmingController::class, 'searchPools'])->name('search');
        Route::get('/export', [YieldFarmingController::class, 'exportPools'])->name('export');
    });
    
    // Liquidity Pools Routes
    Route::prefix('pools')->name('pools.')->group(function () {
        Route::get('/', [LiquidityPoolController::class, 'index'])->name('index');
        Route::post('/create', [LiquidityPoolController::class, 'createPool'])->name('create');
        Route::get('/list', [LiquidityPoolController::class, 'getPools'])->name('list');
        Route::get('/{id}', [LiquidityPoolController::class, 'getPool'])->name('show');
        Route::put('/{id}', [LiquidityPoolController::class, 'updatePool'])->name('update');
        Route::post('/add-liquidity', [LiquidityPoolController::class, 'addLiquidity'])->name('add-liquidity');
        Route::post('/remove-liquidity', [LiquidityPoolController::class, 'removeLiquidity'])->name('remove-liquidity');
        Route::post('/swap', [LiquidityPoolController::class, 'swapTokens'])->name('swap');
        Route::get('/{id}/stats', [LiquidityPoolController::class, 'getPoolStats'])->name('stats');
        Route::get('/stats', [LiquidityPoolController::class, 'getDefiStats'])->name('defi-stats');
        Route::post('/search', [LiquidityPoolController::class, 'searchPools'])->name('search');
        Route::get('/export', [LiquidityPoolController::class, 'exportPools'])->name('export');
    });
    
    // Property Tokenization Routes
    Route::prefix('tokenization')->name('tokenization.')->group(function () {
        Route::get('/', [PropertyTokenizationController::class, 'index'])->name('index');
        Route::post('/tokenize', [PropertyTokenizationController::class, 'tokenizeProperty'])->name('tokenize');
        Route::get('/tokens', [PropertyTokenizationController::class, 'getTokens'])->name('tokens');
        Route::get('/tokens/{id}', [PropertyTokenizationController::class, 'getToken'])->name('token');
        Route::put('/tokens/{id}', [PropertyTokenizationController::class, 'updateToken'])->name('update-token');
        Route::post('/purchase', [PropertyTokenizationController::class, 'purchaseTokens'])->name('purchase');
        Route::post('/sell', [PropertyTokenizationController::class, 'sellTokens'])->name('sell');
        Route::get('/tokens/{id}/stats', [PropertyTokenizationController::class, 'getTokenStats'])->name('token-stats');
        Route::get('/tokens/{id}/holders', [PropertyTokenizationController::class, 'getTokenHolders'])->name('token-holders');
        Route::get('/tokens/{id}/transactions', [PropertyTokenizationController::class, 'getTokenTransactions'])->name('token-transactions');
        Route::get('/tokens/{id}/income', [PropertyTokenizationController::class, 'getTokenIncome'])->name('token-income');
        Route::get('/tokens/{id}/performance', [PropertyTokenizationController::class, 'getTokenPerformance'])->name('token-performance');
        Route::get('/stats', [PropertyTokenizationController::class, 'getPropertyTokenizationStats'])->name('stats');
        Route::post('/search', [PropertyTokenizationController::class, 'searchTokens'])->name('search');
        Route::get('/export', [PropertyTokenizationController::class, 'exportTokens'])->name('export');
    });
    
    // API Routes for AJAX requests
    Route::prefix('api')->name('api.')->group(function () {
        // Blockchain API
        Route::get('/records', [BlockchainController::class, 'getRecords'])->name('records');
        Route::get('/latest-block', [BlockchainController::class, 'getLatestBlock'])->name('latest-block');
        Route::get('/stats', [BlockchainController::class, 'getBlockchainStats'])->name('stats');
        Route::get('/network-info', [BlockchainController::class, 'getNetworkInfo'])->name('network-info');
        Route::post('/search-transactions', [BlockchainController::class, 'searchTransactions'])->name('search-transactions');
        Route::get('/transaction-stats', [BlockchainController::class, 'getTransactionStats'])->name('transaction-stats');
        
        // Smart Contracts API
        Route::get('/contracts', [SmartContractController::class, 'getContracts'])->name('contracts');
        Route::get('/contracts/{address}', [SmartContractController::class, 'getContract'])->name('contract');
        Route::get('/contracts/stats', [SmartContractController::class, 'getContractStats'])->name('contract-stats');
        Route::post('/contracts/execute', [SmartContractController::class, 'executeContract'])->name('execute-contract');
        Route::post('/contracts/call', [SmartContractController::class, 'callContract'])->name('call-contract');
        
        // NFTs API
        Route::get('/nfts', [NftController::class, 'getNfts'])->name('nfts');
        Route::get('/nfts/{id}', [NftController::class, 'getNft'])->name('nft');
        Route::get('/nfts/stats', [NftController::class, 'getNftStats'])->name('nft-stats');
        Route::post('/nfts/search', [NftController::class, 'searchNfts'])->name('search-nfts');
        Route::post('/nfts/transfer', [NftController::class, 'transferNft'])->name('transfer-nft');
        Route::post('/nfts/list', [NftController::class, 'listNft'])->name('list-nft');
        Route::post('/nfts/buy', [NftController::class, 'buyNft'])->name('buy-nft');
        
        // Wallets API
        Route::get('/wallets', [CryptoWalletController::class, 'getWallets'])->name('wallets');
        Route::get('/wallets/{id}', [CryptoWalletController::class, 'getWallet'])->name('wallet');
        Route::get('/wallets/{address}/balance', [CryptoWalletController::class, 'getBalance'])->name('wallet-balance');
        Route::get('/wallets/stats', [CryptoWalletController::class, 'getWalletStats'])->name('wallet-stats');
        Route::post('/wallets/send', [CryptoWalletController::class, 'sendTransaction'])->name('wallet-send');
        
        // Transactions API
        Route::get('/transactions', [CryptoTransactionController::class, 'getTransactions'])->name('transactions');
        Route::get('/transactions/{id}', [CryptoTransactionController::class, 'getTransaction'])->name('transaction');
        Route::get('/transactions/stats', [CryptoTransactionController::class, 'getTransactionStats'])->name('transaction-stats');
        Route::get('/transactions/pending', [CryptoTransactionController::class, 'getPendingTransactions'])->name('pending-transactions');
        Route::get('/transactions/confirmed', [CryptoTransactionController::class, 'getConfirmedTransactions'])->name('confirmed-transactions');
        Route::post('/transactions/search', [CryptoTransactionController::class, 'searchTransactions'])->name('search-transactions');
        
        // Tokens API
        Route::get('/tokens', [TokenController::class, 'getTokens'])->name('tokens');
        Route::get('/tokens/{id}', [TokenController::class, 'getToken'])->name('token');
        Route::get('/tokens/{address}/balance/{walletAddress}', [TokenController::class, 'getTokenBalance'])->name('token-balance');
        Route::get('/tokens/{address}/stats', [TokenController::class, 'getTokenStats'])->name('token-stats');
        Route::get('/tokens/{address}/holders', [TokenController::class, 'getTokenHolders'])->name('token-holders');
        Route::get('/tokens/{address}/transactions', [TokenController::class, 'getTokenTransactions'])->name('token-transactions');
        Route::get('/tokens/{address}/price', [TokenController::class, 'getTokenPrice'])->name('token-price');
        Route::get('/tokens/{address}/chart', [TokenController::class, 'getTokenChart'])->name('token-chart');
        Route::get('/tokens/top', [TokenController::class, 'getTopTokens'])->name('top-tokens');
        Route::get('/tokens/verified', [TokenController::class, 'getVerifiedTokens'])->name('verified-tokens');
        
        // DAOs API
        Route::get('/daos', [DaoController::class, 'getDaos'])->name('daos');
        Route::get('/daos/{id}', [DaoController::class, 'getDao'])->name('dao');
        Route::get('/daos/{id}/stats', [DaoController::class, 'getDaoStats'])->name('dao-stats');
        Route::get('/daos/{id}/members', [DaoController::class, 'getDaoMembers'])->name('dao-members');
        Route::get('/daos/{id}/proposals', [DaoController::class, 'getProposals'])->name('dao-proposals');
        Route::get('/daos/{id}/treasury', [DaoController::class, 'getDaoTreasury'])->name('dao-treasury');
        Route::post('/daos/{id}/join', [DaoController::class, 'joinDao'])->name('join-dao');
        Route::post('/daos/{id}/proposals', [DaoController::class, 'createProposal'])->name('create-proposal');
        Route::post('/daos/proposals/{id}/vote', [DaoController::class, 'voteOnProposal'])->name('vote-proposal');
        Route::post('/daos/proposals/{id}/execute', [DaoController::class, 'executeProposal'])->name('execute-proposal');
        
        // DeFi API
        Route::get('/defi/loans', [DefiController::class, 'getLoans'])->name('defi-loans');
        Route::get('/defi/loans/{id}', [DefiController::class, 'getLoan'])->name('defi-loan');
        Route::get('/defi/stats', [DefiController::class, 'getDefiStats'])->name('defi-stats');
        Route::get('/defi/protocols', [DefiController::class, 'getProtocolStats'])->name('defi-protocols');
        Route::get('/defi/collateral-stats', [DefiController::class, 'getCollateralStats'])->name('defi-collateral');
        Route::get('/defi/interest-rates', [DefiController::class, 'getInterestRates'])->name('defi-rates');
        Route::post('/defi/search', [DefiController::class, 'searchLoans'])->name('search-defi');
        Route::post('/defi/loans/{id}/repay', [DefiController::class, 'repayLoan'])->name('repay-loan');
        Route::post('/defi/loans/{id}/liquidate', [DefiController::class, 'liquidateLoan'])->name('liquidate-loan');
        
        // Staking API
        Route::get('/staking/pools', [StakingController::class, 'getPools'])->name('staking-pools');
        Route::get('/staking/pools/{id}', [StakingController::class, 'getPool'])->name('staking-pool');
        Route::get('/staking/pools/{id}/stats', [StakingController::class, 'getPoolStats'])->name('staking-pool-stats');
        Route::get('/staking/pools/{id}/stakers', [StakingController::class, 'getPoolStakers'])->name('staking-pool-stakers');
        Route::get('/staking/pools/{id}/rewards', [StakingController::class, 'getRewards'])->name('staking-rewards');
        Route::get('/staking/stats', [StakingController::class, 'getStakingStats'])->name('staking-stats');
        Route::get('/staking/staker/{address}/stats', [StakingController::class, 'getStakerStats'])->name('staker-stats');
        Route::post('/staking/stake', [StakingController::class, 'stake'])->name('stake');
        Route::post('/staking/unstake', [StakingController::class, 'unstake'])->name('unstake');
        Route::post('/staking/search', [StakingController::class, 'searchPools'])->name('search-staking');
        
        // Yield Farming API
        Route::get('/yield/pools', [YieldFarmingController::class, 'getPools'])->name('yield-pools');
        Route::get('/yield/pools/{id}', [YieldFarmingController::class, 'getPool'])->name('yield-pool');
        Route::get('/yield/pools/{id}/stats', [YieldFarmingController::class, 'getPoolStats'])->name('yield-pool-stats');
        Route::get('/yield/provider/{address}/stats', [YieldFarmingController::class, 'getProviderStats'])->name('yield-provider-stats');
        Route::get('/yield/stats', [YieldFarmingController::class, 'getDefiStats'])->name('yield-stats');
        Route::post('/yield/add-liquidity', [YieldFarmingController::class, 'addLiquidity'])->name('yield-add-liquidity');
        Route::post('/yield/remove-liquidity', [YieldFarmingController::class, 'removeLiquidity'])->name('yield-remove-liquidity');
        Route::post('/yield/swap', [YieldFarmingController::class, 'swapTokens'])->name('yield-swap');
        Route::post('/yield/search', [YieldFarmingController::class, 'searchPools'])->name('search-yield');
        
        // Liquidity Pools API
        Route::get('/pools/list', [LiquidityPoolController::class, 'getPools'])->name('liquidity-pools');
        Route::get('/pools/{id}', [LiquidityPoolController::class, 'getPool'])->name('liquidity-pool');
        Route::get('/pools/{id}/stats', [LiquidityPoolController::class, 'getPoolStats'])->name('liquidity-pool-stats');
        Route::get('/pools/stats', [LiquidityPoolController::class, 'getDefiStats'])->name('liquidity-stats');
        Route::post('/pools/add-liquidity', [LiquidityPoolController::class, 'addLiquidity'])->name('liquidity-add');
        Route::post('/pools/remove-liquidity', [LiquidityPoolController::class, 'removeLiquidity'])->name('liquidity-remove');
        Route::post('/pools/swap', [LiquidityPoolController::class, 'swapTokens'])->name('liquidity-swap');
        Route::post('/pools/search', [LiquidityPoolController::class, 'searchPools'])->name('search-pools');
        
        // Property Tokenization API
        Route::get('/tokenization/tokens', [PropertyTokenizationController::class, 'getTokens'])->name('property-tokens');
        Route::get('/tokenization/tokens/{id}', [PropertyTokenizationController::class, 'getToken'])->name('property-token');
        Route::get('/tokenization/tokens/{id}/stats', [PropertyTokenizationController::class, 'getTokenStats'])->name('property-token-stats');
        Route::get('/tokenization/tokens/{id}/holders', [PropertyTokenizationController::class, 'getTokenHolders'])->name('property-token-holders');
        Route::get('/tokenization/tokens/{id}/transactions', [PropertyTokenizationController::class, 'getTokenTransactions'])->name('property-token-transactions');
        Route::get('/tokenization/tokens/{id}/income', [PropertyTokenizationController::class, 'getTokenIncome'])->name('property-token-income');
        Route::get('/tokenization/tokens/{id}/performance', [PropertyTokenizationController::class, 'getTokenPerformance'])->name('property-token-performance');
        Route::get('/tokenization/stats', [PropertyTokenizationController::class, 'getPropertyTokenizationStats'])->name('property-tokenization-stats');
        Route::post('/tokenization/purchase', [PropertyTokenizationController::class, 'purchaseTokens'])->name('purchase-tokens');
        Route::post('/tokenization/sell', [PropertyTokenizationController::class, 'sellTokens'])->name('sell-tokens');
        Route::post('/tokenization/search', [PropertyTokenizationController::class, 'searchTokens'])->name('search-property-tokens');
    });
});
