<?php

namespace App\Http\Controllers;

use App\Models\AppreciationProjection;
use App\Models\PropertyFinancialAnalysis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyAppreciationCalculatorController extends Controller
{
    public function index(): View
    {
        return view('financial.appreciation-calculator.index');
    }

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_value' => 'required|numeric|min:0',
            'base_appreciation_rate' => 'required|numeric|min:-20|max:50',
            'projection_period' => 'required|integer|min:1|max:30',
            'appreciation_model' => 'required|in:linear,compound,variable,market_based',
            'market_factors' => 'nullable|array',
            'property_factors' => 'nullable|array',
            'economic_assumptions' => 'nullable|array'
        ]);

        $appreciationData = $this->performAppreciationCalculation($validated);

        return response()->json([
            'success' => true,
            'data' => $appreciationData
        ]);
    }

    public function detailed(): View
    {
        return view('financial.appreciation-calculator.detailed');
    }

    public function compare(): View
    {
        return view('financial.appreciation-calculator.compare');
    }

    public function saveProjection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_financial_analysis_id' => 'required|exists:property_financial_analyses,id',
            'projection_year' => 'required|integer|min:2020|max:2050',
            'projected_value' => 'required|numeric|min:0',
            'appreciation_rate' => 'required|numeric',
            'projection_model' => 'required|string',
            'assumptions' => 'required|array',
            'confidence_level' => 'required|integer|min:0|max:100'
        ]);

        $projection = AppreciationProjection::create([
            'property_financial_analysis_id' => $validated['property_financial_analysis_id'],
            'projection_year' => $validated['projection_year'],
            'projected_value' => $validated['projected_value'],
            'appreciation_rate' => $validated['appreciation_rate'],
            'projection_model' => $validated['projection_model'],
            'market_factors' => $validated['assumptions']['market_factors'] ?? [],
            'property_factors' => $validated['assumptions']['property_factors'] ?? [],
            'economic_assumptions' => $validated['assumptions']['economic_assumptions'] ?? [],
            'confidence_level' => $validated['confidence_level'],
            'projected_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ توقع ارتفاع القيمة بنجاح',
            'projection' => $projection
        ]);
    }

    public function marketAnalysis(): View
    {
        return view('financial.appreciation-calculator.market-analysis');
    }

    public function performMarketAnalysis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'location' => 'required|string',
            'property_type' => 'required|string',
            'time_period' => 'required|array',
            'market_indicators' => 'required|array'
        ]);

        $marketAnalysis = $this->analyzeMarketTrends($validated);

        return response()->json([
            'success' => true,
            'data' => $marketAnalysis
        ]);
    }

    public function scenarios(PropertyFinancialAnalysis $analysis): View
    {
        $scenarios = $this->generateAppreciationScenarios($analysis);
        
        return view('financial.appreciation-calculator.scenarios', compact('analysis', 'scenarios'));
    }

    public function sensitivity(): View
    {
        return view('financial.appreciation-calculator.sensitivity');
    }

    public function performSensitivity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'base_parameters' => 'required|array',
            'sensitivity_variables' => 'required|array',
            'variation_ranges' => 'required|array'
        ]);

        $sensitivityAnalysis = $this->performSensitivityAnalysis($validated);

        return response()->json([
            'success' => true,
            'data' => $sensitivityAnalysis
        ]);
    }

    public function export(PropertyFinancialAnalysis $analysis)
    {
        $projections = $analysis->appreciationProjections()->orderBy('projection_year')->get();
        
        return response()->json([
            'analysis_id' => $analysis->id,
            'projections' => $projections,
            'exported_at' => now()
        ]);
    }

    private function performAppreciationCalculation(array $data): array
    {
        $currentValue = $data['current_value'];
        $baseRate = $data['base_appreciation_rate'] / 100;
        $period = $data['projection_period'];
        $model = $data['appreciation_model'];
        $marketFactors = $data['market_factors'] ?? [];
        $propertyFactors = $data['property_factors'] ?? [];
        $economicAssumptions = $data['economic_assumptions'] ?? [];

        $projections = [];
        $adjustedRates = $this->calculateAdjustedRates($baseRate, $marketFactors, $propertyFactors, $economicAssumptions, $period);

        for ($year = 1; $year <= $period; $year++) {
            $yearlyRate = $adjustedRates[$year - 1];
            $projectedValue = $this->calculateYearValue($currentValue, $yearlyRate, $year, $model);
            
            $projections[] = [
                'year' => $year,
                'projected_value' => $projectedValue,
                'appreciation_rate' => $yearlyRate * 100,
                'annual_appreciation' => $projectedValue - ($year > 1 ? $projections[$year - 2]['projected_value'] : $currentValue),
                'cumulative_appreciation' => $projectedValue - $currentValue,
                'cumulative_appreciation_percentage' => (($projectedValue - $currentValue) / $currentValue) * 100,
                'confidence_level' => $this->calculateConfidenceLevel($year, $period, $model)
            ];
        }

        $finalValue = $projections[$period - 1]['projected_value'];
        $totalAppreciation = $finalValue - $currentValue;
        $averageAnnualRate = $period > 0 ? pow($finalValue / $currentValue, 1 / $period) - 1 : 0;

        return [
            'projections' => $projections,
            'summary' => [
                'current_value' => $currentValue,
                'final_projected_value' => $finalValue,
                'total_appreciation' => $totalAppreciation,
                'total_appreciation_percentage' => ($totalAppreciation / $currentValue) * 100,
                'average_annual_rate' => $averageAnnualRate * 100,
                'projection_period' => $period,
                'model_used' => $model
            ],
            'risk_analysis' => $this->analyzeAppreciationRisk($projections, $adjustedRates),
            'comparative_analysis' => $this->compareWithMarket($projections, $marketFactors),
            'recommendations' => $this->generateAppreciationRecommendations($projections, $model)
        ];
    }

    private function calculateAdjustedRates(float $baseRate, array $marketFactors, array $propertyFactors, array $economicAssumptions, int $period): array
    {
        $adjustedRates = [];

        for ($year = 1; $year <= $period; $year++) {
            $rate = $baseRate;

            // Apply market factor adjustments
            if (isset($marketFactors['inflation_trend'])) {
                $inflationImpact = $marketFactors['inflation_trend'] * ($year / $period);
                $rate += $inflationImpact;
            }

            if (isset($marketFactors['supply_demand'])) {
                $supplyDemandImpact = $marketFactors['supply_demand'] * (1 - ($year / $period));
                $rate += $supplyDemandImpact;
            }

            // Apply property factor adjustments
            if (isset($propertyFactors['property_age'])) {
                $ageImpact = -($propertyFactors['property_age'] / 100) * ($year / $period);
                $rate += $ageImpact;
            }

            if (isset($propertyFactors['renovation_value'])) {
                $renovationImpact = $propertyFactors['renovation_value'] * exp(-$year / 10);
                $rate += $renovationImpact;
            }

            // Apply economic assumptions
            if (isset($economicAssumptions['interest_rate_trend'])) {
                $interestImpact = -$economicAssumptions['interest_rate_trend'] * 0.5;
                $rate += $interestImpact;
            }

            if (isset($economicAssumptions['gdp_growth'])) {
                $gdpImpact = $economicAssumptions['gdp_growth'] * 0.3;
                $rate += $gdpImpact;
            }

            // Add some randomness for realistic projections
            $randomFactor = (mt_rand() / mt_getrandmax() - 0.5) * 0.02; // ±1% random variation
            $rate += $randomFactor;

            $adjustedRates[] = max(-0.2, min(0.5, $rate)); // Cap between -20% and +50%
        }

        return $adjustedRates;
    }

    private function calculateYearValue(float $currentValue, float $rate, int $year, string $model): float
    {
        switch ($model) {
            case 'linear':
                return $currentValue * (1 + $rate * $year);
            
            case 'compound':
                return $currentValue * pow(1 + $rate, $year);
            
            case 'variable':
                // Variable rate with diminishing returns
                return $currentValue * pow(1 + $rate * exp(-$year / 20), $year);
            
            case 'market_based':
                // Market-based with cyclical patterns
                $cycleFactor = 1 + 0.1 * sin($year * 2 * pi() / 7); // 7-year cycle
                return $currentValue * pow(1 + $rate, $year) * $cycleFactor;
            
            default:
                return $currentValue * pow(1 + $rate, $year);
        }
    }

    private function calculateConfidenceLevel(int $year, int $totalPeriod, string $model): int
    {
        // Confidence decreases with time and varies by model
        $baseConfidence = [
            'linear' => 85,
            'compound' => 75,
            'variable' => 70,
            'market_based' => 65
        ];

        $confidence = $baseConfidence[$model] ?? 70;
        $timeDecay = ($year / $totalPeriod) * 40; // Lose up to 40% confidence over time
        
        return max(20, $confidence - $timeDecay);
    }

    private function analyzeAppreciationRisk(array $projections, array $rates): array
    {
        $values = array_column($projections, 'projected_value');
        $rateStdDev = $this->calculateStandardDeviation($rates);
        $valueVolatility = $this->calculateValueVolatility($values);
        
        // Calculate downside risk
        $negativeYears = array_filter($rates, function($rate) {
            return $rate < 0;
        });
        $downsideRisk = count($negativeYears) / count($rates) * 100;

        // Calculate maximum drawdown potential
        $maxDrawdown = $this->calculateMaxDrawdown($values);

        return [
            'rate_volatility' => $rateStdDev * 100,
            'value_volatility' => $valueVolatility,
            'downside_risk' => $downsideRisk,
            'max_drawdown_potential' => $maxDrawdown,
            'risk_level' => $this->assessAppreciationRiskLevel($rateStdDev, $downsideRisk),
            'risk_factors' => $this->identifyRiskFactors($rates)
        ];
    }

    private function calculateStandardDeviation(array $values): float
    {
        if (empty($values)) return 0;
        
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $values)) / count($values);
        
        return sqrt($variance);
    }

    private function calculateValueVolatility(array $values): float
    {
        if (count($values) < 2) return 0;
        
        $returns = [];
        for ($i = 1; $i < count($values); $i++) {
            $returns[] = ($values[$i] - $values[$i - 1]) / $values[$i - 1];
        }
        
        return $this->calculateStandardDeviation($returns);
    }

    private function calculateMaxDrawdown(array $values): float
    {
        if (empty($values)) return 0;
        
        $peak = $values[0];
        $maxDrawdown = 0;
        
        foreach ($values as $value) {
            if ($value > $peak) {
                $peak = $value;
            }
            $drawdown = ($peak - $value) / $peak;
            if ($drawdown > $maxDrawdown) {
                $maxDrawdown = $drawdown;
            }
        }
        
        return $maxDrawdown * 100;
    }

    private function assessAppreciationRiskLevel(float $volatility, float $downsideRisk): string
    {
        $riskScore = ($volatility * 50) + ($downsideRisk * 0.5);
        
        if ($riskScore < 10) return 'low';
        if ($riskScore < 20) return 'medium';
        if ($riskScore < 30) return 'high';
        return 'very_high';
    }

    private function identifyRiskFactors(array $rates): array
    {
        $riskFactors = [];
        
        $avgRate = array_sum($rates) / count($rates);
        $negativeRateCount = count(array_filter($rates, function($rate) {
            return $rate < 0;
        }));
        
        if ($avgRate < 0.02) {
            $riskFactors[] = 'Low appreciation expectations';
        }
        
        if ($negativeRateCount > count($rates) * 0.3) {
            $riskFactors[] = 'High probability of value decline';
        }
        
        if (max($rates) - min($rates) > 0.15) {
            $riskFactors[] = 'High rate volatility';
        }
        
        return $riskFactors;
    }

    private function compareWithMarket(array $projections, array $marketFactors): array
    {
        $projectedGrowth = $projections[count($projections) - 1]['cumulative_appreciation_percentage'];
        
        // Market benchmarks (simplified)
        $marketBenchmarks = [
            'national_average' => 45, // 45% over 10 years
            'regional_average' => 55,
            'property_type_average' => 50
        ];
        
        $comparison = [];
        foreach ($marketBenchmarks as $benchmark => $value) {
            $comparison[$benchmark] = [
                'benchmark_value' => $value,
                'projected_value' => $projectedGrowth,
                'difference' => $projectedGrowth - $value,
                'performance' => $projectedGrowth > $value ? 'outperforming' : 'underperforming'
            ];
        }
        
        return [
            'market_comparison' => $comparison,
            'market_factors_impact' => $this->analyzeMarketFactorsImpact($marketFactors),
            'competitive_position' => $this->assessCompetitivePosition($projectedGrowth, $marketBenchmarks)
        ];
    }

    private function analyzeMarketFactorsImpact(array $marketFactors): array
    {
        $impact = [];
        
        foreach ($marketFactors as $factor => $value) {
            $impact[$factor] = [
                'value' => $value,
                'impact_level' => $this->assessFactorImpact($factor, $value),
                'description' => $this->getFactorDescription($factor, $value)
            ];
        }
        
        return $impact;
    }

    private function assessFactorImpact(string $factor, float $value): string
    {
        $thresholds = [
            'inflation_trend' => ['low' => 0.02, 'medium' => 0.04],
            'supply_demand' => ['low' => 0.01, 'medium' => 0.03],
            'interest_rate_trend' => ['low' => -0.02, 'medium' => 0]
        ];
        
        if (isset($thresholds[$factor])) {
            if ($value < $thresholds[$factor]['low']) return 'negative';
            if ($value < $thresholds[$factor]['medium']) return 'neutral';
            return 'positive';
        }
        
        return 'neutral';
    }

    private function getFactorDescription(string $factor, float $value): string
    {
        $descriptions = [
            'inflation_trend' => 'Inflation impact on property values',
            'supply_demand' => 'Supply and demand balance effect',
            'interest_rate_trend' => 'Interest rate influence on affordability'
        ];
        
        return $descriptions[$factor] ?? 'Market factor influence';
    }

    private function assessCompetitivePosition(float $projectedGrowth, array $benchmarks): string
    {
        $avgBenchmark = array_sum($benchmarks) / count($benchmarks);
        
        if ($projectedGrowth > $avgBenchmark * 1.2) return 'excellent';
        if ($projectedGrowth > $avgBenchmark) return 'good';
        if ($projectedGrowth > $avgBenchmark * 0.8) return 'fair';
        return 'poor';
    }

    private function generateAppreciationRecommendations(array $projections, string $model): array
    {
        $recommendations = [];
        $finalProjection = end($projections);
        $totalAppreciation = $finalProjection['cumulative_appreciation_percentage'];
        
        // Performance-based recommendations
        if ($totalAppreciation > 60) {
            $recommendations[] = [
                'type' => 'opportunity',
                'title' => 'Strong appreciation potential',
                'content' => 'Property shows excellent appreciation potential. Consider long-term holding strategy.',
                'priority' => 'high'
            ];
        } elseif ($totalAppreciation < 20) {
            $recommendations[] = [
                'type' => 'caution',
                'title' => 'Limited appreciation expected',
                'content' => 'Low appreciation expectations. Consider value-add improvements or alternative investments.',
                'priority' => 'high'
            ];
        }
        
        // Model-specific recommendations
        if ($model === 'market_based') {
            $recommendations[] = [
                'type' => 'strategy',
                'title' => 'Market-sensitive approach',
                'content' => 'Monitor market cycles closely for optimal entry/exit points.',
                'priority' => 'medium'
            ];
        }
        
        // Risk-based recommendations
        $riskLevel = $this->assessAppreciationRiskLevel(
            $this->calculateStandardDeviation(array_column($projections, 'appreciation_rate')),
            0
        );
        
        if ($riskLevel === 'high' || $riskLevel === 'very_high') {
            $recommendations[] = [
                'type' => 'risk',
                'title' => 'High volatility detected',
                'content' => 'Consider hedging strategies or diversification to manage risk.',
                'priority' => 'medium'
            ];
        }
        
        return $recommendations;
    }

    private function analyzeMarketTrends(array $data): array
    {
        $location = $data['location'];
        $propertyType = $data['property_type'];
        $timePeriod = $data['time_period'];
        $indicators = $data['market_indicators'];

        // Simulated market analysis (in real implementation, this would fetch actual market data)
        $historicalData = $this->generateHistoricalMarketData($location, $propertyType, $timePeriod);
        $trendAnalysis = $this->analyzeHistoricalTrends($historicalData);
        $futureProjections = $this->projectMarketTrends($trendAnalysis, $indicators);

        return [
            'historical_analysis' => [
                'data_points' => $historicalData,
                'trend_analysis' => $trendAnalysis,
                'key_insights' => $this->extractKeyInsights($trendAnalysis)
            ],
            'future_projections' => $futureProjections,
            'market_indicators' => $this->analyzeMarketIndicators($indicators),
            'recommendations' => $this->generateMarketRecommendations($trendAnalysis, $futureProjections)
        ];
    }

    private function generateHistoricalMarketData(string $location, string $propertyType, array $timePeriod): array
    {
        $data = [];
        $startYear = $timePeriod['start_year'] ?? 2015;
        $endYear = $timePeriod['end_year'] ?? 2024;
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $data[] = [
                'year' => $year,
                'median_price' => 200000 + ($year - $startYear) * 15000 + (mt_rand() / mt_getrandmax() - 0.5) * 10000,
                'price_growth_rate' => 3 + (mt_rand() / mt_getrandmax() - 0.5) * 4,
                'inventory_level' => 100 + (mt_rand() / mt_getrandmax() - 0.5) * 50,
                'days_on_market' => 45 + (mt_rand() / mt_getrandmax() - 0.5) * 30
            ];
        }
        
        return $data;
    }

    private function analyzeHistoricalTrends(array $data): array
    {
        if (empty($data)) return [];
        
        $priceGrowthRates = array_column($data, 'price_growth_rate');
        $avgGrowthRate = array_sum($priceGrowthRates) / count($priceGrowthRates);
        $growthVolatility = $this->calculateStandardDeviation($priceGrowthRates);
        
        return [
            'average_annual_growth' => $avgGrowthRate,
            'growth_volatility' => $growthVolatility,
            'trend_direction' => $avgGrowthRate > 0 ? 'increasing' : 'decreasing',
            'market_maturity' => $this->assessMarketMaturity($data),
            'cycle_phase' => $this->identifyMarketCycle($data)
        ];
    }

    private function assessMarketMaturity(array $data): string
    {
        $recentGrowth = array_slice(array_column($data, 'price_growth_rate'), -3);
        $avgRecentGrowth = array_sum($recentGrowth) / count($recentGrowth);
        
        if ($avgRecentGrowth > 6) return 'rapid_growth';
        if ($avgRecentGrowth > 3) return 'stable_growth';
        if ($avgRecentGrowth > 0) return 'mature';
        return 'declining';
    }

    private function identifyMarketCycle(array $data): string
    {
        $recentData = array_slice($data, -5);
        $growthRates = array_column($recentData, 'price_growth_rate');
        
        $increasing = 0;
        for ($i = 1; $i < count($growthRates); $i++) {
            if ($growthRates[$i] > $growthRates[$i - 1]) $increasing++;
        }
        
        if ($increasing >= 3) return 'expansion';
        if ($increasing <= 1) return 'contraction';
        return 'transition';
    }

    private function projectMarketTrends(array $trendAnalysis, array $indicators): array
    {
        $baseGrowth = $trendAnalysis['average_annual_growth'] ?? 3;
        $projections = [];
        
        for ($year = 1; $year <= 5; $year++) {
            $projectedGrowth = $baseGrowth;
            
            // Apply indicator adjustments
            if (isset($indicators['economic_outlook'])) {
                $projectedGrowth += $indicators['economic_outlook'] * 0.5;
            }
            
            if (isset($indicators['population_growth'])) {
                $projectedGrowth += $indicators['population_growth'] * 0.3;
            }
            
            $projections[] = [
                'year' => 2024 + $year,
                'projected_growth_rate' => $projectedGrowth,
                'confidence_level' => max(50, 90 - ($year * 10)),
                'key_factors' => $this->identifyKeyFactors($year, $indicators)
            ];
        }
        
        return $projections;
    }

    private function identifyKeyFactors(int $year, array $indicators): array
    {
        $factors = [];
        
        foreach ($indicators as $indicator => $value) {
            if (abs($value) > 0.05) {
                $factors[] = $indicator;
            }
        }
        
        return $factors;
    }

    private function extractKeyInsights(array $trendAnalysis): array
    {
        $insights = [];
        
        if ($trendAnalysis['average_annual_growth'] > 5) {
            $insights[] = 'Strong historical growth indicates robust market';
        } elseif ($trendAnalysis['average_annual_growth'] < 2) {
            $insights[] = 'Modest growth suggests mature market';
        }
        
        if ($trendAnalysis['growth_volatility'] > 3) {
            $insights[] = 'High volatility indicates cyclical market';
        }
        
        return $insights;
    }

    private function analyzeMarketIndicators(array $indicators): array
    {
        $analysis = [];
        
        foreach ($indicators as $indicator => $value) {
            $analysis[$indicator] = [
                'current_value' => $value,
                'interpretation' => $this->interpretIndicator($indicator, $value),
                'impact_on_appreciation' => $this->assessIndicatorImpact($indicator, $value)
            ];
        }
        
        return $analysis;
    }

    private function interpretIndicator(string $indicator, float $value): string
    {
        $interpretations = [
            'economic_outlook' => $value > 0.03 ? 'Positive economic outlook' : 'Cautious economic outlook',
            'population_growth' => $value > 0.02 ? 'Strong population growth' : 'Moderate population growth',
            'interest_rates' => $value < 0.05 ? 'Favorable interest rates' : 'Challenging interest rates'
        ];
        
        return $interpretations[$indicator] ?? 'Neutral indicator';
    }

    private function assessIndicatorImpact(string $indicator, float $value): string
    {
        $impact = abs($value) * 100;
        
        if ($impact > 10) return 'high';
        if ($impact > 5) return 'medium';
        return 'low';
    }

    private function generateMarketRecommendations(array $trendAnalysis, array $projections): array
    {
        $recommendations = [];
        
        if ($trendAnalysis['trend_direction'] === 'increasing') {
            $recommendations[] = [
                'type' => 'market_timing',
                'title' => 'Favorable market conditions',
                'content' => 'Current market trends support property investment',
                'priority' => 'high'
            ];
        }
        
        $avgProjectedGrowth = array_sum(array_column($projections, 'projected_growth_rate')) / count($projections);
        
        if ($avgProjectedGrowth > 4) {
            $recommendations[] = [
                'type' => 'growth_potential',
                'title' => 'Strong growth expected',
                'content' => 'Market projections indicate continued appreciation',
                'priority' => 'medium'
            ];
        }
        
        return $recommendations;
    }

    private function generateAppreciationScenarios(PropertyFinancialAnalysis $analysis): array
    {
        $baseData = [
            'current_value' => $analysis->current_value,
            'base_appreciation_rate' => $analysis->appreciation_rate,
            'projection_period' => 10
        ];

        $scenarios = [];

        // Optimistic scenario
        $optimistic = $baseData;
        $optimistic['base_appreciation_rate'] *= 1.5;
        $optimistic['appreciation_model'] = 'compound';
        $scenarios['optimistic'] = $this->performAppreciationCalculation($optimistic);

        // Conservative scenario
        $conservative = $baseData;
        $conservative['base_appreciation_rate'] *= 0.7;
        $conservative['appreciation_model'] = 'linear';
        $scenarios['conservative'] = $this->performAppreciationCalculation($conservative);

        // Market-based scenario
        $marketBased = $baseData;
        $marketBased['appreciation_model'] = 'market_based';
        $marketBased['market_factors'] = [
            'inflation_trend' => 0.03,
            'supply_demand' => 0.02
        ];
        $scenarios['market_based'] = $this->performAppreciationCalculation($marketBased);

        return $scenarios;
    }

    private function performSensitivityAnalysis(array $data): array
    {
        $baseParameters = $data['base_parameters'];
        $sensitivityVariables = $data['sensitivity_variables'];
        $variationRanges = $data['variation_ranges'];

        $sensitivityResults = [];

        foreach ($sensitivityVariables as $variable) {
            $variableResults = [];
            
            foreach ($variationRanges[$variable] as $variation) {
                $modifiedParameters = $baseParameters;
                $modifiedParameters[$variable] = $baseParameters[$variable] * (1 + $variation / 100);
                
                $result = $this->performAppreciationCalculation($modifiedParameters);
                $finalValue = $result['summary']['final_projected_value'];
                $baseValue = $this->performAppreciationCalculation($baseParameters)['summary']['final_projected_value'];
                
                $variableResults[] = [
                    'variation' => $variation,
                    'final_value' => $finalValue,
                    'value_change' => $finalValue - $baseValue,
                    'percentage_change' => $baseValue > 0 ? (($finalValue - $baseValue) / $baseValue) * 100 : 0,
                    'sensitivity' => abs(($finalValue - $baseValue) / $baseValue) * 100
                ];
            }
            
            $sensitivityResults[$variable] = $variableResults;
        }

        return [
            'sensitivity_analysis' => $sensitivityResults,
            'most_sensitive_variable' => $this->findMostSensitiveVariable($sensitivityResults),
            'tornado_chart_data' => $this->prepareTornadoChartData($sensitivityResults)
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

    private function prepareTornadoChartData(array $sensitivityResults): array
    {
        $tornadoData = [];
        
        foreach ($sensitivityResults as $variable => $results) {
            $maxPositive = max(array_column($results, 'percentage_change'));
            $maxNegative = min(array_column($results, 'percentage_change'));
            
            $tornadoData[] = [
                'variable' => $variable,
                'positive_impact' => $maxPositive,
                'negative_impact' => abs($maxNegative)
            ];
        }
        
        // Sort by impact magnitude
        usort($tornadoData, function($a, $b) {
            $impactA = max($a['positive_impact'], $a['negative_impact']);
            $impactB = max($b['positive_impact'], $b['negative_impact']);
            return $impactB <=> $impactA;
        });
        
        return $tornadoData;
    }
}
