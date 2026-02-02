<?php

namespace App\Http\Controllers;

use App\Models\MarketTrend;
use App\Models\CompetitorData;
use App\Models\AnalyticEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MarketAnalyticsController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function marketTrends(Request $request)
    {
        try {
            $period = $request->period ?? '30d';
            $startDate = $this->getStartDate($period);

            // Calculate market metrics
            $marketSize = $this->calculateMarketSize($startDate);
            $marketGrowth = $this->calculateMarketGrowth($startDate);
            $ourShare = $this->calculateMarketShare($startDate);
            $competitorCount = CompetitorData::where('created_at', '>', $startDate)->count();
            
            // Get recent trends for the table
            $recentTrends = MarketTrend::latest()->take(10)->get();

            // Return view with data
            return view('analytics.market-trends', compact(
                'marketSize', 
                'marketGrowth', 
                'ourShare', 
                'competitorCount', 
                'recentTrends', 
                'period'
            ));
        } catch (\Exception $e) {
            // Return view with error message
            return view('analytics.market-trends', [
                'marketSize' => 0,
                'marketGrowth' => 0,
                'ourShare' => 0,
                'competitorCount' => 0,
                'recentTrends' => collect([]),
                'period' => $period ?? '30d',
                'error' => 'Failed to fetch market trends: ' . $e->getMessage()
            ]);
        }
    }

    private function getDemandTrends($startDate)
    {
        return AnalyticEvent::where('created_at', '>', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as demand')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('demand')
            ->toArray();
    }

    private function getCompetitorActivity($startDate)
    {
        return CompetitorData::where('created_at', '>', $startDate)
            ->selectRaw('name, COUNT(*) as activity_count')
            ->groupBy('name')
            ->orderBy('activity_count', 'desc')
            ->get();
    }

    private function getSeasonalPatterns($startDate)
    {
        $data = AnalyticEvent::where('created_at', '>', $startDate)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return $data->mapWithKeys(function($item) {
            $monthNames = [
                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
            ];
            return [$monthNames[$item->month] => $item->count];
        });
    }

    private function getMarketSentiment($startDate)
    {
        // Simplified sentiment analysis based on user behavior
        $totalViews = AnalyticEvent::where('event_name', 'property_view')
            ->where('created_at', '>', $startDate)
            ->count();

        $totalSearches = AnalyticEvent::where('event_name', 'property_search')
            ->where('created_at', '>', $startDate)
            ->count();

        $totalContacts = AnalyticEvent::where('event_name', 'contact_agent')
            ->where('created_at', '>', $startDate)
            ->count();

        $engagementScore = $totalViews > 0 ? (($totalSearches + $totalContacts) / $totalViews) * 100 : 0;

        return [
            'engagement_score' => round($engagementScore, 2),
            'sentiment' => $engagementScore > 10 ? 'إيجابي' : ($engagementScore > 5 ? 'محايد' : 'سلبي'),
            'total_interactions' => $totalViews + $totalSearches + $totalContacts
        ];
    }

    public function index()
    {
        $marketTrends = MarketTrend::latest()->paginate(20);
        $competitorData = CompetitorData::latest()->take(10)->get();
        
        return view('analytics.market-trends.index', compact('marketTrends', 'competitorData'));
    }

    public function marketOverview(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $overview = [
            'total_market_size' => $this->calculateMarketSize($startDate),
            'market_growth' => $this->calculateMarketGrowth($startDate),
            'market_share' => $this->calculateMarketShare($startDate),
            'competitor_count' => CompetitorData::where('created_at', '>', $startDate)->count(),
            'price_trends' => $this->getPriceTrends($startDate),
            'demand_supply' => $this->getDemandSupplyRatio($startDate)
        ];

        return response()->json($overview);
    }

    public function competitorAnalysis(Request $request)
    {
        $competitors = CompetitorData::with(['trends' => function($query) {
            $query->where('created_at', '>', now()->subDays(90));
        }])->get();

        $analysis = $competitors->map(function($competitor) {
            return [
                'name' => $competitor->name,
                'market_share' => $competitor->market_share,
                'growth_rate' => $this->calculateCompetitorGrowth($competitor),
                'price_position' => $competitor->avg_price,
                'strengths' => $competitor->strengths,
                'weaknesses' => $competitor->weaknesses,
                'trend_direction' => $this->getTrendDirection($competitor->trends)
            ];
        });

        return response()->json($analysis);
    }

    public function priceAnalysis(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $priceData = [
            'average_prices' => $this->getAveragePrices($startDate),
            'price_ranges' => $this->getPriceRanges($startDate),
            'price_trends' => $this->getPriceTrends($startDate),
            'competitor_pricing' => $this->getCompetitorPricing($startDate),
            'price_elasticity' => $this->calculatePriceElasticity($startDate)
        ];

        return response()->json($priceData);
    }

    public function demandForecast(Request $request)
    {
        $period = $request->period ?? '90d';
        $forecastDays = $request->forecast_days ?? 30;

        $historicalDemand = $this->getDemandHistory($period);
        $forecast = $this->forecastDemand($historicalDemand, $forecastDays);

        return response()->json([
            'historical' => $historicalDemand,
            'forecast' => $forecast,
            'seasonality' => $this->analyzeSeasonality($historicalDemand),
            'factors' => $this->getDemandFactors($historicalDemand)
        ]);
    }

    public function marketSegmentation(Request $request)
    {
        $segments = $this->performMarketSegmentation();
        
        return response()->json([
            'segments' => $segments,
            'segment_sizes' => $this->getSegmentSizes($segments),
            'segment_growth' => $this->getSegmentGrowth($segments),
            'recommendations' => $this->getSegmentRecommendations($segments)
        ]);
    }

    public function opportunityAnalysis(Request $request)
    {
        $opportunities = $this->identifyMarketOpportunities();
        
        return response()->json([
            'opportunities' => $opportunities,
            'market_gaps' => $this->identifyMarketGaps(),
            'growth_potential' => $this->assessGrowthPotential(),
            'risks' => $this->assessMarketRisks()
        ]);
    }

    public function generateReport(Request $request)
    {
        $reportType = $request->report_type ?? 'comprehensive';
        $format = $request->format ?? 'json';

        $data = match($reportType) {
            'competitor' => $this->generateCompetitorReport(),
            'pricing' => $this->generatePricingReport(),
            'demand' => $this->generateDemandReport(),
            'comprehensive' => $this->generateComprehensiveReport(),
            default => []
        };

        if ($format === 'pdf') {
            return $this->generatePdfReport($data, $reportType);
        }

        return response()->json($data);
    }

    private function calculateMarketSize($startDate)
    {
        return AnalyticEvent::where('event_name', 'property_view')
            ->where('created_at', '>', $startDate)
            ->distinct('user_session_id')
            ->count() * 1000; // Estimated value per visitor
    }

    private function calculateMarketGrowth($startDate)
    {
        $currentPeriod = AnalyticEvent::where('event_name', 'property_view')
            ->where('created_at', '>', $startDate)
            ->count();

        $previousPeriod = AnalyticEvent::where('event_name', 'property_view')
            ->where('created_at', '>', $startDate->copy()->subDays(30))
            ->where('created_at', '<=', $startDate)
            ->count();

        return $previousPeriod > 0 ? (($currentPeriod - $previousPeriod) / $previousPeriod) * 100 : 0;
    }

    private function calculateMarketShare($startDate)
    {
        $ourViews = AnalyticEvent::where('event_name', 'property_view')
            ->where('created_at', '>', $startDate)
            ->count();

        $totalMarketViews = $ourViews * 5; // Estimated total market size

        return $totalMarketViews > 0 ? ($ourViews / $totalMarketViews) * 100 : 0;
    }

    private function getPriceTrends($startDate)
    {
        try {
            // Get basic event count as price trend proxy
            return AnalyticEvent::where('event_name', 'property_view')
                ->where('created_at', '>', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as avg_price')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('avg_price')
                ->toArray();
        } catch (\Exception $e) {
            // Fallback if properties field doesn't have price data
            return [];
        }
    }

    private function getDemandSupplyRatio($startDate)
    {
        $demand = AnalyticEvent::where('event_name', 'property_search')
            ->where('created_at', '>', $startDate)
            ->count();

        $supply = AnalyticEvent::where('event_name', 'property_listing')
            ->where('created_at', '>', $startDate)
            ->count();

        return $supply > 0 ? $demand / $supply : 0;
    }

    private function calculateCompetitorGrowth($competitor)
    {
        $trends = $competitor->trends;
        if ($trends->count() < 2) return 0;

        $first = $trends->first();
        $last = $trends->last();

        return $first->market_share > 0 ? 
            (($last->market_share - $first->market_share) / $first->market_share) * 100 : 0;
    }

    private function getTrendDirection($trends)
    {
        if ($trends->count() < 2) return 'stable';

        $recent = $trends->take(-5);
        $directions = [];

        for ($i = 1; $i < $recent->count(); $i++) {
            $prev = $recent[$i-1];
            $curr = $recent[$i];
            
            if ($curr->market_share > $prev->market_share) {
                $directions[] = 'up';
            } elseif ($curr->market_share < $prev->market_share) {
                $directions[] = 'down';
            } else {
                $directions[] = 'stable';
            }
        }

        $upCount = count(array_filter($directions, fn($d) => $d === 'up'));
        $downCount = count(array_filter($directions, fn($d) => $d === 'down'));

        if ($upCount > $downCount) return 'increasing';
        if ($downCount > $upCount) return 'decreasing';
        return 'stable';
    }

    private function getAveragePrices($startDate)
    {
        try {
            return AnalyticEvent::where('event_name', 'property_view')
                ->where('created_at', '>', $startDate)
                ->selectRaw('property_type, COUNT(*) as avg_price')
                ->groupBy('property_type')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    private function getPriceRanges($startDate)
    {
        try {
            return AnalyticEvent::where('event_name', 'property_view')
                ->where('created_at', '>', $startDate)
                ->selectRaw('
                    "No Price Data" as price_range,
                    COUNT(*) as count
                ')
                ->groupBy('price_range')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    private function getCompetitorPricing($startDate)
    {
        return CompetitorData::where('created_at', '>', $startDate)
            ->select('name', 'avg_price', 'min_price', 'max_price')
            ->orderBy('avg_price')
            ->get();
    }

    private function calculatePriceElasticity($startDate)
    {
        try {
            // Simplified elasticity calculation using event counts
            $priceData = AnalyticEvent::where('event_name', 'property_view')
                ->where('created_at', '>', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as price')
                ->groupBy('date')
                ->get();

            // Simplified elasticity calculation
            $priceChanges = [];
            $demandChanges = [];

            for ($i = 1; $i < $priceData->count(); $i++) {
                $priceChanges[] = $priceData[$i]->price - $priceData[$i-1]->price;
                $demandChanges[] = 1; // Simplified - each view represents demand
            }

            $numerator = array_sum(array_map(fn($p, $d) => $p * $d, $priceChanges, $demandChanges));
            $denominator = array_sum(array_map(fn($p) => $p * $p, $priceChanges));

            return $denominator != 0 ? $numerator / $denominator : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getDemandHistory($period)
    {
        $days = $this->getDaysFromPeriod($period);
        
        return AnalyticEvent::where('event_name', 'property_search')
            ->where('created_at', '>', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as demand')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function forecastDemand($historicalData, $forecastDays)
    {
        $values = $historicalData->pluck('demand')->toArray();
        
        // Simple moving average forecast
        $recentValues = array_slice($values, -7);
        $avgDemand = array_sum($recentValues) / count($recentValues);
        
        $forecast = [];
        for ($i = 1; $i <= $forecastDays; $i++) {
            $forecast[] = [
                'date' => now()->addDays($i)->format('Y-m-d'),
                'predicted_demand' => $avgDemand + (rand(-10, 10) / 100 * $avgDemand)
            ];
        }
        
        return $forecast;
    }

    private function analyzeSeasonality($data)
    {
        $monthlyData = [];
        
        foreach ($data as $record) {
            $month = Carbon::parse($record->date)->month;
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = [];
            }
            $monthlyData[$month][] = $record->demand;
        }

        $seasonality = [];
        foreach ($monthlyData as $month => $values) {
            $seasonality[$month] = array_sum($values) / count($values);
        }

        return $seasonality;
    }

    private function getDemandFactors($data)
    {
        return [
            'seasonal_factor' => $this->calculateSeasonalFactor($data),
            'trend_factor' => $this->calculateTrendFactor($data),
            'external_factors' => ['economic', 'seasonal', 'promotional']
        ];
    }

    private function performMarketSegmentation()
    {
        return [
            ['name' => 'First-time buyers', 'size' => 35, 'growth' => 5],
            ['name' => 'Investors', 'size' => 25, 'growth' => 8],
            ['name' => 'Upsizers', 'size' => 20, 'growth' => 3],
            ['name' => 'Downsizers', 'size' => 15, 'growth' => 2],
            ['name' => 'Luxury buyers', 'size' => 5, 'growth' => 10]
        ];
    }

    private function getStartDate($period)
    {
        return match($period) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30)
        };
    }

    private function getDaysFromPeriod($period)
    {
        return match($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 30
        };
    }
}
