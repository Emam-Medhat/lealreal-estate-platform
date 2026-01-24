<?php

namespace App\Http\Controllers;

use App\Models\AiPrediction;
use App\Models\AnalyticEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AiInsightsController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        $insights = AiPrediction::latest()->paginate(20);
        
        return view('analytics.ai-insights.index', compact('insights'));
    }

    public function generateInsights(Request $request)
    {
        $request->validate([
            'data_source' => 'required|string',
            'insight_type' => 'required|string|in:anomaly,pattern,prediction,recommendation',
            'time_range' => 'required|string|in:1d,7d,30d,90d'
        ]);

        $insights = $this->generateAiInsights($request->all());

        return response()->json([
            'status' => 'success',
            'insights' => $insights
        ]);
    }

    public function anomalyDetection(Request $request)
    {
        $period = $request->period ?? '30d';
        $anomalies = $this->detectAnomalies($period);

        return response()->json($anomalies);
    }

    public function patternRecognition(Request $request)
    {
        $patterns = $this->recognizePatterns();

        return response()->json($patterns);
    }

    public function predictiveInsights(Request $request)
    {
        $predictions = $this->generatePredictiveInsights();

        return response()->json($predictions);
    }

    public function recommendations(Request $request)
    {
        $recommendations = $this->generateRecommendations();

        return response()->json($recommendations);
    }

    private function generateAiInsights($params)
    {
        return match($params['insight_type']) {
            'anomaly' => $this->detectAnomalies($params['time_range']),
            'pattern' => $this->recognizePatterns(),
            'prediction' => $this->generatePredictiveInsights(),
            'recommendation' => $this->generateRecommendations(),
            default => []
        };
    }

    private function detectAnomalies($period)
    {
        $data = $this->getAnalyticsData($period);
        $anomalies = [];

        // Simple anomaly detection using statistical methods
        $mean = array_sum($data) / count($data);
        $stdDev = $this->calculateStandardDeviation($data, $mean);

        foreach ($data as $index => $value) {
            $zScore = ($value - $mean) / $stdDev;
            
            if (abs($zScore) > 2) {
                $anomalies[] = [
                    'date' => now()->subDays(count($data) - $index)->format('Y-m-d'),
                    'value' => $value,
                    'z_score' => $zScore,
                    'type' => $zScore > 0 ? 'spike' : 'drop',
                    'severity' => abs($zScore) > 3 ? 'high' : 'medium'
                ];
            }
        }

        return $anomalies;
    }

    private function recognizePatterns()
    {
        return [
            'seasonal_patterns' => $this->detectSeasonalPatterns(),
            'trend_patterns' => $this->detectTrendPatterns(),
            'behavioral_patterns' => $this->detectBehavioralPatterns(),
            'conversion_patterns' => $this->detectConversionPatterns()
        ];
    }

    private function generatePredictiveInsights()
    {
        return [
            'traffic_forecast' => $this->forecastTraffic(),
            'conversion_forecast' => $this->forecastConversions(),
            'revenue_forecast' => $this->forecastRevenue(),
            'user_growth_forecast' => $this->forecastUserGrowth()
        ];
    }

    private function generateRecommendations()
    {
        return [
            'optimization_recommendations' => $this->generateOptimizationRecommendations(),
            'content_recommendations' => $this->generateContentRecommendations(),
            'user_experience_recommendations' => $this->generateUXRecommendations(),
            'marketing_recommendations' => $this->generateMarketingRecommendations()
        ];
    }

    private function getAnalyticsData($period)
    {
        $days = $this->getDaysFromPeriod($period);
        
        return AnalyticEvent::where('created_at', '>', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    private function calculateStandardDeviation($data, $mean)
    {
        $variance = 0;
        foreach ($data as $value) {
            $variance += pow($value - $mean, 2);
        }
        return sqrt($variance / count($data));
    }

    private function detectSeasonalPatterns()
    {
        return [
            'weekly_pattern' => 'Higher traffic on weekends',
            'monthly_pattern' => 'Peak activity mid-month',
            'seasonal_pattern' => 'Summer months show 20% increase'
        ];
    }

    private function detectTrendPatterns()
    {
        return [
            'upward_trend' => 'Overall traffic increasing by 15%',
            'conversion_trend' => 'Conversion rate stable at 2.5%',
            'engagement_trend' => 'Session duration improving'
        ];
    }

    private function detectBehavioralPatterns()
    {
        return [
            'navigation_pattern' => 'Users prefer direct property search',
            'time_pattern' => 'Peak activity between 6-9 PM',
            'device_pattern' => 'Mobile traffic 60% of total'
        ];
    }

    private function detectConversionPatterns()
    {
        return [
            'path_pattern' => 'Most conversions from property detail pages',
            'time_pattern' => 'Higher conversion on weekdays',
            'source_pattern' => 'Search engines drive 40% of conversions'
        ];
    }

    private function forecastTraffic()
    {
        return [
            'next_7_days' => [1200, 1250, 1300, 1350, 1400, 1380, 1420],
            'next_30_days' => '10% increase expected',
            'confidence' => 85
        ];
    }

    private function forecastConversions()
    {
        return [
            'next_7_days' => [30, 32, 35, 33, 38, 36, 40],
            'next_30_days' => '12% increase expected',
            'confidence' => 80
        ];
    }

    private function forecastRevenue()
    {
        return [
            'next_7_days' => [15000, 16000, 16500, 17000, 17500, 17200, 18000],
            'next_30_days' => '15% increase expected',
            'confidence' => 75
        ];
    }

    private function forecastUserGrowth()
    {
        return [
            'next_7_days' => [100, 105, 110, 108, 115, 112, 120],
            'next_30_days' => '8% increase expected',
            'confidence' => 70
        ];
    }

    private function generateOptimizationRecommendations()
    {
        return [
            'Optimize page load speed for better conversion',
            'Improve mobile navigation for 60% mobile users',
            'Add more property images to increase engagement',
            'Implement live chat for better user support'
        ];
    }

    private function generateContentRecommendations()
    {
        return [
            'Create more video content for property tours',
            'Write neighborhood guides for popular areas',
            'Publish market analysis articles',
            'Share customer success stories'
        ];
    }

    private function generateUXRecommendations()
    {
        return [
            'Simplify property search filters',
            'Add saved search functionality',
            'Implement property comparison feature',
            'Improve property image gallery'
        ];
    }

    private function generateMarketingRecommendations()
    {
        return [
            'Focus on SEO for organic traffic growth',
            'Run targeted social media campaigns',
            'Implement email marketing automation',
            'Use retargeting ads for abandoned carts'
        ];
    }

    private function getDaysFromPeriod($period)
    {
        return match($period) {
            '1d' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 30
        };
    }
}
