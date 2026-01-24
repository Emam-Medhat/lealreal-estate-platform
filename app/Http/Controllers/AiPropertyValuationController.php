<?php

namespace App\Http\Controllers;

use App\Models\AiPropertyValuation;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AiPropertyValuationController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_valuations' => AiPropertyValuation::count(),
            'pending_valuations' => AiPropertyValuation::where('status', 'pending')->count(),
            'completed_valuations' => AiPropertyValuation::where('status', 'completed')->count(),
            'average_valuation' => $this->getAverageValuation(),
            'accuracy_score' => $this->getAccuracyScore(),
            'total_value_valued' => $this->getTotalValueValued(),
        ];

        $recentValuations = AiPropertyValuation::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $valuationTrends = $this->getValuationTrends();
        $accuracyMetrics = $this->getAccuracyMetrics();

        return view('ai.property-valuation.dashboard', compact(
            'stats',
            'recentValuations',
            'valuationTrends',
            'accuracyMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = AiPropertyValuation::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $valuations = $query->latest()->paginate(20);

        $properties = Property::all();
        $statuses = ['pending', 'completed', 'failed', 'cancelled'];

        return view('ai.property-valuation.index', compact('valuations', 'properties', 'statuses'));
    }

    public function create()
    {
        $properties = Property::all();
        $valuationModels = $this->getAvailableValuationModels();

        return view('ai.property-valuation.create', compact('properties', 'valuationModels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'valuation_model' => 'required|string|in:' . implode(',', array_keys($this->getAvailableValuationModels())),
            'data_points' => 'required|array|min:5',
            'property_features' => 'required|array',
            'market_conditions' => 'required|array',
            'valuation_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $property = Property::findOrFail($validated['property_id']);
        $valuationModel = $validated['valuation_model'];

        $valuation = AiPropertyValuation::create([
            'property_id' => $validated['property_id'],
            'user_id' => auth()->id(),
            'valuation_model' => $valuationModel,
            'property_features' => $validated['property_features'],
            'market_conditions' => $validated['market_conditions'],
            'data_points' => $validated['data_points'],
            'valuation_date' => $validated['valuation_date'],
            'estimated_value' => $this->calculateEstimatedValue($property, $validated),
            'confidence_score' => $this->calculateConfidenceScore($validated),
            'notes' => $validated['notes'],
            'status' => 'pending',
            'metadata' => [
                'model_version' => 'v1.0',
                'training_data_points' => count($validated['data_points']),
                'features_used' => $validated['property_features'],
                'market_conditions' => $validated['market_conditions'],
            ],
        ]);

        // Trigger AI valuation process
        $this->processValuation($valuation);

        return redirect()->route('ai.property-valuation.show', $valuation)
            ->with('success', 'تم إنشاء تقييم العقار بالذكاء الاصطناعي بنجاح');
    }

    public function show(AiPropertyValuation $valuation)
    {
        $valuation->load(['property', 'user', 'metadata']);

        $valuationDetails = $this->getValuationDetails($valuation);
        $comparableProperties = $this->getComparableProperties($valuation);
        $marketAnalysis = $this->getMarketAnalysis($valuation);

        return view('ai.property-valuation.show', compact(
            'valuation',
            'valuationDetails',
            'comparableProperties',
            'marketAnalysis'
        ));
    }

    public function edit(AiPropertyValuation $valuation)
    {
        if ($valuation->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل التقييم المكتمل');
        }

        $property = Property::all();
        $valuationModels = $this->getAvailableValuationModels();

        return view('ai.property-valuation.edit', compact('valuation', 'property', 'valuationModels'));
    }

    public function update(Request $request, AiPropertyValuation $valuation)
    {
        if ($valuation->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل التقييم المكتمل');
        }

        $validated = $request->validate([
            'data_points' => 'nullable|array|min:5',
            'property_features' => 'nullable|array',
            'market_conditions' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $propertyFeatures = $validated['property_features'] ?? $valuation->property_features;
        $marketConditions = $validated['market_conditions'] ?? $valuation->market_conditions;
        $dataPoints = $validated['data_points'] ?? $valuation->data_points;

        $valuation->update([
            'property_features' => $propertyFeatures,
            'market_conditions' => $marketConditions,
            'data_points' => $dataPoints,
            'notes' => $validated['notes'],
            'metadata' => array_merge($valuation->metadata, [
                'features_updated' => $propertyFeatures,
                'conditions_updated' => $marketConditions,
                'data_points_updated' => $dataPoints,
                'updated_at' => now(),
            ]),
        ]);

        // Re-process valuation with updated data
        $this->processValuation($valuation);

        return redirect()->route('ai.property-valuation.show', $valuation)
            ->with('success', 'تم تحديث التقييم العقار بنجاح');
    }

    public function destroy(AiPropertyValuation $valuation)
    {
        if ($valuation->status === 'completed') {
            return back()->with('error', 'لا يمكن حذف التقييم المكتمل');
        }

        $valuation->delete();

        return redirect()->route('ai.property-valuation.index')
            ->with('success', 'تم حذف التقييم العقار بنجاح');
    }

    public function analyze(AiPropertyValuation $valuation)
    {
        $analysis = $this->performAiAnalysis($valuation);

        $valuation->update([
            'metadata' => array_merge($valuation->metadata, [
                'analysis_results' => $analysis,
                'analysis_date' => now(),
                'model_accuracy' => $analysis['accuracy'] ?? 0,
                'confidence_score' => $analysis['confidence'] ?? 0,
            ]),
        ]);

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'updated_valuation' => $valuation->fresh(),
        ]);
    }

    public function report(AiPropertyValuation $valuation)
    {
        $report = $this->generateValuationReport($valuation);

        return response()->json($report);
    }

    public function comparison(AiPropertyValuation $valuation)
    {
        $comparable = $this->getComparableProperties($valuation);

        return response()->json([
            'comparable_properties' => $comparable,
            'valuation' => $valuation,
            'market_comparison' => $this->getMarketComparison($valuation),
        ]);
    }

    // Helper Methods
    private function calculateEstimatedValue(Property $property, array $data): float
    {
        $baseValue = $property->price;
        $features = $data['property_features'] ?? [];
        $conditions = $data['market_conditions'] ?? [];

        // AI-based value adjustment
        $featureAdjustment = $this->calculateFeatureAdjustment($features);
        $marketAdjustment = $this->calculateMarketAdjustment($conditions);

        return $baseValue * (1 + $featureAdjustment + $marketAdjustment);
    }

    private function calculateConfidenceScore(array $data): float
    {
        $dataPoints = count($data['data_points'] ?? []);
        $modelAccuracy = $this->getModelAccuracy($data['valuation_model'] ?? 'default');
        $dataQuality = $this->assessDataQuality($data);

        return ($dataPoints * 0.3) + ($modelAccuracy * 0.4) + ($dataQuality * 0.3);
    }

    private function calculateFeatureAdjustment(array $features): float
    {
        $featureWeights = [
            'location_score' => 0.15,
            'size_score' => 0.25,
            'condition_score' => 0.20,
            'age_score' => 0.10,
            'amenities_score' => 0.10,
        ];

        $score = 0;
        $totalWeight = array_sum($featureWeights);

        foreach ($featureWeights as $feature => $weight) {
            if (isset($features[$feature])) {
                $score += ($features[$feature] / $totalWeight) * $weight;
            }
        }

        return $score;
    }

    private function calculateMarketAdjustment(array $conditions): float
    {
        $marketFactors = [
            'demand_score' => 0.2,
            'supply_score' => 0.15,
            'interest_rate' => 0.1,
            'market_trend' => 0.1,
            'seasonal_factor' => 0.05,
        ];

        $score = 0;
        $totalWeight = array_sum($marketFactors);

        foreach ($marketFactors as $factor => $weight) {
            if (isset($conditions[$factor])) {
                $score += ($conditions[$factor] / $totalWeight) * $weight;
            }
        }

        return $score;
    }

    private function assessDataQuality(array $data): float
    {
        $completeness = $this->assessDataCompleteness($data);
        $consistency = $this->assessDataConsistency($data);
        $accuracy = $this->assessDataAccuracy($data);

        return ($completeness + $consistency + $accuracy) / 3;
    }

    private function assessDataCompleteness(array $data): float
    {
        $requiredFields = ['property_features', 'market_conditions', 'data_points'];
        $presentFields = array_keys($data);
        $missingFields = array_diff($requiredFields, $presentFields);

        return count($missingFields) === 0 ? 1.0 : (count($presentFields) / count($requiredFields)) * 100;
    }

    private function assessDataConsistency(array $data): float
    {
        // Check for logical consistency in data
        $consistencyScore = 0.9; // Placeholder
        return $consistencyScore;
    }

    private function assessDataAccuracy(array $data): float
    {
        // Compare with historical data
        $accuracyScore = 0.85; // Placeholder
        return $accuracyScore;
    }

    private function getModelAccuracy(string $model): float
    {
        $accuracyScores = [
            'random_forest' => 0.75,
            'linear_regression' => 0.82,
            'neural_network' => 0.88,
            'ensemble' => 0.91,
        ];

        return $accuracyScores[$model] ?? 0.75;
    }

    private function getAvailableValuationModels(): array
    {
        return [
            'random_forest' => 'Random Forest',
            'linear_regression' => 'Linear Regression',
            'neural_network' => 'Neural Network',
            'ensemble' => 'Ensemble Model',
            'xgboost' => 'XGBoost',
            'lightgbm' => 'LightGBM',
            'catboost' => 'CatBoost',
        ];
    }

    private function getComparableProperties(AiPropertyValuation $valuation): Collection
    {
        $property = $valuation->property;
        $location = $property->location ?? '';
        $propertyType = $property->type ?? 'residential';
        $bedrooms = $property->bedrooms ?? 0;
        $area = $property->area ?? 0;

        return Property::where('location', 'like', "%{$location}%")
            ->where('type', $propertyType)
            ->where('bedrooms', '>=', $bedrooms - 1)
            ->where('area', '>=', $area - 50)
            ->where('area', '<=', $area + 200)
            ->orderBy('price', 'desc')
            ->take(10);
    }

    private function getMarketAnalysis(AiPropertyValuation $valuation): array
    {
        $property = $valuation->property;
        $location = $property->location ?? '';
        $area = $property->area ?? 0;
        $price = $property->price ?? 0;
        $bedrooms = $property->bedrooms ?? 0;

        return [
            'location' => $location,
            'area' => $area,
            'price_per_sqm' => $area > 0 ? $price / $area : 0,
            'price_per_bedroom' => $bedrooms > 0 ? $price / $bedrooms : 0,
            'market_demand' => $this->getMarketDemand($location),
            'price_trend' => $this->getPriceTrend($location),
        ];
    }

    private function getMarketDemand(string $location): float
    {
        $demandScores = [
            'downtown' => 0.9,
            'suburban' => 0.7,
            'suburban' => 0.6,
            'rural' => 0.4,
            'industrial' => 0.3,
        ];

        return $demandScores[$location] ?? 0.5;
    }

    private function getPriceTrend(string $location): string
    {
        $trends = [
            'downtown' => 'increasing',
            'suburban' => 'stable',
            'suburban' => 'decreasing',
            'rural' => 'stable',
        ];

        return $trends[$location] ?? 'stable';
    }

    private function getValuationTrends(): array
    {
        return AiPropertyValuation::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('AVG(estimated_value) as avg_value')
        )
            ->groupBy('date')
            ->orderBy('date')
            ->get()->toArray();
    }

    private function getAccuracyMetrics(): array
    {
        return [
            'total_valuations' => AiPropertyValuation::count(),
            'accurate_valuations' => AiPropertyValuation::where('accuracy', '>=', 0.8)->count(),
            'average_accuracy' => AiPropertyValuation::avg('accuracy') ?? 0,
            'model_performance' => $this->getModelPerformance(),
            'data_quality_score' => $this->getDataQualityScore(),
        ];
    }

    private function getModelPerformance(): array
    {
        return [
            'random_forest' => 0.82,
            'linear_regression' => 0.78,
            'neural_network' => 0.85,
            'ensemble' => 0.89,
        ];
    }

    private function getDataQualityScore(): float
    {
        return 0.88; // Placeholder for data quality assessment
    }

    private function getTotalValueValued(): float
    {
        return AiPropertyValuation::where('status', 'completed')
            ->sum('estimated_value');
    }

    private function processValuation(AiPropertyValuation $valuation): void
    {
        // Simulate AI valuation process
        $this->sendAiRequest($valuation, 'analyze', [
            'property_id' => $valuation->property_id,
            'data_points' => $valuation->data_points,
            'features' => $valuation->property_features,
            'conditions' => $valuation->market_conditions,
        ]);

        // Update status to processing
        $valuation->update(['status' => 'processing']);
    }

    private function generateValuationReport(AiPropertyValuation $valuation): array
    {
        return [
            'valuation_id' => $valuation->id,
            'property_id' => $valuation->property_id,
            'estimated_value' => $valuation->estimated_value,
            'confidence_score' => $valuation->confidence_score,
            'accuracy_score' => $valuation->accuracy_score ?? 0,
            'model_used' => $valuation->valuation_model,
            'data_points_count' => count($valuation->data_points ?? []),
            'features_used' => count($valuation->property_features ?? []),
            'market_conditions' => $valuation->market_conditions ?? [],
            'created_at' => $valuation->created_at,
            'updated_at' => $valuation->updated_at,
            'analysis_results' => $valuation->metadata['analysis_results'] ?? [],
            'recommendations' => $this->generateRecommendations($valuation),
        ];
    }

    private function generateRecommendations(AiPropertyValuation $valuation): array
    {
        $recommendations = [];

        if ($valuation->estimated_value > $valuation->property->price * 1.2) {
            $recommendations[] = [
                'السعر السعر الحالي للعقار',
                'فكر فرصة استثمار',
                'التحقق من قيمة السوق',
            ];
        }

        if ($valuation->confidence_score < 0.7) {
            $recommendations[] = [
                'زيادة دقة البيانات لتحسين الدقة',
                'استخدم نموذج مختلف',
                'احصل على مراجعة الخبرة',
            ];
        }

        if ($valuation->accuracy_score < 0.75) {
            $recommendations[] = [
                'أعدل من التدريب',
                'استخدم نموذج مختلف',
                'تحقق من البيانات التاريخية',
            ];
        }

        return $recommendations;
    }
}
