<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlockchainController;
use App\Http\Controllers\BlockchainVerificationController;
use App\Http\Controllers\CryptoTransactionController;
use App\Http\Controllers\CryptoWalletController;
use App\Http\Controllers\SmartContractController;
use App\Http\Controllers\NftController;
use App\Http\Controllers\DaoController;
use App\Http\Controllers\MetaverseController;
use App\Http\Controllers\DefiController;
use App\Http\Controllers\LiquidityPoolController;

Route::middleware(['auth'])->prefix('blockchain')->name('blockchain.')->group(function () {
    Route::get('/', [BlockchainController::class, 'index'])->name('index');
    Route::get('/transactions', function() {
        return view('blockchain.transactions.index', ['transactions' => collect([])]);
    })->name('transactions');
    Route::get('/blocks', [BlockchainController::class, 'getRecords'])->name('blocks');
    Route::post('/records', [BlockchainController::class, 'createRecord'])->name('records.create');
    Route::get('/records', [BlockchainController::class, 'getRecords'])->name('records.index');
    Route::get('/block', [BlockchainController::class, 'getBlock'])->name('block.show');
    Route::get('/latest-block', [BlockchainController::class, 'getLatestBlock'])->name('block.latest');

    // Verification
    Route::resource('/verification', BlockchainVerificationController::class);
    
    // Crypto Wallet & Transactions
    Route::get('/wallets', [CryptoWalletController::class, 'index'])->name('wallets.index');
    Route::resource('/wallets', CryptoWalletController::class);
    Route::resource('/transactions', CryptoTransactionController::class);
    
    // Smart Contracts & NFTs
    Route::resource('/smartcontracts', SmartContractController::class);
    Route::resource('/nfts', NftController::class);
    
    // DAO & DeFi
    Route::get('/dao', [DaoController::class, 'index'])->name('dao.index');
    Route::get('/dao/create', [DaoController::class, 'create'])->name('dao.create');
    Route::get('/dao/{id}', [DaoController::class, 'show'])->name('dao.show');
    Route::get('/dao/{id}/members', [DaoController::class, 'members'])->name('dao.members');
    Route::get('/dao/{id}/members/add', [DaoController::class, 'addMember'])->name('dao.members.add');
    Route::post('/dao/{id}/members', [DaoController::class, 'storeMember'])->name('dao.members.store');
    Route::get('/dao/{id}/members/{member_id}', [DaoController::class, 'showMember'])->name('dao.members.show');
    Route::get('/dao/{id}/members/{member_id}/edit', [DaoController::class, 'editMember'])->name('dao.members.edit');
    Route::put('/dao/{id}/members/{member_id}', [DaoController::class, 'updateMember'])->name('dao.members.update');
    Route::delete('/dao/{id}/members/{member_id}', [DaoController::class, 'deleteMember'])->name('dao.members.delete');
    Route::get('/dao/{id}/members/export', [DaoController::class, 'exportMembers'])->name('dao.members.export');
    Route::get('/dao/{id}/proposals', [DaoController::class, 'proposals'])->name('dao.proposals');
    Route::get('/dao/{id}/proposals/create', [DaoController::class, 'createProposal'])->name('dao.proposals.create');
    Route::post('/dao/{id}/proposals', [DaoController::class, 'storeProposal'])->name('dao.proposals.store');
    Route::get('/dao/{id}/proposals/{proposal_id}', [DaoController::class, 'showProposal'])->name('dao.proposals.show');
    Route::get('/dao/{id}/proposals/{proposal_id}/edit', [DaoController::class, 'editProposal'])->name('dao.proposals.edit');
    Route::put('/dao/{id}/proposals/{proposal_id}', [DaoController::class, 'updateProposal'])->name('dao.proposals.update');
    Route::delete('/dao/{id}/proposals/{proposal_id}', [DaoController::class, 'deleteProposal'])->name('dao.proposals.delete');
    Route::post('/dao/{id}/proposals/{proposal_id}/vote', [DaoController::class, 'voteOnProposal'])->name('dao.proposals.vote');
    Route::post('/dao/{id}/proposals/{proposal_id}/execute', [DaoController::class, 'executeProposal'])->name('dao.proposals.execute');
    Route::get('/dao/{id}/vote', [DaoController::class, 'vote'])->name('dao.vote');
    Route::get('/dao/{id}/treasury', [DaoController::class, 'treasury'])->name('dao.treasury');
    Route::get('/defi', [DefiController::class, 'index'])->name('defi.index');
    
    // Metaverse Routes
    Route::get('/metaverse/properties', [MetaverseController::class, 'properties'])->name('blockchain.metaverse.properties');
    Route::get('/metaverse/marketplace', [MetaverseController::class, 'marketplace'])->name('blockchain.metaverse.marketplace');
    Route::get('/metaverse/marketplace/create', [MetaverseController::class, 'create'])->name('blockchain.metaverse.marketplace.create');
    Route::post('/metaverse/marketplace', [MetaverseController::class, 'store'])->name('blockchain.metaverse.marketplace.store');
    Route::get('/metaverse/nft', [MetaverseController::class, 'nft'])->name('blockchain.metaverse.nft');
    
    // Geospatial Routes
    Route::get('/geospatial/analysis', [MetaverseController::class, 'geospatialAnalysis'])->name('geospatial.analysis');
    Route::get('/geospatial/security', [MetaverseController::class, 'geospatialSecurity'])->name('geospatial.security');
    Route::get('/geospatial/intelligence', [MetaverseController::class, 'geospatialIntelligence'])->name('geospatial.intelligence');
    Route::get('/geospatial/intelligence/refresh', [MetaverseController::class, 'refreshIntelligenceData'])->name('geospatial.intelligence.refresh');

    // Legal Routes
    Route::get('/legal/compliance', [MetaverseController::class, 'legalCompliance'])->name('legal.compliance');
    Route::get('/legal/notary', [MetaverseController::class, 'legalNotary'])->name('legal.notary');
    Route::get('/legal/signatures', [MetaverseController::class, 'legalSignatures'])->name('legal.signatures');
    
    // Legal API Routes
    Route::get('/legal/compliance/refresh', [MetaverseController::class, 'refreshComplianceData'])->name('legal.compliance.refresh');
    Route::post('/legal/compliance/check', [MetaverseController::class, 'performComplianceCheck'])->name('legal.compliance.check');
    Route::get('/legal/notary/refresh', [MetaverseController::class, 'refreshNotaryData'])->name('legal.notary.refresh');
    Route::post('/legal/notary/request', [MetaverseController::class, 'requestNewService'])->name('legal.notary.request');
    Route::post('/legal/notary/notarize', [MetaverseController::class, 'requestDocumentNotarization'])->name('legal.notary.notarize');
    Route::post('/legal/notary/certificate', [MetaverseController::class, 'getDigitalCertificate'])->name('legal.notary.certificate');
    Route::post('/legal/notary/consultation', [MetaverseController::class, 'bookConsultation'])->name('legal.notary.consultation');
    Route::get('/legal/notary/requests', [MetaverseController::class, 'getAllRequests'])->name('legal.notary.requests');
    Route::get('/legal/signatures/refresh', [MetaverseController::class, 'refreshSignaturesData'])->name('legal.signatures.refresh');
    Route::post('/legal/signatures/create', [MetaverseController::class, 'createNewSignature'])->name('legal.signatures.create');
    Route::post('/legal/signatures/verify', [MetaverseController::class, 'verifySignature'])->name('legal.signatures.verify');
    Route::get('/legal/signatures/all', [MetaverseController::class, 'getAllSignatures'])->name('legal.signatures.all');

    // Geospatial API Routes
    Route::prefix('api/geospatial')->group(function () {
        Route::post('/satellite/scan', [MetaverseController::class, 'startSatelliteScan']);
        Route::get('/satellite/progress', [MetaverseController::class, 'getScanProgress']);
        Route::get('/satellite/images', [MetaverseController::class, 'getSatelliteImages']);
        
        Route::post('/analysis/start', [MetaverseController::class, 'startAdvancedAnalysis']);
        Route::get('/analysis/progress/{analysisId}', [MetaverseController::class, 'getAnalysisProgress']);
        
        Route::get('/predictions', [MetaverseController::class, 'getPredictions']);
        
        Route::post('/monitoring/start', [MetaverseController::class, 'startMonitoring']);
        Route::post('/monitoring/stop/{monitoringId}', [MetaverseController::class, 'stopMonitoring']);
        Route::get('/monitoring/stats/{monitoringId?}', [MetaverseController::class, 'getMonitoringStats']);
    });
    
    // DeFi Sub-pages
    Route::get('/defi/lending', [DefiController::class, 'lending'])->name('defi.lending');
    Route::get('/defi/staking', [DefiController::class, 'staking'])->name('defi.staking');
    Route::get('/defi/yield', [DefiController::class, 'yield'])->name('defi.yield');
    Route::get('/defi/pools', [DefiController::class, 'pools'])->name('defi.pools');
    Route::get('/defi/pools/{id}', [DefiController::class, 'showPool'])->name('defi.pool.show');
    Route::get('/defi/pools/{id}/deposit', [DefiController::class, 'depositPool'])->name('defi.pool.deposit');
    Route::post('/defi/pools/{id}/deposit', [DefiController::class, 'processDeposit'])->name('defi.pool.deposit.process');
    Route::get('/defi/pools/{id}/withdraw', [DefiController::class, 'withdrawPool'])->name('defi.pool.withdraw');
    Route::post('/defi/pools/{id}/withdraw', [DefiController::class, 'processWithdraw'])->name('defi.pool.withdraw.process');
    Route::get('/defi/refresh', [DefiController::class, 'refresh'])->name('defi.refresh');
    
    Route::get('/liquidity-pools', [LiquidityPoolController::class, 'index'])->name('liquidity-pools.index');
});
