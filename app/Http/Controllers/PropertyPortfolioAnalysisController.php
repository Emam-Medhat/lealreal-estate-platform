<?php

namespace App\Http\Controllers;

use App\Models\PortfolioAnalysis;
use App\Models\PropertyFinancialAnalysis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyPortfolioAnalysisController extends Controller
{
    public function index(): View
    {
        return view('financial.portfolio-analysis.index');
    }

    public function create(): View
    {
        return view('financial.portfolio-analysis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'property_ids' => 'required|array|min:1',
            'property_ids.*' => 'exists:properties,id',
            'analysis_parameters' => 'required|array',
            'risk_tolerance' => 'required|in:low,medium,high',
            'investment_goals' => 'required|array',
            'time_horizon' => 'required|integer|min:1|max:30'
        ]);

        $portfolioAnalysis = PortfolioAnalysis::create($validated);

        return redirect()
            ->route('financial.portfolio.show', $portfolioAnalysis)
            ->with('success', 'تم إنشاء تحليل المحفظة العقارية بنجاح');
    }

    public function show(PortfolioAnalysis $portfolio): View
    {
        $portfolio->load(['properties']);
        $analysis = $this->performPortfolioAnalysis($portfolio);
        
        return view('financial.portfolio-analysis.show', compact('portfolio', 'analysis'));
    }

    public function edit(PortfolioAnalysis $portfolio): View
    {
        return view('financial.portfolio-analysis.edit', compact('portfolio'));
    }

    public function update(Request $request, PortfolioAnalysis $portfolio)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'analysis_parameters' => 'required|array',
            'risk_tolerance' => 'required|in:low,medium,high',
            'investment_goals' => 'required|array',
            'time_horizon' => 'required|integer|min:1|max:30'
        ]);

        $portfolio->update($validated);

        return redirect()
            ->route('financial.portfolio.show', $portfolio)
            ->with('success', 'تم تحديث تحليل المحفظة العقارية بنجاح');
    }

    public function destroy(PortfolioAnalysis $portfolio)
    {
        $portfolio->delete();
        return redirect()
            ->route('financial.portfolio.index')
            ->with('success', 'تم حذف تحليل المحفظة العقارية بنجاح');
    }

    public function analyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'properties' => 'required|array|min:1',
            'properties.*.id' => 'required|exists:properties,id',
            'properties.*.weight' => 'required|numeric|min:0|max:100',
            'analysis_parameters' => 'required|array',
            'time_horizon' => 'required|integer|min:1|max:30'
        ]);

        $analysisResult = $this->performRealTimePortfolioAnalysis($validated);

        return response()->json([
            'success' => true,
            'data' => $analysisResult
        ]);
    }

    public function diversification(): View
    {
        return view('financial.portfolio-analysis.diversification');
    }

    public function calculateDiversification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'properties' => 'required|array|min:1',
            'diversification_metrics' => 'required|array'
        ]);

        $diversificationResult = $this->calculateDiversificationMetrics($validated);

        return response()->json([
            'success' => true,
            'data' => $diversificationResult
        ]);
    }

    public function optimization(): View
    {
        return view('financial.portfolio-analysis.optimization');
    }

    public function optimizePortfolio(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'available_properties' => 'required|array|min:1',
            'optimization_constraints' => 'required|array',
            'objective_function' => 'required|in:max_return,min_risk,max_sharpe'
        ]);

        $optimizationResult = $this->performPortfolioOptimization($validated);

        return response()->json([
            'success' => true,
            'data' => $optimizationResult
        ]);
    }

    public function riskAnalysis(): View
    {
        return view('financial.portfolio-analysis.risk-analysis');
    }

    public function performRiskAnalysis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'portfolio_properties' => 'required|array|min:1',
            'risk_scenarios' => 'required|array',
            'correlation_matrix' => 'nullable|array'
        ]);

        $riskAnalysisResult = $this->performPortfolioRiskAnalysis($validated);

        return response()->json([
            'success' => true,
            'data' => $riskAnalysisResult
        ]);
    }

    public function performance(): View
    {
        return view('financial.portfolio-analysis.performance');
    }

    public function calculatePerformance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'portfolio_data' => 'required|array',
            'benchmark_data' => 'nullable|array',
            'time_period' => 'required|array'
        ]);

        $performanceResult = $this->calculatePortfolioPerformance($validated);

        return response()->json([
            'success' => true,
            'data' => $performanceResult
        ]);
    }

    public function rebalancing(): View
    {
        return view('financial.portfolio-analysis.rebalancing');
    }

    public function generateRebalancing(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_portfolio' => 'required|array',
            'target_allocation' => 'required|array',
            'rebalancing_constraints' => 'nullable|array'
        ]);

        $rebalancingResult = $this->generateRebalancingStrategy($validated);

        return response()->json([
            'success' => true,
            'data' => $rebalancingResult
        ]);
    }

    public function export(PortfolioAnalysis $portfolio)
    {
        $analysis = $this->performPortfolioAnalysis($portfolio);
        
        return response()->json([
            'portfolio' => $portfolio->load(['properties']),
            'analysis' => $analysis,
            'exported_at' => now()
        ]);
    }

    private function performPortfolioAnalysis(PortfolioAnalysis $portfolio): array
    {
        $properties = $portfolio->properties;
        $totalValue = $properties->sum(function($property) {
            return $property->financialAnalysis?->current_value ?? $property->price ?? 0;
        });

        $portfolioMetrics = $this->calculatePortfolioMetrics($properties, $totalValue);
        $riskMetrics = $this->calculatePortfolioRisk($properties);
        $diversificationMetrics = $this->calculateDiversificationScore($properties);
        $performanceMetrics = $this->calculateHistoricalPerformance($properties);

        return [
            'summary' => [
                'total_properties' => $properties->count(),
                'total_value' => $totalValue,
                'average_property_value' => $properties->count() > 0 ? $totalValue / $properties->count() : 0,
                'portfolio_age' => $this->calculatePortfolioAge($properties)
            ],
            'metrics' => $portfolioMetrics,
            'risk_analysis' => $riskMetrics,
            'diversification' => $diversificationMetrics,
            'performance' => $performanceMetrics,
            'recommendations' => $this->generatePortfolioRecommendations($portfolioMetrics, $riskMetrics, $diversificationMetrics)
        ];
    }

    private function calculatePortfolioMetrics($properties, float $totalValue): array
    {
        $totalIncome = 0;
        $totalExpenses = 0;
        $weightedRoi = 0;
        $propertyTypes = [];

        foreach ($properties as $property) {
            $financialAnalysis = $property->financialAnalysis;
            if ($financialAnalysis) {
                $propertyValue = $financialAnalysis->current_value;
                $weight = $totalValue > 0 ? $propertyValue / $totalValue : 0;
                
                $annualIncome = $financialAnalysis->rental_income * 12 * (1 - $financialAnalysis->vacancy_rate / 100);
                $annualExpenses = $financialAnalysis->operating_expenses * 12;
                
                $totalIncome += $annualIncome;
                $totalExpenses += $annualExpenses;
                
                $propertyRoi = $financialAnalysis->roiCalculations()->first()?->roi_percentage ?? 0;
                $weightedRoi += $propertyRoi * $weight;
                
                $propertyTypes[$property->property_type ?? 'unknown'] = ($propertyTypes[$property->property_type ?? 'unknown'] ?? 0) + $propertyValue;
            }
        }

        $netOperatingIncome = $totalIncome - $totalExpenses;
        $portfolioCapRate = $totalValue > 0 ? ($netOperatingIncome / $totalValue) * 100 : 0;
        $cashFlowYield = $totalValue > 0 ? ($netOperatingIncome / $totalValue) * 100 : 0;

        return [
            'total_annual_income' => $totalIncome,
            'total_annual_expenses' => $totalExpenses,
            'net_operating_income' => $netOperatingIncome,
            'portfolio_cap_rate' => $portfolioCapRate,
            'cash_flow_yield' => $cashFlowYield,
            'weighted_average_roi' => $weightedRoi,
            'operating_efficiency' => $totalIncome > 0 ? ($netOperatingIncome / $totalIncome) * 100 : 0,
            'property_type_distribution' => $this->calculateTypeDistribution($propertyTypes, $totalValue)
        ];
    }

    private function calculatePortfolioRisk($properties): array
    {
        $riskScores = [];
        $volatilityScores = [];
        $concentrationRisk = 0;

        foreach ($properties as $property) {
            $financialAnalysis = $property->financialAnalysis;
            if ($financialAnalysis) {
                // Calculate individual property risk
                $vacancyRisk = $financialAnalysis->vacancy_rate;
                $marketRisk = $this->calculateMarketRisk($property);
                $propertyRisk = ($vacancyRisk + $marketRisk) / 2;
                
                $riskScores[] = $propertyRisk;
                
                // Calculate volatility based on cash flow projections
                $cashFlows = $financialAnalysis->cashFlowProjections()->pluck('net_cash_flow');
                if ($cashFlows->count() > 0) {
                    $mean = $cashFlows->avg();
                    $variance = $cashFlows->sum(function($value) use ($mean) {
                        return pow($value - $mean, 2);
                    }) / $cashFlows->count();
                    $volatilityScores[] = sqrt($variance);
                }
            }
        }

        // Calculate concentration risk (largest property as % of portfolio)
        if ($properties->count() > 0) {
            $totalValue = $properties->sum(function($property) {
                return $property->financialAnalysis?->current_value ?? 0;
            });
            $maxPropertyValue = $properties->max(function($property) {
                return $property->financialAnalysis?->current_value ?? 0;
            });
            $concentrationRisk = $totalValue > 0 ? ($maxPropertyValue / $totalValue) * 100 : 0;
        }

        $averageRisk = count($riskScores) > 0 ? array_sum($riskScores) / count($riskScores) : 0;
        $averageVolatility = count($volatilityScores) > 0 ? array_sum($volatilityScores) / count($volatilityScores) : 0;

        return [
            'average_property_risk' => $averageRisk,
            'portfolio_volatility' => $averageVolatility,
            'concentration_risk' => $concentrationRisk,
            'risk_level' => $this->assessRiskLevel($averageRisk, $concentrationRisk),
            'diversification_benefit' => $this->calculateDiversificationBenefit($riskScores)
        ];
    }

    private function calculateDiversificationScore($properties): array
    {
        $diversificationFactors = [
            'geographic' => $this->calculateGeographicDiversification($properties),
            'property_type' => $this->calculatePropertyTypeDiversification($properties),
            'price_range' => $this->calculatePriceRangeDiversification($properties),
            'income_source' => $this->calculateIncomeSourceDiversification($properties)
        ];

        $overallScore = array_sum($diversificationFactors) / count($diversificationFactors);

        return [
            'overall_score' => $overallScore,
            'factors' => $diversificationFactors,
            'diversification_level' => $this->assessDiversificationLevel($overallScore),
            'recommendations' => $this->generateDiversificationRecommendations($diversificationFactors)
        ];
    }

    private function calculateHistoricalPerformance($properties): array
    {
        $performanceData = [];
        $monthlyReturns = [];

        foreach ($properties as $property) {
            $financialAnalysis = $property->financialAnalysis;
            if ($financialAnalysis) {
                $valuations = $financialAnalysis->propertyValuations()
                    ->orderBy('valuation_date')
                    ->get();

                if ($valuations->count() > 1) {
                    $propertyReturns = $this->calculatePropertyReturns($valuations);
                    $performanceData[$property->id] = $propertyReturns;
                    $monthlyReturns = array_merge($monthlyReturns, $propertyReturns);
                }
            }
        }

        // Calculate portfolio-level performance metrics
        if (!empty($monthlyReturns)) {
            $averageMonthlyReturn = array_sum($monthlyReturns) / count($monthlyReturns);
            $volatility = $this->calculateVolatility($monthlyReturns);
            $sharpeRatio = $volatility > 0 ? ($averageMonthlyReturn / $volatility) * sqrt(12) : 0;

            return [
                'average_monthly_return' => $averageMonthlyReturn,
                'annualized_return' => $averageMonthlyReturn * 12,
                'volatility' => $volatility,
                'sharpe_ratio' => $sharpeRatio,
                'max_drawdown' => $this->calculateMaxDrawdown($monthlyReturns),
                'property_performance' => $performanceData
            ];
        }

        return [
            'average_monthly_return' => 0,
            'annualized_return' => 0,
            'volatility' => 0,
            'sharpe_ratio' => 0,
            'max_drawdown' => 0,
            'property_performance' => []
        ];
    }

    private function performRealTimePortfolioAnalysis(array $data): array
    {
        $properties = $data['properties'];
        $analysisParameters = $data['analysis_parameters'];
        $timeHorizon = $data['time_horizon'];

        // Calculate weighted portfolio metrics
        $totalWeight = array_sum(array_column($properties, 'weight'));
        $portfolioMetrics = [
            'weighted_roi' => 0,
            'weighted_risk' => 0,
            'diversification_score' => 0
        ];

        foreach ($properties as $property) {
            $weight = $property['weight'] / $totalWeight;
            // Simulate property analysis (in real implementation, this would fetch actual data)
            $propertyAnalysis = $this->simulatePropertyAnalysis($property['id'], $analysisParameters);
            
            $portfolioMetrics['weighted_roi'] += $propertyAnalysis['roi'] * $weight;
            $portfolioMetrics['weighted_risk'] += $propertyAnalysis['risk'] * $weight;
            $portfolioMetrics['diversification_score'] += $propertyAnalysis['diversification'] * $weight;
        }

        // Generate projections
        $projections = $this->generatePortfolioProjections($portfolioMetrics, $timeHorizon);

        return [
            'current_metrics' => $portfolioMetrics,
            'projections' => $projections,
            'risk_analysis' => $this->analyzePortfolioRisk($properties, $portfolioMetrics),
            'optimization_suggestions' => $this->generateOptimizationSuggestions($portfolioMetrics)
        ];
    }

    private function calculateDiversificationMetrics(array $data): array
    {
        $properties = $data['properties'];
        $metrics = $data['diversification_metrics'];

        $diversificationScores = [];

        // Geographic diversification
        if (isset($metrics['geographic'])) {
            $diversificationScores['geographic'] = $this->calculateGeographicSpread($properties);
        }

        // Property type diversification
        if (isset($metrics['property_types'])) {
            $diversificationScores['property_types'] = $this->calculateTypeSpread($properties);
        }

        // Price range diversification
        if (isset($metrics['price_ranges'])) {
            $diversificationScores['price_ranges'] = $this->calculatePriceSpread($properties);
        }

        // Income diversification
        if (isset($metrics['income_sources'])) {
            $diversificationScores['income_sources'] = $this->calculateIncomeSpread($properties);
        }

        $overallScore = array_sum($diversificationScores) / count($diversificationScores);

        return [
            'scores' => $diversificationScores,
            'overall_score' => $overallScore,
            'diversification_level' => $this->assessDiversificationLevel($overallScore),
            'improvement_areas' => $this->identifyDiversificationGaps($diversificationScores)
        ];
    }

    private function performPortfolioOptimization(array $data): array
    {
        $availableProperties = $data['available_properties'];
        $constraints = $data['optimization_constraints'];
        $objective = $data['objective_function'];

        // This is a simplified optimization algorithm
        // In practice, you would use more sophisticated optimization methods
        
        $optimalPortfolios = [];
        
        // Generate multiple portfolio combinations
        for ($i = 0; $i < 100; $i++) {
            $portfolio = $this->generateRandomPortfolio($availableProperties, $constraints);
            $portfolioMetrics = $this->evaluatePortfolio($portfolio, $objective);
            $optimalPortfolios[] = $portfolioMetrics;
        }

        // Sort by objective function and select best
        usort($optimalPortfolios, function($a, $b) use ($objective) {
            return $b['objective_score'] <=> $a['objective_score'];
        });

        $bestPortfolio = $optimalPortfolios[0];

        return [
            'optimal_portfolio' => $bestPortfolio,
            'efficient_frontier' => $this->calculateEfficientFrontier($optimalPortfolios),
            'optimization_details' => [
                'objective_function' => $objective,
                'constraints' => $constraints,
                'portfolios_evaluated' => count($optimalPortfolios)
            ],
            'sensitivity_analysis' => $this->performOptimizationSensitivity($bestPortfolio, $constraints)
        ];
    }

    private function performPortfolioRiskAnalysis(array $data): array
    {
        $properties = $data['portfolio_properties'];
        $scenarios = $data['risk_scenarios'];
        $correlationMatrix = $data['correlation_matrix'] ?? [];

        $riskAnalysis = [];

        foreach ($scenarios as $scenarioName => $scenarioData) {
            $scenarioImpact = $this->calculateScenarioImpact($properties, $scenarioData, $correlationMatrix);
            $riskAnalysis[$scenarioName] = $scenarioImpact;
        }

        // Calculate aggregate risk metrics
        $aggregateRisk = $this->calculateAggregateRisk($riskAnalysis);

        return [
            'scenario_analysis' => $riskAnalysis,
            'aggregate_risk' => $aggregateRisk,
            'risk_metrics' => [
                'value_at_risk_95' => $this->calculateVaR($properties, 0.95),
                'conditional_var' => $this->calculateCVaR($properties, 0.95),
                'stress_test_results' => $this->performStressTests($properties)
            ],
            'risk_mitigation' => $this->generateRiskMitigationStrategies($aggregateRisk)
        ];
    }

    private function calculatePortfolioPerformance(array $data): array
    {
        $portfolioData = $data['portfolio_data'];
        $benchmarkData = $data['benchmark_data'] ?? null;
        $timePeriod = $data['time_period'];

        $returns = $this->calculateTimeSeriesReturns($portfolioData, $timePeriod);
        $benchmarkReturns = $benchmarkData ? $this->calculateTimeSeriesReturns($benchmarkData, $timePeriod) : null;

        $performanceMetrics = [
            'total_return' => $this->calculateTotalReturn($returns),
            'annualized_return' => $this->calculateAnnualizedReturn($returns, $timePeriod),
            'volatility' => $this->calculateVolatility($returns),
            'sharpe_ratio' => $this->calculateSharpeRatio($returns),
            'max_drawdown' => $this->calculateMaxDrawdown($returns),
            'alpha' => $benchmarkReturns ? $this->calculateAlpha($returns, $benchmarkReturns) : 0,
            'beta' => $benchmarkReturns ? $this->calculateBeta($returns, $benchmarkReturns) : 0
        ];

        return [
            'performance_metrics' => $performanceMetrics,
            'benchmark_comparison' => $benchmarkReturns ? $this->compareWithBenchmark($performanceMetrics, $benchmarkReturns) : null,
            'performance_attribution' => $this->calculatePerformanceAttribution($portfolioData),
            'rolling_returns' => $this->calculateRollingReturns($returns)
        ];
    }

    private function generateRebalancingStrategy(array $data): array
    {
        $currentPortfolio = $data['current_portfolio'];
        $targetAllocation = $data['target_allocation'];
        $constraints = $data['rebalancing_constraints'] ?? [];

        $rebalancingActions = [];
        $totalCurrentValue = array_sum(array_column($currentPortfolio, 'current_value'));

        foreach ($targetAllocation as $asset => $targetWeight) {
            $currentValue = $currentPortfolio[$asset]['current_value'] ?? 0;
            $currentWeight = $totalCurrentValue > 0 ? ($currentValue / $totalCurrentValue) * 100 : 0;
            $targetValue = ($targetWeight / 100) * $totalCurrentValue;
            
            $difference = $targetValue - $currentValue;
            $action = $difference > 0 ? 'buy' : 'sell';
            $amount = abs($difference);

            if ($amount > 0) {
                $rebalancingActions[] = [
                    'asset' => $asset,
                    'action' => $action,
                    'amount' => $amount,
                    'current_weight' => $currentWeight,
                    'target_weight' => $targetWeight,
                    'priority' => $this->calculateRebalancingPriority(abs($currentWeight - $targetWeight))
                ];
            }
        }

        // Sort by priority
        usort($rebalancingActions, function($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        return [
            'rebalancing_actions' => $rebalancingActions,
            'total_transactions' => count($rebalancingActions),
            'estimated_costs' => $this->estimateRebalancingCosts($rebalancingActions),
            'tax_implications' => $this->calculateTaxImplications($rebalancingActions),
            'implementation_schedule' => $this->createImplementationSchedule($rebalancingActions, $constraints)
        ];
    }

    // Helper methods (simplified implementations)
    private function calculateMarketRisk($property): float
    {
        // Simplified market risk calculation
        return 15.0; // Base market risk percentage
    }

    private function calculateTypeDistribution(array $propertyTypes, float $totalValue): array
    {
        $distribution = [];
        foreach ($propertyTypes as $type => $value) {
            $distribution[$type] = $totalValue > 0 ? ($value / $totalValue) * 100 : 0;
        }
        return $distribution;
    }

    private function assessRiskLevel(float $averageRisk, float $concentrationRisk): string
    {
        $totalRisk = ($averageRisk + $concentrationRisk) / 2;
        
        if ($totalRisk < 20) return 'low';
        if ($totalRisk < 40) return 'medium';
        if ($totalRisk < 60) return 'high';
        return 'very_high';
    }

    private function calculateDiversificationBenefit(array $riskScores): float
    {
        if (empty($riskScores)) return 0;
        
        $averageRisk = array_sum($riskScores) / count($riskScores);
        $portfolioRisk = $averageRisk / sqrt(count($riskScores)); // Simplified diversification benefit
        
        return max(0, $averageRisk - $portfolioRisk);
    }

    private function calculateGeographicDiversification($properties): float
    {
        // Simplified geographic diversification calculation
        $locations = $properties->pluck('city')->unique()->count();
        return min(100, ($locations / 5) * 100); // Max 5 locations for full diversification
    }

    private function calculatePropertyTypeDiversification($properties): float
    {
        $types = $properties->pluck('property_type')->unique()->count();
        return min(100, ($types / 4) * 100); // Max 4 types for full diversification
    }

    private function calculatePriceRangeDiversification($properties): float
    {
        // Simplified price range diversification
        return 75.0; // Placeholder
    }

    private function calculateIncomeSourceDiversification($properties): float
    {
        // Simplified income source diversification
        return 80.0; // Placeholder
    }

    private function assessDiversificationLevel(float $score): string
    {
        if ($score >= 80) return 'excellent';
        if ($score >= 60) return 'good';
        if ($score >= 40) return 'moderate';
        return 'poor';
    }

    private function generateDiversificationRecommendations(array $factors): array
    {
        $recommendations = [];
        
        foreach ($factors as $factor => $score) {
            if ($score < 60) {
                $recommendations[] = [
                    'factor' => $factor,
                    'recommendation' => "تحسين التنويع في {$factor}",
                    'priority' => $score < 40 ? 'high' : 'medium'
                ];
            }
        }
        
        return $recommendations;
    }

    private function calculatePropertyReturns($valuations): array
    {
        $returns = [];
        for ($i = 1; $i < $valuations->count(); $i++) {
            $previousValue = $valuations[$i-1]->valuation_amount;
            $currentValue = $valuations[$i]->valuation_amount;
            $return = $previousValue > 0 ? (($currentValue - $previousValue) / $previousValue) * 100 : 0;
            $returns[] = $return;
        }
        return $returns;
    }

    private function calculateVolatility(array $returns): float
    {
        if (empty($returns)) return 0;
        
        $mean = array_sum($returns) / count($returns);
        $variance = array_sum(array_map(function($return) use ($mean) {
            return pow($return - $mean, 2);
        }, $returns)) / count($returns);
        
        return sqrt($variance);
    }

    private function calculateMaxDrawdown(array $returns): float
    {
        if (empty($returns)) return 0;
        
        $peak = $returns[0];
        $maxDrawdown = 0;
        $currentValue = 0;
        
        foreach ($returns as $return) {
            $currentValue += $return;
            if ($currentValue > $peak) {
                $peak = $currentValue;
            }
            $drawdown = $peak - $currentValue;
            if ($drawdown > $maxDrawdown) {
                $maxDrawdown = $drawdown;
            }
        }
        
        return $maxDrawdown;
    }

    private function calculatePortfolioAge($properties): float
    {
        if ($properties->isEmpty()) return 0;
        
        $totalAge = $properties->sum(function($property) {
            return $property->created_at->diffInYears(now());
        });
        
        return $totalAge / $properties->count();
    }

    private function generatePortfolioRecommendations(array $metrics, array $risk, array $diversification): array
    {
        $recommendations = [];
        
        // ROI recommendations
        if ($metrics['weighted_average_roi'] < 8) {
            $recommendations[] = [
                'type' => 'performance',
                'message' => 'العائد على الاستثمار منخفض. فكر في تحسين الدخل أو إعادة تقييم العقارات',
                'priority' => 'high'
            ];
        }
        
        // Risk recommendations
        if ($risk['risk_level'] === 'high' || $risk['risk_level'] === 'very_high') {
            $recommendations[] = [
                'type' => 'risk',
                'message' => 'مستوى المخاطر مرتفع. فكر في تنويع المحفظة',
                'priority' => 'high'
            ];
        }
        
        // Diversification recommendations
        if ($diversification['diversification_level'] === 'poor') {
            $recommendations[] = [
                'type' => 'diversification',
                'message' => 'التنويع ضعيف. أضف عقارات من أنواع أو مناطق مختلفة',
                'priority' => 'medium'
            ];
        }
        
        return $recommendations;
    }

    // Additional helper methods for portfolio optimization and analysis
    private function simulatePropertyAnalysis(int $propertyId, array $parameters): array
    {
        // Simulated property analysis - in real implementation, fetch actual data
        return [
            'roi' => rand(5, 15),
            'risk' => rand(10, 30),
            'diversification' => rand(40, 80)
        ];
    }

    private function generatePortfolioProjections(array $metrics, int $timeHorizon): array
    {
        $projections = [];
        $currentValue = 1000000; // Base value
        $annualReturn = $metrics['weighted_roi'] / 100;
        
        for ($year = 1; $year <= $timeHorizon; $year++) {
            $projectedValue = $currentValue * pow(1 + $annualReturn, $year);
            $projections[] = [
                'year' => $year,
                'projected_value' => $projectedValue,
                'annual_return' => $annualReturn * 100,
                'cumulative_return' => (($projectedValue - $currentValue) / $currentValue) * 100
            ];
        }
        
        return $projections;
    }

    private function analyzePortfolioRisk(array $properties, array $metrics): array
    {
        return [
            'portfolio_risk_score' => $metrics['weighted_risk'],
            'risk_factors' => [
                'market_risk' => 60,
                'concentration_risk' => 30,
                'liquidity_risk' => 20
            ],
            'risk_mitigation_suggestions' => [
                'Increase diversification across geographic areas',
                'Consider properties with different risk profiles',
                'Maintain adequate cash reserves'
            ]
        ];
    }

    private function generateOptimizationSuggestions(array $metrics): array
    {
        $suggestions = [];
        
        if ($metrics['weighted_roi'] < 10) {
            $suggestions[] = 'Consider adding higher-yielding properties';
        }
        
        if ($metrics['weighted_risk'] > 25) {
            $suggestions[] = 'Reduce exposure to high-risk properties';
        }
        
        if ($metrics['diversification_score'] < 70) {
            $suggestions[] = 'Improve portfolio diversification';
        }
        
        return $suggestions;
    }
}
