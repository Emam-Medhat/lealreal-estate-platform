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
        try {
            // Simple test to see what we're getting
            \Log::info('=== CUSTOM REPORT STORE START ===');
            \Log::info('Request Method: ' . $request->method(), []);
            \Log::info('Request All Data: ', [$request->all()]);
            \Log::info('Request has is_template: ', [$request->has('is_template')]);
            \Log::info('Request is_template value: ', [$request->input('is_template')]);
            \Log::info('Request is_template boolean: ', [$request->boolean('is_template')]);

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

            // Debug: Log the validated data
            \Log::info('Custom Report Validated Data:', $validated);

            // Create the main report record
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

            // Create the custom report record
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

            // Generate the report data asynchronously
            dispatch(function () use ($report, $customReport) {
                $this->generateCustomReport($report, $customReport);
            });

            return redirect()->route('reports.custom.show', $report)
                ->with('success', 'تم إنشاء التقرير المخصص بنجاح! جاري معالجة البيانات...');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Check if validation exception has any messages
            $errorMessages = $e->errors();
            
            // Check if error messages object exists and is not empty
            $hasErrors = $errorMessages && count($errorMessages) > 0;
            
            \Log::error('Custom Report Validation Error:', [
                'message' => $e->getMessage(),
                'errors' => $hasErrors ? $errorMessages : [],
                'has_errors' => $hasErrors,
                'error_count' => $hasErrors ? count($errorMessages) : 0,
                'request_data' => $request->all(),
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'حدث خطأ في التحقق من البيانات: ' . $e->getMessage());
                
        } catch (\Exception $e) {
            \Log::error('Custom Report Creation Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء التقرير: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // Try to find as Report first
        $report = Report::find($id);
        $customReport = null;

        if ($report) {
            // Found a Report, try to find associated CustomReport
            if ($report->relationLoaded('customReport') || $report->customReport) {
                $customReport = $report->customReport;
            } else {
                $customReport = CustomReport::where('report_id', $report->id)->first();
            }
        } else {
            // Not a Report ID, try finding as CustomReport ID
            $customReport = CustomReport::find($id);
            if ($customReport) {
                // Found CustomReport, now get the associated Report
                $report = Report::find($customReport->report_id);
            }
        }
        
        if ($report && !$customReport && $report->type === 'custom') {
            // Self-healing: Create missing CustomReport from Report data
            try {
                $customReport = CustomReport::create([
                    'report_id' => $report->id,
                    'name' => $report->title,
                    'description' => $report->description,
                    'data_sources' => $report->filters ?? [], // Map filters to data_sources
                    'query_config' => $report->parameters ?? [], // Map parameters to query_config
                    'visualization_config' => [],
                    'custom_fields' => [],
                    'is_template' => false,
                    'created_by' => $report->generated_by,
                    'active' => true,
                    'usage_count' => 0,
                    'is_public' => false
                ]);
                
                \Log::info("Self-healed missing CustomReport for Report ID: {$report->id}");
            } catch (\Exception $e) {
                \Log::error("Failed to self-heal CustomReport for Report ID: {$report->id}. Error: " . $e->getMessage());
            }
        }
        
        if (!$customReport || !$report) {
            abort(404, 'Custom report data not found');
        }

        // Check authorization
        $this->authorize('view', $customReport);

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

            // Convert collection/array to array
            $results[$source] = json_decode(json_encode($sourceData), true);
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

        // Apply sorting
        if (isset($config['sort_by'])) {
            $query->orderBy($config['sort_by'], $config['sort_direction'] ?? 'asc');
        }

        // Apply limit
        if (isset($config['limit'])) {
            $query->limit((int)$config['limit']);
        } else {
            $query->limit(100); // Default limit
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

        if (isset($config['limit'])) {
            $query->limit((int)$config['limit']);
        } else {
            $query->limit(100);
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

        if (isset($config['limit'])) {
            $query->limit((int)$config['limit']);
        } else {
            $query->limit(100);
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

        if (isset($config['limit'])) {
            $query->limit((int)$config['limit']);
        } else {
            $query->limit(100);
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

        if (isset($config['limit'])) {
            $query->limit((int)$config['limit']);
        } else {
            $query->limit(100);
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
