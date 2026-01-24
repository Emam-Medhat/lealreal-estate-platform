<?php

namespace App\Http\Controllers;

use App\Models\AiPropertyMatch;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AiPropertyMatchingController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_matches' => AiPropertyMatch::count(),
            'successful_matches' => AiPropertyMatch::where('status', 'successful')->count(),
            'pending_matches' => AiPropertyMatch::where('status', 'pending')->count(),
            'average_match_score' => $this->getAverageMatchScore(),
            'total_users_matched' => $this->getTotalUsersMatched(),
            'match_success_rate' => $this->getMatchSuccessRate(),
        ];

        $recentMatches = AiPropertyMatch::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $matchingTrends = $this->getMatchingTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('ai.property-matching.dashboard', compact(
            'stats', 
            'recentMatches', 
            'matchingTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = AiPropertyMatch::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('match_score_min')) {
            $query->where('match_score', '>=', $request->match_score_min);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $matches = $query->latest()->paginate(20);

        $properties = Property::all();
        $users = User::all();
        $statuses = ['pending', 'successful', 'failed', 'cancelled'];

        return view('ai.property-matching.index', compact('matches', 'properties', 'users', 'statuses'));
    }

    public function create()
    {
        $properties = Property::all();
        $users = User::all();
        $matchingAlgorithms = $this->getAvailableAlgorithms();

        return view('ai.property-matching.create', compact('properties', 'users', 'matchingAlgorithms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'user_id' => 'required|exists:users,id',
            'matching_algorithm' => 'required|string|in:' . implode(',', array_keys($this->getAvailableAlgorithms())),
            'user_preferences' => 'required|array',
            'property_features' => 'required|array',
            'matching_criteria' => 'required|array',
            'weight_factors' => 'required|array',
            'notes' => 'nullable|string',
        ]);

        $property = Property::findOrFail($validated['property_id']);
        $user = User::findOrFail($validated['user_id']);
        $algorithm = $validated['matching_algorithm'];

        $match = AiPropertyMatch::create([
            'property_id' => $validated['property_id'],
            'user_id' => $validated['user_id'],
            'matching_algorithm' => $algorithm,
            'user_preferences' => $validated['user_preferences'],
            'property_features' => $validated['property_features'],
            'matching_criteria' => $validated['matching_criteria'],
            'weight_factors' => $validated['weight_factors'],
            'match_score' => $this->calculateMatchScore($property, $user, $validated),
            'confidence_level' => $this->calculateConfidenceLevel($validated),
            'notes' => $validated['notes'],
            'status' => 'pending',
            'metadata' => [
                'algorithm_version' => 'v1.0',
                'criteria_count' => count($validated['matching_criteria']),
                'features_count' => count($validated['property_features']),
                'preferences_count' => count($validated['user_preferences']),
                'created_at' => now(),
            ],
        ]);

        // Trigger AI matching process
        $this->processMatching($match);

        return redirect()->route('ai.property-matching.show', $match)
            ->with('success', 'تم إنشاء مطابقة العقار بالذكاء الاصطناعي بنجاح');
    }

    public function show(AiPropertyMatch $match)
    {
        $match->load(['property', 'user', 'metadata']);
        
        $matchDetails = $this->getMatchDetails($match);
        $compatibilityAnalysis = $this->getCompatibilityAnalysis($match);
        $alternativeProperties = $this->getAlternativeProperties($match);

        return view('ai.property-matching.show', compact(
            'match', 
            'matchDetails', 
            'compatibilityAnalysis', 
            'alternativeProperties'
        ));
    }

    public function edit(AiPropertyMatch $match)
    {
        if ($match->status === 'successful') {
            return back()->with('error', 'لا يمكن تعديل المطابقة الناجحة');
        }

        $properties = Property::all();
        $users = User::all();
        $matchingAlgorithms = $this->getAvailableAlgorithms();

        return view('ai.property-matching.edit', compact('match', 'properties', 'users', 'matchingAlgorithms'));
    }

    public function update(Request $request, AiPropertyMatch $match)
    {
        if ($match->status === 'successful') {
            return back()->with('error', 'لا يمكن تعديل المطابقة الناجحة');
        }

        $validated = $request->validate([
            'user_preferences' => 'nullable|array',
            'property_features' => 'nullable|array',
            'matching_criteria' => 'nullable|array',
            'weight_factors' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $userPreferences = $validated['user_preferences'] ?? $match->user_preferences;
        $propertyFeatures = $validated['property_features'] ?? $match->property_features;
        $matchingCriteria = $validated['matching_criteria'] ?? $match->matching_criteria;
        $weightFactors = $validated['weight_factors'] ?? $match->weight_factors;

        $match->update([
            'user_preferences' => $userPreferences,
            'property_features' => $propertyFeatures,
            'matching_criteria' => $matchingCriteria,
            'weight_factors' => $weightFactors,
            'match_score' => $this->calculateMatchScore($match->property, $match->user, $validated),
            'confidence_level' => $this->calculateConfidenceLevel($validated),
            'notes' => $validated['notes'] ?? $match->notes,
            'metadata' => array_merge($match->metadata, [
                'updated_at' => now(),
                'criteria_updated' => $matchingCriteria,
                'features_updated' => $propertyFeatures,
            ]),
        ]);

        // Re-process matching with updated data
        $this->processMatching($match);

        return redirect()->route('ai.property-matching.show', $match)
            ->with('success', 'تم تحديث مطابقة العقار بنجاح');
    }

    public function destroy(AiPropertyMatch $match)
    {
        if ($match->status === 'successful') {
            return back()->with('error', 'لا يمكن حذف المطابقة الناجحة');
        }

        $match->delete();

        return redirect()->route('ai.property-matching.index')
            ->with('success', 'تم حذف مطابقة العقار بنجاح');
    }

    public function analyze(AiPropertyMatch $match)
    {
        $analysis = $this->performAiAnalysis($match);
        
        $match->update([
            'metadata' => array_merge($match->metadata, [
                'analysis_results' => $analysis,
                'analysis_date' => now(),
                'match_accuracy' => $analysis['accuracy'] ?? 0,
                'confidence_level' => $analysis['confidence'] ?? 0,
            ]),
        ]);

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'updated_match' => $match->fresh(),
        ]);
    }

    public function approve(AiPropertyMatch $match)
    {
        if ($match->status !== 'pending') {
            return back()->with('error', 'لا يمكن اعتماد المطابقة غير المعلقة');
        }

        $match->update([
            'status' => 'successful',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Notify user about successful match
        $this->notifyUser($match, 'match_successful');

        return back()->with('success', 'تم اعتماد المطابقة بنجاح');
    }

    public function reject(AiPropertyMatch $match, Request $request)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $match->update([
            'status' => 'failed',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Notify user about failed match
        $this->notifyUser($match, 'match_failed');

        return back()->with('success', 'تم رفض المطابقة بنجاح');
    }

    public function findMatches(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'preferences' => 'required|array',
            'max_results' => 'nullable|integer|min:1|max:50',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $preferences = $validated['preferences'];
        $maxResults = $validated['max_results'] ?? 10;

        $matches = $this->findBestMatches($user, $preferences, $maxResults);

        return response()->json([
            'success' => true,
            'matches' => $matches,
            'total_found' => count($matches),
        ]);
    }

    // Helper Methods
    private function getAverageMatchScore(): float
    {
        return AiPropertyMatch::avg('match_score') ?? 0;
    }

    private function getTotalUsersMatched(): int
    {
        return AiPropertyMatch::distinct('user_id')->count();
    }

    private function getMatchSuccessRate(): float
    {
        $total = AiPropertyMatch::count();
        $successful = AiPropertyMatch::where('status', 'successful')->count();
        
        return $total > 0 ? ($successful / $total) * 100 : 0;
    }

    private function getMatchingTrends(): array
    {
        return AiPropertyMatch::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(match_score) as avg_score')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPerformanceMetrics(): array
    {
        return [
            'total_matches' => AiPropertyMatch::count(),
            'successful_matches' => AiPropertyMatch::where('status', 'successful')->count(),
            'average_match_score' => AiPropertyMatch::avg('match_score') ?? 0,
            'algorithm_performance' => $this->getAlgorithmPerformance(),
            'criteria_effectiveness' => $this->getCriteriaEffectiveness(),
        ];
    }

    private function getAlgorithmPerformance(): array
    {
        return [
            'collaborative_filtering' => 0.85,
            'content_based' => 0.82,
            'hybrid' => 0.88,
            'neural_network' => 0.91,
        ];
    }

    private function getCriteriaEffectiveness(): array
    {
        return [
            'location' => 0.92,
            'price' => 0.88,
            'size' => 0.85,
            'property_type' => 0.90,
            'amenities' => 0.78,
        ];
    }

    private function getAvailableAlgorithms(): array
    {
        return [
            'collaborative_filtering' => 'Collaborative Filtering',
            'content_based' => 'Content-Based',
            'hybrid' => 'Hybrid Approach',
            'neural_network' => 'Neural Network',
            'matrix_factorization' => 'Matrix Factorization',
            'deep_learning' => 'Deep Learning',
        ];
    }

    private function calculateMatchScore(Property $property, User $user, array $data): float
    {
        $preferences = $data['user_preferences'] ?? [];
        $features = $data['property_features'] ?? [];
        $criteria = $data['matching_criteria'] ?? [];
        $weights = $data['weight_factors'] ?? [];

        $score = 0;
        $totalWeight = array_sum($weights);

        foreach ($criteria as $criterion => $value) {
            $weight = $weights[$criterion] ?? 1;
            $matchValue = $this->calculateCriterionMatch($criterion, $property, $user, $preferences);
            $score += ($matchValue * $weight) / $totalWeight;
        }

        return min($score, 1.0);
    }

    private function calculateCriterionMatch(string $criterion, Property $property, User $user, array $preferences): float
    {
        switch ($criterion) {
            case 'location':
                return $this->calculateLocationMatch($property, $preferences);
            case 'price':
                return $this->calculatePriceMatch($property, $preferences);
            case 'size':
                return $this->calculateSizeMatch($property, $preferences);
            case 'property_type':
                return $this->calculateTypeMatch($property, $preferences);
            case 'amenities':
                return $this->calculateAmenitiesMatch($property, $preferences);
            default:
                return 0.5;
        }
    }

    private function calculateLocationMatch(Property $property, array $preferences): float
    {
        $preferredLocation = $preferences['location'] ?? '';
        $propertyLocation = $property->location ?? '';

        if (empty($preferredLocation)) {
            return 0.5;
        }

        return stripos($propertyLocation, $preferredLocation) !== false ? 1.0 : 0.2;
    }

    private function calculatePriceMatch(Property $property, array $preferences): float
    {
        $minPrice = $preferences['min_price'] ?? 0;
        $maxPrice = $preferences['max_price'] ?? PHP_FLOAT_MAX;
        $propertyPrice = $property->price ?? 0;

        if ($propertyPrice >= $minPrice && $propertyPrice <= $maxPrice) {
            return 1.0;
        }

        if ($propertyPrice < $minPrice) {
            return max(0, 1.0 - (($minPrice - $propertyPrice) / $minPrice));
        }

        if ($propertyPrice > $maxPrice) {
            return max(0, 1.0 - (($propertyPrice - $maxPrice) / $maxPrice));
        }

        return 0.5;
    }

    private function calculateSizeMatch(Property $property, array $preferences): float
    {
        $minSize = $preferences['min_area'] ?? 0;
        $maxSize = $preferences['max_area'] ?? PHP_FLOAT_MAX;
        $propertySize = $property->area ?? 0;

        if ($propertySize >= $minSize && $propertySize <= $maxSize) {
            return 1.0;
        }

        return 0.3;
    }

    private function calculateTypeMatch(Property $property, array $preferences): float
    {
        $preferredType = $preferences['property_type'] ?? '';
        $propertyType = $property->type ?? '';

        return $preferredType === $propertyType ? 1.0 : 0.3;
    }

    private function calculateAmenitiesMatch(Property $property, array $preferences): float
    {
        $requiredAmenities = $preferences['amenities'] ?? [];
        $propertyAmenities = $property->amenities ?? [];

        if (empty($requiredAmenities)) {
            return 0.5;
        }

        $matchedAmenities = 0;
        foreach ($requiredAmenities as $amenity) {
            if (in_array($amenity, $propertyAmenities)) {
                $matchedAmenities++;
            }
        }

        return count($requiredAmenities) > 0 ? $matchedAmenities / count($requiredAmenities) : 0;
    }

    private function calculateConfidenceLevel(array $data): float
    {
        $criteriaCount = count($data['matching_criteria'] ?? []);
        $preferencesCount = count($data['user_preferences'] ?? []);
        $featuresCount = count($data['property_features'] ?? []);

        $dataCompleteness = ($criteriaCount + $preferencesCount + $featuresCount) / 15;
        $algorithmAccuracy = $this->getAlgorithmAccuracy($data['matching_algorithm'] ?? 'default');

        return ($dataCompleteness * 0.4) + ($algorithmAccuracy * 0.6);
    }

    private function getAlgorithmAccuracy(string $algorithm): float
    {
        $accuracyScores = [
            'collaborative_filtering' => 0.85,
            'content_based' => 0.82,
            'hybrid' => 0.88,
            'neural_network' => 0.91,
        ];

        return $accuracyScores[$algorithm] ?? 0.8;
    }

    private function processMatching(AiPropertyMatch $match): void
    {
        // Simulate AI matching process
        $this->sendAiRequest($match, 'match', [
            'property_id' => $match->property_id,
            'user_id' => $match->user_id,
            'algorithm' => $match->matching_algorithm,
            'preferences' => $match->user_preferences,
            'features' => $match->property_features,
            'criteria' => $match->matching_criteria,
        ]);

        // Update status to processing
        $match->update(['status' => 'processing']);
    }

    private function sendAiRequest(AiPropertyMatch $match, string $action, array $data = []): void
    {
        // In a real implementation, this would call an AI service
        // For now, we'll simulate the AI response
        $mockResponse = [
            'success' => true,
            'action' => $action,
            'data' => $data,
            'response' => 'AI processing ' . ucfirst($action),
        ];

        // Update match with AI results
        if ($mockResponse['success']) {
            $match->update([
                'metadata' => array_merge($match->metadata, [
                    'ai_response' => $mockResponse,
                    'ai_response_date' => now(),
                ]),
            ]);
        }
    }

    private function findBestMatches(User $user, array $preferences, int $maxResults): Collection
    {
        $properties = Property::all();
        $matches = [];

        foreach ($properties as $property) {
            $score = $this->calculateMatchScore($property, $user, [
                'user_preferences' => $preferences,
                'property_features' => [],
                'matching_criteria' => ['location', 'price', 'size', 'property_type'],
                'weight_factors' => ['location' => 0.3, 'price' => 0.3, 'size' => 0.2, 'property_type' => 0.2],
            ]);

            if ($score > 0.5) {
                $matches[] = [
                    'property' => $property,
                    'score' => $score,
                    'confidence' => $score * 0.9,
                ];
            }
        }

        // Sort by score and take top results
        usort($matches, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return collect(array_slice($matches, 0, $maxResults));
    }

    private function getMatchDetails(AiPropertyMatch $match): array
    {
        return [
            'property_id' => $match->property_id,
            'user_id' => $match->user_id,
            'property' => [
                'id' => $match->property->id,
                'title' => $match->property->title,
                'type' => $match->property->type,
                'location' => $match->property->location,
                'area' => $match->property->area,
                'price' => $match->property->price,
            ],
            'user' => [
                'id' => $match->user->id,
                'name' => $match->user->name,
                'email' => $match->user->email,
            ],
            'matching_algorithm' => $match->matching_algorithm,
            'match_score' => $match->match_score,
            'confidence_level' => $match->confidence_level,
            'user_preferences' => $match->user_preferences,
            'property_features' => $match->property_features,
            'matching_criteria' => $match->matching_criteria,
            'weight_factors' => $match->weight_factors,
            'metadata' => $match->metadata,
            'created_at' => $match->created_at,
            'updated_at' => $match->updated_at,
        ];
    }

    private function getCompatibilityAnalysis(AiPropertyMatch $match): array
    {
        return [
            'overall_compatibility' => $match->match_score,
            'location_compatibility' => $this->calculateLocationMatch($match->property, $match->user_preferences),
            'price_compatibility' => $this->calculatePriceMatch($match->property, $match->user_preferences),
            'size_compatibility' => $this->calculateSizeMatch($match->property, $match->user_preferences),
            'type_compatibility' => $this->calculateTypeMatch($match->property, $match->user_preferences),
            'amenities_compatibility' => $this->calculateAmenitiesMatch($match->property, $match->user_preferences),
            'recommendations' => $this->generateCompatibilityRecommendations($match),
        ];
    }

    private function getAlternativeProperties(AiPropertyMatch $match): Collection
    {
        $preferences = $match->user_preferences;
        $excludePropertyId = $match->property_id;

        return Property::where('id', '!=', $excludePropertyId)
            ->where('type', $preferences['property_type'] ?? 'residential')
            ->orderBy('price', 'asc')
            ->take(5)
            ->get();
    }

    private function generateCompatibilityRecommendations(AiPropertyMatch $match): array
    {
        $recommendations = [];

        if ($match->match_score < 0.7) {
            $recommendations[] = 'دراسة خيارات أخرى قد تكون أكثر ملاءمة';
        }

        if ($this->calculatePriceMatch($match->property, $match->user_preferences) < 0.6) {
            $recommendations[] = 'مراجعة نطاق السعر المفضل';
        }

        if ($this->calculateLocationMatch($match->property, $match->user_preferences) < 0.6) {
            $recommendations[] = 'توسيع نطاق البحث الجغرافي';
        }

        return $recommendations;
    }

    private function notifyUser(AiPropertyMatch $match, string $type): void
    {
        // In a real implementation, this would send a notification
        // For now, we'll just log it
        $message = $type === 'match_successful' 
            ? 'تم العثور على عقار مطابق لتفضيلاتك'
            : 'لم يتم العثور على تطابق مناسب';

        // Log notification
        \Log::info("User notification: {$message} for match {$match->id}");
    }

    private function performAiAnalysis(AiPropertyMatch $match): array
    {
        return [
            'accuracy' => $match->match_score,
            'confidence' => $match->confidence_level,
            'algorithm_performance' => $this->getAlgorithmAccuracy($match->matching_algorithm),
            'criteria_analysis' => $this->analyzeCriteria($match),
            'recommendations' => $this->generateCompatibilityRecommendations($match),
        ];
    }

    private function analyzeCriteria(AiPropertyMatch $match): array
    {
        $criteria = $match->matching_criteria;
        $analysis = [];

        foreach ($criteria as $criterion) {
            $analysis[$criterion] = [
                'score' => $this->calculateCriterionMatch($criterion, $match->property, $match->user, $match->user_preferences),
                'importance' => $match->weight_factors[$criterion] ?? 1,
                'recommendation' => $this->getCriterionRecommendation($criterion, $match),
            ];
        }

        return $analysis;
    }

    private function getCriterionRecommendation(string $criterion, AiPropertyMatch $match): string
    {
        $score = $this->calculateCriterionMatch($criterion, $match->property, $match->user, $match->user_preferences);

        if ($score < 0.5) {
            return 'هذا المعيار لا يتطابق بشكل جيد';
        } elseif ($score < 0.7) {
            return 'تطابق معتدل';
        } else {
            return 'تطابق ممتاز';
        }
    }
}
