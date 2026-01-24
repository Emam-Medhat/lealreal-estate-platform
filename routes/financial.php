<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyFinancialAnalysisController;
use App\Http\Controllers\RoiCalculatorController;
use App\Http\Controllers\CashFlowAnalysisController;
use App\Http\Controllers\CapRateCalculatorController;
use App\Http\Controllers\PropertyValuationModelController;
use App\Http\Controllers\InvestmentScenarioController;
use App\Http\Controllers\PropertyPortfolioAnalysisController;
use App\Http\Controllers\TaxBenefitCalculatorController;
use App\Http\Controllers\PropertyAppreciationCalculatorController;

/*
|--------------------------------------------------------------------------
| Property Financial Analysis Routes
|--------------------------------------------------------------------------
|
| Routes for comprehensive property financial analysis system including
| ROI calculations, cash flow analysis, cap rate calculations, property
| valuations, investment scenarios, portfolio analysis, tax benefits,
| and appreciation projections.
|
*/

// Main Property Financial Analysis Routes
Route::prefix('financial')->name('financial.')->middleware(['auth'])->group(function () {
    
    // Dashboard and Overview
    Route::get('/', [PropertyFinancialAnalysisController::class, 'index'])->name('index');
    Route::get('/dashboard', [PropertyFinancialAnalysisController::class, 'dashboard'])->name('dashboard');
    Route::get('/overview', [PropertyFinancialAnalysisController::class, 'overview'])->name('overview');
    Route::get('/statistics', [PropertyFinancialAnalysisController::class, 'statistics'])->name('statistics');
    
    // CRUD Operations
    Route::get('/analyses/create', [PropertyFinancialAnalysisController::class, 'create'])->name('analyses.create');
    Route::post('/analyses', [PropertyFinancialAnalysisController::class, 'store'])->name('analyses.store');
    Route::get('/analyses/{analysis}', [PropertyFinancialAnalysisController::class, 'show'])->name('analyses.show');
    Route::get('/analyses/{analysis}/edit', [PropertyFinancialAnalysisController::class, 'edit'])->name('analyses.edit');
    Route::put('/analyses/{analysis}', [PropertyFinancialAnalysisController::class, 'update'])->name('analyses.update');
    Route::delete('/analyses/{analysis}', [PropertyFinancialAnalysisController::class, 'destroy'])->name('analyses.destroy');
    
    // Analysis Operations
    Route::post('/analyses/{analysis}/recalculate', [PropertyFinancialAnalysisController::class, 'recalculate'])->name('analyses.recalculate');
    Route::get('/analyses/{analysis}/export', [PropertyFinancialAnalysisController::class, 'export'])->name('analyses.export');
    Route::get('/analyses/{analysis}/preview', [PropertyFinancialAnalysisController::class, 'preview'])->name('analyses.preview');
    Route::get('/analyses/{analysis}/analytics', [PropertyFinancialAnalysisController::class, 'analytics'])->name('analyses.analytics');
    
    // ROI Calculator Routes
    Route::prefix('roi-calculator')->name('roi.')->group(function () {
        Route::get('/', [RoiCalculatorController::class, 'index'])->name('index');
        Route::post('/calculate', [RoiCalculatorController::class, 'calculate'])->name('calculate');
        Route::get('/advanced', [RoiCalculatorController::class, 'advanced'])->name('advanced');
        Route::post('/advanced-calculate', [RoiCalculatorController::class, 'advancedCalculate'])->name('advanced.calculate');
        Route::get('/compare', [RoiCalculatorController::class, 'compare'])->name('compare');
        Route::post('/compare-scenarios', [RoiCalculatorController::class, 'compareScenarios'])->name('compare.scenarios');
        Route::post('/save-calculation', [RoiCalculatorController::class, 'saveCalculation'])->name('save');
        Route::get('/scenarios', [RoiCalculatorController::class, 'scenarios'])->name('scenarios');
        Route::get('/export', [RoiCalculatorController::class, 'export'])->name('export');
    });
    
    // Cash Flow Analysis Routes
    Route::prefix('cash-flow')->name('cash_flow.')->group(function () {
        Route::get('/', [CashFlowAnalysisController::class, 'index'])->name('index');
        Route::get('/cashflow', [CashFlowAnalysisController::class, 'index'])->name('cashflow.index');
        Route::get('/cashflow', [CashFlowAnalysisController::class, 'index'])->name('cashflow.index');
        Route::post('/analyze', [CashFlowAnalysisController::class, 'analyze'])->name('analyze');
        Route::get('/detailed/{analysis}', [CashFlowAnalysisController::class, 'detailed'])->name('detailed');
        Route::get('/compare', [CashFlowAnalysisController::class, 'compare'])->name('compare');
        Route::post('/save-projection', [CashFlowAnalysisController::class, 'saveProjection'])->name('save');
        Route::get('/sensitivity', [CashFlowAnalysisController::class, 'sensitivity'])->name('sensitivity');
        Route::post('/perform-sensitivity', [CashFlowAnalysisController::class, 'performSensitivity'])->name('sensitivity.perform');
        Route::get('/export', [CashFlowAnalysisController::class, 'export'])->name('export');
    });
    
    // Cash Flow Analysis Routes (outside prefix for specific naming)
    Route::get('/cashflow', [CashFlowAnalysisController::class, 'index'])->name('cashflow.index');
    
    // Cap Rate Calculator Routes (outside prefix for specific naming)
    Route::get('/caprate', [CapRateCalculatorController::class, 'index'])->name('caprate.index');
    
    // Investment Scenarios Routes (outside prefix for specific naming)
    Route::get('/scenario', [InvestmentScenarioController::class, 'index'])->name('scenario.index');
    
    // Tax Benefits Routes (outside prefix for specific naming)
    Route::get('/tax', [TaxBenefitCalculatorController::class, 'index'])->name('tax.index');
    
    // ROI Calculator Routes (outside prefix for specific naming)
    Route::get('/roi', [RoiCalculatorController::class, 'index'])->name('roi.index');
    
    // Property Valuation Routes (outside prefix for specific naming)
    Route::get('/valuation', [PropertyValuationModelController::class, 'index'])->name('valuation.index');
    
    // Portfolio Analysis Routes (outside prefix for specific naming)
    Route::get('/portfolio', [PropertyPortfolioAnalysisController::class, 'index'])->name('portfolio.index');
    
    // Cap Rate Calculator Routes
    Route::prefix('cap-rate')->name('cap_rate.')->group(function () {
        Route::get('/', [CapRateCalculatorController::class, 'index'])->name('index');
        Route::post('/calculate', [CapRateCalculatorController::class, 'calculate'])->name('calculate');
        Route::get('/advanced', [CapRateCalculatorController::class, 'advanced'])->name('advanced');
        Route::post('/advanced-calculate', [CapRateCalculatorController::class, 'advancedCalculate'])->name('advanced.calculate');
        Route::get('/compare', [CapRateCalculatorController::class, 'compare'])->name('compare');
        Route::post('/save-calculation', [CapRateCalculatorController::class, 'saveCalculation'])->name('save');
        Route::get('/market-analysis', [CapRateCalculatorController::class, 'marketAnalysis'])->name('market.analysis');
        Route::post('/perform-market-analysis', [CapRateCalculatorController::class, 'performMarketAnalysis'])->name('market.analysis.perform');
        Route::get('/scenarios', [CapRateCalculatorController::class, 'scenarios'])->name('scenarios');
        Route::get('/export', [CapRateCalculatorController::class, 'export'])->name('export');
    });
    
    // Property Valuation Model Routes
    Route::prefix('valuation')->name('valuation.')->group(function () {
        Route::get('/', [PropertyValuationModelController::class, 'index'])->name('index');
        Route::get('/create', [PropertyValuationModelController::class, 'create'])->name('create');
        Route::post('/', [PropertyValuationModelController::class, 'store'])->name('store');
        Route::get('/{valuation}', [PropertyValuationModelController::class, 'show'])->name('show');
        Route::get('/{valuation}/edit', [PropertyValuationModelController::class, 'edit'])->name('edit');
        Route::put('/{valuation}', [PropertyValuationModelController::class, 'update'])->name('update');
        Route::delete('/{valuation}', [PropertyValuationModelController::class, 'destroy'])->name('destroy');
        
        // Valuation Methods
        Route::get('/comparative-analysis', [PropertyValuationModelController::class, 'comparativeAnalysis'])->name('comparative');
        Route::post('/perform-comparative', [PropertyValuationModelController::class, 'performComparativeAnalysis'])->name('comparative.perform');
        Route::get('/income-approach', [PropertyValuationModelController::class, 'incomeApproach'])->name('income');
        Route::post('/perform-income', [PropertyValuationModelController::class, 'performIncomeApproach'])->name('income.perform');
        Route::get('/cost-approach', [PropertyValuationModelController::class, 'costApproach'])->name('cost');
        Route::post('/perform-cost', [PropertyValuationModelController::class, 'performCostApproach'])->name('cost.perform');
        Route::get('/residual-method', [PropertyValuationModelController::class, 'residualMethod'])->name('residual');
        Route::post('/perform-residual', [PropertyValuationModelController::class, 'performResidualMethod'])->name('residual.perform');
        Route::get('/automated-valuation', [PropertyValuationModelController::class, 'automatedValuation'])->name('automated');
        Route::post('/perform-automated', [PropertyValuationModelController::class, 'performAutomatedValuation'])->name('automated.perform');
        
        Route::get('/history', [PropertyValuationModelController::class, 'valuationHistory'])->name('history');
        Route::get('/export', [PropertyValuationModelController::class, 'export'])->name('export');
    });
    
    // Investment Scenario Routes
    Route::prefix('scenarios')->name('scenarios.')->group(function () {
        Route::get('/', [InvestmentScenarioController::class, 'index'])->name('index');
        Route::get('/create', [InvestmentScenarioController::class, 'create'])->name('create');
        Route::post('/', [InvestmentScenarioController::class, 'store'])->name('store');
        Route::get('/{scenario}', [InvestmentScenarioController::class, 'show'])->name('show');
        Route::get('/{scenario}/edit', [InvestmentScenarioController::class, 'edit'])->name('edit');
        Route::put('/{scenario}', [InvestmentScenarioController::class, 'update'])->name('update');
        Route::delete('/{scenario}', [InvestmentScenarioController::class, 'destroy'])->name('destroy');
        
        Route::get('/{scenario}/analyze', [InvestmentScenarioController::class, 'analyze'])->name('analyze');
        Route::get('/{scenario}/compare', [InvestmentScenarioController::class, 'compare'])->name('compare');
        Route::post('/compare-scenarios', [InvestmentScenarioController::class, 'compareScenarios'])->name('compare.data');
        Route::get('/monte-carlo', [InvestmentScenarioController::class, 'monteCarlo'])->name('monte_carlo');
        Route::post('/run-monte-carlo', [InvestmentScenarioController::class, 'runMonteCarlo'])->name('monte_carlo.run');
        Route::get('/sensitivity', [InvestmentScenarioController::class, 'sensitivity'])->name('sensitivity');
        Route::post('/perform-sensitivity', [InvestmentScenarioController::class, 'performSensitivity'])->name('sensitivity.perform');
        Route::get('/stress-test', [InvestmentScenarioController::class, 'stressTest'])->name('stress_test');
        Route::post('/perform-stress-test', [InvestmentScenarioController::class, 'performStressTest'])->name('stress_test.perform');
        
        Route::get('/template', [InvestmentScenarioController::class, 'scenarios'])->name('template');
        Route::get('/export', [InvestmentScenarioController::class, 'export'])->name('export');
    });
    
    // Portfolio Analysis Routes
    Route::prefix('portfolio')->name('portfolio.')->group(function () {
        Route::get('/', [PropertyPortfolioAnalysisController::class, 'index'])->name('index');
        Route::get('/create', [PropertyPortfolioAnalysisController::class, 'create'])->name('create');
        Route::post('/', [PropertyPortfolioAnalysisController::class, 'store'])->name('store');
        Route::get('/{portfolio}', [PropertyPortfolioAnalysisController::class, 'show'])->name('show');
        Route::get('/{portfolio}/edit', [PropertyPortfolioAnalysisController::class, 'edit'])->name('edit');
        Route::put('/{portfolio}', [PropertyPortfolioAnalysisController::class, 'update'])->name('update');
        Route::delete('/{portfolio}', [PropertyPortfolioAnalysisController::class, 'destroy'])->name('destroy');
        
        Route::get('/{portfolio}/analyze', [PropertyPortfolioAnalysisController::class, 'analyze'])->name('analyze');
        Route::get('/diversification', [PropertyPortfolioAnalysisController::class, 'diversification'])->name('diversification');
        Route::post('/calculate-diversification', [PropertyPortfolioAnalysisController::class, 'calculateDiversification'])->name('diversification.calculate');
        Route::get('/optimization', [PropertyPortfolioAnalysisController::class, 'optimization'])->name('optimization');
        Route::post('/optimize-portfolio', [PropertyPortfolioAnalysisController::class, 'optimizePortfolio'])->name('optimization.optimize');
        Route::get('/risk-analysis', [PropertyPortfolioAnalysisController::class, 'riskAnalysis'])->name('risk');
        Route::post('/perform-risk-analysis', [PropertyPortfolioAnalysisController::class, 'performRiskAnalysis'])->name('risk.perform');
        Route::get('/performance', [PropertyPortfolioAnalysisController::class, 'performance'])->name('performance');
        Route::post('/calculate-performance', [PropertyPortfolioAnalysisController::class, 'calculatePerformance'])->name('performance.calculate');
        Route::get('/rebalancing', [PropertyPortfolioAnalysisController::class, 'rebalancing'])->name('rebalancing');
        Route::post('/generate-rebalancing', [PropertyPortfolioAnalysisController::class, 'generateRebalancing'])->name('rebalancing.generate');
        
        Route::get('/export', [PropertyPortfolioAnalysisController::class, 'export'])->name('export');
    });
    
    // Tax Benefit Calculator Routes
    Route::prefix('tax-benefits')->name('tax_benefits.')->group(function () {
        Route::get('/', [TaxBenefitCalculatorController::class, 'index'])->name('index');
        Route::post('/calculate', [TaxBenefitCalculatorController::class, 'calculate'])->name('calculate');
        Route::get('/detailed/{analysis}', [TaxBenefitCalculatorController::class, 'detailed'])->name('detailed');
        Route::get('/compare', [TaxBenefitCalculatorController::class, 'compare'])->name('compare');
        Route::post('/save-calculation', [TaxBenefitCalculatorController::class, 'saveCalculation'])->name('save');
        
        Route::get('/depreciation-schedule', [TaxBenefitCalculatorController::class, 'depreciationSchedule'])->name('depreciation');
        Route::post('/generate-depreciation', [TaxBenefitCalculatorController::class, 'generateDepreciationSchedule'])->name('depreciation.generate');
        Route::get('/scenarios', [TaxBenefitCalculatorController::class, 'scenarios'])->name('scenarios');
        Route::get('/tax-optimization', [TaxBenefitCalculatorController::class, 'taxOptimization'])->name('optimization');
        Route::post('/generate-optimization', [TaxBenefitCalculatorController::class, 'generateOptimization'])->name('optimization.generate');
        
        Route::get('/export', [TaxBenefitCalculatorController::class, 'export'])->name('export');
    });
    
    // Property Appreciation Calculator Routes
    Route::prefix('appreciation')->name('appreciation.')->group(function () {
        Route::get('/', [PropertyAppreciationCalculatorController::class, 'index'])->name('index');
        Route::post('/calculate', [PropertyAppreciationCalculatorController::class, 'calculate'])->name('calculate');
        Route::get('/detailed/{projection}', [PropertyAppreciationCalculatorController::class, 'detailed'])->name('detailed');
        Route::get('/compare', [PropertyAppreciationCalculatorController::class, 'compare'])->name('compare');
        Route::post('/save-projection', [PropertyAppreciationCalculatorController::class, 'saveProjection'])->name('save');
        
        Route::get('/market-analysis', [PropertyAppreciationCalculatorController::class, 'marketAnalysis'])->name('market');
        Route::post('/perform-market-analysis', [PropertyAppreciationCalculatorController::class, 'performMarketAnalysis'])->name('market.perform');
        Route::get('/scenarios', [PropertyAppreciationCalculatorController::class, 'scenarios'])->name('scenarios');
        Route::get('/sensitivity', [PropertyAppreciationCalculatorController::class, 'sensitivity'])->name('sensitivity');
        Route::post('/perform-sensitivity', [PropertyAppreciationCalculatorController::class, 'performSensitivity'])->name('sensitivity.perform');
        
        Route::get('/export', [PropertyAppreciationCalculatorController::class, 'export'])->name('export');
    });
});

