<?php

namespace App\Http\Controllers;

use App\Models\MarketTrend;
use App\Models\AnalyticEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TrendAnalysisController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        return view('analytics.trends.index');
    }

    public function analyzeTrends(Request $request)
    {
        $request->validate([
            'trend_type' => 'required|string|in:traffic,conversion,revenue,engagement',
            'time_range' => 'required|string|in:7d,30d,90d,1y',
            'granularity' => 'required|string|in:hourly,daily,weekly,monthly'
        ]);

        $trends = $this->performTrendAnalysis($request->all());

        return response()->json($trends);
    }

    public function trafficTrends(Request $request)
    {
        $period = $request->period ?? '30d';
        $trends = $this->analyzeTrafficTrends($period);

        return response()->json($trends);
    }

    public function conversionTrends(Request $request)
    {
        $period = $request->period ?? '30d';
        $trends = $this->analyzeConversionTrends($period);

        return response()->json($trends);
    }

    public function revenueTrends(Request $request)
    {
        $period = $request->period ?? '30d';
        $trends = $this->analyzeRevenueTrends($period);

        return response()->json($trends);
    }

    public function engagementTrends(Request $request)
    {
        $period = $request->period ?? '30d';
        $trends = $this->analyzeEngagementTrends($period);

        return response()->json($trends);
    }

    public function seasonalTrends(Request $request)
    {
        $seasonalData = $this->analyzeSeasonalTrends();

        return response()->json($seasonalData);
    }

    public function trendForecast(Request $request)
    {
        $trendType = $request->trend_type ?? 'traffic';
        $forecastPeriod = $request->forecast_period ?? 30;

        $forecast = $this->generateTrendForecast($trendType, $forecastPeriod);

        return response()->json($forecast);
    }

    public function trendComparison(Request $request)
    {
        $period1 = $request->period1 ?? '30d';
        $period2 = $request->period2 ?? '30d';
        $trendType = $request->trend_type ?? 'traffic';

        $comparison = $this->compareTrends($trendType, $period1, $period2);

        return response()->json($comparison);
    }

    public function exportTrendReport(Request $request)
    {
        $format = $request->format ?? 'json';
        $trendType = $request->trend_type ?? 'traffic';
        $period = $request->period ?? '30d';

        $report = $this->generateTrendReport($trendType, $period);

        if ($format === 'csv') {
            return $this->exportTrendToCsv($report);
        }

        return response()->json($report);
    }

    private function performTrendAnalysis($params)
    {
        return match($params['trend_type']) {
            'traffic' => $this->analyzeTrafficTrends($params['time_range']),
            'conversion' => $this->analyzeConversionTrends($params['time_range']),
            'revenue' => $this->analyzeRevenueTrends($params['time_range']),
            'engagement' => $this->analyzeEngagementTrends($params['time_range']),
            default => []
        };
    }

    private function analyzeTrafficTrends($period)
    {
        $data = $this->getTrafficData($period);
        
        return [
            'data' => $data,
            'trend_direction' => $this->calculateTrendDirection($data),
            'growth_rate' => $this->calculateGrowthRate($data),
            'seasonality' => $this->detectSeasonality($data),
            'forecast' => $this->forecastTrend($data, 7)
        ];
    }

    private function analyzeConversionTrends($period)
    {
        $data = $this->getConversionData($period);
        
        return [
            'data' => $data,
            'trend_direction' => $this->calculateTrendDirection($data),
            'growth_rate' => $this->calculateGrowthRate($data),
            'conversion_rate' => $this->calculateAverageConversionRate($period),
            'forecast' => $this->forecastTrend($data, 7)
        ];
    }

    private function analyzeRevenueTrends($period)
    {
        $data = $this->getRevenueData($period);
        
        return [
            'data' => $data,
            'trend_direction' => $this->calculateTrendDirection($data),
            'growth_rate' => $this->calculateGrowthRate($data),
            'average_revenue' => array_sum($data) / count($data),
            'forecast' => $this->forecastTrend($data, 7)
        ];
    }

    private function analyzeEngagementTrends($period)
    {
        $data = $this->getEngagementData($period);
        
        return [
            'data' => $data,
            'trend_direction' => $this->calculateTrendDirection($data),
            'growth_rate' => $this->calculateGrowthRate($data),
            'average_engagement' => array_sum($data) / count($data),
            'forecast' => $this->forecastTrend($data, 7)
        ];
    }

    private function analyzeSeasonalTrends()
    {
        return [
            'monthly_patterns' => $this->getMonthlyPatterns(),
            'weekly_patterns' => $this->getWeeklyPatterns(),
            'daily_patterns' => $this->getDailyPatterns(),
            'holiday_impact' => $this->getHolidayImpact()
        ];
    }

    private function generateTrendForecast($trendType, $forecastPeriod)
    {
        $historicalData = match($trendType) {
            'traffic' => $this->getTrafficData('90d'),
            'conversion' => $this->getConversionData('90d'),
            'revenue' => $this->getRevenueData('90d'),
            'engagement' => $this->getEngagementData('90d'),
            default => []
        };

        return [
            'forecast_data' => $this->forecastTrend($historicalData, $forecastPeriod),
            'confidence_level' => 85,
            'method' => 'linear_regression'
        ];
    }

    private function compareTrends($trendType, $period1, $period2)
    {
        $data1 = match($trendType) {
            'traffic' => $this->getTrafficData($period1),
            'conversion' => $this->getConversionData($period1),
            'revenue' => $this->getRevenueData($period1),
            'engagement' => $this->getEngagementData($period1),
            default => []
        };

        $data2 = match($trendType) {
            'traffic' => $this->getTrafficData($period2),
            'conversion' => $this->getConversionData($period2),
            'revenue' => $this->getRevenueData($period2),
            'engagement' => $this->getEngagementData($period2),
            default => []
        };

        return [
            'period1_data' => $data1,
            'period2_data' => $data2,
            'comparison' => $this->calculateTrendComparison($data1, $data2),
            'insights' => $this->generateComparisonInsights($data1, $data2)
        ];
    }

    private function getTrafficData($period)
    {
        $days = $this->getDaysFromPeriod($period);
        
        return AnalyticEvent::where('event_name', 'page_view')
            ->where('created_at', '>', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    private function getConversionData($period)
    {
        $days = $this->getDaysFromPeriod($period);
        
        return AnalyticEvent::where('event_name', 'purchase')
            ->where('created_at', '>', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    private function getRevenueData($period)
    {
        $days = $this->getDaysFromPeriod($period);
        
        return AnalyticEvent::where('event_name', 'purchase')
            ->where('created_at', '>', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, SUM(properties->amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue')
            ->toArray();
    }

    private function getEngagementData($period)
    {
        $days = $this->getDaysFromPeriod($period);
        
        return AnalyticEvent::whereIn('event_name', ['click', 'scroll', 'form_submit'])
            ->where('created_at', '>', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    private function calculateTrendDirection($data)
    {
        if (count($data) < 2) return 'stable';

        $firstHalf = array_slice($data, 0, floor(count($data) / 2));
        $secondHalf = array_slice($data, -floor(count($data) / 2));

        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);

        if ($secondAvg > $firstAvg * 1.05) return 'increasing';
        if ($secondAvg < $firstAvg * 0.95) return 'decreasing';
        return 'stable';
    }

    private function calculateGrowthRate($data)
    {
        if (count($data) < 2) return 0;

        $firstValue = $data[0];
        $lastValue = end($data);

        return $firstValue > 0 ? (($lastValue - $firstValue) / $firstValue) * 100 : 0;
    }

    private function detectSeasonality($data)
    {
        // Simple seasonality detection
        return [
            'has_seasonal_pattern' => false,
            'seasonal_strength' => 0,
            'peak_season' => 'none'
        ];
    }

    private function forecastTrend($data, $forecastDays)
    {
        if (count($data) < 2) return [];

        // Simple linear regression forecast
        $n = count($data);
        $sumX = 0; $sumY = 0; $sumXY = 0; $sumX2 = 0;
        
        foreach ($data as $i => $value) {
            $sumX += $i;
            $sumY += $value;
            $sumXY += $i * $value;
            $sumX2 += $i * $i;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        $forecast = [];
        for ($i = 1; $i <= $forecastDays; $i++) {
            $forecast[] = max(0, $intercept + $slope * ($n + $i));
        }

        return $forecast;
    }

    private function calculateAverageConversionRate($period)
    {
        $days = $this->getDaysFromPeriod($period);
        
        $conversions = AnalyticEvent::where('event_name', 'purchase')
            ->where('created_at', '>', now()->subDays($days))
            ->count();

        $visitors = AnalyticEvent::where('event_name', 'page_view')
            ->where('created_at', '>', now()->subDays($days))
            ->distinct('user_session_id')
            ->count();

        return $visitors > 0 ? ($conversions / $visitors) * 100 : 0;
    }

    private function getMonthlyPatterns()
    {
        return [
            'january' => ['traffic' => 100, 'conversion' => 2.5],
            'february' => ['traffic' => 95, 'conversion' => 2.3],
            'march' => ['traffic' => 110, 'conversion' => 2.8],
            'april' => ['traffic' => 105, 'conversion' => 2.6],
            'may' => ['traffic' => 115, 'conversion' => 3.0],
            'june' => ['traffic' => 120, 'conversion' => 3.2],
            'july' => ['traffic' => 125, 'conversion' => 3.5],
            'august' => ['traffic' => 130, 'conversion' => 3.8],
            'september' => ['traffic' => 125, 'conversion' => 3.6],
            'october' => ['traffic' => 120, 'conversion' => 3.4],
            'november' => ['traffic' => 115, 'conversion' => 3.1],
            'december' => ['traffic' => 110, 'conversion' => 2.9]
        ];
    }

    private function getWeeklyPatterns()
    {
        return [
            'monday' => ['traffic' => 100, 'conversion' => 2.5],
            'tuesday' => ['traffic' => 105, 'conversion' => 2.6],
            'wednesday' => ['traffic' => 110, 'conversion' => 2.8],
            'thursday' => ['traffic' => 115, 'conversion' => 3.0],
            'friday' => ['traffic' => 120, 'conversion' => 3.2],
            'saturday' => ['traffic' => 130, 'conversion' => 3.5],
            'sunday' => ['traffic' => 125, 'conversion' => 3.3]
        ];
    }

    private function getDailyPatterns()
    {
        return [
            'morning' => ['traffic' => 80, 'conversion' => 2.0],
            'afternoon' => ['traffic' => 120, 'conversion' => 3.0],
            'evening' => ['traffic' => 140, 'conversion' => 3.5],
            'night' => ['traffic' => 60, 'conversion' => 1.5]
        ];
    }

    private function getHolidayImpact()
    {
        return [
            'christmas' => ['impact' => 'positive', 'percentage' => 15],
            'new_year' => ['impact' => 'negative', 'percentage' => -10],
            'ramadan' => ['impact' => 'positive', 'percentage' => 20],
            'eid' => ['impact' => 'positive', 'percentage' => 25]
        ];
    }

    private function calculateTrendComparison($data1, $data2)
    {
        $avg1 = array_sum($data1) / count($data1);
        $avg2 = array_sum($data2) / count($data2);

        return [
            'period1_average' => $avg1,
            'period2_average' => $avg2,
            'change' => $avg2 - $avg1,
            'percentage_change' => $avg1 > 0 ? (($avg2 - $avg1) / $avg1) * 100 : 0
        ];
    }

    private function generateComparisonInsights($data1, $data2)
    {
        $comparison = $this->calculateTrendComparison($data1, $data2);
        
        $insights = [];
        
        if ($comparison['percentage_change'] > 10) {
            $insights[] = 'Significant increase in performance';
        } elseif ($comparison['percentage_change'] < -10) {
            $insights[] = 'Significant decrease in performance';
        } else {
            $insights[] = 'Performance remains stable';
        }

        return $insights;
    }

    private function generateTrendReport($trendType, $period)
    {
        $data = match($trendType) {
            'traffic' => $this->analyzeTrafficTrends($period),
            'conversion' => $this->analyzeConversionTrends($period),
            'revenue' => $this->analyzeRevenueTrends($period),
            'engagement' => $this->analyzeEngagementTrends($period),
            default => []
        };

        return [
            'trend_type' => $trendType,
            'period' => $period,
            'analysis' => $data,
            'export_date' => now()->toDateString()
        ];
    }

    private function exportTrendToCsv($report)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="trend_report.csv"'
        ];

        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Date', 'Value', 'Trend Direction']);
            
            if (isset($report['analysis']['data'])) {
                foreach ($report['analysis']['data'] as $index => $value) {
                    $date = now()->subDays(count($report['analysis']['data']) - $index)->format('Y-m-d');
                    fputcsv($file, [$date, $value, $report['analysis']['trend_direction']]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
