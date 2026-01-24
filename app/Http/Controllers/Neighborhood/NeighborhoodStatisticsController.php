<?php

namespace App\Http\Controllers\Neighborhood;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood\Neighborhood;
use App\Models\Neighborhood\NeighborhoodStatistic;
use App\Models\Neighborhood\Community;
use App\Models\Neighborhood\LocalBusiness;
use App\Models\Neighborhood\CommunityAmenity;
use App\Models\Neighborhood\CommunityEvent;
use App\Models\Neighborhood\NeighborhoodReview;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class NeighborhoodStatisticsController extends Controller
{
    /**
     * Display the neighborhood statistics dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['neighborhood_id', 'statistic_type', 'date_range']);
        
        // Get statistics overview
        $overview = $this->getOverviewStatistics();
        
        // Get neighborhood statistics with filters
        $statistics = NeighborhoodStatistic::with(['neighborhood'])
            ->when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })
            ->when($filters['statistic_type'], function ($query, $statisticType) {
                return $query->where('statistic_type', $statisticType);
            })
            ->when($filters['date_range'], function ($query, $dateRange) {
                if ($dateRange === 'today') {
                    return $query->whereDate('created_at', today());
                } elseif ($dateRange === 'week') {
                    return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($dateRange === 'month') {
                    return $query->whereMonth('created_at', now()->month());
                } elseif ($dateRange === 'year') {
                    return $query->whereYear('created_at', now()->year());
                }
            })
            ->latest()
            ->paginate(20);

        // Get neighborhoods and statistic types for filters
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $statisticTypes = ['population', 'property', 'business', 'amenity', 'safety', 'education', 'transportation', 'healthcare', 'recreation', 'economic', 'demographic', 'infrastructure'];
        $dateRanges = ['today', 'week', 'month', 'year'];

        return Inertia::render('NeighborhoodStatistics/Index', [
            'overview' => $overview,
            'statistics' => $statistics,
            'neighborhoods' => $neighborhoods,
            'statisticTypes' => $statisticTypes,
            'dateRanges' => $dateRanges,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new neighborhood statistic.
     */
    public function create(): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $statisticTypes = ['population', 'property', 'business', 'amenity', 'safety', 'education', 'transportation', 'healthcare', 'recreation', 'economic', 'demographic', 'infrastructure'];
        $periods = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'];

        return Inertia::render('NeighborhoodStatistics/Create', [
            'neighborhoods' => $neighborhoods,
            'statisticTypes' => $statisticTypes,
            'periods' => $periods,
        ]);
    }

    /**
     * Store a newly created neighborhood statistic.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'statistic_type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'period' => 'required|string',
            'data_source' => 'nullable|string|max:255',
            'collection_method' => 'nullable|string|max:100',
            'collection_date' => 'nullable|date',
            'data_points' => 'nullable|array',
            'data_points.*.date' => 'required|date',
            'data_points.*.value' => 'required|numeric',
            'data_points.*.unit' => 'nullable|string|max:50',
            'data_points.*.metadata' => 'nullable|array',
            'aggregated_data' => 'nullable|array',
            'aggregated_data.total' => 'nullable|numeric',
            'aggregated_data.average' => 'nullable|numeric',
            'aggregated_data.minimum' => 'nullable|numeric',
            'aggregated_data.maximum' => 'nullable|numeric',
            'aggregated_data.median' => 'nullable|numeric',
            'aggregated_data.count' => 'nullable|integer|min:0',
            'trend_analysis' => 'nullable|array',
            'trend_analysis.trend' => 'nullable|string|in:increasing,decreasing,stable,volatile',
            'trend_analysis.percentage_change' => 'nullable|numeric',
            'trend_analysis.confidence_level' => 'nullable|numeric|min:0|max:100',
            'trend_analysis.analysis_period' => 'nullable|string|max:100',
            'comparative_data' => 'nullable|array',
            'comparative_data.previous_period' => 'nullable|numeric',
            'comparative_data.percentage_change' => 'nullable|numeric',
            'comparative_data.benchmark' => 'nullable|numeric',
            'comparative_data.period' => 'nullable|string|max:100',
            'forecast_data' => 'nullable|array',
            'forecast_data.next_period' => 'nullable|numeric',
            'forecast_data.confidence_level' => 'nullable|numeric|min:0|max:100',
            'forecast_data.method' => 'nullable|string|max:100',
            'forecast_data.period' => 'nullable|string|max:100',
            'visualization_data' => 'nullable|array',
            'visualization_data.chart_type' => 'nullable|string|in:line,bar,pie,area,scatter,heatmap',
            'visualization_data.color_scheme' => 'nullable|array',
            'visualization_data.data_format' => 'nullable|string|max:100',
            'metadata' => 'nullable|array',
            'metadata.accuracy_level' => 'nullable|string|max:50',
            'metadata.reliability' => 'nullable|string|max:50',
            'metadata.last_updated' => 'nullable|date',
            'metadata.data_quality' => 'nullable|string|max:50',
            'metadata.notes' => 'nullable|string|max:1000',
        ]);

        try {
            $statistic = NeighborhoodStatistic::create([
                'neighborhood_id' => $validated['neighborhood_id'],
                'statistic_type' => $validated['statistic_type'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'period' => $validated['period'],
                'data_source' => $validated['data_source'] ?? null,
                'collection_method' => $validated['collection_method'] ?? null,
                'collection_date' => $validated['collection_date'] ?? null,
                'data_points' => $validated['data_points'] ?? [],
                'aggregated_data' => $validated['aggregated_data'] ?? [],
                'trend_analysis' => $validated['trend_analysis'] ?? [],
                'comparative_data' => $validated['comparative_data'] ?? [],
                'forecast_data' => $validated['forecast_data'] ?? [],
                'visualization_data' => $validated['visualization_data'] ?? [],
                'metadata' => $validated['metadata'] ?? [],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء إحصائية الحي بنجاح',
                'statistic' => $statistic,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء إحصائية الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified neighborhood statistic.
     */
    public function show(NeighborhoodStatistic $statistic): \Inertia\Response
    {
        $statistic->load(['neighborhood']);

        // Get related statistics
        $relatedStatistics = NeighborhoodStatistic::where('neighborhood_id', $statistic->neighborhood_id)
            ->where('id', '!=', $statistic->id)
            ->where('statistic_type', $statistic->statistic_type)
            ->take(3)
            ->get(['id', 'title', 'period', 'created_at']);

        // Get historical data for comparison
        $historicalData = $this->getHistoricalData($statistic);

        return Inertia::render('NeighborhoodStatistics/Show', [
            'statistic' => $statistic,
            'relatedStatistics' => $relatedStatistics,
            'historicalData' => $historicalData,
        ]);
    }

    /**
     * Show the form for editing the specified neighborhood statistic.
     */
    public function edit(NeighborhoodStatistic $statistic): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $statisticTypes = ['population', 'property', 'business', 'amenity', 'safety', 'education', 'transportation', 'healthcare', 'recreation', 'economic', 'demographic', 'infrastructure'];
        $periods = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'];

        return Inertia::render('NeighborhoodStatistics/Edit', [
            'statistic' => $statistic,
            'neighborhoods' => $neighborhoods,
            'statisticTypes' => $statisticTypes,
            'periods' => $periods,
        ]);
    }

    /**
     * Update the specified neighborhood statistic.
     */
    public function update(Request $request, NeighborhoodStatistic $statistic): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'period' => 'required|string',
            'data_source' => 'nullable|string|max:255',
            'collection_method' => 'nullable|string|max:100',
            'collection_date' => 'nullable|date',
            'data_points' => 'nullable|array',
            'aggregated_data' => 'nullable|array',
            'trend_analysis' => 'nullable|array',
            'comparative_data' => 'nullable|array',
            'forecast_data' => 'nullable|array',
            'visualization_data' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $statistic->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث إحصائية الحي بنجاح',
                'statistic' => $statistic,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث إحصائية الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified neighborhood statistic.
     */
    public function destroy(NeighborhoodStatistic $statistic): JsonResponse
    {
        try {
            $statistic->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف إحصائية الحي بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف إحصائية الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics overview.
     */
    public function getOverview(): JsonResponse
    {
        try {
            $overview = [
                'total_statistics' => NeighborhoodStatistic::count(),
                'active_neighborhoods' => Neighborhood::where('status', 'active')->count(),
                'total_data_points' => NeighborhoodStatistic::sum('aggregated_data.count') ?? 0,
                'latest_update' => NeighborhoodStatistic::latest('updated_at')->value('updated_at'),
                'popular_types' => $this->getPopularTypes(),
                'recent_statistics' => $this->getRecentStatistics(),
                'data_quality_score' => $this->calculateDataQualityScore(),
            ];

            return response()->json([
                'success' => true,
                'overview' => $overview,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب نظرة عامة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics by type.
     */
    public function getByType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'statistic_type' => 'required|string|in:population,property,business,amenity,safety,education,transportation,healthcare,recreation,economic,demographic,infrastructure',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $validated['limit'] ?? 20;

        try {
            $statistics = NeighborhoodStatistic::where('statistic_type', $validated['statistic_type'])
                ->with(['neighborhood'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get(['id', 'title', 'period', 'neighborhood_id', 'created_at', 'aggregated_data']);

            return response()->json([
                'success' => true,
                'statistics' => $statistics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإحصائيات حسب النوع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics by neighborhood.
     */
    public function getByNeighborhood(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $validated['limit'] ?? 20;

        try {
            $statistics = NeighborhoodStatistic::where('neighborhood_id', $validated['neighborhood_id'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get(['id', 'title', 'statistic_type', 'period', 'created_at', 'aggregated_data']);

            return response()->json([
                'success' => true,
                'statistics' => $statistics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب إحصائيات الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get trend analysis.
     */
    public function getTrendAnalysis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'statistic_type' => 'required|string',
            'period' => 'required|string|in:daily,weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $statistics = NeighborhoodStatistic::where('neighborhood_id', $validated['neighborhood_id'])
                ->where('statistic_type', $validated['statistic_type'])
                ->where('period', $validated['period'])
                ->whereBetween('created_at', [$validated['start_date'], $validated['end_date']])
                ->orderBy('created_at', 'asc')
                ->get(['id', 'title', 'data_points', 'trend_analysis', 'created_at']);

            $trendData = $statistics->map(function ($statistic) {
                return [
                    'id' => $statistic->id,
                    'title' => $statistic->title,
                    'data_points' => $statistic->data_points ?? [],
                    'trend_analysis' => $statistic->trend_analysis ?? [],
                    'created_at' => $statistic->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'trend_data' => $trendData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => 'false',
                'message' => 'حدث خطأ أثناء تحليل الاتجاهات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get comparative analysis.
     */
    public function getComparativeAnalysis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_ids' => 'required|array|min:2|max:5',
            'neighborhood_ids.*' => 'exists:neighborhoods,id',
            'statistic_type' => 'required|string',
            'period' => 'required|string|in:daily,weekly,monthly,quarterly,yearly',
            'comparison_period' => 'required|string|in:previous_period,same_period,yoy_over_yoy',
        ]);

        try {
            $statistics = NeighborhoodStatistic::whereIn('neighborhood_id', $validated['neighborhood_ids'])
                ->where('statistic_type', $validated['statistic_type'])
                ->where('period', $validated['period'])
                ->with(['neighborhood'])
                ->get(['id', 'title', 'neighborhood_id', 'aggregated_data', 'comparative_data', 'created_at']);

            $comparisonData = $statistics->map(function ($statistic) {
                return [
                    'id' => $statistic->id,
                    'neighborhood' => $statistic->neighborhood?->name ?? 'غير معروف',
                    'title' => $statistic->title,
                    'value' => $statistic->aggregated_data['total'] ?? 0,
                    'average' => $statistic->aggregated_data['average'] ?? 0,
                    'comparative_data' => $statistic->comparative_data ?? [],
                    'created_at' => $statistic->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'comparison_data' => $comparisonData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحليل المقارن: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export statistics data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'filters' => 'nullable|array',
            'include_data_points' => 'nullable|boolean',
            'include_visualization' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareStatisticsExport($validated);
            $filename = $this->generateStatisticsExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات الإحصائيات للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير بيانات الإحصائيات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate statistics report.
     */
    public function generateReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'report_type' => 'required|string|in:comprehensive,trend,comparative,detailed',
            'period' => 'required|string|in:daily,weekly,monthly,quarterly,yearly',
            'include_visualizations' => 'nullable|boolean',
            'format' => 'required|in:pdf,html',
        ]);

        try {
            // Mock implementation - in real app, this would generate a comprehensive report
            $reportData = $this->generateReportData($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء تقرير الإحصائيات بنجاح',
                'report_data' => $reportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء تقرير الإحصائيات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get popular types.
     */
    private function getPopularTypes(): array
    {
        return NeighborhoodStatistic::select('statistic_type', DB::raw('count(*) as count'))
            ->groupBy('statistic_type')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->toArray();
    }

    /**
     * Get recent statistics.
     */
    private function getRecentStatistics(): array
    {
        return NeighborhoodStatistic::latest('created_at')
            ->take(5)
            ->with(['neighborhood'])
            ->get(['id', 'title', 'statistic_type', 'neighborhood_id', 'created_at'])
            ->toArray();
    }

    /**
     * Calculate data quality score.
     */
    private function calculateDataQualityScore(): float
    {
        // Mock implementation - in real app, this would analyze various quality metrics
        return 85.5;
    }

    /**
     * Get historical data.
     */
    private function getHistoricalData(NeighborhoodStatistic $statistic): array
    {
        // Mock implementation - in real app, this would get historical data points
        $historicalData = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $historicalData[] = [
                'date' => now()->subMonths($i)->format('Y-m-d'),
                'value' => rand(100, 1000),
                'unit' => 'count',
            ];
        }
        
        return $historicalData;
    }

    /**
     * Prepare statistics export data.
     */
    private function prepareStatisticsExport(array $options): array
    {
        $filters = $options['filters'] ?? [];
        $includeDataPoints = $options['include_data_points'] ?? false;
        $includeVisualization = $options['include_visualization'] ?? false;

        $query = NeighborhoodStatistic::with(['neighborhood']);
        
        if (isset($filters['neighborhood_id'])) {
            $query->where('neighborhood_id', $filters['neighborhood_id']);
        }
        
        if (isset($filters['statistic_type'])) {
            $query->where('statistic_type', $filters['statistic_type']);
        }

        $statistics = $query->get();

        $data = $statistics->map(function ($statistic) use ($includeDataPoints, $includeVisualization) {
            $item = [
                'id' => $statistic->id,
                'title' => $statistic->title,
                'neighborhood' => $statistic->neighborhood?->name ?? 'غير معروف',
                'statistic_type' => $statistic->statistic_type,
                'description' => $statistic->description,
                'period' => $statistic->period,
                'data_source' => $statistic->data_source,
                'collection_method' => $statistic->collection_method,
                'collection_date' => $statistic->collection_date?->format('Y-m-d H:i:s'),
                'aggregated_data' => $statistic->aggregated_data ?? [],
                'trend_analysis' => $statistic->trend_analysis ?? [],
                'comparative_data' => $statistic->comparative_data ?? [],
                'forecast_data' => $statistic->forecast_data ?? [],
                'visualization_data' => $statistic->visualization_data ?? [],
                'metadata' => $statistic->metadata ?? [],
                'created_at' => $statistic->created_at->format('Y-m-d H:i:s'),
            ];

            if ($includeDataPoints) {
                $item['data_points'] = $statistic->data_points ?? [];
            }

            if ($includeVisualization) {
                $item['visualization_data'] = $statistic->visualization_data ?? [];
            }

            return $item;
        });

        return [
            'headers' => ['ID', 'Title', 'Neighborhood', 'Type', 'Period', 'Created At'],
            'rows' => $data->toArray(),
        ];
    }

    /**
     * Generate statistics export filename.
     */
    private function generateStatisticsExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "neighborhood_statistics_export_{$timestamp}.{$format}";
    }

    /**
     * Generate report data.
     */
    private function generateReportData(array $options): array
    {
        // Mock implementation - in real app, this would generate comprehensive report data
        return [
            'report_type' => $options['report_type'],
            'period' => $options['period'],
            'neighborhood_id' => $options['neighborhood_id'],
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'data' => [
                'summary' => 'ملخصص عام للإحصائيات',
                'sections' => [
                    'overview' => [
                        'title' => 'نظرة عامة',
                        'content' => 'هذا تقرير يعرض نظرة عامة على إحصائيات الحي.',
                    ],
                    'trends' => [
                        'title' => 'اتجاهات',
                        'content' => 'تحليل الاتجاهات والاتجاهات المستقبلية.',
                    ],
                    'comparisons' => [
                        'title' => 'مقارنات',
                        'content' => 'مقارنات بين الأحياء المختلفة.',
                    ],
                ],
                'visualizations' => [
                    [
                        'type' => 'chart',
                        'title' => 'رسم بياني',
                        'data' => [],
                    ],
                ],
            ],
        ];
    }
}
