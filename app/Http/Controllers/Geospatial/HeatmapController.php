<?php

namespace App\Http\Controllers\Geospatial;

use App\Http\Controllers\Controller;
use App\Models\Geospatial\PropertyHeatmap;
use App\Models\Metaverse\MetaverseProperty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HeatmapController extends Controller
{
    /**
     * Display the heatmap dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['city', 'property_type', 'price_range', 'heatmap_type']);
        
        // Get heatmap statistics
        $stats = [
            'total_heatmaps' => PropertyHeatmap::count(),
            'active_heatmaps' => PropertyHeatmap::where('status', 'active')->count(),
            'highest_price_area' => $this->getHighestPriceArea(),
            'lowest_price_area' => $this->getLowestPriceArea(),
            'average_price' => $this->getAveragePrice(),
            'price_variance' => $this->getPriceVariance(),
        ];

        // Get recent heatmaps
        $recentHeatmaps = PropertyHeatmap::with(['property'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($heatmap) {
                return [
                    'id' => $heatmap->id,
                    'property_id' => $heatmap->property_id,
                    'property_name' => $heatmap->property?->name ?? 'Unknown',
                    'heatmap_type' => $heatmap->heatmap_type,
                    'intensity' => $heatmap->intensity,
                    'status' => $heatmap->status,
                    'created_at' => $heatmap->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get heatmap types
        $heatmapTypes = [
            'price_density' => 'كثافة الأسعار',
            'price_appreciation' => 'ارتفاع الأسعار',
            'investment_hotspot' => 'نقاط الاستثمار الساخنة',
            'risk_assessment' => 'تقييم المخاطر',
            'market_activity' => 'نشاط السوق',
            'demand_supply' => 'العرض والطلب',
            'accessibility' => 'سهولة الوصول',
            'development_potential' => 'إمكانية التطوير',
        ];

        return Inertia::render('Geospatial/Heatmap/Index', [
            'stats' => $stats,
            'recentHeatmaps' => $recentHeatmaps,
            'heatmapTypes' => $heatmapTypes,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new heatmap.
     */
    public function create(): \Inertia\Response
    {
        $properties = MetaverseProperty::select('id', 'name', 'latitude', 'longitude', 'city', 'price')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $heatmapTypes = [
            'price_density' => 'كثافة الأسعار',
            'price_appreciation' => 'ارتفاع الأسعار',
            'investment_hotspot' => 'نقاط الاستثمار الساخنة',
            'risk_assessment' => 'تقييم المخاطر',
            'market_activity' => 'نشاط السوق',
            'demand_supply' => 'العرض والطلب',
            'accessibility' => 'سهولة الوصول',
            'development_potential' => 'إمكانية التطوير',
        ];

        $intensityLevels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'very_high' => 'مرتفع جداً',
        ];

        return Inertia::render('Geospatial/Heatmap/Create', [
            'properties' => $properties,
            'heatmapTypes' => $heatmapTypes,
            'intensityLevels' => $intensityLevels,
        ]);
    }

    /**
     * Store a newly created heatmap.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:metaverse_properties,id',
            'heatmap_type' => 'required|string',
            'bounds' => 'nullable|array',
            'zoom_level' => 'nullable|integer|min:1|max:20',
            'intensity' => 'required|string',
            'color_scheme' => 'nullable|string',
            'data_points' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            // Generate heatmap data
            $heatmapData = $this->generateHeatmapData($validated);

            $heatmap = PropertyHeatmap::create([
                'property_id' => $validated['property_id'],
                'heatmap_type' => $validated['heatmap_type'],
                'bounds' => $validated['bounds'] ?? [],
                'zoom_level' => $validated['zoom_level'] ?? 10,
                'intensity' => $validated['intensity'],
                'color_scheme' => $validated['color_scheme'] ?? 'default',
                'data_points' => $heatmapData['data_points'],
                'metadata' => $heatmapData['metadata'],
                'status' => 'active',
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء خريطة الحرارة بنجاح',
                'heatmap' => $heatmap,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء خريطة الحرارة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified heatmap.
     */
    public function show(PropertyHeatmap $heatmap): \Inertia\Response
    {
        $heatmap->load(['property']);

        // Get related heatmaps
        $relatedHeatmaps = PropertyHeatmap::where('property_id', $heatmap->property_id)
            ->where('id', '!=', $heatmap->id)
            ->with('property')
            ->take(5)
            ->get();

        return Inertia::render('Geospatial/Heatmap/Show', [
            'heatmap' => $heatmap,
            'relatedHeatmaps' => $relatedHeatmaps,
        ]);
    }

    /**
     * Show the form for editing the specified heatmap.
     */
    public function edit(PropertyHeatmap $heatmap): \Inertia\Response
    {
        $heatmapTypes = [
            'price_density' => 'كثافة الأسعار',
            'price_appreciation' => 'ارتفاع الأسعار',
            'investment_hotspot' => 'نقاط الاستثمار الساخنة',
            'risk_assessment' => 'تقييم المخاطر',
            'market_activity' => 'نشاط السوق',
            'demand_supply' => 'العرض والطلب',
            'accessibility' => 'سهولة الوصول',
            'development_potential' => 'إمكانية التطوير',
        ];

        $intensityLevels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'very_high' => 'مرتفع جداً',
        ];

        return Inertia::render('Geospatial/Heatmap/Edit', [
            'heatmap' => $heatmap,
            'heatmapTypes' => $heatmapTypes,
            'intensityLevels' => $intensityLevels,
        ]);
    }

    /**
     * Update the specified heatmap.
     */
    public function update(Request $request, PropertyHeatmap $heatmap): JsonResponse
    {
        $validated = $request->validate([
            'heatmap_type' => 'required|string',
            'bounds' => 'nullable|array',
            'zoom_level' => 'nullable|integer|min:1|max:20',
            'intensity' => 'required|string',
            'color_scheme' => 'nullable|string',
            'data_points' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            // Regenerate heatmap data if parameters changed
            if ($this->heatmapParametersChanged($heatmap, $validated)) {
                $heatmapData = $this->generateHeatmapData($validated);
                $validated['data_points'] = $heatmapData['data_points'];
                $validated['metadata'] = $heatmapData['metadata'];
            }

            $heatmap->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث خريطة الحرارة بنجاح',
                'heatmap' => $heatmap,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث خريطة الحرارة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified heatmap.
     */
    public function destroy(PropertyHeatmap $heatmap): JsonResponse
    {
        try {
            $heatmap->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف خريطة الحرارة بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف خريطة الحرارة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get heatmap data for visualization.
     */
    public function getHeatmapData(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'heatmap_type' => 'required|string',
            'bounds' => 'nullable|array',
            'zoom_level' => 'nullable|integer|min:1|max:20',
            'property_type' => 'nullable|string',
            'price_range' => 'nullable|array',
        ]);

        try {
            $cacheKey = "heatmap_data_" . md5(json_encode($validated));
            
            $data = Cache::remember($cacheKey, 3600, function () use ($validated) {
                return $this->generateHeatmapVisualization($validated);
            });

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات خريطة الحرارة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get heatmap statistics for a specific area.
     */
    public function getHeatmapStats(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bounds' => 'required|array',
            'heatmap_type' => 'required|string',
            'zoom_level' => 'nullable|integer|min:1|max:20',
        ]);

        try {
            $stats = $this->calculateHeatmapStatistics($validated);

            return response()->json([
                'success' => true,
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حساب إحصائيات خريطة الحرارة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export heatmap data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'heatmap_type' => 'required|string',
            'format' => 'required|in:csv,xlsx,json,kml',
            'bounds' => 'nullable|array',
            'include_metadata' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareHeatmapExport($validated);
            $filename = $this->generateHeatmapExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات خريطة الحرارة للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير خريطة الحرارة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Compare multiple heatmaps.
     */
    public function compare(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'heatmap_ids' => 'required|array|min:2',
            'heatmap_ids.*' => 'exists:property_heatmaps,id',
            'comparison_type' => 'required|in:overlay,side_by_side,statistical',
        ]);

        try {
            $comparison = $this->performHeatmapComparison($validated);

            return response()->json([
                'success' => true,
                'comparison' => $comparison,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مقارنة خرائط الحرارة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate heatmap data based on type and parameters.
     */
    private function generateHeatmapData(array $data): array
    {
        $heatmapType = $data['heatmap_type'];
        $bounds = $data['bounds'] ?? [];
        $zoomLevel = $data['zoom_level'] ?? 10;

        switch ($heatmapType) {
            case 'price_density':
                return $this->generatePriceDensityHeatmap($bounds, $zoomLevel);
            case 'price_appreciation':
                return $this->generatePriceAppreciationHeatmap($bounds, $zoomLevel);
            case 'investment_hotspot':
                return $this->generateInvestmentHotspotHeatmap($bounds, $zoomLevel);
            case 'risk_assessment':
                return $this->generateRiskAssessmentHeatmap($bounds, $zoomLevel);
            case 'market_activity':
                return $this->generateMarketActivityHeatmap($bounds, $zoomLevel);
            case 'demand_supply':
                return $this->generateDemandSupplyHeatmap($bounds, $zoomLevel);
            case 'accessibility':
                return $this->generateAccessibilityHeatmap($bounds, $zoomLevel);
            case 'development_potential':
                return $this->generateDevelopmentPotentialHeatmap($bounds, $zoomLevel);
            default:
                throw new \InvalidArgumentException('نوع خريطة الحرارة غير مدعوم');
        }
    }

    /**
     * Generate price density heatmap.
     */
    private function generatePriceDensityHeatmap(array $bounds, int $zoomLevel): array
    {
        // Mock implementation - would integrate with real property data
        $dataPoints = [];
        for ($i = 0; $i < 100; $i++) {
            $dataPoints[] = [
                'lat' => 24.7136 + (rand(-100, 100) / 10000),
                'lng' => 46.6753 + (rand(-100, 100) / 10000),
                'value' => rand(200000, 1500000),
                'intensity' => rand(1, 100),
            ];
        }

        return [
            'data_points' => $dataPoints,
            'metadata' => [
                'min_value' => 200000,
                'max_value' => 1500000,
                'average_value' => 650000,
                'data_points_count' => count($dataPoints),
                'generation_method' => 'price_density_analysis',
            ],
        ];
    }

    /**
     * Generate price appreciation heatmap.
     */
    private function generatePriceAppreciationHeatmap(array $bounds, int $zoomLevel): array
    {
        // Mock implementation
        $dataPoints = [];
        for ($i = 0; $i < 100; $i++) {
            $dataPoints[] = [
                'lat' => 24.7136 + (rand(-100, 100) / 10000),
                'lng' => 46.6753 + (rand(-100, 100) / 10000),
                'value' => rand(-5, 25), // Percentage appreciation
                'intensity' => rand(1, 100),
            ];
        }

        return [
            'data_points' => $dataPoints,
            'metadata' => [
                'min_value' => -5,
                'max_value' => 25,
                'average_value' => 8.5,
                'data_points_count' => count($dataPoints),
                'time_period' => '12_months',
            ],
        ];
    }

    /**
     * Generate investment hotspot heatmap.
     */
    private function generateInvestmentHotspotHeatmap(array $bounds, int $zoomLevel): array
    {
        // Mock implementation
        $dataPoints = [];
        for ($i = 0; $i < 50; $i++) {
            $dataPoints[] = [
                'lat' => 24.7136 + (rand(-100, 100) / 10000),
                'lng' => 46.6753 + (rand(-100, 100) / 10000),
                'value' => rand(60, 95), // Investment score
                'intensity' => rand(1, 100),
            ];
        }

        return [
            'data_points' => $dataPoints,
            'metadata' => [
                'min_value' => 60,
                'max_value' => 95,
                'average_value' => 78,
                'data_points_count' => count($dataPoints),
                'hotspot_threshold' => 80,
            ],
        ];
    }

    /**
     * Generate risk assessment heatmap.
     */
    private function generateRiskAssessmentHeatmap(array $bounds, int $zoomLevel): array
    {
        // Mock implementation
        $dataPoints = [];
        for ($i = 0; $i < 100; $i++) {
            $dataPoints[] = [
                'lat' => 24.7136 + (rand(-100, 100) / 10000),
                'lng' => 46.6753 + (rand(-100, 100) / 10000),
                'value' => rand(10, 90), // Risk score (lower is better)
                'intensity' => rand(1, 100),
            ];
        }

        return [
            'data_points' => $dataPoints,
            'metadata' => [
                'min_value' => 10,
                'max_value' => 90,
                'average_value' => 45,
                'data_points_count' => count($dataPoints),
                'risk_categories' => ['low', 'medium', 'high'],
            ],
        ];
    }

    /**
     * Generate market activity heatmap.
     */
    private function generateMarketActivityHeatmap(array $bounds, int $zoomLevel): array
    {
        // Mock implementation
        $dataPoints = [];
        for ($i = 0; $i < 100; $i++) {
            $dataPoints[] = [
                'lat' => 24.7136 + (rand(-100, 100) / 10000),
                'lng' => 46.6753 + (rand(-100, 100) / 10000),
                'value' => rand(5, 50), // Number of transactions
                'intensity' => rand(1, 100),
            ];
        }

        return [
            'data_points' => $dataPoints,
            'metadata' => [
                'min_value' => 5,
                'max_value' => 50,
                'average_value' => 22,
                'data_points_count' => count($dataPoints),
                'activity_period' => '30_days',
            ],
        ];
    }

    /**
     * Generate demand supply heatmap.
     */
    private function generateDemandSupplyHeatmap(array $bounds, int $zoomLevel): array
    {
        // Mock implementation
        $dataPoints = [];
        for ($i = 0; $i < 100; $i++) {
            $dataPoints[] = [
                'lat' => 24.7136 + (rand(-100, 100) / 10000),
                'lng' => 46.6753 + (rand(-100, 100) / 10000),
                'value' => rand(0.3, 2.5), // Demand/supply ratio
                'intensity' => rand(1, 100),
            ];
        }

        return [
            'data_points' => $dataPoints,
            'metadata' => [
                'min_value' => 0.3,
                'max_value' => 2.5,
                'average_value' => 1.2,
                'data_points_count' => count($dataPoints),
                'equilibrium_ratio' => 1.0,
            ],
        ];
    }

    /**
     * Generate accessibility heatmap.
     */
    private function generateAccessibilityHeatmap(array $bounds, int $zoomLevel): array
    {
        // Mock implementation
        $dataPoints = [];
        for ($i = 0; $i < 100; $i++) {
            $dataPoints[] = [
                'lat' => 24.7136 + (rand(-100, 100) / 10000),
                'lng' => 46.6753 + (rand(-100, 100) / 10000),
                'value' => rand(30, 95), // Accessibility score
                'intensity' => rand(1, 100),
            ];
        }

        return [
            'data_points' => $dataPoints,
            'metadata' => [
                'min_value' => 30,
                'max_value' => 95,
                'average_value' => 68,
                'data_points_count' => count($dataPoints),
                'accessibility_factors' => ['transit', 'walkability', 'amenities'],
            ],
        ];
    }

    /**
     * Generate development potential heatmap.
     */
    private function generateDevelopmentPotentialHeatmap(array $bounds, int $zoomLevel): array
    {
        // Mock implementation
        $dataPoints = [];
        for ($i = 0; $i < 50; $i++) {
            $dataPoints[] = [
                'lat' => 24.7136 + (rand(-100, 100) / 10000),
                'lng' => 46.6753 + (rand(-100, 100) / 10000),
                'value' => rand(40, 85), // Development potential score
                'intensity' => rand(1, 100),
            ];
        }

        return [
            'data_points' => $dataPoints,
            'metadata' => [
                'min_value' => 40,
                'max_value' => 85,
                'average_value' => 62,
                'data_points_count' => count($dataPoints),
                'potential_factors' => ['zoning', 'infrastructure', 'market_demand'],
            ],
        ];
    }

    /**
     * Generate heatmap visualization data.
     */
    private function generateHeatmapVisualization(array $params): array
    {
        $heatmapType = $params['heatmap_type'];
        $bounds = $params['bounds'] ?? [];
        $zoomLevel = $params['zoom_level'] ?? 10;

        // Generate visualization data based on type
        $heatmapData = $this->generateHeatmapData([
            'heatmap_type' => $heatmapType,
            'bounds' => $bounds,
            'zoom_level' => $zoomLevel,
        ]);

        return [
            'type' => $heatmapType,
            'bounds' => $bounds,
            'zoom_level' => $zoomLevel,
            'data_points' => $heatmapData['data_points'],
            'metadata' => $heatmapData['metadata'],
            'color_scheme' => $this->getColorScheme($heatmapType),
            'legend' => $this->generateLegend($heatmapData['metadata']),
        ];
    }

    /**
     * Get color scheme for heatmap type.
     */
    private function getColorScheme(string $heatmapType): array
    {
        $schemes = [
            'price_density' => ['#ffffcc', '#c2e699', '#78c679', '#31a354', '#006837'],
            'price_appreciation' => ['#f7fbff', '#deebf7', '#c6dbef', '#9ecae1', '#4292c6', '#2171b5', '#08519c'],
            'investment_hotspot' => ['#ffffb2', '#fecc5c', '#fd8d3c', '#f03b20', '#bd0026'],
            'risk_assessment' => ['#e5f5e0', '#a1d99b', '#41ab5d', '#238b45', '#006d2c', '#00441b'],
            'market_activity' => ['#fff5f0', '#fee0d2', '#fcbba1', '#fc9272', '#fb6a4a', '#ef3b2c', '#cb181d'],
            'demand_supply' => ['#f1eef6', '#d0d1e6', '#a6bddb', '#74a9cf', '#3690c0', '#0570b0', '#034e7b'],
            'accessibility' => ['#ffffcc', '#ffeda0', '#fed976', '#feb24c', '#fd8d3c', '#fc4e2a', '#e31a1c', '#bd0026'],
            'development_potential' => ['#edf8e9', '#bae4b3', '#74c476', '#41ab5d', '#238b45', '#006d2c', '#00441b'],
        ];

        return $schemes[$heatmapType] ?? $schemes['price_density'];
    }

    /**
     * Generate legend for heatmap.
     */
    private function generateLegend(array $metadata): array
    {
        return [
            'min' => $metadata['min_value'],
            'max' => $metadata['max_value'],
            'average' => $metadata['average_value'],
            'units' => $this->getUnitsForHeatmapType($metadata['generation_method'] ?? 'default'),
        ];
    }

    /**
     * Get units for heatmap type.
     */
    private function getUnitsForHeatmapType(string $method): string
    {
        $units = [
            'price_density_analysis' => 'ريال سعودي',
            'price_appreciation_analysis' => '%',
            'investment_hotspot_analysis' => 'درجة',
            'risk_assessment_analysis' => 'مستوى خطر',
            'market_activity_analysis' => 'معاملات',
            'demand_supply_analysis' => 'نسبة',
            'accessibility_analysis' => 'درجة',
            'development_potential_analysis' => 'درجة',
        ];

        return $units[$method] ?? 'قيمة';
    }

    /**
     * Calculate heatmap statistics.
     */
    private function calculateHeatmapStatistics(array $params): array
    {
        // Mock implementation
        return [
            'total_points' => 1250,
            'average_value' => 650000,
            'median_value' => 580000,
            'standard_deviation' => 185000,
            'quartiles' => [
                'q1' => 420000,
                'q2' => 580000,
                'q3' => 780000,
            ],
            'outliers' => 45,
            'data_quality' => 'high',
        ];
    }

    /**
     * Prepare heatmap export data.
     */
    private function prepareHeatmapExport(array $options): array
    {
        $heatmapType = $options['heatmap_type'];
        $format = $options['format'];
        $includeMetadata = $options['include_metadata'] ?? false;

        // Mock implementation
        $data = [
            'headers' => ['Latitude', 'Longitude', 'Value', 'Intensity'],
            'rows' => [
                [24.7136, 46.6753, 650000, 75],
                [24.7146, 46.6763, 720000, 82],
                [24.7126, 46.6743, 580000, 68],
            ],
        ];

        if ($includeMetadata) {
            $data['metadata'] = [
                'heatmap_type' => $heatmapType,
                'export_date' => now()->format('Y-m-d H:i:s'),
                'total_points' => count($data['rows']),
            ];
        }

        return $data;
    }

    /**
     * Generate heatmap export filename.
     */
    private function generateHeatmapExportFilename(array $options): string
    {
        $heatmapType = $options['heatmap_type'];
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "heatmap_{$heatmapType}_{$timestamp}.{$format}";
    }

    /**
     * Perform heatmap comparison.
     */
    private function performHeatmapComparison(array $params): array
    {
        $heatmapIds = $params['heatmap_ids'];
        $comparisonType = $params['comparison_type'];

        // Mock implementation
        return [
            'comparison_type' => $comparisonType,
            'heatmaps' => PropertyHeatmap::whereIn('id', $heatmapIds)->get(),
            'correlation_matrix' => [
                [1.0, 0.75, 0.62],
                [0.75, 1.0, 0.58],
                [0.62, 0.58, 1.0],
            ],
            'similarity_scores' => [
                'heatmap_1_vs_2' => 0.75,
                'heatmap_1_vs_3' => 0.62,
                'heatmap_2_vs_3' => 0.58,
            ],
            'differences' => [
                'max_difference' => 0.38,
                'average_difference' => 0.22,
                'significant_areas' => ['center', 'north', 'south'],
            ],
        ];
    }

    /**
     * Check if heatmap parameters changed.
     */
    private function heatmapParametersChanged(PropertyHeatmap $heatmap, array $newData): bool
    {
        return $heatmap->heatmap_type !== $newData['heatmap_type'] ||
               $heatmap->bounds !== ($newData['bounds'] ?? []) ||
               $heatmap->zoom_level !== ($newData['zoom_level'] ?? 10);
    }

    /**
     * Get highest price area.
     */
    private function getHighestPriceArea(): array
    {
        return [
            'area' => 'وسط المدينة',
            'average_price' => 1250000,
            'property_count' => 45,
        ];
    }

    /**
     * Get lowest price area.
     */
    private function getLowestPriceArea(): array
    {
        return [
            'area' => 'الضاحية الجنوبية',
            'average_price' => 320000,
            'property_count' => 28,
        ];
    }

    /**
     * Get average price.
     */
    private function getAveragePrice(): float
    {
        return MetaverseProperty::avg('price') ?? 0;
    }

    /**
     * Get price variance.
     */
    private function getPriceVariance(): float
    {
        // Mock implementation
        return 185000;
    }
}
