<?php

namespace App\Http\Controllers;

use App\Models\AiLeadScore;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AiLeadScoringController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_scores' => AiLeadScore::count(),
            'high_quality_leads' => AiLeadScore::where('score', '>=', 80)->count(),
            'medium_quality_leads' => AiLeadScore::whereBetween('score', [50, 79])->count(),
            'low_quality_leads' => AiLeadScore::where('score', '<', 50)->count(),
            'average_score' => $this->getAverageScore(),
            'scoring_accuracy' => $this->getScoringAccuracy(),
        ];

        $recentScores = AiLeadScore::with(['lead', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $scoringTrends = $this->getScoringTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('ai.lead-scoring.dashboard', compact(
            'stats', 
            'recentScores', 
            'scoringTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = AiLeadScore::with(['lead', 'user']);

        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->lead_id);
        }

        if ($request->filled('score_min')) {
            $query->where('score', '>=', $request->score_min);
        }

        if ($request->filled('score_max')) {
            $query->where('score', '<=', $request->score_max);
        }

        if ($request->filled('quality_level')) {
            $query->where('quality_level', $request->quality_level);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $scores = $query->latest()->paginate(20);

        $leads = Lead::all();
        $qualityLevels = ['high', 'medium', 'low'];

        return view('ai.lead-scoring.index', compact('scores', 'leads', 'qualityLevels'));
    }

    public function create()
    {
        $leads = Lead::all();
        $scoringModels = $this->getScoringModels();
        $scoringFactors = $this->getScoringFactors();

        return view('ai.lead-scoring.create', compact('leads', 'scoringModels', 'scoringFactors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'scoring_model' => 'required|string|in:' . implode(',', array_keys($this->getScoringModels())),
            'scoring_factors' => 'required|array',
            'lead_data' => 'required|array',
            'behavioral_data' => 'required|array',
            'demographic_data' => 'required|array',
            'engagement_metrics' => 'required|array',
            'notes' => 'nullable|string',
        ]);

        $lead = Lead::findOrFail($validated['lead_id']);
        $model = $validated['scoring_model'];

        $leadScore = AiLeadScore::create([
            'lead_id' => $validated['lead_id'],
            'user_id' => auth()->id(),
            'scoring_model' => $model,
            'scoring_factors' => $validated['scoring_factors'],
            'lead_data' => $validated['lead_data'],
            'behavioral_data' => $validated['behavioral_data'],
            'demographic_data' => $validated['demographic_data'],
            'engagement_metrics' => $validated['engagement_metrics'],
            'score' => $this->calculateLeadScore($lead, $validated),
            'quality_level' => $this->determineQualityLevel($this->calculateLeadScore($lead, $validated)),
            'confidence_score' => $this->calculateConfidenceScore($validated),
            'recommendations' => $this->generateRecommendations($lead, $validated),
            'notes' => $validated['notes'],
            'status' => 'active',
            'metadata' => [
                'model_version' => 'v1.0',
                'scoring_date' => now(),
                'factors_count' => count($validated['scoring_factors']),
                'created_at' => now(),
            ],
        ]);

        return redirect()->route('ai.lead-scoring.show', $leadScore)
            ->with('success', 'تم إنشاء تقييم العميل المحتمل بالذكاء الاصطناعي بنجاح');
    }

    public function show(AiLeadScore $leadScore)
    {
        $leadScore->load(['lead', 'user', 'metadata']);
        
        $scoreDetails = $this->getScoreDetails($leadScore);
        $factorAnalysis = $this->getFactorAnalysis($leadScore);
        $recommendations = $this->getDetailedRecommendations($leadScore);

        return view('ai.lead-scoring.show', compact(
            'leadScore', 
            'scoreDetails', 
            'factorAnalysis', 
            'recommendations'
        ));
    }

    public function edit(AiLeadScore $leadScore)
    {
        $leads = Lead::all();
        $scoringModels = $this->getScoringModels();
        $scoringFactors = $this->getScoringFactors();

        return view('ai.lead-scoring.edit', compact('leadScore', 'leads', 'scoringModels', 'scoringFactors'));
    }

    public function update(Request $request, AiLeadScore $leadScore)
    {
        $validated = $request->validate([
            'scoring_factors' => 'nullable|array',
            'lead_data' => 'nullable|array',
            'behavioral_data' => 'nullable|array',
            'demographic_data' => 'nullable|array',
            'engagement_metrics' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $scoringFactors = $validated['scoring_factors'] ?? $leadScore->scoring_factors;
        $leadData = $validated['lead_data'] ?? $leadScore->lead_data;
        $behavioralData = $validated['behavioral_data'] ?? $leadScore->behavioral_data;
        $demographicData = $validated['demographic_data'] ?? $leadScore->demographic_data;
        $engagementMetrics = $validated['engagement_metrics'] ?? $leadScore->engagement_metrics;

        $updatedData = array_merge($validated, [
            'scoring_factors' => $scoringFactors,
            'lead_data' => $leadData,
            'behavioral_data' => $behavioralData,
            'demographic_data' => $demographicData,
            'engagement_metrics' => $engagementMetrics,
        ]);

        $newScore = $this->calculateLeadScore($leadScore->lead, $updatedData);

        $leadScore->update([
            'scoring_factors' => $scoringFactors,
            'lead_data' => $leadData,
            'behavioral_data' => $behavioralData,
            'demographic_data' => $demographicData,
            'engagement_metrics' => $engagementMetrics,
            'score' => $newScore,
            'quality_level' => $this->determineQualityLevel($newScore),
            'confidence_score' => $this->calculateConfidenceScore($updatedData),
            'recommendations' => $this->generateRecommendations($leadScore->lead, $updatedData),
            'notes' => $validated['notes'] ?? $leadScore->notes,
            'metadata' => array_merge($leadScore->metadata, [
                'updated_at' => now(),
                'last_updated_by' => auth()->id(),
            ]),
        ]);

        return redirect()->route('ai.lead-scoring.show', $leadScore)
            ->with('success', 'تم تحديث تقييم العميل المحتمل بنجاح');
    }

    public function destroy(AiLeadScore $leadScore)
    {
        $leadScore->delete();

        return redirect()->route('ai.lead-scoring.index')
            ->with('success', 'تم حذف تقييم العميل المحتمل بنجاح');
    }

    public function rescore(AiLeadScore $leadScore)
    {
        $rescoringResults = $this->performRescoring($leadScore);
        
        $leadScore->update([
            'score' => $rescoringResults['score'],
            'quality_level' => $this->determineQualityLevel($rescoringResults['score']),
            'confidence_score' => $rescoringResults['confidence_score'],
            'recommendations' => $rescoringResults['recommendations'],
            'metadata' => array_merge($leadScore->metadata, [
                'rescoring_results' => $rescoringResults,
                'rescoring_date' => now(),
            ]),
        ]);

        return response()->json([
            'success' => true,
            'lead_score' => $leadScore->fresh(),
            'results' => $rescoringResults,
        ]);
    }

    public function analyze(AiLeadScore $leadScore)
    {
        $analysis = $this->performScoreAnalysis($leadScore);
        
        $leadScore->update([
            'metadata' => array_merge($leadScore->metadata, [
                'analysis_results' => $analysis,
                'analysis_date' => now(),
            ]),
        ]);

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'lead_score' => $leadScore->fresh(),
        ]);
    }

    public function compare(AiLeadScore $leadScore)
    {
        $comparisons = $this->getScoreComparisons($leadScore);
        
        return response()->json([
            'success' => true,
            'comparisons' => $comparisons,
            'lead_score' => $leadScore,
        ]);
    }

    public function insights()
    {
        $insights = $this->generateScoringInsights();
        
        return response()->json([
            'success' => true,
            'insights' => $insights,
        ]);
    }

    // Helper Methods
    private function getAverageScore(): float
    {
        return AiLeadScore::avg('score') ?? 0;
    }

    private function getScoringAccuracy(): float
    {
        // Simulate scoring accuracy calculation
        return 0.87;
    }

    private function getScoringTrends(): array
    {
        return AiLeadScore::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(score) as avg_score')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPerformanceMetrics(): array
    {
        return [
            'total_scores' => AiLeadScore::count(),
            'high_quality_leads' => AiLeadScore::where('score', '>=', 80)->count(),
            'medium_quality_leads' => AiLeadScore::whereBetween('score', [50, 79])->count(),
            'low_quality_leads' => AiLeadScore::where('score', '<', 50)->count(),
            'average_score' => $this->getAverageScore(),
            'model_performance' => $this->getModelPerformance(),
        ];
    }

    private function getModelPerformance(): array
    {
        return [
            'random_forest' => 0.88,
            'logistic_regression' => 0.82,
            'neural_network' => 0.91,
            'ensemble' => 0.93,
            'xgboost' => 0.89,
        ];
    }

    private function getScoringModels(): array
    {
        return [
            'random_forest' => 'Random Forest',
            'logistic_regression' => 'Logistic Regression',
            'neural_network' => 'Neural Network',
            'ensemble' => 'Ensemble Model',
            'xgboost' => 'XGBoost',
            'lightgbm' => 'LightGBM',
        ];
    }

    private function getScoringFactors(): array
    {
        return [
            'demographic' => 'Demographic Factors',
            'behavioral' => 'Behavioral Factors',
            'engagement' => 'Engagement Metrics',
            'source' => 'Lead Source',
            'timing' => 'Timing Factors',
            'budget' => 'Budget Alignment',
        ];
    }

    private function calculateLeadScore(Lead $lead, array $data): float
    {
        $scoringFactors = $data['scoring_factors'] ?? [];
        $leadData = $data['lead_data'] ?? [];
        $behavioralData = $data['behavioral_data'] ?? [];
        $demographicData = $data['demographic_data'] ?? [];
        $engagementMetrics = $data['engagement_metrics'] ?? [];

        $score = 0;

        // Demographic factors (30% weight)
        $demographicScore = $this->calculateDemographicScore($demographicData);
        $score += $demographicScore * 0.3;

        // Behavioral factors (25% weight)
        $behavioralScore = $this->calculateBehavioralScore($behavioralData);
        $score += $behavioralScore * 0.25;

        // Engagement metrics (25% weight)
        $engagementScore = $this->calculateEngagementScore($engagementMetrics);
        $score += $engagementScore * 0.25;

        // Lead data (20% weight)
        $leadDataScore = $this->calculateLeadDataScore($leadData);
        $score += $leadDataScore * 0.2;

        return min($score, 100);
    }

    private function calculateDemographicScore(array $demographicData): float
    {
        $score = 0;

        // Age factor
        if (isset($demographicData['age'])) {
            $age = $demographicData['age'];
            if ($age >= 25 && $age <= 65) {
                $score += 20;
            } elseif ($age >= 18 && $age < 25) {
                $score += 15;
            } else {
                $score += 10;
            }
        }

        // Income factor
        if (isset($demographicData['income'])) {
            $income = $demographicData['income'];
            if ($income >= 50000) {
                $score += 25;
            } elseif ($income >= 30000) {
                $score += 20;
            } else {
                $score += 10;
            }
        }

        // Location factor
        if (isset($demographicData['location_quality'])) {
            $locationQuality = $demographicData['location_quality'];
            $score += $locationQuality * 15;
        }

        // Education factor
        if (isset($demographicData['education_level'])) {
            $education = $demographicData['education_level'];
            $educationScores = [
                'phd' => 15,
                'masters' => 12,
                'bachelors' => 10,
                'associate' => 8,
                'high_school' => 5,
                'other' => 3,
            ];
            $score += $educationScores[$education] ?? 5;
        }

        return min($score, 100);
    }

    private function calculateBehavioralScore(array $behavioralData): float
    {
        $score = 0;

        // Website visits
        if (isset($behavioralData['website_visits'])) {
            $visits = $behavioralData['website_visits'];
            $score += min($visits * 5, 25);
        }

        // Page views
        if (isset($behavioralData['page_views'])) {
            $pageViews = $behavioralData['page_views'];
            $score += min($pageViews * 2, 20);
        }

        // Time on site
        if (isset($behavioralData['time_on_site'])) {
            $timeOnSite = $behavioralData['time_on_site']; // in minutes
            $score += min($timeOnSite / 3, 25);
        }

        // Previous interactions
        if (isset($behavioralData['previous_interactions'])) {
            $interactions = $behavioralData['previous_interactions'];
            $score += min($interactions * 10, 30);
        }

        return min($score, 100);
    }

    private function calculateEngagementScore(array $engagementMetrics): float
    {
        $score = 0;

        // Email opens
        if (isset($engagementMetrics['email_opens'])) {
            $opens = $engagementMetrics['email_opens'];
            $score += min($opens * 8, 25);
        }

        // Email clicks
        if (isset($engagementMetrics['email_clicks'])) {
            $clicks = $engagementMetrics['email_clicks'];
            $score += min($clicks * 12, 30);
        }

        // Social media engagement
        if (isset($engagementMetrics['social_engagement'])) {
            $engagement = $engagementMetrics['social_engagement'];
            $score += min($engagement * 5, 20);
        }

        // Form submissions
        if (isset($engagementMetrics['form_submissions'])) {
            $submissions = $engagementMetrics['form_submissions'];
            $score += min($submissions * 15, 25);
        }

        return min($score, 100);
    }

    private function calculateLeadDataScore(array $leadData): float
    {
        $score = 0;

        // Budget alignment
        if (isset($leadData['budget_alignment'])) {
            $alignment = $leadData['budget_alignment'];
            $score += $alignment * 30;
        }

        // Urgency level
        if (isset($leadData['urgency_level'])) {
            $urgency = $leadData['urgency_level'];
            $urgencyScores = [
                'immediate' => 25,
                'high' => 20,
                'medium' => 15,
                'low' => 10,
                'very_low' => 5,
            ];
            $score += $urgencyScores[$urgency] ?? 10;
        }

        // Decision maker
        if (isset($leadData['is_decision_maker'])) {
            $isDecisionMaker = $leadData['is_decision_maker'];
            $score += $isDecisionMaker ? 20 : 10;
        }

        // Timeline
        if (isset($leadData['timeline'])) {
            $timeline = $leadData['timeline'];
            $timelineScores = [
                'immediate' => 15,
                '1_month' => 12,
                '3_months' => 10,
                '6_months' => 8,
                '1_year' => 5,
                'long_term' => 3,
            ];
            $score += $timelineScores[$timeline] ?? 5;
        }

        return min($score, 100);
    }

    private function determineQualityLevel(float $score): string
    {
        if ($score >= 80) {
            return 'high';
        } elseif ($score >= 50) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    private function calculateConfidenceScore(array $data): float
    {
        $dataCompleteness = $this->assessDataCompleteness($data);
        $modelAccuracy = $this->getModelAccuracy($data['scoring_model'] ?? 'default');
        $dataQuality = $this->assessDataQuality($data);

        return ($dataCompleteness * 0.3) + ($modelAccuracy * 0.4) + ($dataQuality * 0.3);
    }

    private function assessDataCompleteness(array $data): float
    {
        $requiredFields = ['scoring_factors', 'lead_data', 'behavioral_data', 'demographic_data', 'engagement_metrics'];
        $presentFields = array_keys($data);
        $missingFields = array_diff($requiredFields, $presentFields);

        return count($missingFields) === 0 ? 1.0 : (count($presentFields) / count($requiredFields));
    }

    private function assessDataQuality(array $data): float
    {
        // Simulate data quality assessment
        return 0.85;
    }

    private function getModelAccuracy(string $model): float
    {
        $accuracyScores = [
            'random_forest' => 0.88,
            'logistic_regression' => 0.82,
            'neural_network' => 0.91,
            'ensemble' => 0.93,
            'xgboost' => 0.89,
        ];

        return $accuracyScores[$model] ?? 0.85;
    }

    private function generateRecommendations(Lead $lead, array $data): array
    {
        $score = $this->calculateLeadScore($lead, $data);
        $recommendations = [];

        if ($score >= 80) {
            $recommendations[] = [
                'action' => 'immediate_follow_up',
                'priority' => 'high',
                'message' => 'اتصل بالعميل المحتمل فوراً لتحديد موعد',
            ];
            $recommendations[] = [
                'action' => 'personalized_offer',
                'priority' => 'medium',
                'message' => 'قدم عرضاً مخصصاً بناءً على احتياجات العميل',
            ];
        } elseif ($score >= 50) {
            $recommendations[] = [
                'action' => 'nurturing_campaign',
                'priority' => 'medium',
                'message' => 'أضف العميل إلى حملة التغذية',
            ];
            $recommendations[] = [
                'action' => 'additional_info',
                'priority' => 'low',
                'message' => 'جمع المزيد من المعلومات عن العميل',
            ];
        } else {
            $recommendations[] = [
                'action' => 'long_term_nurturing',
                'priority' => 'low',
                'message' => 'ضع العميل في قائمة الانتظار الطويلة',
            ];
            $recommendations[] = [
                'action' => 'data_enrichment',
                'priority' => 'low',
                'message' => 'إثراء بيانات العميل لتحسين التقييم',
            ];
        }

        return $recommendations;
    }

    private function performRescoring(AiLeadScore $leadScore): array
    {
        $data = [
            'scoring_factors' => $leadScore->scoring_factors,
            'lead_data' => $leadScore->lead_data,
            'behavioral_data' => $leadScore->behavioral_data,
            'demographic_data' => $leadScore->demographic_data,
            'engagement_metrics' => $leadScore->engagement_metrics,
            'scoring_model' => $leadScore->scoring_model,
        ];

        $newScore = $this->calculateLeadScore($leadScore->lead, $data);
        $confidenceScore = $this->calculateConfidenceScore($data);
        $recommendations = $this->generateRecommendations($leadScore->lead, $data);

        return [
            'previous_score' => $leadScore->score,
            'new_score' => $newScore,
            'score_change' => $newScore - $leadScore->score,
            'confidence_score' => $confidenceScore,
            'recommendations' => $recommendations,
            'rescoring_date' => now(),
        ];
    }

    private function performScoreAnalysis(AiLeadScore $leadScore): array
    {
        return [
            'score_breakdown' => [
                'demographic' => $this->calculateDemographicScore($leadScore->demographic_data),
                'behavioral' => $this->calculateBehavioralScore($leadScore->behavioral_data),
                'engagement' => $this->calculateEngagementScore($leadScore->engagement_metrics),
                'lead_data' => $this->calculateLeadDataScore($leadScore->lead_data),
            ],
            'factor_importance' => $this->getFactorImportance($leadScore),
            'strengths' => $this->identifyStrengths($leadScore),
            'weaknesses' => $this->identifyWeaknesses($leadScore),
            'improvement_suggestions' => $this->getImprovementSuggestions($leadScore),
        ];
    }

    private function getScoreComparisons(AiLeadScore $leadScore): array
    {
        $similarLeads = AiLeadScore::where('id', '!=', $leadScore->id)
            ->whereBetween('score', [$leadScore->score - 10, $leadScore->score + 10])
            ->take(5)
            ->get();

        $comparisons = [];
        foreach ($similarLeads as $similarLead) {
            $comparisons[] = [
                'lead_id' => $similarLead->lead_id,
                'score' => $similarLead->score,
                'quality_level' => $similarLead->quality_level,
                'difference' => $similarLead->score - $leadScore->score,
            ];
        }

        return $comparisons;
    }

    private function generateScoringInsights(): array
    {
        return [
            'total_leads_scored' => AiLeadScore::count(),
            'average_score' => $this->getAverageScore(),
            'score_distribution' => $this->getScoreDistribution(),
            'top_scoring_factors' => $this->getTopScoringFactors(),
            'conversion_correlation' => $this->getConversionCorrelation(),
            'model_performance' => $this->getModelPerformance(),
        ];
    }

    private function getScoreDistribution(): array
    {
        return [
            'high' => AiLeadScore::where('score', '>=', 80)->count(),
            'medium' => AiLeadScore::whereBetween('score', [50, 79])->count(),
            'low' => AiLeadScore::where('score', '<', 50)->count(),
        ];
    }

    private function getTopScoringFactors(): array
    {
        return [
            'demographic' => 0.30,
            'behavioral' => 0.25,
            'engagement' => 0.25,
            'lead_data' => 0.20,
        ];
    }

    private function getConversionCorrelation(): array
    {
        return [
            'high_score_conversion' => 0.75,
            'medium_score_conversion' => 0.45,
            'low_score_conversion' => 0.15,
        ];
    }

    private function getScoreDetails(AiLeadScore $leadScore): array
    {
        return [
            'lead_score_id' => $leadScore->id,
            'lead' => [
                'id' => $leadScore->lead->id,
                'name' => $leadScore->lead->name,
                'email' => $leadScore->lead->email,
                'phone' => $leadScore->lead->phone,
            ],
            'scoring_model' => $leadScore->scoring_model,
            'score' => $leadScore->score,
            'quality_level' => $leadScore->quality_level,
            'confidence_score' => $leadScore->confidence_score,
            'scoring_factors' => $leadScore->scoring_factors,
            'recommendations' => $leadScore->recommendations,
            'status' => $leadScore->status,
            'metadata' => $leadScore->metadata,
            'created_at' => $leadScore->created_at,
            'updated_at' => $leadScore->updated_at,
        ];
    }

    private function getFactorAnalysis(AiLeadScore $leadScore): array
    {
        return [
            'demographic_score' => $this->calculateDemographicScore($leadScore->demographic_data),
            'behavioral_score' => $this->calculateBehavioralScore($leadScore->behavioral_data),
            'engagement_score' => $this->calculateEngagementScore($leadScore->engagement_metrics),
            'lead_data_score' => $this->calculateLeadDataScore($leadScore->lead_data),
            'factor_weights' => [
                'demographic' => 0.30,
                'behavioral' => 0.25,
                'engagement' => 0.25,
                'lead_data' => 0.20,
            ],
        ];
    }

    private function getDetailedRecommendations(AiLeadScore $leadScore): array
    {
        $recommendations = $leadScore->recommendations ?? [];
        
        return [
            'primary_recommendations' => array_slice($recommendations, 0, 2),
            'secondary_recommendations' => array_slice($recommendations, 2),
            'action_priority' => $this->getActionPriority($leadScore),
            'next_steps' => $this->getNextSteps($leadScore),
        ];
    }

    private function getFactorImportance(AiLeadScore $leadScore): array
    {
        return [
            'demographic' => 0.30,
            'behavioral' => 0.25,
            'engagement' => 0.25,
            'lead_data' => 0.20,
        ];
    }

    private function identifyStrengths(AiLeadScore $leadScore): array
    {
        $strengths = [];
        
        if ($this->calculateDemographicScore($leadScore->demographic_data) >= 70) {
            $strengths[] = 'بيانات ديموغرافية قوية';
        }
        
        if ($this->calculateBehavioralScore($leadScore->behavioral_data) >= 70) {
            $strengths[] = 'سلوك نشط';
        }
        
        if ($this->calculateEngagementScore($leadScore->engagement_metrics) >= 70) {
            $strengths[] = 'مشاركة عالية';
        }
        
        if ($this->calculateLeadDataScore($leadScore->lead_data) >= 70) {
            $strengths[] = 'بيانات العميل متوافقة';
        }

        return $strengths;
    }

    private function identifyWeaknesses(AiLeadScore $leadScore): array
    {
        $weaknesses = [];
        
        if ($this->calculateDemographicScore($leadScore->demographic_data) < 50) {
            $weaknesses[] = 'بيانات ديموغرافية محدودة';
        }
        
        if ($this->calculateBehavioralScore($leadScore->behavioral_data) < 50) {
            $weaknesses[] = 'سلوك غير نشط';
        }
        
        if ($this->calculateEngagementScore($leadScore->engagement_metrics) < 50) {
            $weaknesses[] = 'مشاركة منخفضة';
        }
        
        if ($this->calculateLeadDataScore($leadScore->lead_data) < 50) {
            $weaknesses[] = 'بيانات العميل غير متوافقة';
        }

        return $weaknesses;
    }

    private function getImprovementSuggestions(AiLeadScore $leadScore): array
    {
        $suggestions = [];
        
        if ($this->calculateDemographicScore($leadScore->demographic_data) < 50) {
            $suggestions[] = 'جمع المزيد من البيانات الديموغرافية';
        }
        
        if ($this->calculateBehavioralScore($leadScore->behavioral_data) < 50) {
            $suggestions[] = 'تحفيز المزيد من التفاعل مع الموقع';
        }
        
        if ($this->calculateEngagementScore($leadScore->engagement_metrics) < 50) {
            $suggestions[] = 'تحسين حملات البريد الإلكتروني';
        }
        
        if ($this->calculateLeadDataScore($leadScore->lead_data) < 50) {
            $suggestions[] = 'تحديث معلومات الميزانية والجدول الزمني';
        }

        return $suggestions;
    }

    private function getActionPriority(AiLeadScore $leadScore): string
    {
        if ($leadScore->score >= 80) {
            return 'immediate';
        } elseif ($leadScore->score >= 50) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    private function getNextSteps(AiLeadScore $leadScore): array
    {
        if ($leadScore->score >= 80) {
            return [
                'اتصل بالعميل خلال 24 ساعة',
                'حدد موعداً لزيارة العقار',
                'قدم عرضاً مخصصاً',
            ];
        } elseif ($leadScore->score >= 50) {
            return [
                'أرسل معلومات إضافية',
                'أضف إلى حملة التغذية',
                'تابع بعد أسبوع',
            ];
        } else {
            return [
                'أرسل معلومات عامة',
                'ضع في قائمة الانتظار',
                'تابع شهرياً',
            ];
        }
    }
}
