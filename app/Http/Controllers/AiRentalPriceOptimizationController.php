<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AiRentalPriceOptimizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the rental price optimization dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        
        // Get rental optimization statistics
        $stats = [
            'total_properties' => DB::table('properties')->where('type', 'rental')->count(),
            'optimized_properties' => $this->getOptimizedPropertiesCount(),
            'average_rent_increase' => $this->getAverageRentIncrease(),
            'occupancy_rate' => $this->getOccupancyRate(),
            'market_demand' => $this->getMarketDemand(),
            'revenue_optimization' => $this->getRevenueOptimizationValue(),
        ];

        // Get recent optimizations
        $recentOptimizations = $this->getRecentOptimizations();

        return view('ai.rental-price-optimization', compact('stats', 'recentOptimizations'));
    }

    /**
     * Optimize rental price for a property.
     */
    public function optimizePrice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'current_rent' => 'required|numeric|min:0',
            'optimization_goal' => 'required|in:maximize_occupancy,maximize_revenue,balance',
            'time_horizon' => 'required|in:1month,3months,6months,1year',
            'market_conditions' => 'nullable|array',
            'property_features' => 'nullable|array',
        ]);

        try {
            $optimization = $this->performPriceOptimization($validated);
            
            return response()->json([
                'success' => true,
                'optimization' => $optimization,
                'message' => 'تم تحسين سعر الإيجار بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحسين سعر الإيجار: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get market rental analysis.
     */
    public function getMarketAnalysis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'location' => 'required|string',
            'property_type' => 'required|string',
            'area_range' => 'nullable|array',
            'analysis_period' => 'required|in:1month,3months,6months,1year',
        ]);

        try {
            $analysis = $this->performMarketAnalysis($validated);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'message' => 'تم تحليل السوق بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحليل السوق: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get demand forecast.
     */
    public function getDemandForecast(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'forecast_period' => 'required|in:1month,3months,6months,1year',
            'seasonal_adjustment' => 'boolean',
            'external_factors' => 'nullable|array',
        ]);

        try {
            $forecast = $this->generateDemandForecast($validated);
            
            return response()->json([
                'success' => true,
                'forecast' => $forecast,
                'message' => 'تم إنشاء توقعات الطلب بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء توقعات الطلب: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get competitor pricing.
     */
    public function getCompetitorPricing(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'radius' => 'required|integer|min:1|max:10',
            'property_features' => 'nullable|array',
            'include_vacancy' => 'boolean',
        ]);

        try {
            $competitorData = $this->analyzeCompetitorPricing($validated);
            
            return response()->json([
                'success' => true,
                'competitor_data' => $competitorData,
                'message' => 'تم تحليل أسعار المنافسين بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحليل أسعار المنافسين: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dynamic pricing recommendations.
     */
    public function getDynamicPricing(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'pricing_strategy' => 'required|in:aggressive,moderate,conservative',
            'adjustment_frequency' => 'required|in:daily,weekly,monthly',
            'min_max_bounds' => 'nullable|array',
            'demand_sensitivity' => 'required|integer|min:1|max:10',
        ]);

        try {
            $dynamicPricing = $this->generateDynamicPricing($validated);
            
            return response()->json([
                'success' => true,
                'dynamic_pricing' => $dynamicPricing,
                'message' => 'تم إنشاء التسعير الديناميكي بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء التسعير الديناميكي: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get revenue optimization report.
     */
    public function getRevenueOptimization(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'time_period' => 'required|in:1month,3months,6months,1year',
            'optimization_scenarios' => 'nullable|array',
            'cost_analysis' => 'boolean',
        ]);

        try {
            $report = $this->generateRevenueOptimizationReport($validated);
            
            return response()->json([
                'success' => true,
                'report' => $report,
                'message' => 'تم إنشاء تقرير تحسين الإيرادات بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء تقرير تحسين الإيرادات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform price optimization using AI simulation.
     */
    private function performPriceOptimization(array $data): array
    {
        $currentRent = $data['current_rent'];
        $marketData = $this->getMarketData($data['property_id']);
        
        // Simulate AI optimization
        $optimizationFactors = $this->calculateOptimizationFactors($data, $marketData);
        $recommendedRent = $this->calculateRecommendedRent($currentRent, $optimizationFactors);
        $potentialIncrease = (($recommendedRent - $currentRent) / $currentRent) * 100;
        
        return [
            'property_id' => $data['property_id'],
            'current_rent' => $currentRent,
            'recommended_rent' => $recommendedRent,
            'potential_increase' => round($potentialIncrease, 2),
            'optimization_goal' => $data['optimization_goal'],
            'time_horizon' => $data['time_horizon'],
            'confidence_score' => rand(75, 95),
            'optimization_factors' => $optimizationFactors,
            'market_comparison' => $this->getMarketComparison($recommendedRent, $marketData),
            'occupancy_impact' => $this->calculateOccupancyImpact($potentialIncrease),
            'revenue_projection' => $this->projectRevenue($recommendedRent, $data['time_horizon']),
            'implementation_strategy' => $this->getImplementationStrategy($data['optimization_goal']),
            'risk_assessment' => $this->assessOptimizationRisks($potentialIncrease),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Perform market analysis.
     */
    private function performMarketAnalysis(array $data): array
    {
        return [
            'location' => $data['location'],
            'property_type' => $data['property_type'],
            'analysis_period' => $data['analysis_period'],
            'average_rent' => rand(2000, 8000),
            'rent_trend' => $this->getRentTrend(),
            'vacancy_rate' => rand(3, 12),
            'demand_score' => rand(60, 95),
            'supply_level' => $this->getSupplyLevel(),
            'seasonal_patterns' => $this->getSeasonalPatterns(),
            'economic_indicators' => $this->getEconomicIndicators(),
            'future_outlook' => $this->getFutureOutlook(),
        ];
    }

    /**
     * Generate demand forecast.
     */
    private function generateDemandForecast(array $data): array
    {
        $periods = [
            '1month' => 1,
            '3months' => 3,
            '6months' => 6,
            '1year' => 12,
        ];

        $months = $periods[$data['forecast_period']] ?? 12;
        $baseDemand = rand(60, 90);
        
        $forecast = [];
        for ($i = 1; $i <= $months; $i++) {
            $seasonalFactor = $data['seasonal_adjustment'] ? $this->getSeasonalFactor($i) : 1;
            $demand = $baseDemand * $seasonalFactor * (1 + rand(-5, 10) / 100);
            
            $forecast[] = [
                'month' => $i,
                'demand_score' => round($demand, 2),
                'expected_occupancy' => min(95, $demand),
                'price_pressure' => $this->getPricePressure($demand),
            ];
        }

        return [
            'property_id' => $data['property_id'],
            'forecast_period' => $data['forecast_period'],
            'monthly_forecast' => $forecast,
            'overall_trend' => 'increasing',
            'peak_months' => $this->getPeakMonths($forecast),
            'recommendations' => $this->getForecastRecommendations($forecast),
        ];
    }

    /**
     * Analyze competitor pricing.
     */
    private function analyzeCompetitorPricing(array $data): array
    {
        $competitors = [];
        $numCompetitors = rand(5, 15);
        
        for ($i = 1; $i <= $numCompetitors; $i++) {
            $competitors[] = [
                'property_id' => rand(1000, 9999),
                'distance_km' => rand(1, $data['radius']),
                'rent_price' => rand(1500, 6000),
                'occupancy_rate' => rand(70, 100),
                'property_score' => rand(60, 95),
                'amenities_score' => rand(50, 90),
            ];
        }

        usort($competitors, fn($a, $b) => $a['distance_km'] <=> $b['distance_km']);
        
        return [
            'property_id' => $data['property_id'],
            'radius_km' => $data['radius'],
            'total_competitors' => count($competitors),
            'competitors' => array_slice($competitors, 0, 10),
            'average_competitor_rent' => array_sum(array_column($competitors, 'rent_price')) / count($competitors),
            'price_positioning' => $this->calculatePricePositioning($competitors),
            'competitive_advantages' => $this->identifyCompetitiveAdvantages($competitors),
            'market_gap_opportunities' => $this->findMarketGaps($competitors),
        ];
    }

    /**
     * Generate dynamic pricing.
     */
    private function generateDynamicPricing(array $data): array
    {
        $basePrice = rand(2000, 5000);
        $strategies = [
            'aggressive' => ['min_adjustment' => -10, 'max_adjustment' => 20],
            'moderate' => ['min_adjustment' => -5, 'max_adjustment' => 10],
            'conservative' => ['min_adjustment' => -2, 'max_adjustment' => 5],
        ];

        $strategy = $strategies[$data['pricing_strategy']] ?? $strategies['moderate'];
        $sensitivity = $data['demand_sensitivity'] / 10;
        
        $dynamicPrices = [];
        for ($i = 0; $i < 30; $i++) { // 30 days forecast
            $demandFactor = 1 + (rand(-20, 30) / 100) * $sensitivity;
            $adjustment = rand($strategy['min_adjustment'], $strategy['max_adjustment']);
            $price = $basePrice * (1 + $adjustment / 100) * $demandFactor;
            
            $dynamicPrices[] = [
                'day' => $i + 1,
                'recommended_price' => round($price, 2),
                'demand_factor' => round($demandFactor, 2),
                'adjustment_reason' => $this->getAdjustmentReason($demandFactor),
            ];
        }

        return [
            'property_id' => $data['property_id'],
            'pricing_strategy' => $data['pricing_strategy'],
            'adjustment_frequency' => $data['adjustment_frequency'],
            'demand_sensitivity' => $data['demand_sensitivity'],
            'dynamic_prices' => $dynamicPrices,
            'expected_revenue' => $this->calculateExpectedRevenue($dynamicPrices),
            'optimization_potential' => $this->calculateOptimizationPotential($dynamicPrices, $basePrice),
        ];
    }

    /**
     * Generate revenue optimization report.
     */
    private function generateRevenueOptimizationReport(array $data): array
    {
        $currentRevenue = rand(24000, 96000); // Annual
        $optimizedRevenue = $currentRevenue * (1 + rand(5, 20) / 100);
        
        return [
            'property_id' => $data['property_id'],
            'time_period' => $data['time_period'],
            'current_annual_revenue' => $currentRevenue,
            'optimized_annual_revenue' => $optimizedRevenue,
            'revenue_increase' => $optimizedRevenue - $currentRevenue,
            'increase_percentage' => round((($optimizedRevenue - $currentRevenue) / $currentRevenue) * 100, 2),
            'optimization_scenarios' => $this->generateOptimizationScenarios($currentRevenue),
            'cost_analysis' => $data['cost_analysis'] ? $this->performCostAnalysis() : null,
            'break_even_analysis' => $this->performBreakEvenAnalysis($currentRevenue, $optimizedRevenue),
            'risk_factors' => $this->identifyRevenueRisks(),
            'implementation_timeline' => $this->getImplementationTimeline(),
        ];
    }

    /**
     * Helper methods
     */
    private function getOptimizedPropertiesCount(): int
    {
        return rand(20, 80);
    }

    private function getAverageRentIncrease(): float
    {
        return rand(5, 15) + rand(0, 99) / 100;
    }

    private function getOccupancyRate(): float
    {
        return rand(75, 95) + rand(0, 99) / 100;
    }

    private function getMarketDemand(): string
    {
        $levels = ['low', 'medium', 'high', 'very_high'];
        return $levels[array_rand($levels)];
    }

    private function getRevenueOptimizationValue(): float
    {
        return rand(8, 25) + rand(0, 99) / 100;
    }

    private function getRecentOptimizations(): array
    {
        return [
            ['property_id' => 1001, 'old_rent' => 3000, 'new_rent' => 3300, 'increase' => '10%'],
            ['property_id' => 1002, 'old_rent' => 2500, 'new_rent' => 2750, 'increase' => '10%'],
            ['property_id' => 1003, 'old_rent' => 4000, 'new_rent' => 4200, 'increase' => '5%'],
        ];
    }

    private function getMarketData(int $propertyId): array
    {
        return [
            'average_rent' => rand(2000, 6000),
            'demand_level' => rand(60, 95),
            'competition_level' => rand(3, 8),
            'seasonal_factor' => rand(0.9, 1.1),
        ];
    }

    private function calculateOptimizationFactors(array $data, array $marketData): array
    {
        return [
            'market_demand' => $marketData['demand_level'],
            'competition_pressure' => $marketData['competition_level'],
            'seasonal_adjustment' => $marketData['seasonal_factor'],
            'property_condition' => rand(70, 95),
            'location_score' => rand(75, 98),
            'amenities_score' => rand(60, 90),
        ];
    }

    private function calculateRecommendedRent(float $currentRent, array $factors): float
    {
        $totalFactor = array_sum($factors) / count($factors) / 100;
        $adjustment = ($totalFactor - 0.8) * 0.2; // Max 20% adjustment
        return $currentRent * (1 + $adjustment);
    }

    private function getMarketComparison(float $recommendedRent, array $marketData): array
    {
        return [
            'position_in_market' => $recommendedRent > $marketData['average_rent'] ? 'above_average' : 'below_average',
            'market_percentile' => rand(40, 80),
            'competitiveness_score' => rand(65, 90),
        ];
    }

    private function calculateOccupancyImpact(float $potentialIncrease): array
    {
        $impact = min(15, $potentialIncrease * 0.5); // Max 15% impact
        return [
            'occupancy_change' => -$impact,
            'new_occupancy_rate' => max(70, 90 - $impact),
            'vacancy_risk' => $impact > 10 ? 'high' : ($impact > 5 ? 'medium' : 'low'),
        ];
    }

    private function projectRevenue(float $recommendedRent, string $timeHorizon): array
    {
        $months = [
            '1month' => 1,
            '3months' => 3,
            '6months' => 6,
            '1year' => 12,
        ];

        $period = $months[$timeHorizon] ?? 12;
        return [
            'projected_revenue' => $recommendedRent * $period,
            'confidence_level' => rand(75, 90),
            'risk_adjusted_revenue' => $recommendedRent * $period * 0.9,
        ];
    }

    private function getImplementationStrategy(string $goal): array
    {
        $strategies = [
            'maximize_occupancy' => 'gradual_increase_with_incentives',
            'maximize_revenue' => 'aggressive_pricing_with_premium_features',
            'balance' => 'balanced_approach_with_monitoring',
        ];

        return [
            'strategy' => $strategies[$goal] ?? $strategies['balance'],
            'implementation_time' => rand(2, 6) . ' weeks',
            'monitoring_frequency' => 'weekly',
        ];
    }

    private function assessOptimizationRisks(float $potentialIncrease): array
    {
        $riskLevel = $potentialIncrease > 15 ? 'high' : ($potentialIncrease > 8 ? 'medium' : 'low');
        
        return [
            'risk_level' => $riskLevel,
            'tenant_retention_risk' => min(20, $potentialIncrease * 1.2),
            'vacancy_risk' => min(15, $potentialIncrease * 0.8),
            'market_rejection_risk' => min(10, $potentialIncrease * 0.5),
        ];
    }

    private function getRentTrend(): string
    {
        $trends = ['increasing', 'stable', 'decreasing'];
        return $trends[array_rand($trends)];
    }

    private function getSupplyLevel(): string
    {
        $levels = ['low', 'medium', 'high'];
        return $levels[array_rand($levels)];
    }

    private function getSeasonalPatterns(): array
    {
        return [
            'peak_season' => 'Summer',
            'low_season' => 'Winter',
            'seasonal_variation' => rand(10, 30) . '%',
        ];
    }

    private function getEconomicIndicators(): array
    {
        return [
            'gdp_growth' => rand(2, 6) . '%',
            'inflation_rate' => rand(1, 4) . '%',
            'employment_rate' => rand(92, 98) . '%',
        ];
    }

    private function getFutureOutlook(): array
    {
        return [
            'short_term' => 'positive',
            'medium_term' => 'stable',
            'long_term' => 'positive',
            'confidence' => rand(70, 85),
        ];
    }

    private function getSeasonalFactor(int $month): float
    {
        $factors = [1.0, 0.95, 0.9, 0.95, 1.0, 1.05, 1.1, 1.1, 1.05, 1.0, 0.95, 0.9];
        return $factors[$month - 1] ?? 1.0;
    }

    private function getPricePressure(float $demand): string
    {
        if ($demand > 80) return 'high';
        if ($demand > 60) return 'medium';
        return 'low';
    }

    private function getPeakMonths(array $forecast): array
    {
        $months = array_column($forecast, 'demand_score');
        arsort($months);
        return array_keys(array_slice($months, 0, 3, true));
    }

    private function getForecastRecommendations(array $forecast): array
    {
        $avgDemand = array_sum(array_column($forecast, 'demand_score')) / count($forecast);
        
        if ($avgDemand > 80) {
            return ['increase_prices', 'reduce_marketing'];
        } elseif ($avgDemand < 60) {
            return ['offer_incentives', 'increase_marketing'];
        } else {
            return ['maintain_prices', 'monitor_trends'];
        }
    }

    private function calculatePricePositioning(array $competitors): array
    {
        $prices = array_column($competitors, 'rent_price');
        sort($prices);
        
        return [
            'position' => 'mid_range',
            'percentile' => 50,
            'competitive_gap' => '5%',
        ];
    }

    private function identifyCompetitiveAdvantages(array $competitors): array
    {
        return [
            'location_advantage' => true,
            'price_competitiveness' => false,
            'amenities_superiority' => true,
            'property_condition' => true,
        ];
    }

    private function findMarketGaps(array $competitors): array
    {
        return [
            'price_range_gap' => '3000-3500 SAR',
            'amenity_gap' => 'parking_availability',
            'size_gap' => '2-3 bedroom units',
        ];
    }

    private function getAdjustmentReason(float $demandFactor): string
    {
        if ($demandFactor > 1.1) return 'high_demand';
        if ($demandFactor < 0.9) return 'low_demand';
        return 'market_stability';
    }

    private function calculateExpectedRevenue(array $dynamicPrices): float
    {
        return array_sum(array_column($dynamicPrices, 'recommended_price'));
    }

    private function calculateOptimizationPotential(array $dynamicPrices, float $basePrice): float
    {
        $avgDynamicPrice = array_sum(array_column($dynamicPrices, 'recommended_price')) / count($dynamicPrices);
        return (($avgDynamicPrice - $basePrice) / $basePrice) * 100;
    }

    private function generateOptimizationScenarios(float $currentRevenue): array
    {
        return [
            'conservative' => [
                'revenue' => $currentRevenue * 1.05,
                'risk' => 'low',
                'occupancy_impact' => 'minimal',
            ],
            'moderate' => [
                'revenue' => $currentRevenue * 1.12,
                'risk' => 'medium',
                'occupancy_impact' => 'moderate',
            ],
            'aggressive' => [
                'revenue' => $currentRevenue * 1.20,
                'risk' => 'high',
                'occupancy_impact' => 'significant',
            ],
        ];
    }

    private function performCostAnalysis(): array
    {
        return [
            'optimization_cost' => rand(5000, 15000),
            'marketing_cost' => rand(2000, 8000),
            'maintenance_cost' => rand(3000, 10000),
            'total_investment' => rand(10000, 33000),
        ];
    }

    private function performBreakEvenAnalysis(float $currentRevenue, float $optimizedRevenue): array
    {
        $investment = rand(10000, 33000);
        $monthlyGain = ($optimizedRevenue - $currentRevenue) / 12;
        $breakEvenMonths = $monthlyGain > 0 ? $investment / $monthlyGain : 0;
        
        return [
            'investment_required' => $investment,
            'monthly_gain' => $monthlyGain,
            'break_even_months' => round($breakEvenMonths, 1),
            'roi_first_year' => round((($optimizedRevenue - $currentRevenue - $investment) / $investment) * 100, 2),
        ];
    }

    private function identifyRevenueRisks(): array
    {
        return [
            'market_downturn' => 'medium',
            'increased_competition' => 'high',
            'regulatory_changes' => 'low',
            'economic_factors' => 'medium',
        ];
    }

    private function getImplementationTimeline(): array
    {
        return [
            'analysis_phase' => '2 weeks',
            'implementation_phase' => '4 weeks',
            'monitoring_phase' => 'ongoing',
            'total_timeline' => '6 weeks',
        ];
    }
}
