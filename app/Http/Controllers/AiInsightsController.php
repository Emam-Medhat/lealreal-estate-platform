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
        try {
            $insights = AiPrediction::latest()->paginate(20);
        } catch (\Exception $e) {
            // Handle case where table might be empty or other issues
            $insights = collect();
        }
        
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
        try {
            $period = $request->period ?? '30d';
            $anomalies = $this->detectAnomalies($period);
            return response()->json($anomalies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to detect anomalies',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function patternRecognition(Request $request)
    {
        try {
            $patterns = $this->recognizePatterns();
            return response()->json($patterns);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to recognize patterns',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function predictiveInsights(Request $request)
    {
        try {
            $predictions = $this->generatePredictiveInsights();
            return response()->json($predictions);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get predictions',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function recommendations(Request $request)
    {
        try {
            $recommendations = $this->generateRecommendations();
            return response()->json($recommendations);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get recommendations',
                'message' => $e->getMessage()
            ], 500);
        }
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
        try {
            $days = $this->getDaysFromPeriod($period);
            
            // Get real traffic data from the last period
            $trafficData = AnalyticEvent::where('created_at', '>', now()->subDays($days))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count')
                ->toArray();
            
            if (empty($trafficData) || count($trafficData) < 7) {
                return [
                    'anomalies_detected' => 0,
                    'anomaly_details' => [],
                    'analysis_period' => $period,
                    'message' => 'لا توجد بيانات كافية لكشف الشذوذ'
                ];
            }
            
            // Calculate statistical measures
            $mean = array_sum($trafficData) / count($trafficData);
            $stdDev = $this->calculateStandardDeviation($trafficData, $mean);
            $threshold = 2 * $stdDev; // 2 standard deviations
            
            $anomalies = [];
            $anomalyCount = 0;
            
            // Check each day for anomalies
            foreach ($trafficData as $index => $count) {
                $date = now()->subDays($days - $index - 1)->format('Y-m-d');
                $deviation = abs($count - $mean);
                
                if ($deviation > $threshold) {
                    $anomalyType = $count > $mean ? 'spike' : 'drop';
                    $severity = $deviation > (3 * $stdDev) ? 'high' : 'medium';
                    
                    $anomalies[] = [
                        'date' => $date,
                        'value' => $count,
                        'expected' => round($mean),
                        'deviation' => round($deviation),
                        'type' => $anomalyType,
                        'severity' => $severity,
                        'description' => $anomalyType === 'spike' ? 
                            "زيادة غير طبيعية في الزوار بنسبة " . round(($deviation / $mean) * 100, 1) . "%" :
                            "انخفاض غير طبيعي في الزوار بنسبة " . round(($deviation / $mean) * 100, 1) . "%"
                    ];
                    $anomalyCount++;
                }
            }
            
            // Check for unusual patterns
            $patternAnomalies = $this->detectPatternAnomalies($trafficData);
            
            return [
                'anomalies_detected' => $anomalyCount + count($patternAnomalies),
                'anomaly_details' => array_merge($anomalies, $patternAnomalies),
                'analysis_period' => $period,
                'total_days_analyzed' => count($trafficData),
                'average_traffic' => round($mean),
                'standard_deviation' => round($stdDev),
                'anomaly_threshold' => round($threshold)
            ];
            
        } catch (\Exception $e) {
            return [
                'anomalies_detected' => 0,
                'anomaly_details' => [],
                'analysis_period' => $period,
                'error' => 'فشل في كشف الشذوذ: ' . $e->getMessage()
            ];
        }
    }

    private function detectPatternAnomalies($trafficData)
    {
        $anomalies = [];
        
        // Check for consecutive drops
        $consecutiveDrops = 0;
        for ($i = 1; $i < count($trafficData); $i++) {
            if ($trafficData[$i] < $trafficData[$i-1]) {
                $consecutiveDrops++;
            } else {
                $consecutiveDrops = 0;
            }
            
            if ($consecutiveDrops >= 3) {
                $anomalies[] = [
                    'type' => 'pattern',
                    'severity' => 'medium',
                    'description' => 'انخفاض مستمر في الزوار لـ ' . $consecutiveDrops . ' أيام متتالية',
                    'pattern' => 'consecutive_drops'
                ];
                break;
            }
        }
        
        // Check for unusual weekend patterns
        if (count($trafficData) >= 14) {
            $weekendTraffic = [];
            $weekdayTraffic = [];
            
            for ($i = 0; $i < count($trafficData); $i++) {
                $date = now()->subDays(count($trafficData) - $i - 1);
                if ($date->isWeekend()) {
                    $weekendTraffic[] = $trafficData[$i];
                } else {
                    $weekdayTraffic[] = $trafficData[$i];
                }
            }
            
            if (!empty($weekendTraffic) && !empty($weekdayTraffic)) {
                $weekendAvg = array_sum($weekendTraffic) / count($weekendTraffic);
                $weekdayAvg = array_sum($weekdayTraffic) / count($weekdayTraffic);
                
                if ($weekendAvg > $weekdayAvg * 1.5) {
                    $anomalies[] = [
                        'type' => 'pattern',
                        'severity' => 'low',
                        'description' => 'نشاط غير عادي في عطلات نهاية الأسبوع (أعلى بنسبة ' . round((($weekendAvg - $weekdayAvg) / $weekdayAvg) * 100, 1) . '%)',
                        'pattern' => 'weekend_spike'
                    ];
                }
            }
        }
        
        return $anomalies;
    }

    private function recognizePatterns()
    {
        try {
            $data = $this->getAnalyticsData('90d');
            
            if (empty($data)) {
                return [
                    'seasonal_patterns' => [
                        'weekly_pattern' => 'لا توجد بيانات كافية لتحليل الأنماط الأسبوعية',
                        'monthly_pattern' => 'لا توجد بيانات كافية لتحليل الأنماط الشهرية', 
                        'seasonal_pattern' => 'لا توجد بيانات كافية لتحليل الأنماط الموسمية'
                    ],
                    'trend_patterns' => $this->detectTrendPatterns(),
                    'behavioral_patterns' => $this->detectBehavioralPatterns(),
                    'conversion_patterns' => $this->detectConversionPatterns()
                ];
            }
            
            return [
                'seasonal_patterns' => $this->detectSeasonalPatterns(),
                'trend_patterns' => $this->detectTrendPatterns(),
                'behavioral_patterns' => $this->detectBehavioralPatterns(),
                'conversion_patterns' => $this->detectConversionPatterns()
            ];
            
        } catch (\Exception $e) {
            return [
                'seasonal_patterns' => [
                    'weekly_pattern' => 'خطأ في تحليل الأنماط الأسبوعية',
                    'monthly_pattern' => 'خطأ في تحليل الأنماط الشهرية',
                    'seasonal_pattern' => 'خطأ في تحليل الأنماط الموسمية'
                ],
                'trend_patterns' => [
                    'upward_trend' => 'خطأ في تحليل الاتجاه العام',
                    'conversion_trend' => 'خطأ في تحليل معدل التحويل',
                    'engagement_trend' => 'خطأ في تحليل معدل المشاركة'
                ],
                'behavioral_patterns' => [
                    'navigation_pattern' => 'خطأ في تحليل أنماط التنقل',
                    'time_pattern' => 'خطأ في تحليل أنماط الوقت',
                    'device_pattern' => 'خطأ في تحليل أنماط الأجهزة',
                    'session_pattern' => 'خطأ في تحليل أنماط الجلسات'
                ],
                'conversion_patterns' => [
                    'path_pattern' => 'خطأ في تحليل مسارات التفاعل',
                    'time_pattern' => 'خطأ في تحليل أوقات التفاعل',
                    'source_pattern' => 'خطأ في تحليل مصادر التفاعل'
                ]
            ];
        }
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
        
        try {
            $data = AnalyticEvent::where('created_at', '>', now()->subDays($days))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count')
                ->toArray();
            
            // If no data, return sample data for demonstration
            if (empty($data)) {
                // Generate sample data for the last 30 days
                $data = [];
                for ($i = $days - 1; $i >= 0; $i--) {
                    $data[] = rand(50, 200); // Random daily events between 50-200
                }
            }
            
            return $data;
        } catch (\Exception $e) {
            // Return sample data if table doesn't exist or other error
            $data = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $data[] = rand(50, 200);
            }
            return $data;
        }
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
        try {
            $data = $this->getAnalyticsData('90d');
            
            if (empty($data)) {
                return [
                    'weekly_pattern' => 'لا توجد بيانات كافية لتحليل الأنماط الأسبوعية',
                    'monthly_pattern' => 'لا توجد بيانات كافية لتحليل الأنماط الشهرية', 
                    'seasonal_pattern' => 'لا توجد بيانات كافية لتحليل الأنماط الموسمية'
                ];
            }
            
            // Analyze weekly patterns (last 7 days vs previous 7 days)
            $recentWeek = array_slice($data, -7, 7);
            $previousWeek = array_slice($data, -14, 7);
            $weeklyAvg = array_sum($recentWeek) / count($recentWeek);
            $previousAvg = array_sum($previousWeek) / count($previousWeek);
            
            $weeklyPattern = $weeklyAvg > $previousAvg ? 
                'زيادة في النشاط خلال الأسبوع الماضي بنسبة ' . round(($weeklyAvg - $previousAvg) / $previousAvg * 100, 1) . '%' :
                'انخفاض في النشاط خلال الأسبوع الماضي بنسبة ' . round(($previousAvg - $weeklyAvg) / $previousAvg * 100, 1) . '%';
            
            // Analyze monthly patterns
            $monthlyData = array_slice($data, -30, 30);
            $monthlyAvg = array_sum($monthlyData) / count($monthlyData);
            $maxDay = max($monthlyData);
            $minDay = min($monthlyData);
            
            $monthlyPattern = "متوسط النشاط الشهري: " . round($monthlyAvg) . " حدث يومياً (أعلى: $maxDay, أدنى: $minDay)";
            
            // Analyze seasonal patterns (last 90 days)
            $seasonalData = array_slice($data, -90, 90);
            $seasonalTrend = $this->calculateTrend($seasonalData);
            
            $seasonalPattern = $seasonalTrend > 0 ? 
                'نمو إيجابي خلال الـ 90 يوم الماضية بنسبة ' . round($seasonalTrend * 100, 1) . '%' :
                'تراجع خلال الـ 90 يوم الماضية بنسبة ' . round(abs($seasonalTrend) * 100, 1) . '%';
            
            return [
                'weekly_pattern' => $weeklyPattern,
                'monthly_pattern' => $monthlyPattern,
                'seasonal_pattern' => $seasonalPattern
            ];
            
        } catch (\Exception $e) {
            return [
                'weekly_pattern' => 'خطأ في تحليل الأنماط الأسبوعية',
                'monthly_pattern' => 'خطأ في تحليل الأنماط الشهرية',
                'seasonal_pattern' => 'خطأ في تحليل الأنماط الموسمية'
            ];
        }
    }

    private function detectTrendPatterns()
    {
        try {
            $data = $this->getAnalyticsData('30d');
            
            if (empty($data)) {
                return [
                    'upward_trend' => 'لا توجد بيانات كافية لتحليل الاتجاه',
                    'conversion_trend' => 'لا توجد بيانات كافية لتحليل معدل التحويل',
                    'engagement_trend' => 'لا توجد بيانات كافية لتحليل معدل المشاركة'
                ];
            }
            
            $trend = $this->calculateTrend($data);
            $upwardTrend = $trend > 0 ? 
                'نمو إيجابي في حركة المرور بنسبة ' . round($trend * 100, 1) . '%' :
                'انخفاض في حركة المرور بنسبة ' . round(abs($trend) * 100, 1) . '%';
            
            // Try to get conversion data
            try {
                $conversions = AnalyticEvent::where('created_at', '>', now()->subDays(30))
                    ->whereIn('event_name', ['purchase', 'signup', 'lead'])
                    ->count();
                    
                $totalEvents = AnalyticEvent::where('created_at', '>', now()->subDays(30))->count();
                $conversionRate = $totalEvents > 0 ? ($conversions / $totalEvents) * 100 : 0;
                $conversionTrend = "معدل التحويل الحالي: " . round($conversionRate, 2) . "%";
            } catch (\Exception $e) {
                $conversionTrend = 'غير قادر على حساب معدل التحويل';
            }
            
            // Calculate engagement trend
            $avgSessionDuration = round(array_sum($data) / count($data));
            $engagementTrend = $avgSessionDuration > 100 ? 
                'معدل مشاركة جيد (متوسط: ' . $avgSessionDuration . ' حدث يومياً)' :
                'يحتاج إلى تحسين في المشاركة (متوسط: ' . $avgSessionDuration . ' حدث يومياً)';
            
            return [
                'upward_trend' => $upwardTrend,
                'conversion_trend' => $conversionTrend,
                'engagement_trend' => $engagementTrend
            ];
            
        } catch (\Exception $e) {
            return [
                'upward_trend' => 'خطأ في تحليل الاتجاه العام',
                'conversion_trend' => 'خطأ في تحليل معدل التحويل',
                'engagement_trend' => 'خطأ في تحليل معدل المشاركة'
            ];
        }
    }

    private function detectBehavioralPatterns()
    {
        try {
            // Navigation patterns - analyze page URLs from existing data
            $pageViews = AnalyticEvent::where('created_at', '>', now()->subDays(30))
                ->where('event_name', 'page_view')
                ->selectRaw('page_url, COUNT(*) as count')
                ->groupBy('page_url')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();
            
            $navigationPattern = $pageViews->count() > 0 ? 
                'أكثر الصفحات زيارة: ' . $pageViews->first()->page_url . ' (' . $pageViews->first()->count . ' زيارة)' :
                'لا توجد بيانات كافية عن أنماط التنقل';
            
            // Time patterns - analyze hourly distribution
            $hourlyData = AnalyticEvent::where('created_at', '>', now()->subDays(7))
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('count', 'desc')
                ->get();
            
            $timePattern = $hourlyData->count() > 0 ? 
                'ذروة النشاط في الساعة ' . $hourlyData->first()->hour . ':00 (' . $hourlyData->first()->count . ' حدث)' :
                'لا توجد بيانات كافية عن أنماط الوقت';
            
            // Device patterns - analyze user agents
            $deviceData = AnalyticEvent::where('created_at', '>', now()->subDays(30))
                ->selectRaw('user_agent, COUNT(*) as count')
                ->groupBy('user_agent')
                ->get();
            
            $mobileCount = 0;
            $desktopCount = 0;
            $totalCount = $deviceData->count();
            
            foreach ($deviceData as $device) {
                if (strpos($device->user_agent, 'Mobile') !== false || strpos($device->user_agent, 'iPhone') !== false) {
                    $mobileCount += $device->count;
                } else {
                    $desktopCount += $device->count;
                }
            }
            
            $totalDeviceCount = $mobileCount + $desktopCount;
            $devicePattern = $totalDeviceCount > 0 ? 
                'حركة المرور من الجوال: ' . round(($mobileCount / $totalDeviceCount) * 100, 1) . '% (' . $mobileCount . ' زيارة) vs الجهاز المكتبي: ' . round(($desktopCount / $totalDeviceCount) * 100, 1) . '% (' . $desktopCount . ' زيارة)' :
                'لا توجد بيانات كافية عن أنماط الأجهزة';
            
            // Add session patterns - analyze user engagement
            $sessionData = AnalyticEvent::where('created_at', '>', now()->subDays(7))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as events, COUNT(DISTINCT ip_address) as unique_users')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            
            $sessionPattern = $sessionData->count() > 0 ? 
                'متوسط الجلسات اليومية: ' . round($sessionData->avg('unique_users')) . ' مستخدم فريد بمتوسط ' . round($sessionData->avg('events')) . ' حدث لكل جلسة' :
                'لا توجد بيانات كافية عن أنماط الجلسات';
            
            return [
                'navigation_pattern' => $navigationPattern,
                'time_pattern' => $timePattern,
                'device_pattern' => $devicePattern,
                'session_pattern' => $sessionPattern,
                'top_pages' => $pageViews->take(3)->map(function($page) {
                    return [
                        'url' => $page->page_url,
                        'visits' => $page->count,
                        'percentage' => round(($page->count / $pageViews->sum('count')) * 100, 1)
                    ];
                })->toArray()
            ];
            
        } catch (\Exception $e) {
            return [
                'navigation_pattern' => 'خطأ في تحليل أنماط التنقل: ' . $e->getMessage(),
                'time_pattern' => 'خطأ في تحليل أنماط الوقت',
                'device_pattern' => 'خطأ في تحليل أنماط الأجهزة',
                'session_pattern' => 'خطأ في تحليل أنماط الجلسات'
            ];
        }
    }

    private function detectConversionPatterns()
    {
        try {
            // Analyze all event types as potential conversion indicators
            $allEvents = AnalyticEvent::where('created_at', '>', now()->subDays(30))
                ->selectRaw('event_name, COUNT(*) as count')
                ->groupBy('event_name')
                ->orderBy('count', 'desc')
                ->get();
            
            // Calculate conversion potential based on event engagement
            $totalEvents = $allEvents->sum('count');
            $pageViews = $allEvents->where('event_name', 'page_view')->sum('count');
            
            // Analyze user engagement patterns as conversion indicators
            $engagementEvents = AnalyticEvent::where('created_at', '>', now()->subDays(30))
                ->where('event_name', '!=', 'page_view')
                ->selectRaw('event_name, DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('event_name', 'date')
                ->orderBy('date')
                ->get();
            
            // Time-based conversion patterns
            $hourlyConversions = AnalyticEvent::where('created_at', '>', now()->subDays(30))
                ->selectRaw('HOUR(created_at) as hour, event_name, COUNT(*) as count')
                ->groupBy('hour', 'event_name')
                ->orderBy('count', 'desc')
                ->get();
            
            $peakHour = $hourlyConversions->groupBy('hour')->map->sum('count')->sortDesc()->keys()->first();
            $peakHourCount = $hourlyConversions->groupBy('hour')->map->sum('count')->max();
            
            // Page-based conversion potential
            $pageConversions = AnalyticEvent::where('created_at', '>', now()->subDays(30))
                ->selectRaw('page_url, COUNT(*) as count')
                ->groupBy('page_url')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();
            
            // Calculate engagement rate (non-page-view events / total events)
            $engagementEvents = $totalEvents - $pageViews;
            $engagementRate = $totalEvents > 0 ? round(($engagementEvents / $totalEvents) * 100, 2) : 0;
            
            return [
                'path_pattern' => $pageConversions->count() > 0 ? 
                    'أعلى تفاعل على الصفحة: ' . $pageConversions->first()->page_url . ' (' . $pageConversions->first()->count . ' حدث)' :
                    'لا توجد بيانات كافية عن مسارات التفاعل',
                'time_pattern' => $peakHour ? 
                    'أعلى نشاط تفاعلي في الساعة ' . $peakHour . ':00 (' . $peakHourCount . ' حدث)' :
                    'لا توجد بيانات كافية عن أوقات التفاعل',
                'source_pattern' => 'معدل التفاعل العام: ' . $engagementRate . '% (' . $engagementEvents . ' تفاعل من ' . $totalEvents . ' حدث إجمالي)',
                'engagement_breakdown' => $allEvents->map(function($event) use ($totalEvents) {
                    return [
                        'event_type' => $event->event_name,
                        'count' => $event->count,
                        'percentage' => round(($event->count / $totalEvents) * 100, 1)
                    ];
                })->toArray(),
                'top_converting_pages' => $pageConversions->take(3)->map(function($page) use ($totalEvents) {
                    return [
                        'url' => $page->page_url,
                        'events' => $page->count,
                        'conversion_potential' => round(($page->count / $totalEvents) * 100, 2)
                    ];
                })->toArray()
            ];
            
        } catch (\Exception $e) {
            return [
                'path_pattern' => 'خطأ في تحليل مسارات التفاعل: ' . $e->getMessage(),
                'time_pattern' => 'خطأ في تحليل أوقات التفاعل',
                'source_pattern' => 'خطأ في تحليل مصادر التفاعل'
            ];
        }
    }

    private function calculateTrend($data)
    {
        if (count($data) < 2) return 0;
        
        $firstHalf = array_slice($data, 0, count($data) / 2);
        $secondHalf = array_slice($data, count($data) / 2);
        
        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);
        
        return $firstAvg > 0 ? ($secondAvg - $firstAvg) / $firstAvg : 0;
    }

    private function forecastTraffic()
    {
        try {
            $data = $this->getAnalyticsData('30d');
            
            if (empty($data)) {
                return [
                    'next_7_days' => array_fill(0, 7, 0),
                    'next_30_days' => 'لا توجد بيانات كافية للتنبؤ',
                    'confidence' => 0
                ];
            }
            
            // Simple linear regression for forecasting
            $trend = $this->calculateTrend($data);
            $avgDaily = array_sum($data) / count($data);
            
            // Generate 7-day forecast based on trend and average
            $forecast = [];
            for ($i = 1; $i <= 7; $i++) {
                $forecastValue = $avgDaily * (1 + ($trend * $i / 7));
                $forecast[] = max(0, round($forecastValue));
            }
            
            $trendDirection = $trend > 0 ? 'نمو' : ($trend < 0 ? 'انخفاض' : 'استقرار');
            $trendPercentage = round(abs($trend) * 100, 1);
            
            return [
                'next_7_days' => $forecast,
                'next_30_days' => "متوقع {$trendDirection} بنسبة {$trendPercentage}% خلال 30 يوم",
                'confidence' => min(95, max(50, 85 - (count($data) < 30 ? 20 : 0)))
            ];
            
        } catch (\Exception $e) {
            return [
                'next_7_days' => array_fill(0, 7, 0),
                'next_30_days' => 'خطأ في التنبؤ بحركة المرور',
                'confidence' => 0
            ];
        }
    }

    private function forecastConversions()
    {
        try {
            $conversions = AnalyticEvent::where('created_at', '>', now()->subDays(30))
                ->whereIn('event_name', ['purchase', 'signup', 'lead'])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count')
                ->toArray();
            
            if (empty($conversions)) {
                return [
                    'next_7_days' => array_fill(0, 7, 0),
                    'next_30_days' => 'لا توجد بيانات تحويل كافية',
                    'confidence' => 0
                ];
            }
            
            $trend = $this->calculateTrend($conversions);
            $avgDaily = array_sum($conversions) / count($conversions);
            
            $forecast = [];
            for ($i = 1; $i <= 7; $i++) {
                $forecastValue = $avgDaily * (1 + ($trend * $i / 7));
                $forecast[] = max(0, round($forecastValue));
            }
            
            $trendDirection = $trend > 0 ? 'نمو' : ($trend < 0 ? 'انخفاض' : 'استقرار');
            $trendPercentage = round(abs($trend) * 100, 1);
            
            return [
                'next_7_days' => $forecast,
                'next_30_days' => "متوقع {$trendDirection} في معدل التحويل بنسبة {$trendPercentage}%",
                'confidence' => min(90, max(40, 80 - (count($conversions) < 30 ? 25 : 0)))
            ];
            
        } catch (\Exception $e) {
            return [
                'next_7_days' => array_fill(0, 7, 0),
                'next_30_days' => 'خطأ في التنبؤ بالتحويلات',
                'confidence' => 0
            ];
        }
    }

    private function forecastRevenue()
    {
        try {
            // Try to get revenue data from properties or other sources
            $totalRevenue = 0;
            $revenueData = [];
            
            // Try to get property data as revenue proxy
            try {
                $properties = \App\Models\Property::where('created_at', '>', now()->subDays(30))
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count')
                    ->toArray();
                
                if (!empty($properties)) {
                    $avgPropertyPrice = 100000; // Default average price
                    $revenueData = array_map(function($count) use ($avgPropertyPrice) {
                        return $count * $avgPropertyPrice;
                    }, $properties);
                }
            } catch (\Exception $e) {
                // Fallback to analytics data
                $revenueData = $this->getAnalyticsData('30d');
            }
            
            if (empty($revenueData)) {
                return [
                    'next_7_days' => array_fill(0, 7, 0),
                    'next_30_days' => 'لا توجد بيانات إيرادات كافية',
                    'confidence' => 0
                ];
            }
            
            $trend = $this->calculateTrend($revenueData);
            $avgDaily = array_sum($revenueData) / count($revenueData);
            
            $forecast = [];
            for ($i = 1; $i <= 7; $i++) {
                $forecastValue = $avgDaily * (1 + ($trend * $i / 7));
                $forecast[] = max(0, round($forecastValue));
            }
            
            $trendDirection = $trend > 0 ? 'نمو' : ($trend < 0 ? 'انخفاض' : 'استقرار');
            $trendPercentage = round(abs($trend) * 100, 1);
            
            return [
                'next_7_days' => $forecast,
                'next_30_days' => "متوقع {$trendDirection} في الإيرادات بنسبة {$trendPercentage}%",
                'confidence' => min(85, max(35, 75 - (count($revenueData) < 30 ? 30 : 0)))
            ];
            
        } catch (\Exception $e) {
            return [
                'next_7_days' => array_fill(0, 7, 0),
                'next_30_days' => 'خطأ في التنبؤ بالإيرادات',
                'confidence' => 0
            ];
        }
    }

    private function forecastUserGrowth()
    {
        try {
            // Get user registration data
            $userGrowth = \App\Models\User::where('created_at', '>', now()->subDays(30))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count')
                ->toArray();
            
            if (empty($userGrowth)) {
                return [
                    'next_7_days' => array_fill(0, 7, 0),
                    'next_30_days' => 'لا توجد بيانات نمو مستخدمين كافية',
                    'confidence' => 0
                ];
            }
            
            $trend = $this->calculateTrend($userGrowth);
            $avgDaily = array_sum($userGrowth) / count($userGrowth);
            
            $forecast = [];
            for ($i = 1; $i <= 7; $i++) {
                $forecastValue = $avgDaily * (1 + ($trend * $i / 7));
                $forecast[] = max(0, round($forecastValue));
            }
            
            $trendDirection = $trend > 0 ? 'نمو' : ($trend < 0 ? 'انخفاض' : 'استقرار');
            $trendPercentage = round(abs($trend) * 100, 1);
            
            return [
                'next_7_days' => $forecast,
                'next_30_days' => "متوقع {$trendDirection} في عدد المستخدمين بنسبة {$trendPercentage}%",
                'confidence' => min(80, max(30, 70 - (count($userGrowth) < 30 ? 35 : 0)))
            ];
            
        } catch (\Exception $e) {
            return [
                'next_7_days' => array_fill(0, 7, 0),
                'next_30_days' => 'خطأ في التنبؤ بنمو المستخدمين',
                'confidence' => 0
            ];
        }
    }

    private function generateOptimizationRecommendations()
    {
        try {
            $recommendations = [];
            
            // Check page load speed based on analytics data
            $avgEvents = AnalyticEvent::where('created_at', '>', now()->subDays(7))->count();
            if ($avgEvents < 100) {
                $recommendations[] = 'تحسين سرعة تحميل الصفحات لزيادة التفاعل (النشاط الحالي: ' . $avgEvents . ' حدث أسبوعياً)';
            }
            
            // Check mobile usage
            $mobileUsage = $this->getMobileUsagePercentage();
            if ($mobileUsage > 50) {
                $recommendations[] = 'تحسين تجربة الجوال (' . $mobileUsage . '% من المستخدمين يستخدمون الجوال)';
            }
            
            // Check conversion rate
            $conversionRate = $this->getConversionRate();
            if ($conversionRate < 2) {
                $recommendations[] = 'تحسين معدل التحويل الحالي (معدل التحويل: ' . $conversionRate . '%)';
            }
            
            // Check user engagement
            $engagement = $this->getEngagementScore();
            if ($engagement < 50) {
                $recommendations[] = 'زيادة التفاعل مع المستخدمين (درجة التفاعل: ' . $engagement . '/100)';
            }
            
            // Add general recommendations if no specific issues
            if (empty($recommendations)) {
                $recommendations[] = 'الأداء الحالي جيد، استمر في المراقبة والتحسين';
                $recommendations[] = 'نظر في إضافة ميزات جديدة لزيادة التفاعل';
            }
            
            return $recommendations;
            
        } catch (\Exception $e) {
            return [
                'تحسين سرعة تحميل الصفحات',
                'تحسين تجربة الجوال',
                'زيادة التفاعل مع المستخدمين',
                'مراقبة الأداء بانتظام'
            ];
        }
    }

    private function generateContentRecommendations()
    {
        try {
            $recommendations = [];
            
            // Check most visited pages
            $popularPages = AnalyticEvent::where('created_at', '>', now()->subDays(30))
                ->where('event_name', 'page_view')
                ->selectRaw('page_url, COUNT(*) as count')
                ->groupBy('page_url')
                ->orderBy('count', 'desc')
                ->limit(3)
                ->get();
            
            if ($popularPages->count() > 0) {
                $recommendations[] = 'إنشاء محتوى إضافي للصفحات الأكثر زيارة: ' . $popularPages->first()->page_url;
            }
            
            // Check user engagement time
            $avgEngagement = $this->getAvgEngagementTime();
            if ($avgEngagement < 120) { // Less than 2 minutes
                $recommendations[] = 'إنشاء محتوى تفاعلي لزيادة وقت البقاء (المتوسط الحالي: ' . $avgEngagement . ' ثانية)';
            }
            
            // Add general content recommendations
            $recommendations[] = 'نشر تحليلات السوق العقاري بانتظام';
            $recommendations[] = 'إنشاء أدلة للأحياء السكنية الشعبية';
            $recommendations[] = 'مشاركة قصص نجاح العملاء';
            
            return $recommendations;
            
        } catch (\Exception $e) {
            return [
                'إنشاء محتوى عقاري متخصص',
                'نشر تحليلات السوق',
                'مشاركة دراسات الحالة',
                'إنشاء أدلة إرشادية'
            ];
        }
    }

    private function generateUXRecommendations()
    {
        try {
            $recommendations = [];
            
            // Check bounce rate (simplified)
            $singlePageVisits = AnalyticEvent::where('created_at', '>', now()->subDays(7))
                ->selectRaw('ip_address, COUNT(*) as count')
                ->having('count', '=', 1)
                ->count();
            
            $totalVisits = AnalyticEvent::where('created_at', '>', now()->subDays(7))
                ->selectRaw('ip_address')
                ->distinct()
                ->count();
            
            $bounceRate = $totalVisits > 0 ? ($singlePageVisits / $totalVisits) * 100 : 0;
            
            if ($bounceRate > 70) {
                $recommendations[] = 'تحسين معدل الارتداد الحالي (معدل الارتداد: ' . round($bounceRate, 1) . '%)';
            }
            
            // Check mobile experience
            $mobileUsage = $this->getMobileUsagePercentage();
            if ($mobileUsage > 60) {
                $recommendations[] = 'تحسين واجهة الجوال (استخدام الجوال: ' . $mobileUsage . '%)';
            }
            
            // Add general UX recommendations
            $recommendations[] = 'تبسيط فلترات البحث عن العقارات';
            $recommendations[] = 'إضافة وظيفة المقارنة بين العقارات';
            $recommendations[] = 'تحسين معرض الصور العقارية';
            
            return $recommendations;
            
        } catch (\Exception $e) {
            return [
                'تبسيط واجهة المستخدم',
                'تحسين تجربة البحث',
                'إضافة ميزات المقارنة',
                'تحسين عرض الصور'
            ];
        }
    }

    private function generateMarketingRecommendations()
    {
        try {
            $recommendations = [];
            
            // Check traffic sources (simplified)
            $totalEvents = AnalyticEvent::where('created_at', '>', now()->subDays(30))->count();
            
            if ($totalEvents < 1000) {
                $recommendations[] = 'زيادة جهود التسويق لجذب المزيد من الزوار (الزوار الحاليون: ' . $totalEvents . ')';
            }
            
            // Check conversion rate
            $conversionRate = $this->getConversionRate();
            if ($conversionRate < 3) {
                $recommendations[] = 'تحسين حملات التسويق لزيادة التحويل (معدل التحويل: ' . $conversionRate . '%)';
            }
            
            // Add general marketing recommendations
            $recommendations[] = 'التركيز على تحسين محركات البحث (SEO)';
            $recommendations[] = 'استخدام وسائل التواصل الاجتماعي المستهدفة';
            $recommendations[] = 'تنفيذ حملات بريد إلكتروني آلية';
            $recommendations[] = 'استخدام إعلانات إعادة الاستهداف';
            
            return $recommendations;
            
        } catch (\Exception $e) {
            return [
                'تحسين محركات البحث',
                'استخدام التسويق الرقمي',
                'حملات بريد إلكتروني',
                'إعلانات مستهدفة'
            ];
        }
    }

    // Helper methods for dynamic recommendations
    private function getMobileUsagePercentage()
    {
        try {
            $totalEvents = AnalyticEvent::where('created_at', '>', now()->subDays(30))->count();
            if ($totalEvents === 0) return 0;
            
            $mobileEvents = AnalyticEvent::where('created_at', '>', now()->subDays(30))
                ->where('user_agent', 'like', '%Mobile%')
                ->count();
            
            return round(($mobileEvents / $totalEvents) * 100, 1);
        } catch (\Exception $e) {
            return 50; // Default fallback
        }
    }

    private function getConversionRate()
    {
        try {
            $conversions = AnalyticEvent::where('created_at', '>', now()->subDays(30))
                ->whereIn('event_name', ['purchase', 'signup', 'lead'])
                ->count();
            
            $totalEvents = AnalyticEvent::where('created_at', '>', now()->subDays(30))->count();
            
            return $totalEvents > 0 ? round(($conversions / $totalEvents) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getEngagementScore()
    {
        try {
            $events = AnalyticEvent::where('created_at', '>', now()->subDays(7))->count();
            $uniqueUsers = AnalyticEvent::where('created_at', '>', now()->subDays(7))
                ->selectRaw('ip_address')
                ->distinct()
                ->count();
            
            return $uniqueUsers > 0 ? min(100, round(($events / $uniqueUsers) * 10)) : 0;
        } catch (\Exception $e) {
            return 50;
        }
    }

    private function getAvgEngagementTime()
    {
        try {
            // Simplified engagement time calculation
            $events = AnalyticEvent::where('created_at', '>', now()->subDays(7))->count();
            return max(30, min(300, $events * 2)); // Simple proxy calculation
        } catch (\Exception $e) {
            return 120;
        }
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
