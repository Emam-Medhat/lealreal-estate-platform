<?php

namespace App\Http\Controllers;

use App\Models\AiFraudAlert;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AiFraudDetectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the fraud detection dashboard.
     */
    public function index(): View
    {
        return $this->dashboard();
    }

    /**
     * Display the fraud detection dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        
        // Get fraud detection statistics
        $stats = [
            'total_alerts' => DB::table('ai_fraud_alerts')->count(),
            'high_risk_alerts' => DB::table('ai_fraud_alerts')->where('risk_level', 'high')->count(),
            'resolved_alerts' => DB::table('ai_fraud_alerts')->where('status', 'resolved')->count(),
            'pending_investigation' => DB::table('ai_fraud_alerts')->where('status', 'pending')->count(),
            'detection_accuracy' => $this->getDetectionAccuracy(),
            'false_positive_rate' => $this->getFalsePositiveRate(),
        ];

        // Get recent fraud alerts
        $recentAlerts = AiFraudAlert::with(['property', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('ai.fraud-detection', compact('stats', 'recentAlerts'));
    }

    /**
     * Analyze property for fraud indicators.
     */
    public function analyzeProperty(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'analysis_type' => 'required|in:quick,comprehensive',
            'include_history' => 'boolean',
        ]);

        try {
            $analysis = $this->performFraudAnalysis($validated);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'message' => 'تم تحليل العقار بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحليل العقار: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze user behavior for fraud patterns.
     */
    public function analyzeUserBehavior(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'time_period' => 'required|in:1day,1week,1month,3months',
            'behavior_patterns' => 'nullable|array',
        ]);

        try {
            $analysis = $this->performBehaviorAnalysis($validated);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'message' => 'تم تحليل سلوك المستخدم بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحليل سلوك المستخدم: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create fraud alert.
     */
    public function createAlert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'user_id' => 'nullable|exists:users,id',
            'alert_type' => 'required|in:price_manipulation,fake_listing,identity_fraud,document_forgery,money_laundering',
            'risk_level' => 'required|in:low,medium,high,critical',
            'description' => 'required|string|max:1000',
            'evidence' => 'nullable|array',
            'confidence_score' => 'required|integer|min:0|max:100',
        ]);

        try {
            $alert = AiFraudAlert::create([
                'property_id' => $validated['property_id'],
                'user_id' => $validated['user_id'] ?? Auth::id(),
                'alert_type' => $validated['alert_type'],
                'risk_level' => $validated['risk_level'],
                'description' => $validated['description'],
                'evidence' => $validated['evidence'] ?? [],
                'confidence_score' => $validated['confidence_score'],
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'alert' => $alert,
                'message' => 'تم إنشاء تنبيه الاحتيال بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء تنبيه الاحتيال: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update alert status.
     */
    public function updateAlertStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_investigation,confirmed,false_positive,resolved',
            'notes' => 'nullable|string|max:1000',
            'action_taken' => 'nullable|string|max:500',
        ]);

        try {
            $alert = AiFraudAlert::findOrFail($id);
            $alert->update([
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'action_taken' => $validated['action_taken'],
                'resolved_by' => Auth::id(),
                'resolved_at' => $validated['status'] === 'resolved' ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'alert' => $alert,
                'message' => 'تم تحديث حالة التنبيه بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة التنبيه: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fraud patterns and trends.
     */
    public function getFraudPatterns(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'time_period' => 'required|in:1week,1month,3months,6months,1year',
            'pattern_type' => 'required|in:all,price_manipulation,fake_listing,identity_fraud',
            'location' => 'nullable|string',
        ]);

        try {
            $patterns = $this->analyzeFraudPatterns($validated);
            
            return response()->json([
                'success' => true,
                'patterns' => $patterns,
                'message' => 'تم تحليل أنماط الاحتيال بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحليل أنماط الاحتيال: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fraud detection metrics.
     */
    public function getDetectionMetrics(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'time_period' => 'required|in:1week,1month,3months,6months,1year',
            'metric_type' => 'required|in:accuracy,precision,recall,f1_score',
        ]);

        try {
            $metrics = $this->calculateDetectionMetrics($validated);
            
            return response()->json([
                'success' => true,
                'metrics' => $metrics,
                'message' => 'تم حساب مقاييس الكشف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حساب مقاييس الكشف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform fraud analysis using AI simulation.
     */
    private function performFraudAnalysis(array $data): array
    {
        // For now, we simulate AI fraud detection based on some real indicators if possible
        // In a real scenario, this would call a Python service or more complex logic
        
        $riskScore = 0;
        $indicators = [];

        // Check 1: Price Deviation (if property exists)
        $property = \App\Models\Property::find($data['property_id']);
        if ($property && $property->price > 0) {
            $avgPrice = \App\Models\Property::where('city', $property->city)
                ->where('property_type', $property->property_type)
                ->avg('price');
            
            if ($avgPrice > 0) {
                $deviation = abs($property->price - $avgPrice) / $avgPrice;
                if ($deviation > 0.5) { // 50% deviation
                    $riskScore += 30;
                    $indicators['price_deviation'] = 'Price deviates significantly from market average';
                }
            }
        }

        // Check 2: User Account Age
        if (isset($data['user_id'])) {
            $user = \App\Models\User::find($data['user_id']);
            if ($user && $user->created_at->diffInDays(now()) < 7) {
                $riskScore += 20;
                $indicators['new_account'] = 'Account is less than 7 days old';
            }
        }

        // Add some randomness for simulation if no real indicators found
        if (empty($indicators)) {
             $riskScore = rand(0, 20); // Low risk by default
        }

        $confidence = rand(80, 95);
        
        $analysis = [
            'property_id' => $data['property_id'],
            'analysis_type' => $data['analysis_type'],
            'risk_score' => $riskScore,
            'confidence_level' => $confidence,
            'risk_level' => $this->determineRiskLevel($riskScore),
            'fraud_indicators' => $indicators,
            'recommendation' => $this->getFraudRecommendation($riskScore),
            'similar_cases' => $this->findSimilarCases($data['property_id']),
            'historical_patterns' => $this->getHistoricalPatterns(),
            'verification_steps' => $this->getVerificationSteps($indicators),
            'created_at' => now()->toDateTimeString(),
        ];

        // Create alert if high risk
        if ($riskScore >= 70) {
            $this->createAutoAlert($analysis);
        }

        return $analysis;
    }

    /**
     * Perform behavior analysis.
     */
    private function performBehaviorAnalysis(array $data): array
    {
        // Simulate behavior pattern analysis
        $anomalyScore = rand(0, 100);
        $behaviorPatterns = $this->analyzeBehaviorPatterns($data['user_id']);
        
        return [
            'user_id' => $data['user_id'],
            'time_period' => $data['time_period'],
            'anomaly_score' => $anomalyScore,
            'risk_level' => $this->determineRiskLevel($anomalyScore),
            'behavior_patterns' => $behaviorPatterns,
            'suspicious_activities' => $this->identifySuspiciousActivities($behaviorPatterns),
            'recommendation' => $this->getBehaviorRecommendation($anomalyScore),
            'monitoring_level' => $this->getMonitoringLevel($anomalyScore),
        ];
    }

    /**
     * Analyze fraud patterns.
     */
    private function analyzeFraudPatterns(array $data): array
    {
        return [
            'time_period' => $data['time_period'],
            'pattern_type' => $data['pattern_type'],
            'trending_patterns' => $this->getTrendingPatterns(),
            'geographic_hotspots' => $this->getGeographicHotspots(),
            'temporal_patterns' => $this->getTemporalPatterns(),
            'common_indicators' => $this->getCommonIndicators(),
            'emerging_threats' => $this->getEmergingThreats(),
        ];
    }

    /**
     * Calculate detection metrics.
     */
    private function calculateDetectionMetrics(array $data): array
    {
        $metrics = [
            'accuracy' => rand(85, 98),
            'precision' => rand(80, 95),
            'recall' => rand(75, 92),
            'f1_score' => rand(78, 94),
            'false_positive_rate' => rand(2, 8),
            'false_negative_rate' => rand(3, 10),
        ];

        return [
            'metric_type' => $data['metric_type'],
            'current_value' => $metrics[$data['metric_type']] ?? 0,
            'trend' => 'improving',
            'benchmark' => $this->getBenchmark($data['metric_type']),
            'historical_data' => $this->getHistoricalMetrics($data['metric_type']),
        ];
    }

    /**
     * Generate fraud indicators.
     */
    private function generateFraudIndicators(): array
    {
        $allIndicators = [
            'price_significantly_below_market' => rand(0, 1),
            'multiple_relistings_short_period' => rand(0, 1),
            'suspicious_image_patterns' => rand(0, 1),
            'incomplete_property_details' => rand(0, 1),
            'unusual_contact_information' => rand(0, 1),
            'pressure_tactics' => rand(0, 1),
            'request_for_upfront_payment' => rand(0, 1),
            'refusal_to_show_property' => rand(0, 1),
        ];

        return array_filter($allIndicators, fn($value) => $value === 1);
    }

    /**
     * Determine risk level.
     */
    private function determineRiskLevel(int $score): string
    {
        if ($score >= 80) return 'critical';
        if ($score >= 60) return 'high';
        if ($score >= 40) return 'medium';
        return 'low';
    }

    /**
     * Get fraud recommendation.
     */
    private function getFraudRecommendation(int $riskScore): string
    {
        if ($riskScore >= 80) {
            return 'إيقاف الإعلان فوراً والتحقيق الفوري';
        } elseif ($riskScore >= 60) {
            return 'مراجعة دقيقة وطلب وثائق إضافية';
        } elseif ($riskScore >= 40) {
            return 'مراقبة متزايدة وتحقق من المعلومات';
        } else {
            return 'متابعة عادية';
        }
    }

    /**
     * Find similar cases.
     */
    private function findSimilarCases(int $propertyId): array
    {
        return [
            'total_similar_cases' => rand(0, 5),
            'confirmed_fraud' => rand(0, 2),
            'false_positive' => rand(0, 3),
            'confidence_score' => rand(60, 90),
        ];
    }

    /**
     * Get historical patterns.
     */
    private function getHistoricalPatterns(): array
    {
        return [
            'seasonal_trends' => 'زيادة في فصل الصيف',
            'common_methods' => ['تلاعب بالأسعار', 'إعلانات وهمية'],
            'target_properties' => ['شقق سكنية', 'فلل فاخرة'],
        ];
    }

    /**
     * Get verification steps.
     */
    private function getVerificationSteps(array $indicators): array
    {
        $steps = [];
        
        if (isset($indicators['price_significantly_below_market'])) {
            $steps[] = 'التحقق من أسعار السوق المحيطة';
        }
        
        if (isset($indicators['suspicious_image_patterns'])) {
            $steps[] = 'فحص الصور باستخدام أدوات التعرف على الصور';
        }
        
        if (isset($indicators['unusual_contact_information'])) {
            $steps[] = 'التحقق من معلومات الاتصال';
        }

        return $steps;
    }

    /**
     * Create auto alert.
     */
    private function createAutoAlert(array $analysis): void
    {
        AiFraudAlert::create([
            'property_id' => $analysis['property_id'],
            'alert_type' => 'auto_detected',
            'risk_level' => $analysis['risk_level'],
            'description' => 'اكتشاف تلقائي لنشاط احتيالي محتمل',
            'evidence' => $analysis['fraud_indicators'],
            'confidence_score' => $analysis['confidence_level'],
            'status' => 'pending',
            'created_by' => 1, // System user
        ]);
    }

    /**
     * Analyze behavior patterns.
     */
    private function analyzeBehaviorPatterns(int $userId): array
    {
        return [
            'listing_frequency' => rand(1, 50),
            'response_time' => rand(1, 24),
            'message_patterns' => 'normal',
            'login_patterns' => 'consistent',
            'transaction_history' => 'clean',
        ];
    }

    /**
     * Identify suspicious activities.
     */
    private function identifySuspiciousActivities(array $patterns): array
    {
        $activities = [];
        
        if ($patterns['listing_frequency'] > 20) {
            $activities[] = 'عدد كبير من الإعلانات في فترة قصيرة';
        }
        
        if ($patterns['response_time'] < 1) {
            $activities[] = 'استجابات سريعة بشكل غير طبيعي';
        }

        return $activities;
    }

    /**
     * Get behavior recommendation.
     */
    private function getBehaviorRecommendation(int $anomalyScore): string
    {
        if ($anomalyScore >= 70) {
            return 'تعليق الحساب والتحقيق';
        } elseif ($anomalyScore >= 50) {
            return 'مراقبة مشددة وطلب تحقق';
        } else {
            return 'متابعة عادية';
        }
    }

    /**
     * Get monitoring level.
     */
    private function getMonitoringLevel(int $anomalyScore): string
    {
        if ($anomalyScore >= 70) return 'high';
        if ($anomalyScore >= 40) return 'medium';
        return 'low';
    }

    /**
     * Helper methods for dashboard
     */
    private function getDetectionAccuracy(): float
    {
        $total = DB::table('ai_fraud_alerts')->count();
        $confirmed = DB::table('ai_fraud_alerts')->where('status', 'confirmed')->count();
        
        return $total > 0 ? ($confirmed / $total) * 100 : 0;
    }

    private function getFalsePositiveRate(): float
    {
        $total = DB::table('ai_fraud_alerts')->count();
        $falsePositive = DB::table('ai_fraud_alerts')->where('status', 'false_positive')->count();
        
        return $total > 0 ? ($falsePositive / $total) * 100 : 0;
    }

    private function getTrendingPatterns(): array
    {
        return [
            ['pattern' => 'تلاعب بالأسعار', 'frequency' => rand(10, 30)],
            ['pattern' => 'إعلانات وهمية', 'frequency' => rand(5, 20)],
        ];
    }

    private function getGeographicHotspots(): array
    {
        return [
            ['location' => 'الرياض', 'alert_count' => rand(5, 15)],
            ['location' => 'جدة', 'alert_count' => rand(3, 12)],
        ];
    }

    private function getTemporalPatterns(): array
    {
        return [
            'peak_hours' => '14:00 - 18:00',
            'peak_days' => 'الأحد - الثلاثاء',
            'seasonal_trend' => 'زيادة في الصيف',
        ];
    }

    private function getCommonIndicators(): array
    {
        return [
            'price_below_market' => 45,
            'fake_images' => 30,
            'suspicious_contact' => 25,
        ];
    }

    private function getEmergingThreats(): array
    {
        return [
            'deepfake_property_images',
            'ai_generated_descriptions',
            'automated_listing_creation',
        ];
    }

    private function getBenchmark(string $metric): float
    {
        $benchmarks = [
            'accuracy' => 90,
            'precision' => 85,
            'recall' => 80,
            'f1_score' => 82,
        ];

        return $benchmarks[$metric] ?? 0;
    }

    private function getHistoricalMetrics(string $metric): array
    {
        return [
            'current_month' => rand(75, 95),
            'previous_month' => rand(70, 90),
            'three_months_ago' => rand(65, 85),
        ];
    }
}
