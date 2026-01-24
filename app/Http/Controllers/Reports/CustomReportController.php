<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CustomReport;
use App\Models\Report;
use App\Models\DataVisualization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = CustomReport::with(['report.generator', 'creator'])
            ->where('created_by', Auth::id())
            ->latest()
            ->paginate(20);

        $templates = CustomReport::templates()
            ->where('is_public', true)
            ->orWhere('created_by', Auth::id())
            ->latest()
            ->get();

        return view('reports.custom.index', compact('reports', 'templates'));
    }

    public function create()
    {
        return view('reports.custom.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'data_sources' => 'required|array|min:1',
            'query_config' => 'required|array',
            'visualization_config' => 'nullable|array',
            'custom_fields' => 'nullable|array',
            'is_public' => 'boolean',
            'is_template' => 'boolean',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
        ]);

        $report = Report::create([
            'title' => $validated['title'],
            'type' => 'custom',
            'description' => $validated['description'],
            'parameters' => $validated['query_config'],
            'filters' => $validated['data_sources'],
            'status' => 'generating',
            'format' => 'json',
            'generated_by' => Auth::id(),
        ]);

        $customReport = CustomReport::create([
            'report_id' => $report->id,
            'name' => $validated['title'],
            'description' => $validated['description'],
            'data_sources' => $validated['data_sources'],
            'query_config' => $validated['query_config'],
            'visualization_config' => $validated['visualization_config'] ?? [],
            'custom_fields' => $validated['custom_fields'] ?? [],
            'is_template' => $validated['is_template'] ?? false,
            'created_by' => Auth::id(),
        ]);

        dispatch(function () use ($report, $customReport) {
            $this->generateCustomReport($report, $customReport);
        });

        return redirect()->route('reports.custom.show', $report)
            ->with('success', 'Custom report generation started.');
    }

    public function show(Report $report)
    {
        $customReport = $report->customReport;
        
        if (!$customReport) {
            abort(404, 'Custom report data not found');
        }

        $report->load(['visualizations', 'exports']);

        return view('reports.custom.show', compact('report', 'customReport'));
    }

    public function edit(CustomReport $customReport)
    {
        $this->authorize('update', $customReport);
        
        return view('reports.custom.edit', compact('customReport'));
    }

    public function update(Request $request, CustomReport $customReport)
    {
        $this->authorize('update', $customReport);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'data_sources' => 'required|array|min:1',
            'query_config' => 'required|array',
            'visualization_config' => 'nullable|array',
            'custom_fields' => 'nullable|array',
            'is_public' => 'boolean',
            'is_template' => 'boolean',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
        ]);

        $customReport->update($validated);

        return redirect()->route('reports.custom.show', $customReport->report)
            ->with('success', 'Custom report updated successfully.');
    }

    public function destroy(CustomReport $customReport)
    {
        $this->authorize('delete', $customReport);
        
        $customReport->delete();

        return redirect()->route('reports.custom.index')
            ->with('success', 'Custom report deleted successfully.');
    }

    public function duplicate(CustomReport $customReport)
    {
        $this->authorize('view', $customReport);
        
        $newReport = $customReport->duplicate(Auth::id());

        return redirect()->route('reports.custom.edit', $newReport)
            ->with('success', 'Custom report duplicated successfully.');
    }

    public function runReport(CustomReport $customReport)
    {
        $this->authorize('view', $customReport);
        
        $customReport->incrementUsage();

        $report = Report::create([
            'title' => $customReport->name . ' - ' . now()->format('Y-m-d H:i'),
            'type' => 'custom',
            'description' => $customReport->description,
            'parameters' => $customReport->query_config,
            'filters' => $customReport->data_sources,
            'status' => 'generating',
            'format' => 'json',
            'generated_by' => Auth::id(),
        ]);

        dispatch(function () use ($report, $customReport) {
            $this->generateCustomReport($report, $customReport);
        });

        return redirect()->route('reports.custom.show', $report)
            ->with('success', 'Custom report execution started.');
    }

    public function getReportData(Request $request): JsonResponse
    {
        $dataSources = $request->data_sources ?? [];
        $queryConfig = $request->query_config ?? [];

        $data = $this->executeCustomQuery($dataSources, $queryConfig);

        return response()->json(['success' => true, 'data' => $data]);
    }

    private function generateCustomReport(Report $report, CustomReport $customReport)
    {
        try {
            $data = $this->executeCustomQuery(
                $customReport->data_sources,
                $customReport->query_config
            );

            $customReport->update(['report_data' => $data]);

            $report->update([
                'data' => $data,
                'status' => 'completed',
                'generated_at' => now(),
            ]);

            // Create visualizations if configured
            if ($customReport->visualization_config) {
                $this->createCustomVisualizations($report, $customReport->visualization_config);
            }

        } catch (\Exception $e) {
            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    private function executeCustomQuery(array $dataSources, array $queryConfig): array
    {
        $results = [];

        foreach ($dataSources as $source) {
            $sourceData = match($source) {
                'properties' => $this->queryProperties($queryConfig),
                'users' => $this->queryUsers($queryConfig),
                'agents' => $this->queryAgents($queryConfig),
                'transactions' => $this->queryTransactions($queryConfig),
                'reviews' => $this->queryReviews($queryConfig),
                default => [],
            };

            $results[$source] = $sourceData;
        }

        return $results;
    }

    private function queryProperties(array $config): array
    {
        $query = DB::table('properties');

        // Apply filters
        if (isset($config['filters']['status'])) {
            $query->where('status', $config['filters']['status']);
        }

        if (isset($config['filters']['price_range'])) {
            $query->whereBetween('price', $config['filters']['price_range']);
        }

        if (isset($config['filters']['date_range'])) {
            $query->whereBetween('created_at', $config['filters']['date_range']);
        }

        // Apply aggregations
        if (isset($config['aggregations'])) {
            foreach ($config['aggregations'] as $agg) {
                match($agg) {
                    'count' => $query->count(),
                    'avg_price' => $query->avg('price'),
                    'sum_price' => $query->sum('price'),
                    default => $query,
                };
            }
        }

        // Apply sorting
        if (isset($config['sort_by'])) {
            $query->orderBy($config['sort_by'], $config['sort_direction'] ?? 'asc');
        }

        // Apply limit
        if (isset($config['limit'])) {
            $query->limit($config['limit']);
        }

        return $query->get()->toArray();
    }

    private function queryUsers(array $config): array
    {
        $query = DB::table('users');

        if (isset($config['filters']['role'])) {
            $query->where('role', $config['filters']['role']);
        }

        if (isset($config['filters']['created_at'])) {
            $query->whereBetween('created_at', $config['filters']['created_at']);
        }

        return $query->get()->toArray();
    }

    private function queryAgents(array $config): array
    {
        $query = DB::table('agents')
            ->join('users', 'agents.user_id', '=', 'users.id');

        if (isset($config['filters']['status'])) {
            $query->where('agents.status', $config['filters']['status']);
        }

        return $query->select('agents.*', 'users.name', 'users.email')->get()->toArray();
    }

    private function queryTransactions(array $config): array
    {
        $query = DB::table('properties')
            ->where('status', 'sold');

        if (isset($config['filters']['date_range'])) {
            $query->whereBetween('sold_at', $config['filters']['date_range']);
        }

        return $query->get()->toArray();
    }

    private function queryReviews(array $config): array
    {
        $query = DB::table('reviews');

        if (isset($config['filters']['rating'])) {
            $query->where('rating', '>=', $config['filters']['rating']);
        }

        if (isset($config['filters']['date_range'])) {
            $query->whereBetween('created_at', $config['filters']['date_range']);
        }

        return $query->get()->toArray();
    }

    private function createCustomVisualizations(Report $report, array $vizConfig)
    {
        $position = 0;
        
        foreach ($vizConfig['charts'] ?? [] as $chart) {
            DataVisualization::create([
                'report_id' => $report->id,
                'title' => $chart['title'],
                'type' => 'chart',
                'chart_type' => $chart['type'],
                'data_source' => $chart['data_source'] ?? [],
                'chart_config' => $chart['config'] ?? [],
                'position_order' => $position++,
                'is_visible' => true,
            ]);
        }
    }
}
