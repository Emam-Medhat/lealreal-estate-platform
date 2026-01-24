<?php

namespace App\Http\Controllers\Geospatial;

use App\Http\Controllers\Controller;
use App\Models\Geospatial\PropertyDensity;
use App\Models\Metaverse\MetaverseProperty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PropertyDensityController extends Controller
{
    /**
     * Display the property density dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['city', 'property_type', 'density_level', 'area_size']);
        
        // Get density statistics
        $stats = [
            'total_analyses' => PropertyDensity::count(),
            'high_density_areas' => PropertyDensity::where('density_level', 'high')->count(),
            'average_density' => PropertyDensity::avg('properties_per_km2') ?? 0,
            'highest_density' => PropertyDensity::max('properties_per_km2') ?? 0,
            'densest_areas' => $this->getDensestAreas(),
            'emerging_areas' => $this->getEmergingAreas(),
        ];

        // Get recent density analyses
        $recentAnalyses = PropertyDensity::with(['property'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($analysis) {
                return [
                    'id' => $analysis->id,
                    'property_id' => $analysis->property_id,
                    'property_name' => $analysis->property?->name ?? 'Unknown',
                    'properties_per_km2' => $analysis->properties_per_km2,
                    'density_level' => $analysis->density_level,
                    'area_size' => $analysis->area_size,
                    'status' => $analysis->status,
                    'created_at' => $analysis->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get density levels
        $densityLevels = [
            'very_low' => 'منخفض جداً',
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'very_high' => 'مرتفع جداً',
        ];

        return Inertia::render('Geospatial/PropertyDensity/Index', [
            'stats' => $stats,
            'recentAnalyses' => $recentAnalyses,
            'densityLevels' => $densityLevels,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new property density analysis.
     */
    public function create(): \Inertia\Response
    {
        $properties = MetaverseProperty::select('id', 'name', 'latitude', 'longitude', 'city')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $areaSizes = [
            '0.5' => '0.5 كم²',
            '1' => '1 كم²',
            '2' => '2 كم²',
            '5' => '5 كم²',
            '10' => '10 كم²',
            '20' => '20 كم²',
        ];

        $propertyTypes = [
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'mixed' => 'مختلط',
            'industrial' => 'صناعي',
            'all' => 'الكل',
        ];

        return Inertia::render('Geospatial/PropertyDensity/Create', [
            'properties' => $properties,
            'areaSizes' => $areaSizes,
            'propertyTypes' => $propertyTypes,
        ]);
    }

    /**
     * Store a newly created property density analysis.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:metaverse_properties,id',
            'area_size' => 'required|numeric|min:0.1|max:50',
            'property_types' => 'nullable|array',
            'analysis_method' => 'required|string',
            'include_vacant' => 'nullable|boolean',
            'time_period' => 'nullable|string',
        ]);

        try {
            // Perform density analysis
            $densityData = $this->performDensityAnalysis($validated);

            $propertyDensity = PropertyDensity::create([
                'property_id' => $validated['property_id'],
                'area_size' => $validated['area_size'],
                'property_types' => $validated['property_types'] ?? ['all'],
                'analysis_method' => $validated['analysis_method'],
                'include_vacant' => $validated['include_vacant'] ?? true,
                'time_period' => $validated['time_period'] ?? 'current',
                'properties_per_km2' => $densityData['properties_per_km2'],
                'total_properties' => $densityData['total_properties'],
                'occupied_properties' => $densityData['occupied_properties'],
                'vacant_properties' => $densityData['vacant_properties'],
                'density_level' => $densityData['density_level'],
                'density_distribution' => $densityData['density_distribution'],
                'growth_trend' => $densityData['growth_trend'],
                'metadata' => $densityData['metadata'],
                'status' => 'completed',
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء تحليل كثافة العقارات بنجاح',
                'property_density' => $propertyDensity,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء تحليل كثافة العقارات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified property density analysis.
     */
    public function show(PropertyDensity $propertyDensity): \Inertia\Response
    {
        $propertyDensity->load(['property']);

        // Get related analyses
        $relatedAnalyses = PropertyDensity::where('property_id', $propertyDensity->property_id)
            ->where('id', '!=', $propertyDensity->id)
            ->with('property')
            ->take(5)
            ->get();

        return Inertia::render('Geospatial/PropertyDensity/Show', [
            'propertyDensity' => $propertyDensity,
            'relatedAnalyses' => $relatedAnalyses,
        ]);
    }

    /**
     * Show the form for editing the specified property density analysis.
     */
    public function edit(PropertyDensity $propertyDensity): \Inertia\Response
    {
        $areaSizes = [
            '0.5' => '0.5 كم²',
            '1' => '1 كم²',
            '2' => '2 كم²',
            '5' => '5 كم²',
            '10' => '10 كم²',
            '20' => '20 كم²',
        ];

        $propertyTypes = [
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'mixed' => 'مختلط',
            'industrial' => 'صناعي',
            'all' => 'الكل',
        ];

        return Inertia::render('Geospatial/PropertyDensity/Edit', [
            'propertyDensity' => $propertyDensity,
            'areaSizes' => $areaSizes,
            'propertyTypes' => $propertyTypes,
        ]);
    }

    /**
     * Update the specified property density analysis.
     */
    public function update(Request $request, PropertyDensity $propertyDensity): JsonResponse
    {
        $validated = $request->validate([
            'area_size' => 'required|numeric|min:0.1|max:50',
            'property_types' => 'nullable|array',
            'analysis_method' => 'required|string',
            'include_vacant' => 'nullable|boolean',
            'time_period' => 'nullable|string',
        ]);

        try {
            // Re-perform analysis if parameters changed
            if ($this->analysisParametersChanged($propertyDensity, $validated)) {
                $densityData = $this->performDensityAnalysis($validated);
                $validated['properties_per_km2'] = $densityData['properties_per_km2'];
                $validated['total_properties'] = $densityData['total_properties'];
                $validated['occupied_properties'] = $densityData['occupied_properties'];
                $validated['vacant_properties'] = $densityData['vacant_properties'];
                $validated['density_level'] = $densityData['density_level'];
                $validated['density_distribution'] = $densityData['density_distribution'];
                $validated['growth_trend'] = $densityData['growth_trend'];
                $validated['metadata'] = $densityData['metadata'];
                $validated['status'] = 'completed';
            }

            $propertyDensity->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث تحليل كثافة العقارات بنجاح',
                'property_density' => $propertyDensity,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث تحليل كثافة العقارات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified property density analysis.
     */
    public function destroy(PropertyDensity $propertyDensity): JsonResponse
    {
        try {
            $propertyDensity->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف تحليل كثافة العقارات بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف تحليل كثافة العقارات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get density analysis for a specific area.
     */
    public function getAreaDensity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'area_size' => 'nullable|numeric|min:0.1|max:50',
            'property_types' => 'nullable|array',
            'include_vacant' => 'nullable|boolean',
        ]);

        try {
            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            $areaSize = $validated['area_size'] ?? 5;
            $propertyTypes = $validated['property_types'] ?? ['all'];
            $includeVacant = $validated['include_vacant'] ?? true;

            $densityData = $this->generateAreaDensity($latitude, $longitude, $areaSize, $propertyTypes, $includeVacant);

            return response()->json([
                'success' => true,
                'density' => $densityData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تحليل الكثافة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get density heatmap data.
     */
    public function getDensityHeatmap(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bounds' => 'required|array',
            'zoom_level' => 'nullable|integer|min:1|max:20',
            'property_types' => 'nullable|array',
            'grid_size' => 'nullable|integer|min:5|max:50',
        ]);

        try {
            $bounds = $validated['bounds'];
            $zoomLevel = $validated['zoom_level'] ?? 10;
            $propertyTypes = $validated['property_types'] ?? ['all'];
            $gridSize = $validated['grid_size'] ?? 20;

            $heatmapData = $this->generateDensityHeatmap($bounds, $zoomLevel, $propertyTypes, $gridSize);

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
     * Get density trends over time.
     */
    public function getDensityTrends(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'area_size' => 'nullable|numeric|min:0.1|max:50',
            'time_period' => 'required|string|in:1y,3y,5y,10y',
            'property_types' => 'nullable|array',
        ]);

        try {
            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            $areaSize = $validated['area_size'] ?? 5;
            $timePeriod = $validated['time_period'];
            $propertyTypes = $validated['property_types'] ?? ['all'];

            $trends = $this->generateDensityTrends($latitude, $longitude, $areaSize, $timePeriod, $propertyTypes);

            return response()->json([
                'success' => true,
                'trends' => $trends,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب اتجاهات الكثافة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get density comparison between areas.
     */
    public function getDensityComparison(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'areas' => 'required|array|min:2|max:5',
            'areas.*.latitude' => 'required|numeric|between:-90,90',
            'areas.*.longitude' => 'required|numeric|between:-180,180',
            'areas.*.area_size' => 'nullable|numeric|min:0.1|max:50',
            'property_types' => 'nullable|array',
        ]);

        try {
            $areas = $validated['areas'];
            $propertyTypes = $validated['property_types'] ?? ['all'];

            $comparison = $this->performDensityComparison($areas, $propertyTypes);

            return response()->json([
                'success' => true,
                'comparison' => $comparison,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مقارنة الكثافة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export density analysis data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json,kml',
            'property_types' => 'nullable|array',
            'density_range' => 'nullable|array',
            'include_heatmap' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareDensityExport($validated);
            $filename = $this->generateDensityExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات كثافة العقارات للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير كثافة العقارات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Perform density analysis.
     */
    private function performDensityAnalysis(array $data): array
    {
        $property = MetaverseProperty::find($data['property_id']);
        $areaSize = $data['area_size'];
        $propertyTypes = $data['property_types'] ?? ['all'];
        $analysisMethod = $data['analysis_method'];
        $includeVacant = $data['include_vacant'] ?? true;
        $timePeriod = $data['time_period'] ?? 'current';

        // Mock implementation - would integrate with real GIS data
        $totalProperties = rand(50, 500);
        $occupiedProperties = rand(40, 450);
        $vacantProperties = $totalProperties - $occupiedProperties;
        $propertiesPerKm2 = $totalProperties / $areaSize;

        // Determine density level
        $densityLevel = $this->determineDensityLevel($propertiesPerKm2);

        // Generate density distribution
        $densityDistribution = $this->generateDensityDistribution($totalProperties, $areaSize);

        // Generate growth trend
        $growthTrend = $this->generateGrowthTrend($propertiesPerKm2, $timePeriod);

        return [
            'properties_per_km2' => $propertiesPerKm2,
            'total_properties' => $totalProperties,
            'occupied_properties' => $occupiedProperties,
            'vacant_properties' => $vacantProperties,
            'density_level' => $densityLevel,
            'density_distribution' => $densityDistribution,
            'growth_trend' => $growthTrend,
            'metadata' => [
                'analysis_method' => $analysisMethod,
                'property_types' => $propertyTypes,
                'include_vacant' => $includeVacant,
                'time_period' => $timePeriod,
                'area_size' => $areaSize,
                'analysis_date' => now()->format('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * Determine density level.
     */
    private function determineDensityLevel(float $propertiesPerKm2): string
    {
        if ($propertiesPerKm2 < 50) {
            return 'very_low';
        } elseif ($propertiesPerKm2 < 100) {
            return 'low';
        } elseif ($propertiesPerKm2 < 200) {
            return 'medium';
        } elseif ($propertiesPerKm2 < 400) {
            return 'high';
        } else {
            return 'very_high';
        }
    }

    /**
     * Generate density distribution.
     */
    private function generateDensityDistribution(int $totalProperties, float $areaSize): array
    {
        return [
            'residential' => rand(40, 70),
            'commercial' => rand(15, 35),
            'mixed' => rand(5, 20),
            'industrial' => rand(5, 15),
            'other' => rand(2, 10),
            'property_types_distribution' => [
                'apartments' => rand(30, 60),
                'villas' => rand(10, 25),
                'townhouses' => rand(5, 20),
                'offices' => rand(5, 15),
                'retail' => rand(8, 20),
                'warehouses' => rand(2, 10),
            ],
        ];
    }

    /**
     * Generate growth trend.
     */
    private function generateGrowthTrend(float $currentDensity, string $timePeriod): array
    {
        $growthRate = rand(-5, 15);
        $projectedDensity = $currentDensity * (1 + ($growthRate / 100));

        return [
            'current_density' => $currentDensity,
            'growth_rate' => $growthRate,
            'projected_density' => $projectedDensity,
            'trend_direction' => $growthRate > 0 ? 'increasing' : ($growthRate < 0 ? 'decreasing' : 'stable'),
            'time_period' => $timePeriod,
            'confidence_level' => rand(70, 95),
        ];
    }

    /**
     * Generate area density.
     */
    private function generateAreaDensity(float $latitude, float $longitude, float $areaSize, array $propertyTypes, bool $includeVacant): array
    {
        // Mock implementation
        $totalProperties = rand(50, 500);
        $occupiedProperties = rand(40, 450);
        $vacantProperties = $includeVacant ? ($totalProperties - $occupiedProperties) : 0;
        $propertiesPerKm2 = $totalProperties / $areaSize;

        return [
            'location' => ['lat' => $latitude, 'lng' => $longitude],
            'area_size' => $areaSize,
            'property_types' => $propertyTypes,
            'include_vacant' => $includeVacant,
            'properties_per_km2' => $propertiesPerKm2,
            'total_properties' => $totalProperties,
            'occupied_properties' => $occupiedProperties,
            'vacant_properties' => $vacantProperties,
            'density_level' => $this->determineDensityLevel($propertiesPerKm2),
            'occupancy_rate' => $totalProperties > 0 ? ($occupiedProperties / $totalProperties) * 100 : 0,
            'property_type_distribution' => [
                'residential' => rand(40, 70),
                'commercial' => rand(15, 35),
                'mixed' => rand(5, 20),
                'industrial' => rand(5, 15),
            ],
        ];
    }

    /**
     * Generate density heatmap.
     */
    private function generateDensityHeatmap(array $bounds, int $zoomLevel, array $propertyTypes, int $gridSize): array
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
                    'density' => rand(10, 500),
                    'properties_count' => rand(5, 50),
                    'intensity' => rand(1, 100),
                ];
            }
        }

        return [
            'bounds' => $bounds,
            'zoom_level' => $zoomLevel,
            'grid_size' => $gridSize,
            'property_types' => $propertyTypes,
            'data_points' => $heatmapData,
            'max_density' => max(array_column($heatmapData, 'density')),
            'min_density' => min(array_column($heatmapData, 'density')),
            'average_density' => array_sum(array_column($heatmapData, 'density')) / count($heatmapData),
        ];
    }

    /**
     * Generate density trends.
     */
    private function generateDensityTrends(float $latitude, float $longitude, float $areaSize, string $timePeriod, array $propertyTypes): array
    {
        $years = $timePeriod === '1y' ? 1 : ($timePeriod === '3y' ? 3 : ($timePeriod === '5y' ? 5 : 10));
        
        $trends = [];
        $baseDensity = rand(100, 300);
        
        for ($i = 0; $i < $years; $i++) {
            $year = now()->year - $years + $i + 1;
            $growth = rand(-10, 20);
            $density = $baseDensity + ($growth * $i);
            
            $trends[$year] = [
                'properties_per_km2' => $density,
                'total_properties' => $density * $areaSize,
                'growth_rate' => $growth,
                'density_level' => $this->determineDensityLevel($density),
            ];
        }

        return [
            'location' => ['lat' => $latitude, 'lng' => $longitude],
            'area_size' => $areaSize,
            'time_period' => $timePeriod,
            'property_types' => $propertyTypes,
            'trends' => $trends,
            'projection' => [
                'next_year' => $baseDensity + rand(5, 15),
                'five_years' => $baseDensity + rand(20, 50),
                'confidence_level' => rand(70, 90),
            ],
        ];
    }

    /**
     * Perform density comparison.
     */
    private function performDensityComparison(array $areas, array $propertyTypes): array
    {
        // Mock implementation
        $comparison = [];
        
        foreach ($areas as $index => $area) {
            $areaSize = $area['area_size'] ?? 5;
            $totalProperties = rand(50, 500);
            $propertiesPerKm2 = $totalProperties / $areaSize;
            
            $comparison['area_' . ($index + 1)] = [
                'location' => ['lat' => $area['latitude'], 'lng' => $area['longitude']],
                'area_size' => $areaSize,
                'properties_per_km2' => $propertiesPerKm2,
                'total_properties' => $totalProperties,
                'density_level' => $this->determineDensityLevel($propertiesPerKm2),
                'occupancy_rate' => rand(75, 95),
            ];
        }

        $comparison['analysis'] = [
            'highest_density_area' => 'area_1',
            'lowest_density_area' => 'area_3',
            'average_density' => array_sum(array_column($comparison, 'properties_per_km2')) / count($comparison),
            'density_variance' => 'medium',
            'ranking' => ['area_1', 'area_2', 'area_3'],
        ];

        return $comparison;
    }

    /**
     * Prepare density export data.
     */
    private function prepareDensityExport(array $options): array
    {
        $format = $options['format'];
        $propertyTypes = $options['property_types'] ?? ['all'];
        $densityRange = $options['density_range'] ?? [0, 1000];
        $includeHeatmap = $options['include_heatmap'] ?? false;

        // Mock implementation
        $data = [
            'headers' => ['Property ID', 'Area Size', 'Properties per km²', 'Density Level', 'Total Properties', 'Occupancy Rate', 'Analysis Date'],
            'rows' => [
                [1, 5, 250, 'high', 1250, 92, '2024-01-15'],
                [2, 10, 180, 'medium', 1800, 88, '2024-01-16'],
                [3, 2, 450, 'very_high', 900, 95, '2024-01-17'],
            ],
        ];

        if ($includeHeatmap) {
            $data['heatmap_data'] = [
                'bounds' => ['north' => 24.8, 'south' => 24.6, 'east' => 46.8, 'west' => 46.6],
                'grid_size' => 20,
                'data_points' => rand(100, 400),
            ];
        }

        return $data;
    }

    /**
     * Generate density export filename.
     */
    private function generateDensityExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "property_density_analysis_{$timestamp}.{$format}";
    }

    /**
     * Check if analysis parameters changed.
     */
    private function analysisParametersChanged(PropertyDensity $propertyDensity, array $newData): bool
    {
        return $propertyDensity->area_size !== $newData['area_size'] ||
               $propertyDensity->property_types !== ($newData['property_types'] ?? ['all']) ||
               $propertyDensity->analysis_method !== $newData['analysis_method'] ||
               $propertyDensity->include_vacant !== ($newData['include_vacant'] ?? true) ||
               $propertyDensity->time_period !== ($newData['time_period'] ?? 'current');
    }

    /**
     * Get densest areas.
     */
    private function getDensestAreas(): array
    {
        return [
            ['area' => 'وسط المدينة', 'density' => 450, 'properties' => 2250, 'area_size' => 5],
            ['area' => 'المركز التجاري', 'density' => 380, 'properties' => 1900, 'area_size' => 5],
            ['area' => 'الضاحية الشمالية', 'density' => 320, 'properties' => 1600, 'area_size' => 5],
        ];
    }

    /**
     * Get emerging areas.
     */
    private function getEmergingAreas(): array
    {
        return [
            ['area' => 'الضاحية الشرقية', 'growth_rate' => 15.2, 'current_density' => 180, 'potential' => 'high'],
            ['area' => 'المطور الجديد', 'growth_rate' => 12.8, 'current_density' => 150, 'potential' => 'medium'],
            ['area' => 'المنطقة الصناعية', 'growth_rate' => 8.5, 'current_density' => 120, 'potential' => 'medium'],
        ];
    }
}
