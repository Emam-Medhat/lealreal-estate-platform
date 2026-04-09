<?php

namespace App\Http\Controllers\Geospatial;

use App\Http\Controllers\Controller;
use App\Models\Geospatial\SchoolDistrict;
use App\Models\Metaverse\MetaverseProperty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SchoolDistrictController extends Controller
{
    /**
     * Display the school district dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['city', 'district_type', 'rating_range', 'school_level']);
        
        // Get school district statistics
        $stats = [
            'total_districts' => SchoolDistrict::count(),
            'high_rated_districts' => SchoolDistrict::where('overall_rating', '>=', 8)->count(),
            'average_rating' => SchoolDistrict::avg('overall_rating') ?? 0,
            'best_districts' => $this->getBestDistricts(),
            'improvement_areas' => $this->getImprovementAreas(),
        ];

        // Get recent school district analyses
        $recentDistricts = SchoolDistrict::with(['property'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($district) {
                return [
                    'id' => $district->id,
                    'property_id' => $district->property_id,
                    'property_name' => $district->property?->name ?? 'Unknown',
                    'district_name' => $district->district_name,
                    'overall_rating' => $district->overall_rating,
                    'elementary_rating' => $district->elementary_rating,
                    'middle_rating' => $district->middle_rating,
                    'high_rating' => $district->high_rating,
                    'status' => $district->status,
                    'created_at' => $district->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get district types
        $districtTypes = [
            'public' => 'مدارس حكومية',
            'private' => 'مدارس خاصة',
            'charter' => 'مدارس مستقلة',
            'international' => 'مدارس دولية',
            'religious' => 'مدارس دينية',
            'specialized' => 'مدارس متخصصة',
        ];

        // Get school levels
        $schoolLevels = [
            'elementary' => 'المرحلة الابتدائية',
            'middle' => 'المرحلة المتوسطة',
            'high' => 'المرحلة الثانوية',
            'k12' => 'جميع المراحل',
        ];

        return Inertia::render('Geospatial/SchoolDistrict/Index', [
            'stats' => $stats,
            'recentDistricts' => $recentDistricts,
            'districtTypes' => $districtTypes,
            'schoolLevels' => $schoolLevels,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new school district analysis.
     */
    public function create(): \Inertia\Response
    {
        $properties = MetaverseProperty::select('id', 'name', 'latitude', 'longitude', 'city')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $districtTypes = [
            'public' => 'مدارس حكومية',
            'private' => 'مدارس خاصة',
            'charter' => 'مدارس مستقلة',
            'international' => 'مدارس دولية',
            'religious' => 'مدارس دينية',
            'specialized' => 'مدارس متخصصة',
        ];

        $schoolLevels = [
            'elementary' => 'المرحلة الابتدائية',
            'middle' => 'المرحلة المتوسطة',
            'high' => 'المرحلة الثانوية',
            'k12' => 'جميع المراحل',
        ];

        $analysisMethods = [
            'standard_algorithm' => 'الخوارزمية القياسية',
            'test_scores' => 'درجات الاختبارات',
            'parent_reviews' => 'مراجعات أولياء الأمور',
            'accreditation' => 'الاعتماد الأكاديمي',
            'graduation_rates' => 'معدلات التخرج',
        ];

        return Inertia::render('Geospatial/SchoolDistrict/Create', [
            'properties' => $properties,
            'districtTypes' => $districtTypes,
            'schoolLevels' => $schoolLevels,
            'analysisMethods' => $analysisMethods,
        ]);
    }

    /**
     * Store a newly created school district analysis.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:metaverse_properties,id',
            'analysis_radius' => 'nullable|numeric|min:0.5|max:20',
            'district_types' => 'required|array',
            'school_levels' => 'required|array',
            'analysis_method' => 'required|string',
            'include_charter' => 'nullable|boolean',
            'include_private' => 'nullable|boolean',
            'weight_factors' => 'nullable|array',
        ]);

        try {
            // Perform school district analysis
            $districtData = $this->performSchoolDistrictAnalysis($validated);

            $schoolDistrict = SchoolDistrict::create([
                'property_id' => $validated['property_id'],
                'analysis_radius' => $validated['analysis_radius'] ?? 5,
                'district_types' => $validated['district_types'],
                'school_levels' => $validated['school_levels'],
                'analysis_method' => $validated['analysis_method'],
                'include_charter' => $validated['include_charter'] ?? true,
                'include_private' => $validated['include_private'] ?? true,
                'weight_factors' => $validated['weight_factors'] ?? [],
                'district_name' => $districtData['district_name'],
                'district_type' => $districtData['district_type'],
                'overall_rating' => $districtData['overall_rating'],
                'elementary_rating' => $districtData['elementary_rating'],
                'middle_rating' => $districtData['middle_rating'],
                'high_rating' => $districtData['high_rating'],
                'nearby_schools' => $districtData['nearby_schools'],
                'school_performance' => $districtData['school_performance'],
                'demographics' => $districtData['demographics'],
                'facilities' => $districtData['facilities'],
                'metadata' => $districtData['metadata'],
                'status' => 'completed',
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء تحليل المنطقة المدرسية بنجاح',
                'school_district' => $schoolDistrict,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء تحليل المنطقة المدرسية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified school district analysis.
     */
    public function show(SchoolDistrict $schoolDistrict): \Inertia\Response
    {
        $schoolDistrict->load(['property']);

        // Get related analyses
        $relatedDistricts = SchoolDistrict::where('property_id', $schoolDistrict->property_id)
            ->where('id', '!=', $schoolDistrict->id)
            ->with('property')
            ->take(5)
            ->get();

        return Inertia::render('Geospatial/SchoolDistrict/Show', [
            'schoolDistrict' => $schoolDistrict,
            'relatedDistricts' => $relatedDistricts,
        ]);
    }

    /**
     * Show the form for editing the specified school district analysis.
     */
    public function edit(SchoolDistrict $schoolDistrict): \Inertia\Response
    {
        $districtTypes = [
            'public' => 'مدارس حكومية',
            'private' => 'مدارس خاصة',
            'charter' => 'مدارس مستقلة',
            'international' => 'مدارس دولية',
            'religious' => 'مدارس دينية',
            'specialized' => 'مدارس متخصصة',
        ];

        $schoolLevels = [
            'elementary' => 'المرحلة الابتدائية',
            'middle' => 'المرحلة المتوسطة',
            'high' => 'المرحلة الثانوية',
            'k12' => 'جميع المراحل',
        ];

        $analysisMethods = [
            'standard_algorithm' => 'الخوارزمية القياسية',
            'test_scores' => 'درجات الاختبارات',
            'parent_reviews' => 'مراجعات أولياء الأمور',
            'accreditation' => 'الاعتماد الأكاديمي',
            'graduation_rates' => 'معدلات التخرج',
        ];

        return Inertia::render('Geospatial/SchoolDistrict/Edit', [
            'schoolDistrict' => $schoolDistrict,
            'districtTypes' => $districtTypes,
            'schoolLevels' => $schoolLevels,
            'analysisMethods' => $analysisMethods,
        ]);
    }

    /**
     * Update the specified school district analysis.
     */
    public function update(Request $request, SchoolDistrict $schoolDistrict): JsonResponse
    {
        $validated = $request->validate([
            'analysis_radius' => 'nullable|numeric|min:0.5|max:20',
            'district_types' => 'required|array',
            'school_levels' => 'required|array',
            'analysis_method' => 'required|string',
            'include_charter' => 'nullable|boolean',
            'include_private' => 'nullable|boolean',
            'weight_factors' => 'nullable|array',
        ]);

        try {
            // Re-perform analysis if parameters changed
            if ($this->analysisParametersChanged($schoolDistrict, $validated)) {
                $districtData = $this->performSchoolDistrictAnalysis($validated);
                $validated['district_name'] = $districtData['district_name'];
                $validated['district_type'] = $districtData['district_type'];
                $validated['overall_rating'] = $districtData['overall_rating'];
                $validated['elementary_rating'] = $districtData['elementary_rating'];
                $validated['middle_rating'] = $districtData['middle_rating'];
                $validated['high_rating'] = $districtData['high_rating'];
                $validated['nearby_schools'] = $districtData['nearby_schools'];
                $validated['school_performance'] = $districtData['school_performance'];
                $validated['demographics'] = $districtData['demographics'];
                $validated['facilities'] = $districtData['facilities'];
                $validated['metadata'] = $districtData['metadata'];
                $validated['status'] = 'completed';
            }

            $schoolDistrict->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث تحليل المنطقة المدرسية بنجاح',
                'school_district' => $schoolDistrict,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث تحليل المنطقة المدرسية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified school district analysis.
     */
    public function destroy(SchoolDistrict $schoolDistrict): JsonResponse
    {
        try {
            $schoolDistrict->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف تحليل المنطقة المدرسية بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف تحليل المنطقة المدرسية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get school district analysis for a specific location.
     */
    public function getLocationSchoolDistrict(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'district_types' => 'nullable|array',
            'school_levels' => 'nullable|array',
            'analysis_radius' => 'nullable|numeric|min:0.5|max:20',
        ]);

        try {
            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            $districtTypes = $validated['district_types'] ?? ['all'];
            $schoolLevels = $validated['school_levels'] ?? ['all'];
            $analysisRadius = $validated['analysis_radius'] ?? 5;

            $districtData = $this->generateLocationSchoolDistrict($latitude, $longitude, $districtTypes, $schoolLevels, $analysisRadius);

            return response()->json([
                'success' => true,
                'school_district' => $districtData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تحليل المنطقة المدرسية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get school district heatmap data.
     */
    public function getSchoolDistrictHeatmap(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bounds' => 'required|array',
            'zoom_level' => 'nullable|integer|min:1|max:20',
            'grid_size' => 'nullable|integer|min:10|max:100',
            'rating_type' => 'nullable|string|in:overall,elementary,middle,high',
        ]);

        try {
            $bounds = $validated['bounds'];
            $zoomLevel = $validated['zoom_level'] ?? 12;
            $gridSize = $validated['grid_size'] ?? 50;
            $ratingType = $validated['rating_type'] ?? 'overall';

            $heatmapData = $this->generateSchoolDistrictHeatmap($bounds, $zoomLevel, $gridSize, $ratingType);

            return response()->json([
                'success' => true,
                'heatmap' => $heatmapData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب خريطة الحرارة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get school district comparison between properties.
     */
    public function getSchoolDistrictComparison(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_ids' => 'required|array|min:2|max:10',
            'property_ids.*' => 'exists:metaverse_properties,id',
            'district_types' => 'nullable|array',
            'school_levels' => 'nullable|array',
        ]);

        try {
            $propertyIds = $validated['property_ids'];
            $districtTypes = $validated['district_types'] ?? ['all'];
            $schoolLevels = $validated['school_levels'] ?? ['all'];

            $comparison = $this->performSchoolDistrictComparison($propertyIds, $districtTypes, $schoolLevels);

            return response()->json([
                'success' => true,
                'comparison' => $comparison,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مقارنة المناطق المدرسية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export school district data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'rating_range' => 'nullable|array',
            'district_types' => 'nullable|array',
            'school_levels' => 'nullable|array',
            'include_details' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareSchoolDistrictExport($validated);
            $filename = $this->generateSchoolDistrictExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات المنطقة المدرسية للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير المنطقة المدرسية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Perform school district analysis.
     */
    private function performSchoolDistrictAnalysis(array $data): array
    {
        $property = MetaverseProperty::find($data['property_id']);
        $analysisRadius = $data['analysis_radius'] ?? 5;
        $districtTypes = $data['district_types'];
        $schoolLevels = $data['school_levels'];
        $analysisMethod = $data['analysis_method'];
        $includeCharter = $data['include_charter'] ?? true;
        $includePrivate = $data['include_private'] ?? true;
        $weightFactors = $data['weight_factors'] ?? [];

        // Calculate school ratings
        $elementaryRating = in_array('elementary', $schoolLevels) ? $this->calculateElementaryRating($property, $analysisRadius) : 0;
        $middleRating = in_array('middle', $schoolLevels) ? $this->calculateMiddleRating($property, $analysisRadius) : 0;
        $highRating = in_array('high', $schoolLevels) ? $this->calculateHighRating($property, $analysisRadius) : 0;

        // Calculate overall rating
        $elementaryWeight = $weightFactors['elementary'] ?? 0.4;
        $middleWeight = $weightFactors['middle'] ?? 0.3;
        $highWeight = $weightFactors['high'] ?? 0.3;

        $overallRating = ($elementaryRating * $elementaryWeight) + ($middleRating * $middleWeight) + ($highRating * $highWeight);

        // Generate additional data
        $nearbySchools = $this->getNearbySchools($property, $districtTypes, $schoolLevels, $analysisRadius);
        $schoolPerformance = $this->getSchoolPerformance($property, $analysisRadius);
        $demographics = $this->getSchoolDemographics($property, $analysisRadius);
        $facilities = $this->getSchoolFacilities($property, $analysisRadius);

        return [
            'district_name' => $this->getDistrictName($property),
            'district_type' => $this->getDistrictType($property),
            'overall_rating' => round($overallRating, 1),
            'elementary_rating' => round($elementaryRating, 1),
            'middle_rating' => round($middleRating, 1),
            'high_rating' => round($highRating, 1),
            'nearby_schools' => $nearbySchools,
            'school_performance' => $schoolPerformance,
            'demographics' => $demographics,
            'facilities' => $facilities,
            'metadata' => [
                'analysis_method' => $analysisMethod,
                'analysis_radius' => $analysisRadius,
                'district_types' => $districtTypes,
                'school_levels' => $schoolLevels,
                'include_charter' => $includeCharter,
                'include_private' => $includePrivate,
                'weight_factors' => $weightFactors,
                'analysis_date' => now()->format('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * Calculate elementary school rating.
     */
    private function calculateElementaryRating($property, float $radius): float
    {
        // Mock implementation
        $baseRating = 7.5;
        
        // Adjust based on city education quality
        if ($property->city === 'الرياض') {
            $baseRating += 0.8;
        } elseif ($property->city === 'جدة') {
            $baseRating += 0.5;
        }

        return min(10, $baseRating);
    }

    /**
     * Calculate middle school rating.
     */
    private function calculateMiddleRating($property, float $radius): float
    {
        // Mock implementation
        $baseRating = 7.2;
        
        // Adjust based on city education quality
        if ($property->city === 'الرياض') {
            $baseRating += 0.7;
        } elseif ($property->city === 'جدة') {
            $baseRating += 0.4;
        }

        return min(10, $baseRating);
    }

    /**
     * Calculate high school rating.
     */
    private function calculateHighRating($property, float $radius): float
    {
        // Mock implementation
        $baseRating = 7.8;
        
        // Adjust based on city education quality
        if ($property->city === 'الرياض') {
            $baseRating += 0.9;
        } elseif ($property->city === 'جدة') {
            $baseRating += 0.6;
        }

        return min(10, $baseRating);
    }

    /**
     * Get district name.
     */
    private function getDistrictName($property): string
    {
        // Mock implementation
        $districts = [
            'الرياض' => ['منطقة التعليم الشمالية', 'منطقة التعليم الشرقية', 'منطقة التعليم الغربية'],
            'جدة' => ['منطقة التعليم الشمالية', 'منطقة التعليم الجنوبية', 'منطقة التعليم المركزية'],
            'الدمام' => ['منطقة التعليم الشرقية', 'منطقة التعليم الغربية', 'منطقة التعليم المركزية'],
        ];

        $cityDistricts = $districts[$property->city] ?? ['منطقة تعليمية'];
        return $cityDistricts[array_rand($cityDistricts)];
    }

    /**
     * Get district type.
     */
    private function getDistrictType($property): string
    {
        // Mock implementation
        return 'public';
    }

    /**
     * Get nearby schools.
     */
    private function getNearbySchools($property, array $districtTypes, array $schoolLevels, float $radius): array
    {
        // Mock implementation
        $schools = [];
        
        foreach ($schoolLevels as $level) {
            $schools[$level] = [
                'total_count' => rand(3, 12),
                'public_schools' => rand(2, 8),
                'private_schools' => rand(1, 6),
                'average_distance' => rand(800, 2500),
                'closest_distance' => rand(400, 1200),
                'examples' => $this->getSchoolExamples($level, 3),
            ];
        }
        
        return $schools;
    }

    /**
     * Get school performance.
     */
    private function getSchoolPerformance($property, float $radius): array
    {
        // Mock implementation
        return [
            'test_scores' => [
                'math' => rand(75, 92),
                'reading' => rand(78, 95),
                'science' => rand(72, 88),
                'writing' => rand(80, 94),
            ],
            'graduation_rate' => rand(85, 98),
            'college_readiness' => rand(70, 90),
            'student_teacher_ratio' => rand(15, 25),
            'attendance_rate' => rand(92, 98),
        ];
    }

    /**
     * Get school demographics.
     */
    private function getSchoolDemographics($property, float $radius): array
    {
        // Mock implementation
        return [
            'total_students' => rand(2000, 8000),
            'student_diversity' => rand(0.3, 0.8),
            'economic_background' => [
                'low_income' => rand(15, 35),
                'middle_income' => rand(45, 65),
                'high_income' => rand(15, 30),
            ],
            'special_education' => rand(8, 15),
            'gifted_programs' => rand(5, 12),
            'english_learners' => rand(5, 20),
        ];
    }

    /**
     * Get school facilities.
     */
    private function getSchoolFacilities($property, float $radius): array
    {
        // Mock implementation
        return [
            'library' => rand(70, 95),
            'science_labs' => rand(65, 90),
            'computer_labs' => rand(75, 95),
            'sports_facilities' => rand(60, 85),
            'cafeteria' => rand(70, 90),
            'health_services' => rand(65, 85),
            'transportation' => rand(60, 80),
            'security' => rand(75, 95),
        ];
    }

    /**
     * Get school examples.
     */
    private function getSchoolExamples(string $level, int $count): array
    {
        $examples = [
            'elementary' => ['مدرسة الأمل الابتدائية', 'مدرسة المستقبل', 'مدرسة النجاح'],
            'middle' => ['مدرسة الملك فهد المتوسطة', 'مدرسة الأمير محمد', 'مدرسة الرعاية'],
            'high' => ['مدرسة الملك عبدالعزيز الثانوية', 'مدرسة الأمير سعود', 'مدرسة العزيزية'],
        ];

        return array_slice($examples[$level] ?? [], 0, $count);
    }

    /**
     * Generate location school district.
     */
    private function generateLocationSchoolDistrict(float $latitude, float $longitude, array $districtTypes, array $schoolLevels, float $radius): array
    {
        // Mock implementation
        return [
            'location' => ['lat' => $latitude, 'lng' => $longitude],
            'analysis_radius' => $radius,
            'district_types' => $districtTypes,
            'school_levels' => $schoolLevels,
            'district_name' => 'منطقة تعليمية',
            'district_type' => 'public',
            'ratings' => [
                'overall_rating' => rand(6.5, 9.5),
                'elementary_rating' => in_array('elementary', $schoolLevels) ? rand(6.5, 9.5) : 0,
                'middle_rating' => in_array('middle', $schoolLevels) ? rand(6.5, 9.5) : 0,
                'high_rating' => in_array('high', $schoolLevels) ? rand(6.5, 9.5) : 0,
            ],
            'quality_rating' => $this->getQualityRating(rand(6.5, 9.5)),
            'nearby_schools' => $this->getNearbySchoolsByLocation($latitude, $longitude, $districtTypes, $schoolLevels, $radius),
            'performance' => [
                'test_scores' => rand(75, 92),
                'graduation_rate' => rand(85, 98),
                'college_readiness' => rand(70, 90),
            ],
        ];
    }

    /**
     * Get quality rating.
     */
    private function getQualityRating(float $rating): string
    {
        if ($rating >= 9) {
            return 'excellent';
        } elseif ($rating >= 8) {
            return 'very_good';
        } elseif ($rating >= 7) {
            return 'good';
        } elseif ($rating >= 6) {
            return 'average';
        } else {
            return 'below_average';
        }
    }

    /**
     * Get nearby schools by location.
     */
    private function getNearbySchoolsByLocation(float $latitude, float $longitude, array $districtTypes, array $schoolLevels, float $radius): array
    {
        // Mock implementation
        $schools = [];
        
        foreach ($schoolLevels as $level) {
            $schools[$level] = [
                'total_count' => rand(3, 15),
                'within_radius' => rand(2, 8),
                'average_distance' => rand(800, 2500),
                'rating' => rand(6.5, 9.5),
            ];
        }
        
        return $schools;
    }

    /**
     * Generate school district heatmap.
     */
    private function generateSchoolDistrictHeatmap(array $bounds, int $zoomLevel, int $gridSize, string $ratingType): array
    {
        // Mock implementation
        $heatmapData = [];
        
        for ($i = 0; $i < $gridSize; $i++) {
            for ($j = 0; $j < $gridSize; $j++) {
                $lat = $bounds['south'] + (($bounds['north'] - $bounds['south']) / $gridSize) * $i;
                $lng = $bounds['west'] + (($bounds['east'] - $bounds['west']) / $gridSize) * $j;
                
                $heatmapData[] = [
                    'lat' => $lat,
                    'lng' => $lng,
                    'rating' => rand(6.5, 9.5),
                    'quality_rating' => $this->getQualityRating(rand(6.5, 9.5)),
                    'school_count' => rand(2, 12),
                ];
            }
        }

        return [
            'bounds' => $bounds,
            'zoom_level' => $zoomLevel,
            'grid_size' => $gridSize,
            'rating_type' => $ratingType,
            'data_points' => $heatmapData,
            'max_rating' => max(array_column($heatmapData, 'rating')),
            'min_rating' => min(array_column($heatmapData, 'rating')),
            'average_rating' => array_sum(array_column($heatmapData, 'rating')) / count($heatmapData),
        ];
    }

    /**
     * Perform school district comparison.
     */
    private function performSchoolDistrictComparison(array $propertyIds, array $districtTypes, array $schoolLevels): array
    {
        // Mock implementation
        $comparison = [];
        
        foreach ($propertyIds as $propertyId) {
            $property = MetaverseProperty::find($propertyId);
            $comparison['property_' . $propertyId] = [
                'property_id' => $propertyId,
                'property_name' => $property->name,
                'overall_rating' => rand(6.5, 9.5),
                'elementary_rating' => in_array('elementary', $schoolLevels) ? rand(6.5, 9.5) : 0,
                'middle_rating' => in_array('middle', $schoolLevels) ? rand(6.5, 9.5) : 0,
                'high_rating' => in_array('high', $schoolLevels) ? rand(6.5, 9.5) : 0,
                'quality_rating' => $this->getQualityRating(rand(6.5, 9.5)),
            ];
        }

        // Sort by overall rating
        uasort($comparison, function ($a, $b) {
            return $b['overall_rating'] - $a['overall_rating'];
        });

        return [
            'properties' => $comparison,
            'ranking' => array_keys($comparison),
            'best_property' => array_keys($comparison)[0],
            'worst_property' => array_keys($comparison)[count($comparison) - 1],
            'average_rating' => array_sum(array_column($comparison, 'overall_rating')) / count($comparison),
        ];
    }

    /**
     * Prepare school district export data.
     */
    private function prepareSchoolDistrictExport(array $options): array
    {
        $format = $options['format'];
        $ratingRange = $options['rating_range'] ?? [0, 10];
        $districtTypes = $options['district_types'] ?? ['all'];
        $schoolLevels = $options['school_levels'] ?? ['all'];
        $includeDetails = $options['include_details'] ?? false;

        // Mock implementation
        $data = [
            'headers' => ['Property ID', 'District Name', 'Overall Rating', 'Elementary Rating', 'Middle Rating', 'High Rating', 'Quality Rating', 'Analysis Date'],
            'rows' => [
                [1, 'منطقة التعليم الشمالية', 8.5, 8.2, 8.7, 8.6, 'very_good', '2024-01-15'],
                [2, 'منطقة التعليم الشرقية', 7.8, 7.5, 8.0, 7.9, 'good', '2024-01-16'],
                [3, 'منطقة التعليم الغربية', 9.2, 9.0, 9.3, 9.1, 'excellent', '2024-01-17'],
            ],
        ];

        if ($includeDetails) {
            $data['details'] = [
                'school_breakdown' => [
                    'elementary' => ['count' => 5, 'avg_rating' => 8.2],
                    'middle' => ['count' => 3, 'avg_rating' => 8.5],
                    'high' => ['count' => 2, 'avg_rating' => 8.7],
                ],
                'performance_metrics' => [
                    'test_scores' => 85,
                    'graduation_rate' => 94,
                    'college_readiness' => 82,
                ],
            ];
        }

        return $data;
    }

    /**
     * Generate school district export filename.
     */
    private function generateSchoolDistrictExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "school_district_analysis_{$timestamp}.{$format}";
    }

    /**
     * Check if analysis parameters changed.
     */
    private function analysisParametersChanged(SchoolDistrict $schoolDistrict, array $newData): bool
    {
        return $schoolDistrict->analysis_radius !== ($newData['analysis_radius'] ?? 5) ||
               $schoolDistrict->district_types !== $newData['district_types'] ||
               $schoolDistrict->school_levels !== $newData['school_levels'] ||
               $schoolDistrict->analysis_method !== $newData['analysis_method'] ||
               $schoolDistrict->include_charter !== ($newData['include_charter'] ?? true) ||
               $schoolDistrict->include_private !== ($newData['include_private'] ?? true) ||
               $schoolDistrict->weight_factors !== ($newData['weight_factors'] ?? []);
    }

    /**
     * Get best districts.
     */
    private function getBestDistricts(): array
    {
        return [
            ['district' => 'منطقة التعليم الشمالية', 'average_rating' => 8.8, 'school_count' => 25],
            ['district' => 'منطقة التعليم المركزية', 'average_rating' => 8.5, 'school_count' => 32],
            ['district' => 'منطقة التعليم الشرقية', 'average_rating' => 8.2, 'school_count' => 28],
        ];
    }

    /**
     * Get improvement areas.
     */
    private function getImprovementAreas(): array
    {
        return [
            ['district' => 'منطقة التعليم الجنوبية', 'current_rating' => 6.8, 'potential_rating' => 7.5, 'improvements_needed' => 4],
            ['district' => 'منطقة التعليم الغربية', 'current_rating' => 7.2, 'potential_rating' => 8.0, 'improvements_needed' => 3],
            ['district' => 'منطقة التعليم الريفية', 'current_rating' => 6.5, 'potential_rating' => 7.8, 'improvements_needed' => 5],
        ];
    }
}
