<?php

namespace App\Http\Controllers\Geospatial;

use App\Http\Controllers\Controller;
use App\Models\Geospatial\ProximityScore;
use App\Models\Metaverse\MetaverseProperty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProximityAnalysisController extends Controller
{
    /**
     * Display the proximity analysis dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['city', 'property_type', 'amenity_type', 'score_range']);
        
        // Get proximity statistics
        $stats = [
            'total_scores' => ProximityScore::count(),
            'high_scores' => ProximityScore::where('overall_score', '>=', 80)->count(),
            'average_score' => ProximityScore::avg('overall_score') ?? 0,
            'top_amenities' => $this->getTopAmenities(),
            'best_locations' => $this->getBestLocations(),
        ];

        // Get recent proximity analyses
        $recentScores = ProximityScore::with(['property'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($score) {
                return [
                    'id' => $score->id,
                    'property_id' => $score->property_id,
                    'property_name' => $score->property?->name ?? 'Unknown',
                    'overall_score' => $score->overall_score,
                    'walk_score' => $score->walk_score,
                    'transit_score' => $score->transit_score,
                    'amenity_score' => $score->amenity_score,
                    'created_at' => $score->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get amenity types
        $amenityTypes = [
            'schools' => 'المدارس',
            'hospitals' => 'المستشفيات',
            'shopping' => 'مراكز التسوق',
            'restaurants' => 'المطاعم',
            'parks' => 'الحدائق',
            'banks' => 'المصارف',
            'pharmacies' => 'الصيدليات',
            'gyms' => 'النوادي الرياضية',
            'public_transport' => 'المواصلات العامة',
            'government_offices' => 'المكاتب الحكومية',
        ];

        return Inertia::render('Geospatial/ProximityAnalysis/Index', [
            'stats' => $stats,
            'recentScores' => $recentScores,
            'amenityTypes' => $amenityTypes,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new proximity analysis.
     */
    public function create(): \Inertia\Response
    {
        $properties = MetaverseProperty::select('id', 'name', 'latitude', 'longitude', 'city')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $amenityTypes = [
            'schools' => 'المدارس',
            'hospitals' => 'المستشفيات',
            'shopping' => 'مراكز التسوق',
            'restaurants' => 'المطاعم',
            'parks' => 'الحدائق',
            'banks' => 'المصارف',
            'pharmacies' => 'الصيدليات',
            'gyms' => 'النوادي الرياضية',
            'public_transport' => 'المواصلات العامة',
            'government_offices' => 'المكاتب الحكومية',
        ];

        $analysisMethods = [
            'walking_distance' => 'مسافة المشي',
            'driving_distance' => 'مسافة القيادة',
            'transit_time' => 'وقت المواصلات',
            'mixed_transport' => 'وسائل النقل المختلطة',
        ];

        return Inertia::render('Geospatial/ProximityAnalysis/Create', [
            'properties' => $properties,
            'amenityTypes' => $amenityTypes,
            'analysisMethods' => $analysisMethods,
        ]);
    }

    /**
     * Store a newly created proximity analysis.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:metaverse_properties,id',
            'analysis_method' => 'required|string',
            'amenity_types' => 'required|array',
            'max_distance' => 'nullable|numeric|min:0.1|max:50',
            'include_transit' => 'nullable|boolean',
            'weights' => 'nullable|array',
        ]);

        try {
            // Perform proximity analysis
            $analysisData = $this->performProximityAnalysis($validated);

            $proximityScore = ProximityScore::create([
                'property_id' => $validated['property_id'],
                'analysis_method' => $validated['analysis_method'],
                'amenity_types' => $validated['amenity_types'],
                'max_distance' => $validated['max_distance'] ?? 5,
                'include_transit' => $validated['include_transit'] ?? true,
                'weights' => $validated['weights'] ?? [],
                'walk_score' => $analysisData['walk_score'],
                'transit_score' => $analysisData['transit_score'],
                'amenity_score' => $analysisData['amenity_score'],
                'overall_score' => $analysisData['overall_score'],
                'nearby_amenities' => $analysisData['nearby_amenities'],
                'distance_breakdown' => $analysisData['distance_breakdown'],
                'metadata' => $analysisData['metadata'],
                'status' => 'completed',
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء تحليل القرب بنجاح',
                'proximity_score' => $proximityScore,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء تحليل القرب: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified proximity analysis.
     */
    public function show(ProximityScore $proximityScore): \Inertia\Response
    {
        $proximityScore->load(['property']);

        // Get related analyses
        $relatedScores = ProximityScore::where('property_id', $proximityScore->property_id)
            ->where('id', '!=', $proximityScore->id)
            ->with('property')
            ->take(5)
            ->get();

        return Inertia::render('Geospatial/ProximityAnalysis/Show', [
            'proximityScore' => $proximityScore,
            'relatedScores' => $relatedScores,
        ]);
    }

    /**
     * Show the form for editing the specified proximity analysis.
     */
    public function edit(ProximityScore $proximityScore): \Inertia\Response
    {
        $amenityTypes = [
            'schools' => 'المدارس',
            'hospitals' => 'المستشفيات',
            'shopping' => 'مراكز التسوق',
            'restaurants' => 'المطاعم',
            'parks' => 'الحدائق',
            'banks' => 'المصارف',
            'pharmacies' => 'الصيدليات',
            'gyms' => 'النوادي الرياضية',
            'public_transport' => 'المواصلات العامة',
            'government_offices' => 'المكاتب الحكومية',
        ];

        $analysisMethods = [
            'walking_distance' => 'مسافة المشي',
            'driving_distance' => 'مسافة القيادة',
            'transit_time' => 'وقت المواصلات',
            'mixed_transport' => 'وسائل النقل المختلطة',
        ];

        return Inertia::render('Geospatial/ProximityAnalysis/Edit', [
            'proximityScore' => $proximityScore,
            'amenityTypes' => $amenityTypes,
            'analysisMethods' => $analysisMethods,
        ]);
    }

    /**
     * Update the specified proximity analysis.
     */
    public function update(Request $request, ProximityScore $proximityScore): JsonResponse
    {
        $validated = $request->validate([
            'analysis_method' => 'required|string',
            'amenity_types' => 'required|array',
            'max_distance' => 'nullable|numeric|min:0.1|max:50',
            'include_transit' => 'nullable|boolean',
            'weights' => 'nullable|array',
        ]);

        try {
            // Re-perform analysis if parameters changed
            if ($this->analysisParametersChanged($proximityScore, $validated)) {
                $analysisData = $this->performProximityAnalysis($validated);
                $validated['walk_score'] = $analysisData['walk_score'];
                $validated['transit_score'] = $analysisData['transit_score'];
                $validated['amenity_score'] = $analysisData['amenity_score'];
                $validated['overall_score'] = $analysisData['overall_score'];
                $validated['nearby_amenities'] = $analysisData['nearby_amenities'];
                $validated['distance_breakdown'] = $analysisData['distance_breakdown'];
                $validated['metadata'] = $analysisData['metadata'];
                $validated['status'] = 'completed';
            }

            $proximityScore->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث تحليل القرب بنجاح',
                'proximity_score' => $proximityScore,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث تحليل القرب: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified proximity analysis.
     */
    public function destroy(ProximityScore $proximityScore): JsonResponse
    {
        try {
            $proximityScore->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف تحليل القرب بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف تحليل القرب: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get proximity analysis for a specific location.
     */
    public function getLocationProximity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'amenity_types' => 'nullable|array',
            'max_distance' => 'nullable|numeric|min:0.1|max:50',
            'analysis_method' => 'nullable|string',
        ]);

        try {
            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            $amenityTypes = $validated['amenity_types'] ?? ['all'];
            $maxDistance = $validated['max_distance'] ?? 5;
            $analysisMethod = $validated['analysis_method'] ?? 'walking_distance';

            $proximityData = $this->generateLocationProximity($latitude, $longitude, $amenityTypes, $maxDistance, $analysisMethod);

            return response()->json([
                'success' => true,
                'proximity' => $proximityData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تحليل القرب: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get walk score for a property.
     */
    public function getWalkScore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:metaverse_properties,id',
            'include_amenities' => 'nullable|array',
            'max_walking_distance' => 'nullable|numeric|min:0.1|max:5',
        ]);

        try {
            $property = MetaverseProperty::find($validated['property_id']);
            $includeAmenities = $validated['include_amenities'] ?? ['all'];
            $maxWalkingDistance = $validated['max_walking_distance'] ?? 1.6; // 1.6km = 1 mile

            $walkScore = $this->calculateWalkScore($property, $includeAmenities, $maxWalkingDistance);

            return response()->json([
                'success' => true,
                'walk_score' => $walkScore,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حساب درجة المشي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get transit score for a property.
     */
    public function getTransitScore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:metaverse_properties,id',
            'include_transit_types' => 'nullable|array',
            'max_transit_distance' => 'nullable|numeric|min:0.1|max:10',
        ]);

        try {
            $property = MetaverseProperty::find($validated['property_id']);
            $includeTransitTypes = $validated['include_transit_types'] ?? ['bus', 'metro', 'tram'];
            $maxTransitDistance = $validated['max_transit_distance'] ?? 0.8; // 800m

            $transitScore = $this->calculateTransitScore($property, $includeTransitTypes, $maxTransitDistance);

            return response()->json([
                'success' => true,
                'transit_score' => $transitScore,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حساب درجة المواصلات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get amenity proximity for multiple properties.
     */
    public function getBatchProximity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_ids' => 'required|array|min:1|max:50',
            'property_ids.*' => 'exists:metaverse_properties,id',
            'amenity_types' => 'nullable|array',
            'max_distance' => 'nullable|numeric|min:0.1|max:50',
        ]);

        try {
            $propertyIds = $validated['property_ids'];
            $amenityTypes = $validated['amenity_types'] ?? ['all'];
            $maxDistance = $validated['max_distance'] ?? 5;

            $batchResults = [];
            foreach ($propertyIds as $propertyId) {
                $property = MetaverseProperty::find($propertyId);
                $proximityData = $this->generateLocationProximity(
                    $property->latitude,
                    $property->longitude,
                    $amenityTypes,
                    $maxDistance,
                    'walking_distance'
                );
                $batchResults[$propertyId] = $proximityData;
            }

            return response()->json([
                'success' => true,
                'results' => $batchResults,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تحليل القرب الدفعة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export proximity analysis data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'amenity_types' => 'nullable|array',
            'score_range' => 'nullable|array',
            'include_details' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareProximityExport($validated);
            $filename = $this->generateProximityExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات تحليل القرب للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير تحليل القرب: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Perform proximity analysis.
     */
    private function performProximityAnalysis(array $data): array
    {
        $property = MetaverseProperty::find($data['property_id']);
        $analysisMethod = $data['analysis_method'];
        $amenityTypes = $data['amenity_types'];
        $maxDistance = $data['max_distance'] ?? 5;
        $includeTransit = $data['include_transit'] ?? true;
        $weights = $data['weights'] ?? [];

        // Calculate proximity scores
        $walkScore = $this->calculateWalkScore($property, $amenityTypes, 1.6);
        $transitScore = $includeTransit ? $this->calculateTransitScore($property, ['bus', 'metro'], 0.8) : 0;
        $amenityScore = $this->calculateAmenityScore($property, $amenityTypes, $maxDistance);

        // Calculate overall score with weights
        $walkWeight = $weights['walk'] ?? 0.4;
        $transitWeight = $weights['transit'] ?? 0.3;
        $amenityWeight = $weights['amenity'] ?? 0.3;

        $overallScore = ($walkScore * $walkWeight) + ($transitScore * $transitWeight) + ($amenityScore * $amenityWeight);

        return [
            'walk_score' => $walkScore,
            'transit_score' => $transitScore,
            'amenity_score' => $amenityScore,
            'overall_score' => $overallScore,
            'nearby_amenities' => $this->getNearbyAmenities($property, $amenityTypes, $maxDistance),
            'distance_breakdown' => $this->getDistanceBreakdown($property, $amenityTypes, $maxDistance),
            'metadata' => [
                'analysis_method' => $analysisMethod,
                'max_distance' => $maxDistance,
                'include_transit' => $includeTransit,
                'weights' => $weights,
                'analysis_date' => now()->format('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * Calculate walk score.
     */
    private function calculateWalkScore($property, array $amenityTypes, float $maxDistance): int
    {
        // Mock implementation - would integrate with real walk score API
        $baseScore = 75;
        
        // Adjust based on property location
        if ($property->city === 'الرياض') {
            $baseScore += 10;
        } elseif ($property->city === 'جدة') {
            $baseScore += 5;
        }

        // Adjust based on amenities
        $amenityBonus = min(20, count($amenityTypes) * 2);
        
        return min(100, $baseScore + $amenityBonus);
    }

    /**
     * Calculate transit score.
     */
    private function calculateTransitScore($property, array $transitTypes, float $maxDistance): int
    {
        // Mock implementation
        $baseScore = 60;
        
        // Adjust based on city transit infrastructure
        if ($property->city === 'الرياض') {
            $baseScore += 20; // Riyadh Metro
        } elseif ($property->city === 'جدة') {
            $baseScore += 15;
        }

        // Adjust based on transit types available
        $transitBonus = min(20, count($transitTypes) * 5);
        
        return min(100, $baseScore + $transitBonus);
    }

    /**
     * Calculate amenity score.
     */
    private function calculateAmenityScore($property, array $amenityTypes, float $maxDistance): int
    {
        // Mock implementation
        $baseScore = 70;
        
        // Adjust based on number of amenity types
        $amenityBonus = min(25, count($amenityTypes) * 3);
        
        // Adjust based on max distance (smaller distance = higher score)
        $distanceBonus = $maxDistance <= 2 ? 5 : 0;
        
        return min(100, $baseScore + $amenityBonus + $distanceBonus);
    }

    /**
     * Get nearby amenities.
     */
    private function getNearbyAmenities($property, array $amenityTypes, float $maxDistance): array
    {
        // Mock implementation
        $amenities = [];
        
        foreach ($amenityTypes as $type) {
            $amenities[$type] = [
                'count' => rand(2, 15),
                'average_distance' => rand(200, 800),
                'closest_distance' => rand(100, 300),
                'examples' => $this->getAmenityExamples($type, 3),
            ];
        }
        
        return $amenities;
    }

    /**
     * Get distance breakdown.
     */
    private function getDistanceBreakdown($property, array $amenityTypes, float $maxDistance): array
    {
        // Mock implementation
        return [
            'under_500m' => rand(5, 15),
            '500m_to_1km' => rand(8, 20),
            '1km_to_2km' => rand(3, 12),
            'over_2km' => rand(1, 5),
            'average_distance' => rand(400, 1200),
            'median_distance' => rand(350, 900),
        ];
    }

    /**
     * Get amenity examples.
     */
    private function getAmenityExamples(string $type, int $count): array
    {
        $examples = [
            'schools' => ['مدرسة الأمل', 'مدرسة المستقبل', 'مدرسة النجاح'],
            'hospitals' => ['مستشفى الملك فهد', 'مستشفى الأمل', 'مستشفى الرعاية'],
            'shopping' => ['مول الرياض', 'سوق الواحة', 'مركز التجارة'],
            'restaurants' => ['مطعم الشيف', 'مطعم البحر', 'مطعم الجبل'],
            'parks' => ['حديقة الملك فهد', 'حديقة السلام', 'حديقة الأطفال'],
            'banks' => ['بنك الراجحي', 'البنك الأهلي', 'بنك الرياض'],
            'pharmacies' => ['صيدلية النور', 'صيدلية الأمل', 'صيدلية الرعاية'],
            'gyms' => ['نادي الذهب', 'نادي الصحة', 'نادي اللياقة'],
            'public_transport' => ['محطة المترو', 'محطة الباص', 'محطة الترام'],
            'government_offices' => ['بلدية الرياض', 'وزارة التعليم', 'مكتب البريد'],
        ];

        return array_slice($examples[$type] ?? [], 0, $count);
    }

    /**
     * Generate location proximity.
     */
    private function generateLocationProximity(float $latitude, float $longitude, array $amenityTypes, float $maxDistance, string $analysisMethod): array
    {
        // Mock implementation
        return [
            'location' => ['lat' => $latitude, 'lng' => $longitude],
            'analysis_method' => $analysisMethod,
            'max_distance' => $maxDistance,
            'amenity_types' => $amenityTypes,
            'scores' => [
                'walk_score' => rand(60, 95),
                'transit_score' => rand(50, 90),
                'amenity_score' => rand(65, 92),
                'overall_score' => rand(70, 88),
            ],
            'nearby_amenities' => $this->getNearbyAmenitiesByLocation($latitude, $longitude, $amenityTypes, $maxDistance),
            'distance_breakdown' => [
                'excellent' => rand(5, 15), // < 500m
                'good' => rand(8, 20), // 500m - 1km
                'fair' => rand(3, 12), // 1km - 2km
                'poor' => rand(1, 5), // > 2km
            ],
        ];
    }

    /**
     * Get nearby amenities by location.
     */
    private function getNearbyAmenitiesByLocation(float $latitude, float $longitude, array $amenityTypes, float $maxDistance): array
    {
        // Mock implementation
        $amenities = [];
        
        foreach ($amenityTypes as $type) {
            $amenities[$type] = [
                'total_count' => rand(3, 20),
                'within_max_distance' => rand(2, 12),
                'average_distance' => rand(300, 1500),
                'rating' => rand(3.5, 4.8),
            ];
        }
        
        return $amenities;
    }

    /**
     * Prepare proximity export data.
     */
    private function prepareProximityExport(array $options): array
    {
        $format = $options['format'];
        $amenityTypes = $options['amenity_types'] ?? ['all'];
        $scoreRange = $options['score_range'] ?? [0, 100];
        $includeDetails = $options['include_details'] ?? false;

        // Mock implementation
        $data = [
            'headers' => ['Property ID', 'Walk Score', 'Transit Score', 'Amenity Score', 'Overall Score', 'Analysis Date'],
            'rows' => [
                [1, 85, 78, 82, 81, '2024-01-15'],
                [2, 72, 65, 75, 70, '2024-01-16'],
                [3, 90, 82, 88, 86, '2024-01-17'],
            ],
        ];

        if ($includeDetails) {
            $data['details'] = [
                'amenity_breakdown' => [
                    'schools' => ['count' => 5, 'avg_distance' => 450],
                    'hospitals' => ['count' => 2, 'avg_distance' => 800],
                    'shopping' => ['count' => 8, 'avg_distance' => 320],
                ],
            ];
        }

        return $data;
    }

    /**
     * Generate proximity export filename.
     */
    private function generateProximityExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "proximity_analysis_{$timestamp}.{$format}";
    }

    /**
     * Check if analysis parameters changed.
     */
    private function analysisParametersChanged(ProximityScore $proximityScore, array $newData): bool
    {
        return $proximityScore->analysis_method !== $newData['analysis_method'] ||
               $proximityScore->amenity_types !== $newData['amenity_types'] ||
               $proximityScore->max_distance !== ($newData['max_distance'] ?? 5) ||
               $proximityScore->include_transit !== ($newData['include_transit'] ?? true);
    }

    /**
     * Get top amenities.
     */
    private function getTopAmenities(): array
    {
        return [
            ['type' => 'shopping', 'average_score' => 85, 'property_count' => 145],
            ['type' => 'schools', 'average_score' => 78, 'property_count' => 98],
            ['type' => 'restaurants', 'average_score' => 82, 'property_count' => 76],
        ];
    }

    /**
     * Get best locations.
     */
    private function getBestLocations(): array
    {
        return [
            ['location' => 'وسط المدينة', 'average_score' => 88, 'property_count' => 45],
            ['location' => 'الضاحية الشمالية', 'average_score' => 82, 'property_count' => 32],
            ['location' => 'المركز التجاري', 'average_score' => 79, 'property_count' => 28],
        ];
    }
}
