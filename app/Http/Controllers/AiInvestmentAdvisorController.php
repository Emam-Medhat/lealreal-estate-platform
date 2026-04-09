<?php

namespace App\Http\Controllers;

use App\Models\AiMarketInsight;
use App\Models\AiPricePrediction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AiInvestmentAdvisorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the investment advisor dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        
        // Get investment statistics
        $stats = [
            'total_analyses' => DB::table('ai_market_insights')->count(),
            'recent_predictions' => DB::table('ai_price_predictions')
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'high_potential_areas' => $this->getHighPotentialAreas(),
            'market_trends' => $this->getMarketTrends(),
        ];

        // Get recent investment recommendations
        $recommendations = $this->generateInvestmentRecommendations();

        return view('ai.investment-advisor', compact('stats', 'recommendations'));
    }

    /**
     * Analyze investment opportunity.
     */
    public function analyzeOpportunity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'investment_amount' => 'required|numeric|min:10000',
            'investment_type' => 'required|in:buy,rent,flip',
            'time_horizon' => 'required|in:short,medium,long',
            'risk_tolerance' => 'required|in:low,medium,high',
        ]);

        try {
            $analysis = $this->performInvestmentAnalysis($validated);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'message' => 'تم تحليل الفرصة الاستثمارية بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحليل الفرصة الاستثمارية: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get portfolio recommendations.
     */
    public function getPortfolioRecommendations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'portfolio_size' => 'required|numeric|min:100000',
            'diversification_level' => 'required|in:conservative,moderate,aggressive',
            'geographic_focus' => 'nullable|array',
            'property_types' => 'nullable|array',
        ]);

        try {
            $recommendations = $this->generatePortfolioRecommendations($validated);
            
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations,
                'message' => 'تم إنشاء توصيات المحفظة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء توصيات المحفظة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate ROI for investment.
     */
    public function calculateROI(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'purchase_price' => 'required|numeric|min:0',
            'renovation_cost' => 'nullable|numeric|min:0',
            'holding_period' => 'required|integer|min:1|max:30',
            'rental_income' => 'nullable|numeric|min:0',
            'expenses' => 'nullable|numeric|min:0',
        ]);

        try {
            $roi = $this->performROICalculation($validated);
            
            return response()->json([
                'success' => true,
                'roi' => $roi,
                'message' => 'تم حساب العائد على الاستثمار بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حساب العائد على الاستثمار: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get market comparison.
     */
    public function getMarketComparison(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_type' => 'required|string',
            'location' => 'required|string',
            'price_range' => 'nullable|array',
            'comparison_period' => 'required|in:1month,3months,6months,1year',
        ]);

        try {
            $comparison = $this->generateMarketComparison($validated);
            
            return response()->json([
                'success' => true,
                'comparison' => $comparison,
                'message' => 'تم إنشاء مقارنة السوق بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء مقارنة السوق: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform investment analysis using AI simulation.
     */
    private function performInvestmentAnalysis(array $data): array
    {
        // Simulate AI analysis
        $riskScore = rand(30, 90);
        $potentialReturn = rand(5, 25);
        $confidence = rand(70, 95);
        
        $analysis = [
            'property_id' => $data['property_id'],
            'investment_amount' => $data['investment_amount'],
            'investment_type' => $data['investment_type'],
            'time_horizon' => $data['time_horizon'],
            'risk_tolerance' => $data['risk_tolerance'],
            'risk_score' => $riskScore,
            'potential_return' => $potentialReturn,
            'confidence_level' => $confidence,
            'recommendation' => $this->getInvestmentRecommendation($riskScore, $potentialReturn),
            'key_factors' => $this->getInvestmentFactors($data),
            'market_outlook' => $this->getMarketOutlook(),
            'projected_value' => $this->calculateProjectedValue($data),
            'cash_flow_analysis' => $this->analyzeCashFlow($data),
            'created_at' => now()->toDateTimeString(),
        ];

        return $analysis;
    }

    /**
     * Generate portfolio recommendations.
     */
    private function generatePortfolioRecommendations(array $data): array
    {
        $recommendations = [];
        
        // Generate different investment strategies
        $strategies = [
            'conservative' => [
                'allocation' => ['residential' => 60, 'commercial' => 30, 'land' => 10],
                'risk_level' => 'low',
                'expected_return' => '6-8%',
            ],
            'moderate' => [
                'allocation' => ['residential' => 40, 'commercial' => 40, 'land' => 20],
                'risk_level' => 'medium',
                'expected_return' => '8-12%',
            ],
            'aggressive' => [
                'allocation' => ['residential' => 20, 'commercial' => 30, 'land' => 50],
                'risk_level' => 'high',
                'expected_return' => '12-18%',
            ],
        ];

        $strategy = $strategies[$data['diversification_level']] ?? $strategies['moderate'];
        
        foreach ($strategy['allocation'] as $type => $percentage) {
            $amount = ($data['portfolio_size'] * $percentage) / 100;
            $recommendations[] = [
                'property_type' => $type,
                'allocation_percentage' => $percentage,
                'recommended_amount' => $amount,
                'expected_return' => $this->calculateExpectedReturn($type, $amount),
                'risk_level' => $strategy['risk_level'],
            ];
        }

        return [
            'strategy' => $data['diversification_level'],
            'recommendations' => $recommendations,
            'total_expected_return' => $strategy['expected_return'],
            'diversification_score' => $this->calculateDiversificationScore($recommendations),
        ];
    }

    /**
     * Perform ROI calculation.
     */
    private function performROICalculation(array $data): array
    {
        $totalInvestment = $data['purchase_price'] + ($data['renovation_cost'] ?? 0);
        $projectedValue = $this->projectPropertyValue($data['property_id'], $data['holding_period']);
        $totalIncome = ($data['rental_income'] ?? 0) * 12 * $data['holding_period'];
        $totalExpenses = ($data['expenses'] ?? 0) * 12 * $data['holding_period'];
        
        $netProfit = ($projectedValue - $totalInvestment) + ($totalIncome - $totalExpenses);
        $roi = ($netProfit / $totalInvestment) * 100;
        $annualizedROI = $roi / $data['holding_period'];

        return [
            'total_investment' => $totalInvestment,
            'projected_value' => $projectedValue,
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'roi_percentage' => round($roi, 2),
            'annualized_roi' => round($annualizedROI, 2),
            'payback_period' => $this->calculatePaybackPeriod($totalInvestment, $totalIncome - $totalExpenses),
            'profitability_score' => $this->getProfitabilityScore($roi),
        ];
    }

    /**
     * Generate market comparison.
     */
    private function generateMarketComparison(array $data): array
    {
        $periods = [
            '1month' => 1,
            '3months' => 3,
            '6months' => 6,
            '1year' => 12,
        ];

        $months = $periods[$data['comparison_period']] ?? 12;
        
        return [
            'property_type' => $data['property_type'],
            'location' => $data['location'],
            'comparison_period' => $data['comparison_period'],
            'price_trends' => $this->getPriceTrends($data, $months),
            'volume_trends' => $this->getVolumeTrends($data, $months),
            'average_prices' => $this->getAveragePrices($data),
            'market_sentiment' => $this->getMarketSentiment($data),
            'competitor_analysis' => $this->getCompetitorAnalysis($data),
            'investment_hotspots' => $this->getInvestmentHotspots($data),
        ];
    }

    /**
     * Get investment recommendation based on risk and return.
     */
    private function getInvestmentRecommendation(int $riskScore, int $potentialReturn): string
    {
        if ($riskScore <= 40 && $potentialReturn >= 15) {
            return 'استثمار ممتاز - فرصة ذهبية';
        } elseif ($riskScore <= 60 && $potentialReturn >= 10) {
            return 'استثمار جيد - فرصة مواتية';
        } elseif ($riskScore <= 75 && $potentialReturn >= 7) {
            return 'استثمار مقبول - يحتاج دراسة متعمقة';
        } else {
            return 'استثمار محفوف بالمخاطر - غير موصى به حالياً';
        }
    }

    /**
     * Get investment factors.
     */
    private function getInvestmentFactors(array $data): array
    {
        return [
            'market_demand' => rand(60, 95),
            'location_score' => rand(70, 98),
            'property_condition' => rand(65, 92),
            'economic_outlook' => rand(55, 88),
            'infrastructure_development' => rand(60, 90),
            'regulatory_environment' => rand(70, 85),
        ];
    }

    /**
     * Get market outlook.
     */
    private function getMarketOutlook(): array
    {
        return [
            'short_term' => ['trend' => 'positive', 'confidence' => rand(70, 85)],
            'medium_term' => ['trend' => 'stable', 'confidence' => rand(75, 90)],
            'long_term' => ['trend' => 'positive', 'confidence' => rand(80, 95)],
        ];
    }

    /**
     * Calculate projected value.
     */
    private function calculateProjectedValue(array $data): array
    {
        $baseValue = $data['investment_amount'];
        $growthRates = [
            'short' => rand(3, 8),
            'medium' => rand(8, 15),
            'long' => rand(15, 25),
        ];

        return [
            'current_value' => $baseValue,
            '1_year_projection' => $baseValue * (1 + $growthRates['short'] / 100),
            '3_year_projection' => $baseValue * (1 + $growthRates['medium'] / 100),
            '5_year_projection' => $baseValue * (1 + $growthRates['long'] / 100),
        ];
    }

    /**
     * Analyze cash flow.
     */
    private function analyzeCashFlow(array $data): array
    {
        $monthlyIncome = rand(2000, 8000);
        $monthlyExpenses = rand(1000, 3000);
        $netCashFlow = $monthlyIncome - $monthlyExpenses;

        return [
            'monthly_income' => $monthlyIncome,
            'monthly_expenses' => $monthlyExpenses,
            'net_cash_flow' => $netCashFlow,
            'annual_cash_flow' => $netCashFlow * 12,
            'cash_flow_roi' => ($netCashFlow * 12) / $data['investment_amount'] * 100,
        ];
    }

    /**
     * Get high potential areas.
     */
    private function getHighPotentialAreas(): array
    {
        return [
            ['area' => 'الرياض - حي النخيل', 'potential_score' => 92, 'avg_roi' => '12%'],
            ['area' => 'جدة - البحر الأحمر', 'potential_score' => 88, 'avg_roi' => '10%'],
            ['area' => 'الشرقية - الخبر', 'potential_score' => 85, 'avg_roi' => '9%'],
        ];
    }

    /**
     * Get market trends.
     */
    private function getMarketTrends(): array
    {
        return [
            'price_trend' => 'increasing',
            'demand_trend' => 'high',
            'supply_trend' => 'moderate',
            'investor_sentiment' => 'positive',
        ];
    }

    /**
     * Generate investment recommendations.
     */
    private function generateInvestmentRecommendations(): array
    {
        return [
            [
                'type' => 'سكني',
                'location' => 'الرياض',
                'reason' => 'نمو سريع وطلب مرتفع',
                'expected_return' => '12-15%',
                'risk_level' => 'متوسط',
            ],
            [
                'type' => 'تجاري',
                'location' => 'جدة',
                'reason' => 'حركة تجارية نشطة',
                'expected_return' => '8-12%',
                'risk_level' => 'منخفض',
            ],
        ];
    }

    /**
     * Helper methods for calculations
     */
    private function calculateExpectedReturn(string $type, float $amount): array
    {
        $returns = [
            'residential' => ['min' => 6, 'max' => 12],
            'commercial' => ['min' => 8, 'max' => 15],
            'land' => ['min' => 10, 'max' => 20],
        ];

        $range = $returns[$type] ?? $returns['residential'];
        $expected = ($range['min'] + $range['max']) / 2;
        
        return [
            'min_return' => $amount * $range['min'] / 100,
            'max_return' => $amount * $range['max'] / 100,
            'expected_return' => $amount * $expected / 100,
        ];
    }

    private function calculateDiversificationScore(array $recommendations): int
    {
        $types = array_column($recommendations, 'property_type');
        return count(array_unique($types)) * 25;
    }

    private function projectPropertyValue(int $propertyId, int $years): float
    {
        // Simulate property value projection
        $annualGrowth = rand(3, 8);
        $currentValue = rand(500000, 2000000);
        
        return $currentValue * pow(1 + $annualGrowth / 100, $years);
    }

    private function calculatePaybackPeriod(float $investment, float $annualCashFlow): float
    {
        return $annualCashFlow > 0 ? $investment / $annualCashFlow : 0;
    }

    private function getProfitabilityScore(float $roi): string
    {
        if ($roi >= 20) return 'ممتاز';
        if ($roi >= 15) return 'جيد جداً';
        if ($roi >= 10) return 'جيد';
        if ($roi >= 5) return 'مقبول';
        return 'ضعيف';
    }

    private function getPriceTrends(array $data, int $months): array
    {
        return [
            'direction' => 'upward',
            'change_percentage' => rand(5, 15),
            'volatility' => 'low',
        ];
    }

    private function getVolumeTrends(array $data, int $months): array
    {
        return [
            'direction' => 'stable',
            'change_percentage' => rand(-5, 10),
            'market_activity' => 'moderate',
        ];
    }

    private function getAveragePrices(array $data): array
    {
        return [
            'price_per_sqm' => rand(3000, 8000),
            'median_price' => rand(400000, 1500000),
            'price_range' => ['min' => 300000, 'max' => 2000000],
        ];
    }

    private function getMarketSentiment(array $data): array
    {
        return [
            'buyer_sentiment' => 'positive',
            'seller_sentiment' => 'neutral',
            'investor_sentiment' => 'bullish',
            'overall_score' => rand(65, 85),
        ];
    }

    private function getCompetitorAnalysis(array $data): array
    {
        return [
            'competitor_count' => rand(5, 20),
            'average_competition' => 'moderate',
            'market_saturation' => 'low',
        ];
    }

    private function getInvestmentHotspots(array $data): array
    {
        return [
            ['area' => 'حي المروج', 'score' => 88, 'reason' => 'تطور بنية تحتية'],
            ['area' => 'حي العزيزية', 'score' => 82, 'reason' => 'قرب من المرافق'],
        ];
    }
}
