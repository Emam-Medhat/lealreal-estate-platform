<?php

namespace App\Http\Controllers\Geospatial;

use App\Http\Controllers\Controller;
use App\Models\Geospatial\DemographicData;
use App\Models\Metaverse\MetaverseProperty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DemographicAnalysisController extends Controller
{
    /**
     * Display the demographic analysis dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['city', 'area_type', 'income_range', 'age_group']);
        
        // Get demographic statistics
        $stats = [
            'total_analyses' => DemographicData::count(),
            'active_analyses' => DemographicData::where('status', 'active')->count(),
            'average_population_density' => DemographicData::avg('population_density') ?? 0,
            'average_median_income' => DemographicData::avg('median_income') ?? 0,
            'top_demographic_areas' => $this->getTopDemographicAreas(),
            'emerging_demographics' => $this->getEmergingDemographics(),
        ];

        // Get recent demographic analyses
        $recentAnalyses = DemographicData::with(['property'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($analysis) {
                return [
                    'id' => $analysis->id,
                    'property_id' => $analysis->property_id,
                    'property_name' => $analysis->property?->name ?? 'Unknown',
                    'population_density' => $analysis->population_density,
                    'median_income' => $analysis->median_income,
                    'age_distribution' => $analysis->age_distribution,
                    'status' => $analysis->status,
                    'created_at' => $analysis->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get demographic categories
        $demographicCategories = [
            'population_density' => 'كثافة السكان',
            'income_analysis' => 'تحليل الدخل',
            'age_distribution' => 'توزيع الأعمار',
            'education_level' => 'مستوى التعليم',
            'employment_rate' => 'معدل التوظيف',
            'household_composition' => 'تركيبة الأسرة',
            'ethnic_diversity' => 'التنوع العرقي',
            'migration_patterns' => 'أنماط الهجرة',
        ];

        return Inertia::render('Geospatial/Demographics/Index', [
            'stats' => $stats,
            'recentAnalyses' => $recentAnalyses,
            'demographicCategories' => $demographicCategories,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new demographic analysis.
     */
    public function create(): \Inertia\Response
    {
        $properties = MetaverseProperty::select('id', 'name', 'latitude', 'longitude', 'city')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $demographicCategories = [
            'population_density' => 'كثافة السكان',
            'income_analysis' => 'تحليل الدخل',
            'age_distribution' => 'توزيع الأعمار',
            'education_level' => 'مستوى التعليم',
            'employment_rate' => 'معدل التوظيف',
            'household_composition' => 'تركيبة الأسرة',
            'ethnic_diversity' => 'التنوع العرقي',
            'migration_patterns' => 'أنماط الهجرة',
        ];

        $analysisMethods = [
            'census_data' => 'بيانات التعداد السكاني',
            'survey_data' => 'بيانات الاستبيان',
            'mobile_data' => 'بيانات الهاتف المحمول',
            'social_media' => 'وسائل التواصل الاجتماعي',
            'government_statistics' => 'إحصائيات حكومية',
        ];

        return Inertia::render('Geospatial/Demographics/Create', [
            'properties' => $properties,
            'demographicCategories' => $demographicCategories,
            'analysisMethods' => $analysisMethods,
        ]);
    }

    /**
     * Store a newly created demographic analysis.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:metaverse_properties,id',
            'analysis_radius' => 'nullable|numeric|min:0.5|max:20',
            'demographic_categories' => 'required|array',
            'analysis_method' => 'required|string',
            'data_sources' => 'nullable|array',
            'time_period' => 'nullable|string',
        ]);

        try {
            // Perform demographic analysis
            $analysisData = $this->performDemographicAnalysis($validated);

            $demographicData = DemographicData::create([
                'property_id' => $validated['property_id'],
                'analysis_radius' => $validated['analysis_radius'] ?? 5,
                'demographic_categories' => $validated['demographic_categories'],
                'analysis_method' => $validated['analysis_method'],
                'data_sources' => $validated['data_sources'] ?? [],
                'time_period' => $validated['time_period'] ?? 'current',
                'population_density' => $analysisData['population_density'],
                'median_income' => $analysisData['median_income'],
                'age_distribution' => $analysisData['age_distribution'],
                'education_level' => $analysisData['education_level'],
                'employment_rate' => $analysisData['employment_rate'],
                'household_composition' => $analysisData['household_composition'],
                'ethnic_diversity' => $analysisData['ethnic_diversity'],
                'migration_patterns' => $analysisData['migration_patterns'],
                'insights' => $analysisData['insights'],
                'trends' => $analysisData['trends'],
                'metadata' => $analysisData['metadata'],
                'status' => 'completed',
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء التحليل الديموغرافي بنجاح',
                'demographic_data' => $demographicData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء التحليل الديموغرافي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified demographic analysis.
     */
    public function show(DemographicData $demographicData): \Inertia\Response
    {
        $demographicData->load(['property']);

        // Get related analyses
        $relatedAnalyses = DemographicData::where('property_id', $demographicData->property_id)
            ->where('id', '!=', $demographicData->id)
            ->with('property')
            ->take(5)
            ->get();

        return Inertia::render('Geospatial/Demographics/Show', [
            'demographicData' => $demographicData,
            'relatedAnalyses' => $relatedAnalyses,
        ]);
    }

    /**
     * Show the form for editing the specified demographic analysis.
     */
    public function edit(DemographicData $demographicData): \Inertia\Response
    {
        $demographicCategories = [
            'population_density' => 'كثافة السكان',
            'income_analysis' => 'تحليل الدخل',
            'age_distribution' => 'توزيع الأعمار',
            'education_level' => 'مستوى التعليم',
            'employment_rate' => 'معدل التوظيف',
            'household_composition' => 'تركيبة الأسرة',
            'ethnic_diversity' => 'التنوع العرقي',
            'migration_patterns' => 'أنماط الهجرة',
        ];

        $analysisMethods = [
            'census_data' => 'بيانات التعداد السكاني',
            'survey_data' => 'بيانات الاستبيان',
            'mobile_data' => 'بيانات الهاتف المحمول',
            'social_media' => 'وسائل التواصل الاجتماعي',
            'government_statistics' => 'إحصائيات حكومية',
        ];

        return Inertia::render('Geospatial/Demographics/Edit', [
            'demographicData' => $demographicData,
            'demographicCategories' => $demographicCategories,
            'analysisMethods' => $analysisMethods,
        ]);
    }

    /**
     * Update the specified demographic analysis.
     */
    public function update(Request $request, DemographicData $demographicData): JsonResponse
    {
        $validated = $request->validate([
            'analysis_radius' => 'nullable|numeric|min:0.5|max:20',
            'demographic_categories' => 'required|array',
            'analysis_method' => 'required|string',
            'data_sources' => 'nullable|array',
            'time_period' => 'nullable|string',
        ]);

        try {
            // Re-perform analysis if parameters changed
            if ($this->analysisParametersChanged($demographicData, $validated)) {
                $analysisData = $this->performDemographicAnalysis($validated);
                $validated['population_density'] = $analysisData['population_density'];
                $validated['median_income'] = $analysisData['median_income'];
                $validated['age_distribution'] = $analysisData['age_distribution'];
                $validated['education_level'] = $analysisData['education_level'];
                $validated['employment_rate'] = $analysisData['employment_rate'];
                $validated['household_composition'] = $analysisData['household_composition'];
                $validated['ethnic_diversity'] = $analysisData['ethnic_diversity'];
                $validated['migration_patterns'] = $analysisData['migration_patterns'];
                $validated['insights'] = $analysisData['insights'];
                $validated['trends'] = $analysisData['trends'];
                $validated['metadata'] = $analysisData['metadata'];
                $validated['status'] = 'completed';
            }

            $demographicData->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث التحليل الديموغرافي بنجاح',
                'demographic_data' => $demographicData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث التحليل الديموغرافي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified demographic analysis.
     */
    public function destroy(DemographicData $demographicData): JsonResponse
    {
        try {
            $demographicData->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف التحليل الديموغرافي بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف التحليل الديموغرافي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get demographic analysis for a specific area.
     */
    public function getAreaDemographics(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.5|max:20',
            'demographic_categories' => 'nullable|array',
            'time_period' => 'nullable|string',
        ]);

        try {
            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            $radius = $validated['radius'] ?? 5;
            $demographicCategories = $validated['demographic_categories'] ?? ['all'];
            $timePeriod = $validated['time_period'] ?? 'current';

            $demographics = $this->generateAreaDemographics($latitude, $longitude, $radius, $demographicCategories, $timePeriod);

            return response()->json([
                'success' => true,
                'demographics' => $demographics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب التحليل الديموغرافي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get demographic trends over time.
     */
    public function getDemographicTrends(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.5|max:20',
            'time_period' => 'required|string|in:1y,3y,5y,10y',
            'demographic_categories' => 'nullable|array',
        ]);

        try {
            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            $radius = $validated['radius'] ?? 5;
            $timePeriod = $validated['time_period'];
            $demographicCategories = $validated['demographic_categories'] ?? ['all'];

            $trends = $this->generateDemographicTrends($latitude, $longitude, $radius, $timePeriod, $demographicCategories);

            return response()->json([
                'success' => true,
                'trends' => $trends,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب اتجاهات التركيبة السكانية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get demographic comparison between areas.
     */
    public function getDemographicComparison(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'areas' => 'required|array|min:2|max:5',
            'areas.*.latitude' => 'required|numeric|between:-90,90',
            'areas.*.longitude' => 'required|numeric|between:-180,180',
            'areas.*.radius' => 'nullable|numeric|min:0.5|max:20',
            'demographic_categories' => 'nullable|array',
        ]);

        try {
            $areas = $validated['areas'];
            $demographicCategories = $validated['demographic_categories'] ?? ['all'];

            $comparison = $this->performDemographicComparison($areas, $demographicCategories);

            return response()->json([
                'success' => true,
                'comparison' => $comparison,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مقارنة التركيبة السكانية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export demographic analysis data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json,pdf',
            'demographic_categories' => 'nullable|array',
            'date_range' => 'nullable|array',
            'include_trends' => 'nullable|boolean',
            'include_insights' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareDemographicExport($validated);
            $filename = $this->generateDemographicExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات التحليل الديموغرافي للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير التحليل الديموغرافي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Perform demographic analysis.
     */
    private function performDemographicAnalysis(array $data): array
    {
        $property = MetaverseProperty::find($data['property_id']);
        $radius = $data['analysis_radius'] ?? 5;
        $demographicCategories = $data['demographic_categories'];
        $analysisMethod = $data['analysis_method'];
        $dataSources = $data['data_sources'] ?? [];
        $timePeriod = $data['time_period'] ?? 'current';

        // Generate demographic data based on categories
        $analysisData = [];

        if (in_array('population_density', $demographicCategories)) {
            $analysisData['population_density'] = $this->calculatePopulationDensity($property, $radius);
        }

        if (in_array('income_analysis', $demographicCategories)) {
            $analysisData['median_income'] = $this->calculateMedianIncome($property, $radius);
        }

        if (in_array('age_distribution', $demographicCategories)) {
            $analysisData['age_distribution'] = $this->calculateAgeDistribution($property, $radius);
        }

        if (in_array('education_level', $demographicCategories)) {
            $analysisData['education_level'] = $this->calculateEducationLevel($property, $radius);
        }

        if (in_array('employment_rate', $demographicCategories)) {
            $analysisData['employment_rate'] = $this->calculateEmploymentRate($property, $radius);
        }

        if (in_array('household_composition', $demographicCategories)) {
            $analysisData['household_composition'] = $this->calculateHouseholdComposition($property, $radius);
        }

        if (in_array('ethnic_diversity', $demographicCategories)) {
            $analysisData['ethnic_diversity'] = $this->calculateEthnicDiversity($property, $radius);
        }

        if (in_array('migration_patterns', $demographicCategories)) {
            $analysisData['migration_patterns'] = $this->calculateMigrationPatterns($property, $radius);
        }

        // Generate insights and trends
        $analysisData['insights'] = $this->generateDemographicInsights($analysisData);
        $analysisData['trends'] = $this->generateDemographicTrendsData($analysisData);
        $analysisData['metadata'] = [
            'analysis_method' => $analysisMethod,
            'data_sources' => $dataSources,
            'time_period' => $timePeriod,
            'analysis_radius' => $radius,
            'analysis_date' => now()->format('Y-m-d H:i:s'),
        ];

        return $analysisData;
    }

    /**
     * Calculate population density.
     */
    private function calculatePopulationDensity($property, float $radius): array
    {
        // Mock implementation
        return [
            'density_per_km2' => rand(800, 3500),
            'density_level' => 'medium_high',
            'growth_rate' => rand(2, 8),
            'urbanization_level' => 'high',
        ];
    }

    /**
     * Calculate median income.
     */
    private function calculateMedianIncome($property, float $radius): array
    {
        // Mock implementation
        return [
            'median_household_income' => rand(45000, 120000),
            'income_level' => 'upper_middle',
            'income_distribution' => [
                'low_income' => rand(15, 25),
                'middle_income' => rand(45, 65),
                'high_income' => rand(15, 30),
            ],
            'income_growth_rate' => rand(3, 7),
        ];
    }

    /**
     * Calculate age distribution.
     */
    private function calculateAgeDistribution($property, float $radius): array
    {
        // Mock implementation
        return [
            'under_18' => rand(20, 30),
            '18_24' => rand(8, 15),
            '25_34' => rand(15, 25),
            '35_44' => rand(15, 20),
            '45_54' => rand(10, 18),
            '55_64' => rand(8, 15),
            '65_plus' => rand(5, 12),
            'median_age' => rand(32, 42),
            'age_diversity' => 'balanced',
        ];
    }

    /**
     * Calculate education level.
     */
    private function calculateEducationLevel($property, float $radius): array
    {
        // Mock implementation
        return [
            'less_than_high_school' => rand(5, 15),
            'high_school' => rand(20, 30),
            'some_college' => rand(15, 25),
            'bachelor_degree' => rand(25, 35),
            'graduate_degree' => rand(10, 20),
            'education_index' => rand(65, 85),
            'literacy_rate' => rand(92, 98),
        ];
    }

    /**
     * Calculate employment rate.
     */
    private function calculateEmploymentRate($property, float $radius): array
    {
        // Mock implementation
        return [
            'employment_rate' => rand(85, 95),
            'unemployment_rate' => rand(3, 8),
            'labor_force_participation' => rand(70, 85),
            'major_employers' => [
                'technology' => rand(15, 25),
                'healthcare' => rand(10, 20),
                'education' => rand(8, 15),
                'retail' => rand(12, 20),
                'government' => rand(10, 18),
            ],
            'job_growth_rate' => rand(2, 6),
        ];
    }

    /**
     * Calculate household composition.
     */
    private function calculateHouseholdComposition($property, float $radius): array
    {
        // Mock implementation
        return [
            'average_household_size' => rand(2.8, 4.2),
            'single_person_households' => rand(15, 30),
            'family_households' => rand(60, 75),
            'multi_family_households' => rand(5, 15),
            'homeownership_rate' => rand(60, 85),
            'renter_rate' => rand(15, 40),
        ];
    }

    /**
     * Calculate ethnic diversity.
     */
    private function calculateEthnicDiversity($property, float $radius): array
    {
        // Mock implementation
        return [
            'diversity_index' => rand(0.3, 0.8),
            'majority_group' => rand(45, 75),
            'minority_groups' => [
                'group_1' => rand(10, 25),
                'group_2' => rand(8, 20),
                'group_3' => rand(5, 15),
                'other' => rand(5, 12),
            ],
            'linguistic_diversity' => rand(0.2, 0.6),
            'cultural_diversity' => 'moderate_to_high',
        ];
    }

    /**
     * Calculate migration patterns.
     */
    private function calculateMigrationPatterns($property, float $radius): array
    {
        // Mock implementation
        return [
            'in_migration_rate' => rand(2, 8),
            'out_migration_rate' => rand(1, 5),
            'net_migration' => rand(1, 5),
            'migration_sources' => [
                'domestic' => rand(60, 80),
                'international' => rand(20, 40),
            ],
            'migration_trends' => 'inward',
            'population_stability' => rand(70, 90),
        ];
    }

    /**
     * Generate demographic insights.
     */
    private function generateDemographicInsights(array $analysisData): array
    {
        $insights = [];

        if (isset($analysisData['population_density'])) {
            $insights[] = [
                'category' => 'population_density',
                'insight' => 'كثافة سكانية متوسطة إلى عالية تشير على سوق عقاري نشط',
                'impact' => 'positive',
                'confidence' => 0.85,
            ];
        }

        if (isset($analysisData['median_income'])) {
            $insights[] = [
                'category' => 'income',
                'insight' => 'مستوى دخل متوسط إلى مرتفع يدعم استثمارات العقارات الفاخرة',
                'impact' => 'positive',
                'confidence' => 0.90,
            ];
        }

        if (isset($analysisData['age_distribution'])) {
            $insights[] = [
                'category' => 'age',
                'insight' => 'توزيع أعمار متوازن يوفر سوقاً مستقراً',
                'impact' => 'positive',
                'confidence' => 0.80,
            ];
        }

        return $insights;
    }

    /**
     * Generate demographic trends data.
     */
    private function generateDemographicTrendsData(array $analysisData): array
    {
        return [
            'population_trend' => 'growing',
            'income_trend' => 'increasing',
            'education_trend' => 'improving',
            'employment_trend' => 'stable',
            'diversity_trend' => 'increasing',
            'projected_changes' => [
                'population_growth' => rand(3, 8),
                'income_growth' => rand(2, 6),
                'housing_demand' => 'increasing',
                'market_maturity' => 'developing',
            ],
        ];
    }

    /**
     * Generate area demographics.
     */
    private function generateAreaDemographics(float $latitude, float $longitude, float $radius, array $demographicCategories, string $timePeriod): array
    {
        // Mock implementation
        return [
            'location' => ['lat' => $latitude, 'lng' => $longitude, 'radius' => $radius],
            'time_period' => $timePeriod,
            'demographic_categories' => $demographicCategories,
            'population_density' => [
                'density_per_km2' => rand(800, 3500),
                'density_level' => 'medium_high',
                'growth_rate' => rand(2, 8),
            ],
            'income_analysis' => [
                'median_income' => rand(45000, 120000),
                'income_level' => 'upper_middle',
                'growth_rate' => rand(3, 7),
            ],
            'age_distribution' => [
                'median_age' => rand(32, 42),
                'working_age_percentage' => rand(60, 75),
                'dependency_ratio' => rand(0.3, 0.6),
            ],
            'education_level' => [
                'education_index' => rand(65, 85),
                'higher_education_percentage' => rand(35, 55),
                'literacy_rate' => rand(92, 98),
            ],
            'employment_rate' => [
                'employment_rate' => rand(85, 95),
                'unemployment_rate' => rand(3, 8),
                'job_growth_rate' => rand(2, 6),
            ],
        ];
    }

    /**
     * Generate demographic trends.
     */
    private function generateDemographicTrends(float $latitude, float $longitude, float $radius, string $timePeriod, array $demographicCategories): array
    {
        // Mock implementation
        $years = $timePeriod === '1y' ? 1 : ($timePeriod === '3y' ? 3 : ($timePeriod === '5y' ? 5 : 10));
        
        $trends = [];
        for ($i = 0; $i < $years; $i++) {
            $year = now()->year - $years + $i + 1;
            $trends[$year] = [
                'population_density' => rand(800, 3500) + ($i * 50),
                'median_income' => rand(45000, 120000) + ($i * 2000),
                'employment_rate' => rand(85, 95) + ($i * 0.5),
                'education_index' => rand(65, 85) + ($i * 1),
            ];
        }

        return [
            'time_period' => $timePeriod,
            'trends' => $trends,
            'projections' => [
                'next_year' => [
                    'population_density' => 'increasing',
                    'median_income' => 'increasing',
                    'employment_rate' => 'stable',
                ],
            ],
        ];
    }

    /**
     * Perform demographic comparison.
     */
    private function performDemographicComparison(array $areas, array $demographicCategories): array
    {
        // Mock implementation
        $comparison = [];
        
        foreach ($areas as $index => $area) {
            $comparison['area_' . ($index + 1)] = [
                'location' => ['lat' => $area['latitude'], 'lng' => $area['longitude']],
                'radius' => $area['radius'] ?? 5,
                'population_density' => rand(800, 3500),
                'median_income' => rand(45000, 120000),
                'employment_rate' => rand(85, 95),
                'education_index' => rand(65, 85),
            ];
        }

        $comparison['analysis'] = [
            'highest_income_area' => 'area_1',
            'highest_density_area' => 'area_2',
            'best_employment_area' => 'area_1',
            'most_educated_area' => 'area_3',
            'overall_ranking' => ['area_1', 'area_3', 'area_2'],
        ];

        return $comparison;
    }

    /**
     * Prepare demographic export data.
     */
    private function prepareDemographicExport(array $options): array
    {
        $format = $options['format'];
        $demographicCategories = $options['demographic_categories'] ?? ['all'];
        $includeTrends = $options['include_trends'] ?? false;
        $includeInsights = $options['include_insights'] ?? false;

        // Mock implementation
        $data = [
            'headers' => ['Property ID', 'Population Density', 'Median Income', 'Employment Rate', 'Education Index', 'Analysis Date'],
            'rows' => [
                [1, 2500, 75000, 92, 78, '2024-01-15'],
                [2, 1800, 62000, 88, 72, '2024-01-16'],
                [3, 3200, 95000, 94, 85, '2024-01-17'],
            ],
        ];

        if ($includeTrends) {
            $data['trends'] = [
                'population_growth' => 'increasing',
                'income_growth' => 'increasing',
                'employment_stability' => 'stable',
            ];
        }

        if ($includeInsights) {
            $data['insights'] = [
                'market_potential' => 'high',
                'investment_risk' => 'low',
                'growth_prospects' => 'favorable',
            ];
        }

        return $data;
    }

    /**
     * Generate demographic export filename.
     */
    private function generateDemographicExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "demographic_analysis_{$timestamp}.{$format}";
    }

    /**
     * Check if analysis parameters changed.
     */
    private function analysisParametersChanged(DemographicData $demographicData, array $newData): bool
    {
        return $demographicData->analysis_radius !== ($newData['analysis_radius'] ?? 5) ||
               $demographicData->demographic_categories !== $newData['demographic_categories'] ||
               $demographicData->analysis_method !== $newData['analysis_method'] ||
               $demographicData->data_sources !== ($newData['data_sources'] ?? []) ||
               $demographicData->time_period !== ($newData['time_period'] ?? 'current');
    }

    /**
     * Get top demographic areas.
     */
    private function getTopDemographicAreas(): array
    {
        return [
            ['area' => 'وسط المدينة', 'average_income' => 95000, 'education_index' => 82, 'property_count' => 45],
            ['area' => 'الضاحية الشمالية', 'average_income' => 87000, 'education_index' => 78, 'property_count' => 32],
            ['area' => 'المركز التقني', 'average_income' => 105000, 'education_index' => 88, 'property_count' => 28],
        ];
    }

    /**
     * Get emerging demographics.
     */
    private function getEmergingDemographics(): array
    {
        return [
            ['area' => 'الضاحية الشرقية', 'growth_rate' => 12.5, 'income_growth' => 8.2, 'potential' => 'high'],
            ['area' => 'المنطقة الصناعية', 'growth_rate' => 9.8, 'income_growth' => 6.5, 'potential' => 'medium'],
            ['area' => 'المطور الجديد', 'growth_rate' => 15.2, 'income_growth' => 10.3, 'potential' => 'high'],
        ];
    }
}
