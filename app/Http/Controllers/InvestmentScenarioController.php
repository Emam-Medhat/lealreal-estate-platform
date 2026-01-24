<?php

namespace App\Http\Controllers;

use App\Models\InvestmentScenario;
use App\Models\PropertyFinancialAnalysis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class InvestmentScenarioController extends Controller
{
    public function index(): View
    {
        return view('financial.investment-scenarios.index');
    }

    public function create(): View
    {
        return view('financial.investment-scenarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_financial_analysis_id' => 'required|exists:property_financial_analyses,id',
            'scenario_name' => 'required|string|max:255',
            'scenario_type' => 'required|in:optimistic,conservative,pessimistic,custom',
            'investment_parameters' => 'required|array',
            'assumptions' => 'required|array',
            'projected_returns' => 'required|array',
            'risk_factors' => 'nullable|array',
            'probability_score' => 'required|integer|min:0|max:100',
            'time_horizon' => 'required|integer|min:1|max:30',
            'notes' => 'nullable|string'
        ]);

        $scenario = InvestmentScenario::create($validated);

        return redirect()
            ->route('financial.scenarios.show', $scenario)
            ->with('success', 'تم إنشاء سيناريو الاستثمار بنجاح');
    }

    public function show(InvestmentScenario $scenario): View
    {
        $scenario->load(['propertyFinancialAnalysis.property']);
        
        return view('financial.investment-scenarios.show', compact('scenario'));
    }

    public function edit(InvestmentScenario $scenario): View
    {
        return view('financial.investment-scenarios.edit', compact('scenario'));
    }

    public function update(Request $request, InvestmentScenario $scenario)
    {
        $validated = $request->validate([
            'scenario_name' => 'required|string|max:255',
            'scenario_type' => 'required|in:optimistic,conservative,pessimistic,custom',
            'investment_parameters' => 'required|array',
            'assumptions' => 'required|array',
            'projected_returns' => 'required|array',
            'risk_factors' => 'nullable|array',
            'probability_score' => 'required|integer|min:0|max:100',
            'time_horizon' => 'required|integer|min:1|max:30',
            'notes' => 'nullable|string'
        ]);

        $scenario->update($validated);

        return redirect()
            ->route('financial.scenarios.show', $scenario)
            ->with('success', 'تم تحديث سيناريو الاستثمار بنجاح');
    }

    public function destroy(InvestmentScenario $scenario)
    {
        $scenario->delete();
        return redirect()
            ->route('financial.scenarios.index')
            ->with('success', 'تم حذف سيناريو الاستثمار بنجاح');
    }

    public function analyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'base_property_data' => 'required|array',
            'scenario_parameters' => 'required|array',
            'time_horizon' => 'required|integer|min:1|max:30'
        ]);

        $analysisResult = $this->performScenarioAnalysis($validated);

        return response()->json([
            'success' => true,
            'data' => $analysisResult
        ]);
    }

    public function compare(): View
    {
        return view('financial.investment-scenarios.compare');
    }

    public function compareScenarios(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scenarios' => 'required|array|min:2',
            'scenarios.*.name' => 'required|string',
            'scenarios.*.data' => 'required|array'
        ]);

        $comparisonResult = $this->performScenarioComparison($validated['scenarios']);

        return response()->json([
            'success' => true,
            'data' => $comparisonResult
        ]);
    }

    public function monteCarlo(): View
    {
        return view('financial.investment-scenarios.monte-carlo');
    }

    public function runMonteCarlo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'base_parameters' => 'required|array',
            'variable_ranges' => 'required|array',
            'simulations' => 'required|integer|min:100|max:10000'
        ]);

        $monteCarloResult = $this->performMonteCarloSimulation($validated);

        return response()->json([
            'success' => true,
            'data' => $monteCarloResult
        ]);
    }

    public function sensitivity(): View
    {
        return view('financial.investment-scenarios.sensitivity');
    }

    public function performSensitivity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'base_case' => 'required|array',
            'sensitivity_variables' => 'required|array',
            'variation_range' => 'required|array'
        ]);

        $sensitivityResult = $this->performSensitivityAnalysis($validated);

        return response()->json([
            'success' => true,
            'data' => $sensitivityResult
        ]);
    }

    public function stressTest(): View
    {
        return view('financial.investment-scenarios.stress-test');
    }

    public function performStressTest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'base_scenario' => 'required|array',
            'stress_scenarios' => 'required|array'
        ]);

        $stressTestResult = $this->performStressTesting($validated);

        return response()->json([
            'success' => true,
            'data' => $stressTestResult
        ]);
    }

    public function scenarios(PropertyFinancialAnalysis $analysis): View
    {
        $scenarios = $this->generateStandardScenarios($analysis);
        
        return view('financial.investment-scenarios.property', compact('analysis', 'scenarios'));
    }

    public function export(InvestmentScenario $scenario)
    {
        return response()->json([
            'scenario' => $scenario->load(['propertyFinancialAnalysis.property']),
            'exported_at' => now()
        ]);
    }

    private function performScenarioAnalysis(array $data): array
    {
        $basePropertyData = $data['base_property_data'];
        $scenarioParameters = $data['scenario_parameters'];
        $timeHorizon = $data['time_horizon'];

        $scenarios = [];

        // Generate different scenario types
        $scenarioTypes = ['optimistic', 'conservative', 'pessimistic', 'realistic'];

        foreach ($scenarioTypes as $type) {
            $scenarios[$type] = $this->generateScenario($basePropertyData, $scenarioParameters, $type, $timeHorizon);
        }

        // Calculate scenario comparison metrics
        $comparison = $this->compareScenarioData($scenarios);

        return [
            'scenarios' => $scenarios,
            'comparison' => $comparison,
            'recommendations' => $this->generateScenarioRecommendations($scenarios, $comparison)
        ];
    }

    private function generateScenario(array $baseData, array $parameters, string $type, int $timeHorizon): array
    {
        $multipliers = $this->getScenarioMultipliers($type);
        
        $scenarioData = [
            'purchase_price' => $baseData['purchase_price'],
            'monthly_rent' => $baseData['monthly_rent'] * $multipliers['rent'],
            'operating_expenses' => $baseData['operating_expenses'] * $multipliers['expenses'],
            'vacancy_rate' => $baseData['vacancy_rate'] * $multipliers['vacancy'],
            'appreciation_rate' => $baseData['appreciation_rate'] * $multipliers['appreciation'],
            'time_horizon' => $timeHorizon
        ];

        // Calculate cash flows for the scenario
        $cashFlows = $this->calculateScenarioCashFlows($scenarioData);
        
        // Calculate ROI metrics
        $roiMetrics = $this->calculateScenarioRoi($cashFlows, $scenarioData['purchase_price']);
        
        // Calculate risk metrics
        $riskMetrics = $this->calculateScenarioRisk($cashFlows, $scenarioData);

        return [
            'type' => $type,
            'parameters' => $scenarioData,
            'cash_flows' => $cashFlows,
            'roi_metrics' => $roiMetrics,
            'risk_metrics' => $riskMetrics,
            'probability_score' => $multipliers['probability']
        ];
    }

    private function getScenarioMultipliers(string $type): array
    {
        $multipliers = [
            'optimistic' => [
                'rent' => 1.2,
                'expenses' => 0.9,
                'vacancy' => 0.5,
                'appreciation' => 1.5,
                'probability' => 25
            ],
            'conservative' => [
                'rent' => 1.05,
                'expenses' => 1.05,
                'vacancy' => 1.1,
                'appreciation' => 0.8,
                'probability' => 40
            ],
            'pessimistic' => [
                'rent' => 0.8,
                'expenses' => 1.2,
                'vacancy' => 1.5,
                'appreciation' => 0.5,
                'probability' => 15
            ],
            'realistic' => [
                'rent' => 1.0,
                'expenses' => 1.0,
                'vacancy' => 1.0,
                'appreciation' => 1.0,
                'probability' => 20
            ]
        ];

        return $multipliers[$type] ?? $multipliers['realistic'];
    }

    private function calculateScenarioCashFlows(array $scenarioData): array
    {
        $purchasePrice = $scenarioData['purchase_price'];
        $monthlyRent = $scenarioData['monthly_rent'];
        $operatingExpenses = $scenarioData['operating_expenses'];
        $vacancyRate = $scenarioData['vacancy_rate'] / 100;
        $appreciationRate = $scenarioData['appreciation_rate'] / 100;
        $timeHorizon = $scenarioData['time_horizon'];

        $cashFlows = [];
        $cumulativeCashFlow = 0;

        for ($year = 1; $year <= $timeHorizon; $year++) {
            $effectiveRent = $monthlyRent * 12 * (1 - $vacancyRate) * pow(1 + $appreciationRate, $year - 1);
            $totalExpenses = $operatingExpenses * 12 * pow(1.03, $year - 1); // 3% annual expense increase
            $netCashFlow = $effectiveRent - $totalExpenses;
            $cumulativeCashFlow += $netCashFlow;

            $cashFlows[] = [
                'year' => $year,
                'gross_income' => $effectiveRent,
                'expenses' => $totalExpenses,
                'net_cash_flow' => $netCashFlow,
                'cumulative_cash_flow' => $cumulativeCashFlow,
                'property_value' => $purchasePrice * pow(1 + $appreciationRate, $year)
            ];
        }

        return $cashFlows;
    }

    private function calculateScenarioRoi(array $cashFlows, float $investment): array
    {
        $totalCashFlow = collect($cashFlows)->sum('net_cash_flow');
        $finalPropertyValue = collect($cashFlows)->last()['property_value'];
        $totalReturn = $totalCashFlow + ($finalPropertyValue - $investment);
        
        $timeHorizon = count($cashFlows);
        $totalRoi = $investment > 0 ? (($totalReturn - $investment) / $investment) * 100 : 0;
        $annualizedRoi = $timeHorizon > 0 ? (pow(1 + ($totalRoi / 100), 1 / $timeHorizon) - 1) * 100 : 0;
        
        $averageAnnualCashFlow = $totalCashFlow / $timeHorizon;
        $cashOnCashRoi = $investment > 0 ? ($averageAnnualCashFlow / $investment) * 100 : 0;

        return [
            'total_investment' => $investment,
            'total_cash_flow' => $totalCashFlow,
            'total_return' => $totalReturn,
            'total_roi_percentage' => $totalRoi,
            'annualized_roi' => $annualizedRoi,
            'cash_on_cash_roi' => $cashOnCashRoi,
            'payback_period' => $this->calculatePaybackPeriod($cashFlows, $investment),
            'net_present_value' => $this->calculateNPV($cashFlows, 0.08),
            'internal_rate_of_return' => $this->calculateIRR($cashFlows, $investment)
        ];
    }

    private function calculateScenarioRisk(array $cashFlows, array $scenarioData): array
    {
        $netCashFlows = collect($cashFlows)->pluck('net_cash_flow');
        $mean = $netCashFlows->avg();
        $variance = $netCashFlows->sum(function($value) use ($mean) {
            return pow($value - $mean, 2);
        }) / $netCashFlows->count();
        $stdDev = sqrt($variance);
        
        $coefficientOfVariation = $mean != 0 ? ($stdDev / abs($mean)) * 100 : 100;
        
        $negativeYears = $netCashFlows->filter(function($value) {
            return $value < 0;
        })->count();
        
        $riskScore = $this->calculateRiskScore($coefficientOfVariation, $negativeYears, count($cashFlows));

        return [
            'cash_flow_volatility' => $stdDev,
            'coefficient_of_variation' => $coefficientOfVariation,
            'negative_cash_flow_years' => $negativeYears,
            'risk_score' => $riskScore,
            'risk_level' => $this->getRiskLevel($riskScore),
            'worst_year' => $netCashFlows->min(),
            'best_year' => $netCashFlows->max()
        ];
    }

    private function calculateRiskScore(float $cov, int $negativeYears, int $totalYears): int
    {
        $volatilityScore = min(50, $cov); // Max 50 points for volatility
        $negativeYearScore = ($negativeYears / $totalYears) * 50; // Max 50 points for negative years
        
        return min(100, $volatilityScore + $negativeYearScore);
    }

    private function getRiskLevel(int $score): string
    {
        if ($score <= 20) return 'low';
        if ($score <= 40) return 'moderate';
        if ($score <= 60) return 'high';
        if ($score <= 80) return 'very_high';
        return 'extreme';
    }

    private function calculatePaybackPeriod(array $cashFlows, float $investment): ?float
    {
        $cumulativeCashFlow = 0;
        
        foreach ($cashFlows as $index => $cashFlow) {
            $cumulativeCashFlow += $cashFlow['net_cash_flow'];
            
            if ($cumulativeCashFlow >= $investment) {
                return $index + 1;
            }
        }
        
        return null;
    }

    private function calculateNPV(array $cashFlows, float $discountRate): float
    {
        $npv = 0;
        
        foreach ($cashFlows as $index => $cashFlow) {
            $year = $index + 1;
            $npv += $cashFlow['net_cash_flow'] / pow(1 + $discountRate, $year);
        }
        
        return $npv;
    }

    private function calculateIRR(array $cashFlows, float $initialInvestment): float
    {
        $rate = 0.1;
        $iterations = 0;
        $maxIterations = 100;
        $tolerance = 0.0001;
        
        while ($iterations < $maxIterations) {
            $npv = -$initialInvestment;
            $dnpv = 0;
            
            foreach ($cashFlows as $index => $cashFlow) {
                $year = $index + 1;
                $factor = pow(1 + $rate, $year);
                $npv += $cashFlow['net_cash_flow'] / $factor;
                $dnpv -= $year * $cashFlow['net_cash_flow'] / ($factor * (1 + $rate));
            }
            
            if (abs($npv) < $tolerance) {
                break;
            }
            
            $rate = $rate - $npv / $dnpv;
            $iterations++;
        }
        
        return $rate * 100;
    }

    private function compareScenarioData(array $scenarios): array
    {
        $comparison = [];
        
        $metrics = ['total_roi_percentage', 'annualized_roi', 'cash_on_cash_roi', 'risk_score'];
        
        foreach ($metrics as $metric) {
            $values = collect($scenarios)->pluck("roi_metrics.{$metric}");
            $comparison[$metric] = [
                'best' => $values->max(),
                'worst' => $values->min(),
                'average' => $values->avg(),
                'range' => $values->max() - $values->min(),
                'best_scenario' => array_keys($scenarios)[$values->search($values->max())]
            ];
        }
        
        return $comparison;
    }

    private function generateScenarioRecommendations(array $scenarios, array $comparison): array
    {
        $recommendations = [];
        
        // Risk-adjusted returns analysis
        $riskAdjustedReturns = [];
        foreach ($scenarios as $type => $scenario) {
            $roi = $scenario['roi_metrics']['total_roi_percentage'];
            $risk = $scenario['risk_metrics']['risk_score'];
            $riskAdjustedReturns[$type] = $risk > 0 ? $roi / $risk : 0;
        }
        
        $bestRiskAdjusted = array_keys($riskAdjustedReturns, max($riskAdjustedReturns))[0];
        
        $recommendations[] = [
            'type' => 'primary',
            'title' => 'أفضل سيناريو معدل حسب المخاطر',
            'content' => "السيناريو {$bestRiskAdjusted} يقدم أفضل توازن بين العائد والمخاطر",
            'scenario' => $bestRiskAdjusted
        ];
        
        // Conservative recommendation
        if ($scenarios['conservative']['risk_metrics']['risk_score'] < 30) {
            $recommendations[] = [
                'type' => 'conservative',
                'title' => 'خيار آمن',
                'content' => 'السيناريو المحافظ يقدم عائدات مستقرة مع مخاطر منخفضة',
                'scenario' => 'conservative'
            ];
        }
        
        // High potential recommendation
        if ($scenarios['optimistic']['roi_metrics']['total_roi_percentage'] > 100) {
            $recommendations[] = [
                'type' => 'aggressive',
                'title' => 'إمكانات عالية',
                'content' => 'السيناريو المتفائل يقدم إمكانات عائد عالية ولكن مع مخاطر أعلى',
                'scenario' => 'optimistic'
            ];
        }
        
        return $recommendations;
    }

    private function performScenarioComparison(array $scenarios): array
    {
        $comparisonData = [];
        
        foreach ($scenarios as $scenario) {
            $comparisonData[] = [
                'name' => $scenario['name'],
                'total_roi' => $scenario['data']['roi_metrics']['total_roi_percentage'] ?? 0,
                'annualized_roi' => $scenario['data']['roi_metrics']['annualized_roi'] ?? 0,
                'risk_score' => $scenario['data']['risk_metrics']['risk_score'] ?? 0,
                'payback_period' => $scenario['data']['roi_metrics']['payback_period'] ?? null,
                'net_present_value' => $scenario['data']['roi_metrics']['net_present_value'] ?? 0
            ];
        }
        
        // Calculate rankings
        $rankings = $this->calculateScenarioRankings($comparisonData);
        
        return [
            'scenarios' => $comparisonData,
            'rankings' => $rankings,
            'best_overall' => $rankings['overall'][0]['name'] ?? null,
            'safest' => $rankings['safety'][0]['name'] ?? null,
            'highest_return' => $rankings['return'][0]['name'] ?? null
        ];
    }

    private function calculateScenarioRankings(array $scenarios): array
    {
        $rankings = [
            'overall' => [],
            'return' => [],
            'safety' => []
        ];
        
        // Overall ranking (weighted score)
        $overallScores = [];
        foreach ($scenarios as $scenario) {
            $score = ($scenario['total_roi'] * 0.4) + 
                     ((100 - $scenario['risk_score']) * 0.3) + 
                     ($scenario['net_present_value'] > 0 ? 30 : 0);
            $overallScores[] = ['name' => $scenario['name'], 'score' => $score];
        }
        
        usort($overallScores, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        $rankings['overall'] = $overallScores;
        
        // Return ranking
        $returnRanking = $scenarios;
        usort($returnRanking, function($a, $b) {
            return $b['total_roi'] <=> $a['total_roi'];
        });
        $rankings['return'] = $returnRanking;
        
        // Safety ranking (inverse of risk score)
        $safetyRanking = $scenarios;
        usort($safetyRanking, function($a, $b) {
            return $a['risk_score'] <=> $b['risk_score'];
        });
        $rankings['safety'] = $safetyRanking;
        
        return $rankings;
    }

    private function performMonteCarloSimulation(array $data): array
    {
        $baseParameters = $data['base_parameters'];
        $variableRanges = $data['variable_ranges'];
        $simulations = $data['simulations'];
        
        $results = [];
        
        for ($i = 0; $i < $simulations; $i++) {
            $simulatedParameters = $this->generateRandomParameters($baseParameters, $variableRanges);
            $scenarioResult = $this->generateScenario($simulatedParameters, [], 'monte_carlo', 10);
            $results[] = $scenarioResult['roi_metrics']['total_roi_percentage'];
        }
        
        // Calculate statistics
        $mean = array_sum($results) / count($results);
        $variance = array_sum(array_map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $results)) / count($results);
        $stdDev = sqrt($variance);
        
        sort($results);
        $count = count($results);
        $percentile5 = $results[intval($count * 0.05)];
        $percentile25 = $results[intval($count * 0.25)];
        $percentile75 = $results[intval($count * 0.75)];
        $percentile95 = $results[intval($count * 0.95)];
        
        return [
            'simulations_run' => $simulations,
            'statistics' => [
                'mean' => $mean,
                'median' => $results[intval($count / 2)],
                'standard_deviation' => $stdDev,
                'min' => $results[0],
                'max' => $results[$count - 1],
                'percentiles' => [
                    '5th' => $percentile5,
                    '25th' => $percentile25,
                    '75th' => $percentile75,
                    '95th' => $percentile95
                ]
            ],
            'probability_analysis' => [
                'probability_of_positive_return' => count(array_filter($results, function($r) { return $r > 0; })) / $count * 100,
                'probability_of_high_return' => count(array_filter($results, function($r) { return $r > 50; })) / $count * 100,
                'probability_of_loss' => count(array_filter($results, function($r) { return $r < 0; })) / $count * 100
            ]
        ];
    }

    private function generateRandomParameters(array $base, array $ranges): array
    {
        $random = [];
        
        foreach ($base as $key => $value) {
            if (isset($ranges[$key])) {
                $range = $ranges[$key];
                $min = $value * (1 - $range['min_variation'] / 100);
                $max = $value * (1 + $range['max_variation'] / 100);
                $random[$key] = $min + (mt_rand() / mt_getrandmax()) * ($max - $min);
            } else {
                $random[$key] = $value;
            }
        }
        
        return $random;
    }

    private function performSensitivityAnalysis(array $data): array
    {
        $baseCase = $data['base_case'];
        $sensitivityVariables = $data['sensitivity_variables'];
        $variationRange = $data['variation_range'];
        
        $sensitivityResults = [];
        
        foreach ($sensitivityVariables as $variable) {
            $variableResults = [];
            
            foreach ($variationRange as $variation) {
                $modifiedCase = $baseCase;
                $modifiedCase[$variable] = $baseCase[$variable] * (1 + $variation / 100);
                
                $scenarioResult = $this->generateScenario($modifiedCase, [], 'sensitivity', 10);
                $roi = $scenarioResult['roi_metrics']['total_roi_percentage'];
                
                $variableResults[] = [
                    'variation' => $variation,
                    'modified_value' => $modifiedCase[$variable],
                    'roi' => $roi,
                    'roi_change' => $roi - $baseCase['base_roi'],
                    'sensitivity' => $baseCase['base_roi'] != 0 ? abs(($roi - $baseCase['base_roi']) / $baseCase['base_roi']) * 100 : 0
                ];
            }
            
            $sensitivityResults[$variable] = $variableResults;
        }
        
        return [
            'sensitivity_analysis' => $sensitivityResults,
            'most_sensitive_variable' => $this->findMostSensitiveVariable($sensitivityResults),
            'recommendations' => $this->generateSensitivityRecommendations($sensitivityResults)
        ];
    }

    private function findMostSensitiveVariable(array $sensitivityResults): string
    {
        $maxSensitivity = 0;
        $mostSensitive = '';
        
        foreach ($sensitivityResults as $variable => $results) {
            $maxVariableSensitivity = max(array_column($results, 'sensitivity'));
            if ($maxVariableSensitivity > $maxSensitivity) {
                $maxSensitivity = $maxVariableSensitivity;
                $mostSensitive = $variable;
            }
        }
        
        return $mostSensitive;
    }

    private function generateSensitivityRecommendations(array $sensitivityResults): array
    {
        $recommendations = [];
        $mostSensitive = $this->findMostSensitiveVariable($sensitivityResults);
        
        $recommendations[] = [
            'type' => 'focus',
            'title' => 'متغير حساس',
            'content' => "المتغير {$mostSensitive} هو الأكثر تأثيراً على العائد. يجب مراقبته بعناية.",
            'variable' => $mostSensitive
        ];
        
        return $recommendations;
    }

    private function performStressTesting(array $data): array
    {
        $baseScenario = $data['base_scenario'];
        $stressScenarios = $data['stress_scenarios'];
        
        $stressResults = [];
        
        foreach ($stressScenarios as $scenarioName => $scenarioData) {
            $stressCase = array_merge($baseScenario, $scenarioData);
            $stressResult = $this->generateScenario($stressCase, [], 'stress', 10);
            
            $stressResults[$scenarioName] = [
                'scenario_data' => $scenarioData,
                'result' => $stressResult,
                'impact' => $this->calculateStressImpact($baseScenario, $stressResult)
            ];
        }
        
        return [
            'stress_test_results' => $stressResults,
            'worst_case_scenario' => $this->findWorstCaseScenario($stressResults),
            'resilience_score' => $this->calculateResilienceScore($stressResults)
        ];
    }

    private function calculateStressImpact(array $base, array $stress): array
    {
        $baseRoi = $base['base_roi'] ?? 0;
        $stressRoi = $stress['roi_metrics']['total_roi_percentage'] ?? 0;
        
        return [
            'roi_impact' => $stressRoi - $baseRoi,
            'roi_impact_percentage' => $baseRoi != 0 ? (($stressRoi - $baseRoi) / $baseRoi) * 100 : 0,
            'risk_increase' => ($stress['risk_metrics']['risk_score'] ?? 0) - ($base['risk_score'] ?? 0)
        ];
    }

    private function findWorstCaseScenario(array $stressResults): string
    {
        $worstRoi = PHP_FLOAT_MAX;
        $worstScenario = '';
        
        foreach ($stressResults as $scenarioName => $result) {
            $roi = $result['result']['roi_metrics']['total_roi_percentage'] ?? 0;
            if ($roi < $worstRoi) {
                $worstRoi = $roi;
                $worstScenario = $scenarioName;
            }
        }
        
        return $worstScenario;
    }

    private function calculateResilienceScore(array $stressResults): int
    {
        $totalImpact = 0;
        $count = 0;
        
        foreach ($stressResults as $result) {
            $impact = abs($result['impact']['roi_impact_percentage']);
            $totalImpact += $impact;
            $count++;
        }
        
        $averageImpact = $count > 0 ? $totalImpact / $count : 0;
        
        // Resilience score: higher is better (less impact from stress)
        return max(0, 100 - $averageImpact);
    }

    private function generateStandardScenarios(PropertyFinancialAnalysis $analysis): array
    {
        $baseData = [
            'purchase_price' => $analysis->purchase_price,
            'monthly_rent' => $analysis->rental_income,
            'operating_expenses' => $analysis->operating_expenses,
            'vacancy_rate' => $analysis->vacancy_rate,
            'appreciation_rate' => $analysis->appreciation_rate
        ];

        $scenarios = [];
        $scenarioTypes = ['optimistic', 'conservative', 'pessimistic', 'realistic'];

        foreach ($scenarioTypes as $type) {
            $scenarios[$type] = $this->generateScenario($baseData, [], $type, 10);
        }

        return $scenarios;
    }
}
