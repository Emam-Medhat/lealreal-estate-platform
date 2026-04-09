<?php

namespace App\Http\Controllers\Geospatial;

use App\Http\Controllers\Controller;
use App\Models\Geospatial\FloodRisk;
use App\Models\Metaverse\MetaverseProperty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FloodRiskController extends Controller
{
    /**
     * Display the flood risk dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['city', 'risk_level', 'flood_type', 'time_period']);
        
        // Get flood risk statistics
        $stats = [
            'total_assessments' => FloodRisk::count(),
            'high_risk_areas' => FloodRisk::where('risk_level', 'high')->count(),
            'average_risk_score' => FloodRisk::avg('risk_score') ?? 0,
            'safest_areas' => $this->getSafestAreas(),
            'high_risk_zones' => $this->getHighRiskZones(),
        ];

        // Get recent flood risk assessments
        $recentAssessments = FloodRisk::with(['property'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($assessment) {
                return [
                    'id' => $assessment->id,
                    'property_id' => $assessment->property_id,
                    'property_name' => $assessment->property?->name ?? 'Unknown',
                    'risk_score' => $assessment->risk_score,
                    'risk_level' => $assessment->risk_level,
                    'flood_type' => $assessment->flood_type,
                    'status' => $assessment->status,
                    'created_at' => $assessment->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get flood types
        $floodTypes = [
            'riverine' => 'فيضان الأنهار',
            'coastal' => 'فيضان ساحلي',
            'urban' => 'فيضان حضري',
            'flash' => 'فيضان مفاجئ',
            'pluvial' => 'فيضان مطري',
            'groundwater' => 'ارتفاع منسوب المياه الجوفية',
            'dam_break' => 'انهيار السدود',
            'storm_surge' => 'ارتفاع منسوب البحر',
        ];

        // Get risk levels
        $riskLevels = [
            'very_low' => 'منخفض جداً',
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'very_high' => 'مرتفع جداً',
            'extreme' => 'شديد',
        ];

        return Inertia::render('Geospatial/FloodRisk/Index', [
            'stats' => $stats,
            'recentAssessments' => $recentAssessments,
            'floodTypes' => $floodTypes,
            'riskLevels' => $riskLevels,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new flood risk assessment.
     */
    public function create(): \Inertia\Response
    {
        $properties = MetaverseProperty::select('id', 'name', 'latitude', 'longitude', 'city', 'elevation')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $floodTypes = [
            'riverine' => 'فيضان الأنهار',
            'coastal' => 'فيضان ساحلي',
            'urban' => 'فيضان حضري',
            'flash' => 'فيضان مفاجئ',
            'pluvial' => 'فيضان مطري',
            'groundwater' => 'ارتفاع منسوب المياه الجوفية',
            'dam_break' => 'انهيار السدود',
            'storm_surge' => 'ارتفاع منسوب البحر',
        ];

        $assessmentMethods = [
            'historical_data' => 'بيانات تاريخية',
            'hydrological_modeling' => 'نمذجة هيدرولوجية',
            'topographical_analysis' => 'تحليل طبوغرافي',
            'climate_projections' => 'توقعات مناخية',
            'gis_analysis' => 'تحليل نظم المعلومات الجغرافية',
        ];

        return Inertia::render('Geospatial/FloodRisk/Create', [
            'properties' => $properties,
            'floodTypes' => $floodTypes,
            'assessmentMethods' => $assessmentMethods,
        ]);
    }

    /**
     * Store a newly created flood risk assessment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:metaverse_properties,id',
            'flood_types' => 'required|array',
            'assessment_method' => 'required|string',
            'analysis_radius' => 'nullable|numeric|min:0.5|max:50',
            'include_climate_change' => 'nullable|boolean',
            'include_infrastructure' => 'nullable|boolean',
            'weight_factors' => 'nullable|array',
        ]);

        try {
            // Perform flood risk assessment
            $riskData = $this->performFloodRiskAssessment($validated);

            $floodRisk = FloodRisk::create([
                'property_id' => $validated['property_id'],
                'flood_types' => $validated['flood_types'],
                'assessment_method' => $validated['assessment_method'],
                'analysis_radius' => $validated['analysis_radius'] ?? 5,
                'include_climate_change' => $validated['include_climate_change'] ?? false,
                'include_infrastructure' => $validated['include_infrastructure'] ?? true,
                'weight_factors' => $validated['weight_factors'] ?? [],
                'risk_score' => $riskData['risk_score'],
                'risk_level' => $riskData['risk_level'],
                'flood_probability' => $riskData['flood_probability'],
                'potential_damage' => $riskData['potential_damage'],
                'vulnerability_factors' => $riskData['vulnerability_factors'],
                'mitigation_measures' => $riskData['mitigation_measures'],
                'insurance_implications' => $riskData['insurance_implications'],
                'metadata' => $riskData['metadata'],
                'status' => 'completed',
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء تقييم مخاطر الفيضانات بنجاح',
                'flood_risk' => $floodRisk,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء تقييم مخاطر الفيضانات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified flood risk assessment.
     */
    public function show(FloodRisk $floodRisk): \Inertia\Response
    {
        $floodRisk->load(['property']);

        // Get related assessments
        $relatedAssessments = FloodRisk::where('property_id', $floodRisk->property_id)
            ->where('id', '!=', $floodRisk->id)
            ->with('property')
            ->take(5)
            ->get();

        return Inertia::render('Geospatial/FloodRisk/Show', [
            'floodRisk' => $floodRisk,
            'relatedAssessments' => $relatedAssessments,
        ]);
    }

    /**
     * Show the form for editing the specified flood risk assessment.
     */
    public function edit(FloodRisk $floodRisk): \Inertia\Response
    {
        $floodTypes = [
            'riverine' => 'فيضان الأنهار',
            'coastal' => 'فيضان ساحلي',
            'urban' => 'فيضان حضري',
            'flash' => 'فيضان مفاجئ',
            'pluvial' => 'فيضان مطري',
            'groundwater' => 'ارتفاع منسوب المياه الجوفية',
            'dam_break' => 'انهيار السدود',
            'storm_surge' => 'ارتفاع منسوب البحر',
        ];

        $assessmentMethods = [
            'historical_data' => 'بيانات تاريخية',
            'hydrological_modeling' => 'نمذجة هيدرولوجية',
            'topographical_analysis' => 'تحليل طبوغرافي',
            'climate_projections' => 'توقعات مناخية',
            'gis_analysis' => 'تحليل نظم المعلومات الجغرافية',
        ];

        return Inertia::render('Geospatial/FloodRisk/Edit', [
            'floodRisk' => $floodRisk,
            'floodTypes' => $floodTypes,
            'assessmentMethods' => $assessmentMethods,
        ]);
    }

    /**
     * Update the specified flood risk assessment.
     */
    public function update(Request $request, FloodRisk $floodRisk): JsonResponse
    {
        $validated = $request->validate([
            'flood_types' => 'required|array',
            'assessment_method' => 'required|string',
            'analysis_radius' => 'nullable|numeric|min:0.5|max:50',
            'include_climate_change' => 'nullable|boolean',
            'include_infrastructure' => 'nullable|boolean',
            'weight_factors' => 'nullable|array',
        ]);

        try {
            // Re-perform assessment if parameters changed
            if ($this->assessmentParametersChanged($floodRisk, $validated)) {
                $riskData = $this->performFloodRiskAssessment($validated);
                $validated['risk_score'] = $riskData['risk_score'];
                $validated['risk_level'] = $riskData['risk_level'];
                $validated['flood_probability'] = $riskData['flood_probability'];
                $validated['potential_damage'] = $riskData['potential_damage'];
                $validated['vulnerability_factors'] = $riskData['vulnerability_factors'];
                $validated['mitigation_measures'] = $riskData['mitigation_measures'];
                $validated['insurance_implications'] = $riskData['insurance_implications'];
                $validated['metadata'] = $riskData['metadata'];
                $validated['status'] = 'completed';
            }

            $floodRisk->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث تقييم مخاطر الفيضانات بنجاح',
                'flood_risk' => $floodRisk,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث تقييم مخاطر الفيضانات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified flood risk assessment.
     */
    public function destroy(FloodRisk $floodRisk): JsonResponse
    {
        try {
            $floodRisk->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف تقييم مخاطر الفيضانات بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف تقييم مخاطر الفيضانات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get flood risk assessment for a specific location.
     */
    public function getLocationFloodRisk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'flood_types' => 'nullable|array',
            'analysis_radius' => 'nullable|numeric|min:0.5|max:50',
            'include_climate_change' => 'nullable|boolean',
        ]);

        try {
            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            $floodTypes = $validated['flood_types'] ?? ['all'];
            $analysisRadius = $validated['analysis_radius'] ?? 5;
            $includeClimateChange = $validated['include_climate_change'] ?? false;

            $riskData = $this->generateLocationFloodRisk($latitude, $longitude, $floodTypes, $analysisRadius, $includeClimateChange);

            return response()->json([
                'success' => true,
                'flood_risk' => $riskData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تقييم مخاطر الفيضانات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get flood risk heatmap data.
     */
    public function getFloodRiskHeatmap(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bounds' => 'required|array',
            'zoom_level' => 'nullable|integer|min:1|max:20',
            'grid_size' => 'nullable|integer|min:10|max:100',
            'flood_type' => 'nullable|string',
            'risk_level' => 'nullable|string',
        ]);

        try {
            $bounds = $validated['bounds'];
            $zoomLevel = $validated['zoom_level'] ?? 12;
            $gridSize = $validated['grid_size'] ?? 50;
            $floodType = $validated['flood_type'] ?? 'all';
            $riskLevel = $validated['risk_level'] ?? 'all';

            $heatmapData = $this->generateFloodRiskHeatmap($bounds, $zoomLevel, $gridSize, $floodType, $riskLevel);

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
     * Get flood risk projections.
     */
    public function getFloodRiskProjections(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'analysis_radius' => 'nullable|numeric|min:0.5|max:50',
            'projection_years' => 'required|integer|min:1|max:50',
            'climate_scenario' => 'nullable|string|in:low,medium,high',
        ]);

        try {
            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            $analysisRadius = $validated['analysis_radius'] ?? 5;
            $projectionYears = $validated['projection_years'];
            $climateScenario = $validated['climate_scenario'] ?? 'medium';

            $projections = $this->generateFloodRiskProjections($latitude, $longitude, $analysisRadius, $projectionYears, $climateScenario);

            return response()->json([
                'success' => true,
                'projections' => $projections,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب توقعات مخاطر الفيضانات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export flood risk data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'flood_types' => 'nullable|array',
            'risk_levels' => 'nullable|array',
            'include_projections' => 'nullable|boolean',
            'include_mitigation' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareFloodRiskExport($validated);
            $filename = $this->generateFloodRiskExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات مخاطر الفيضانات للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير بيانات مخاطر الفيضانات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Perform flood risk assessment.
     */
    private function performFloodRiskAssessment(array $data): array
    {
        $property = MetaverseProperty::find($data['property_id']);
        $floodTypes = $data['flood_types'];
        $assessmentMethod = $data['assessment_method'];
        $analysisRadius = $data['analysis_radius'] ?? 5;
        $includeClimateChange = $data['include_climate_change'] ?? false;
        $includeInfrastructure = $data['include_infrastructure'] ?? true;
        $weightFactors = $data['weight_factors'] ?? [];

        // Calculate risk score
        $riskScore = $this->calculateRiskScore($property, $floodTypes, $analysisRadius, $includeClimateChange);
        $riskLevel = $this->determineRiskLevel($riskScore);
        $floodProbability = $this->calculateFloodProbability($property, $floodTypes, $analysisRadius);
        $potentialDamage = $this->calculatePotentialDamage($property, $floodTypes);

        // Generate additional data
        $vulnerabilityFactors = $this->getVulnerabilityFactors($property, $analysisRadius);
        $mitigationMeasures = $this->getMitigationMeasures($riskLevel, $vulnerabilityFactors);
        $insuranceImplications = $this->getInsuranceImplications($riskLevel, $potentialDamage);

        return [
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'flood_probability' => $floodProbability,
            'potential_damage' => $potentialDamage,
            'vulnerability_factors' => $vulnerabilityFactors,
            'mitigation_measures' => $mitigationMeasures,
            'insurance_implications' => $insuranceImplications,
            'metadata' => [
                'assessment_method' => $assessmentMethod,
                'analysis_radius' => $analysisRadius,
                'flood_types' => $floodTypes,
                'include_climate_change' => $includeClimateChange,
                'include_infrastructure' => $includeInfrastructure,
                'weight_factors' => $weightFactors,
                'assessment_date' => now()->format('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * Calculate risk score.
     */
    private function calculateRiskScore($property, array $floodTypes, float $radius, bool $includeClimateChange): int
    {
        // Mock implementation - higher score = higher risk
        $baseScore = 35;
        
        // Adjust based on location
        if ($property->city === 'جدة') {
            $baseScore += 15; // Coastal city
        } elseif ($property->city === 'الرياض') {
            $baseScore += 5; // Inland city
        }

        // Adjust based on flood types
        $typeAdjustment = min(25, count($floodTypes) * 3);
        
        // Adjust for climate change
        if ($includeClimateChange) {
            $baseScore += 10;
        }

        return min(100, $baseScore + $typeAdjustment);
    }

    /**
     * Determine risk level.
     */
    private function determineRiskLevel(int $riskScore): string
    {
        if ($riskScore >= 80) {
            return 'extreme';
        } elseif ($riskScore >= 65) {
            return 'very_high';
        } elseif ($riskScore >= 50) {
            return 'high';
        } elseif ($riskScore >= 35) {
            return 'medium';
        } elseif ($riskScore >= 20) {
            return 'low';
        } else {
            return 'very_low';
        }
    }

    /**
     * Calculate flood probability.
     */
    private function calculateFloodProbability($property, array $floodTypes, float $radius): float
    {
        // Mock implementation - probability percentage
        $baseProbability = 0.15;
        
        // Adjust based on city
        if ($property->city === 'جدة') {
            $baseProbability += 0.25;
        } elseif ($property->city === 'الرياض') {
            $baseProbability += 0.05;
        }

        // Adjust based on flood types
        $typeAdjustment = min(0.3, count($floodTypes) * 0.05);
        
        return min(1.0, $baseProbability + $typeAdjustment);
    }

    /**
     * Calculate potential damage.
     */
    private function calculatePotentialDamage($property, array $floodTypes): array
    {
        // Mock implementation
        $baseDamage = $property->price * 0.3;
        
        return [
            'estimated_cost' => $baseDamage * (1 + count($floodTypes) * 0.1),
            'damage_percentage' => 30 + (count($floodTypes) * 5),
            'affected_areas' => [
                'foundation' => true,
                'ground_floor' => true,
                'electrical' => true,
                'hvac' => rand(0, 1),
                'structural' => rand(0, 1),
            ],
            'repair_time' => rand(2, 12) . ' months',
        ];
    }

    /**
     * Get vulnerability factors.
     */
    private function getVulnerabilityFactors($property, float $radius): array
    {
        // Mock implementation
        return [
            'elevation' => rand(10, 100),
            'proximity_to_water' => rand(100, 5000),
            'drainage_system' => rand(40, 90),
            'soil_type' => rand(30, 80),
            'vegetation_cover' => rand(20, 70),
            'building_age' => rand(5, 50),
            'foundation_type' => rand(50, 90),
            'flood_history' => rand(0, 10),
        ];
    }

    /**
     * Get mitigation measures.
     */
    private function getMitigationMeasures(string $riskLevel, array $vulnerabilityFactors): array
    {
        $measures = [];
        
        if ($riskLevel === 'extreme' || $riskLevel === 'very_high') {
            $measures[] = [
                'measure' => 'رفع المبنى',
                'priority' => 'critical',
                'estimated_cost' => 50000,
                'effectiveness' => 85,
            ];
        }
        
        if ($vulnerabilityFactors['drainage_system'] < 60) {
            $measures[] = [
                'measure' => 'تحسين نظام الصرف الصحي',
                'priority' => 'high',
                'estimated_cost' => 15000,
                'effectiveness' => 70,
            ];
        }
        
        if ($vulnerabilityFactors['proximity_to_water'] < 500) {
            $measures[] = [
                'measure' => 'بناء حماية ساحلية',
                'priority' => 'medium',
                'estimated_cost' => 25000,
                'effectiveness' => 75,
            ];
        }

        return $measures;
    }

    /**
     * Get insurance implications.
     */
    private function getInsuranceImplications(string $riskLevel, array $potentialDamage): array
    {
        return [
            'flood_insurance_required' => in_array($riskLevel, ['high', 'very_high', 'extreme']),
            'premium_increase' => $riskLevel === 'extreme' ? 150 : ($riskLevel === 'very_high' ? 100 : ($riskLevel === 'high' ? 50 : 20)),
            'coverage_limitations' => in_array($riskLevel, ['very_high', 'extreme']),
            'deductible_amount' => $potentialDamage['estimated_cost'] * 0.02,
            'policy_exclusions' => in_array($riskLevel, ['extreme']) ? ['gradual_damage', 'maintenance_costs'] : [],
            'recommended_coverage' => $potentialDamage['estimated_cost'] * 1.2,
        ];
    }

    /**
     * Generate location flood risk.
     */
    private function generateLocationFloodRisk(float $latitude, float $longitude, array $floodTypes, float $radius, bool $includeClimateChange): array
    {
        // Mock implementation
        return [
            'location' => ['lat' => $latitude, 'lng' => $longitude],
            'analysis_radius' => $radius,
            'flood_types' => $floodTypes,
            'include_climate_change' => $includeClimateChange,
            'risk_score' => rand(20, 80),
            'risk_level' => $this->determineRiskLevel(rand(20, 80)),
            'flood_probability' => rand(0.05, 0.4),
            'risk_rating' => $this->getRiskRating(rand(20, 80)),
            'vulnerability' => [
                'elevation' => rand(10, 100),
                'drainage' => rand(40, 90),
                'proximity_to_water' => rand(100, 5000),
            ],
        ];
    }

    /**
     * Get risk rating.
     */
    private function getRiskRating(int $score): string
    {
        if ($score >= 80) {
            return 'extreme_risk';
        } elseif ($score >= 65) {
            return 'very_high_risk';
        } elseif ($score >= 50) {
            return 'high_risk';
        } elseif ($score >= 35) {
            return 'moderate_risk';
        } elseif ($score >= 20) {
            return 'low_risk';
        } else {
            return 'very_low_risk';
        }
    }

    /**
     * Generate flood risk heatmap.
     */
    private function generateFloodRiskHeatmap(array $bounds, int $zoomLevel, int $gridSize, string $floodType, string $riskLevel): array
    {
        // Mock implementation
        $heatmapData = [];
        
        for ($i = 0; $i < $gridSize; $i++) {
            for ($j = 0; $j < $gridSize; $j++) {
                $lat = $bounds['south'] + (($bounds['north'] - $bounds['south']) / $gridSize) * $i;
                $lng = $bounds['west'] + (($bounds['east'] - $bounds['west']) / $gridSize) * $j;
                
                $riskScore = rand(20, 80);
                $heatmapData[] = [
                    'lat' => $lat,
                    'lng' => $lng,
                    'risk_score' => $riskScore,
                    'risk_level' => $this->determineRiskLevel($riskScore),
                    'risk_rating' => $this->getRiskRating($riskScore),
                    'flood_probability' => rand(0.05, 0.4),
                ];
            }
        }

        return [
            'bounds' => $bounds,
            'zoom_level' => $zoomLevel,
            'grid_size' => $gridSize,
            'flood_type' => $floodType,
            'risk_level' => $riskLevel,
            'data_points' => $heatmapData,
            'max_risk' => max(array_column($heatmapData, 'risk_score')),
            'min_risk' => min(array_column($heatmapData, 'risk_score')),
            'average_risk' => array_sum(array_column($heatmapData, 'risk_score')) / count($heatmapData),
        ];
    }

    /**
     * Generate flood risk projections.
     */
    private function generateFloodRiskProjections(float $latitude, float $longitude, float $radius, int $projectionYears, string $climateScenario): array
    {
        // Mock implementation
        $projections = [];
        $baseRisk = 35;
        
        for ($i = 0; $i < $projectionYears; $i++) {
            $year = now()->year + $i;
            $scenarioMultiplier = $climateScenario === 'high' ? 1.5 : ($climateScenario === 'low' ? 0.8 : 1.0);
            $climateIncrease = $i * 2 * $scenarioMultiplier;
            
            $projections[$year] = [
                'risk_score' => min(100, $baseRisk + $climateIncrease),
                'risk_level' => $this->determineRiskLevel(min(100, $baseRisk + $climateIncrease)),
                'flood_probability' => min(1.0, 0.15 + ($i * 0.01 * $scenarioMultiplier)),
                'projected_damage' => ($baseRisk + $climateIncrease) * 1000,
                'confidence_level' => max(50, 90 - ($i * 2)),
            ];
        }

        return [
            'location' => ['lat' => $latitude, 'lng' => $longitude],
            'projection_years' => $projectionYears,
            'climate_scenario' => $climateScenario,
            'projections' => $projections,
            'summary' => [
                'final_risk' => end($projections)['risk_score'],
                'risk_increase' => end($projections)['risk_score'] - $baseRisk,
                'trend_direction' => 'increasing',
                'critical_year' => $this->findCriticalYear($projections),
            ],
        ];
    }

    /**
     * Find critical year.
     */
    private function findCriticalYear(array $projections): int
    {
        foreach ($projections as $year => $data) {
            if ($data['risk_level'] === 'very_high' || $data['risk_level'] === 'extreme') {
                return $year;
            }
        }
        return 0;
    }

    /**
     * Prepare flood risk export data.
     */
    private function prepareFloodRiskExport(array $options): array
    {
        $format = $options['format'];
        $floodTypes = $options['flood_types'] ?? ['all'];
        $riskLevels = $options['risk_levels'] ?? ['all'];
        $includeProjections = $options['include_projections'] ?? false;
        $includeMitigation = $options['include_mitigation'] ?? false;

        // Mock implementation
        $data = [
            'headers' => ['Property ID', 'Risk Score', 'Risk Level', 'Flood Probability', 'Risk Rating', 'Assessment Date'],
            'rows' => [
                [1, 65, 'very_high', 0.25, 'very_high_risk', '2024-01-15'],
                [2, 45, 'high', 0.18, 'high_risk', '2024-01-16'],
                [3, 25, 'medium', 0.12, 'moderate_risk', '2024-01-17'],
            ],
        ];

        if ($includeProjections) {
            $data['projections'] = [
                '10_year_risk' => 78,
                '20_year_risk' => 85,
                'trend_direction' => 'increasing',
                'critical_year' => 2035,
            ];
        }

        if ($includeMitigation) {
            $data['mitigation'] = [
                'recommended_measures' => 3,
                'estimated_cost' => 75000,
                'effectiveness' => 75,
            ];
        }

        return $data;
    }

    /**
     * Generate flood risk export filename.
     */
    private function generateFloodRiskExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "flood_risk_assessment_{$timestamp}.{$format}";
    }

    /**
     * Check if assessment parameters changed.
     */
    private function assessmentParametersChanged(FloodRisk $floodRisk, array $newData): bool
    {
        return $floodRisk->flood_types !== $newData['flood_types'] ||
               $floodRisk->assessment_method !== $newData['assessment_method'] ||
               $floodRisk->analysis_radius !== ($newData['analysis_radius'] ?? 5) ||
               $floodRisk->include_climate_change !== ($newData['include_climate_change'] ?? false) ||
               $floodRisk->include_infrastructure !== ($newData['include_infrastructure'] ?? true) ||
               $floodRisk->weight_factors !== ($newData['weight_factors'] ?? []);
    }

    /**
     * Get safest areas.
     */
    private function getSafestAreas(): array
    {
        return [
            ['area' => 'الهضبة العليا', 'average_risk' => 25, 'property_count' => 45],
            ['area' => 'الضاحية الشمالية', 'average_risk' => 30, 'property_count' => 32],
            ['area' => 'المركز التجاري', 'average_risk' => 35, 'property_count' => 28],
        ];
    }

    /**
     * Get high risk zones.
     */
    private function getHighRiskZones(): array
    {
        return [
            ['area' => 'المنطقة الساحلية', 'current_risk' => 75, 'projected_risk' => 85, 'mitigation_needed' => 4],
            ['area' => 'ودي حنيفة', 'current_risk' => 68, 'projected_risk' => 78, 'mitigation_needed' => 3],
            ['area' => 'المنخفضات الشرقية', 'current_risk' => 62, 'projected_risk' => 72, 'mitigation_needed' => 2],
        ];
    }
}