// API Routes for AJAX requests
Route::prefix('api/financial')->name('api.financial.')->middleware(['auth'])->group(function () {
    
    // Property Analysis API
    Route::get('/analyses', [PropertyFinancialAnalysisController::class, 'apiIndex'])->name('analyses.index');
    Route::get('/analyses/{analysis}', [PropertyFinancialAnalysisController::class, 'apiShow'])->name('analyses.show');
    Route::post('/analyses/{analysis}/recalculate', [PropertyFinancialAnalysisController::class, 'apiRecalculate'])->name('analyses.recalculate');
    
    // ROI Calculator API
    Route::post('/roi/calculate', [RoiCalculatorController::class, 'apiCalculate'])->name('roi.calculate');
    Route::post('/roi/advanced-calculate', [RoiCalculatorController::class, 'apiAdvancedCalculate'])->name('roi.advanced.calculate');
    Route::post('/roi/compare', [RoiCalculatorController::class, 'apiCompare'])->name('roi.compare');
    
    // Cash Flow API
    Route::post('/cash-flow/analyze', [CashFlowAnalysisController::class, 'apiAnalyze'])->name('cash_flow.analyze');
    Route::post('/cash-flow/sensitivity', [CashFlowAnalysisController::class, 'apiSensitivity'])->name('cash_flow.sensitivity');
    
    // Cap Rate API
    Route::post('/cap-rate/calculate', [CapRateCalculatorController::class, 'apiCalculate'])->name('cap_rate.calculate');
    Route::post('/cap-rate/market-analysis', [CapRateCalculatorController::class, 'apiMarketAnalysis'])->name('cap_rate.market.analysis');
    
    // Valuation API
    Route::post('/valuation/comparative', [PropertyValuationModelController::class, 'apiComparative'])->name('valuation.comparative');
    Route::post('/valuation/income', [PropertyValuationModelController::class, 'apiIncome'])->name('valuation.income');
    Route::post('/valuation/cost', [PropertyValuationModelController::class, 'apiCost'])->name('valuation.cost');
    Route::post('/valuation/automated', [PropertyValuationModelController::class, 'apiAutomated'])->name('valuation.automated');
    
    // Investment Scenarios API
    Route::post('/scenarios/analyze', [InvestmentScenarioController::class, 'apiAnalyze'])->name('scenarios.analyze');
    Route::post('/scenarios/monte-carlo', [InvestmentScenarioController::class, 'apiMonteCarlo'])->name('scenarios.monte_carlo');
    Route::post('/scenarios/sensitivity', [InvestmentScenarioController::class, 'apiSensitivity'])->name('scenarios.sensitivity');
    Route::post('/scenarios/stress-test', [InvestmentScenarioController::class, 'apiStressTest'])->name('scenarios.stress_test');
    
    // Portfolio API
    Route::post('/portfolio/analyze', [PropertyPortfolioAnalysisController::class, 'apiAnalyze'])->name('portfolio.analyze');
    Route::post('/portfolio/diversification', [PropertyPortfolioAnalysisController::class, 'apiDiversification'])->name('portfolio.diversification');
    Route::post('/portfolio/risk-analysis', [PropertyPortfolioAnalysisController::class, 'apiRiskAnalysis'])->name('portfolio.risk');
    Route::post('/portfolio/performance', [PropertyPortfolioAnalysisController::class, 'apiPerformance'])->name('portfolio.performance');
    
    // Tax Benefits API
    Route::post('/tax-benefits/calculate', [TaxBenefitCalculatorController::class, 'apiCalculate'])->name('tax_benefits.calculate');
    Route::post('/tax-benefits/depreciation', [TaxBenefitCalculatorController::class, 'apiDepreciation'])->name('tax_benefits.depreciation');
    Route::post('/tax-benefits/optimization', [TaxBenefitCalculatorController::class, 'apiOptimization'])->name('tax_benefits.optimization');
    
    // Appreciation API
    Route::post('/appreciation/calculate', [PropertyAppreciationCalculatorController::class, 'apiCalculate'])->name('appreciation.calculate');
    Route::post('/appreciation/market-analysis', [PropertyAppreciationCalculatorController::class, 'apiMarketAnalysis'])->name('appreciation.market');
    Route::post('/appreciation/sensitivity', [PropertyAppreciationCalculatorController::class, 'apiSensitivity'])->name('appreciation.sensitivity');
});

// Public Routes (if needed for public access to certain financial tools)
Route::prefix('financial-tools')->name('public.financial.')->group(function () {
    Route::get('/roi-calculator', [RoiCalculatorController::class, 'publicIndex'])->name('roi.public');
    Route::get('/cash-flow-analyzer', [CashFlowAnalysisController::class, 'publicIndex'])->name('cash_flow.public');
    Route::get('/cap-rate-calculator', [CapRateCalculatorController::class, 'publicIndex'])->name('cap_rate.public');
    Route::get('/appreciation-calculator', [PropertyAppreciationCalculatorController::class, 'publicIndex'])->name('appreciation.public');
});
