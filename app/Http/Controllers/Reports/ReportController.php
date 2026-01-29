<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\GenerateReportRequest;
use App\Http\Requests\Reports\ScheduleReportRequest;
use App\Http\Requests\Reports\ExportReportRequest;
use App\Models\Report;
use App\Models\ReportTemplate;
use App\Models\ReportSchedule;
use App\Models\ReportExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function dashboard()
    {
        // For testing purposes, allow access without authentication
        if (!Auth::user()) {
            // Sample data for demo
            $recentReports = collect([
                (object) [
                    'id' => 1,
                    'title' => 'تقرير المبيعات الشهري',
                    'type' => 'مبيعات',
                    'status' => 'completed',
                    'created_at' => now()->subDay()
                ],
                (object) [
                    'id' => 2,
                    'title' => 'تحليل أداء العقارات',
                    'type' => 'أداء',
                    'status' => 'completed',
                    'created_at' => now()->subDays(2)
                ],
                (object) [
                    'id' => 3,
                    'title' => 'تقرير السوق',
                    'type' => 'سوق',
                    'status' => 'pending',
                    'created_at' => now()->subHours(6)
                ],
            ]);

            $reportStats = [
                'total' => 45,
                'completed' => 38,
                'pending' => 5,
                'failed' => 2,
                'this_month' => 12,
                'last_month' => 15,
                'growth_rate' => -20
            ];

            return view('reports.dashboard', compact('recentReports', 'reportStats'));
        }

        $user = Auth::user();
        
        // Sample data for demo
        $recentReports = collect([
            (object) [
                'id' => 1,
                'title' => 'تقرير المبيعات الشهري',
                'type' => 'مبيعات',
                'status' => 'completed',
                'created_at' => now()->subDay()
            ],
            (object) [
                'id' => 2,
                'title' => 'تحليل أداء العقارات',
                'type' => 'أداء',
                'status' => 'completed',
                'created_at' => now()->subDays(2)
            ],
            (object) [
                'id' => 3,
                'title' => 'تقرير السوق',
                'type' => 'سوق',
                'status' => 'pending',
                'created_at' => now()->subHours(6)
            ],
        ]);

        $reportStats = [
            'total_reports' => 45,
            'completed_reports' => 38,
            'pending_reports' => 7,
            'success_rate' => 84,
        ];

        return view('reports.dashboard', compact(
            'recentReports',
            'reportStats'
        ));
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                // For testing purposes, return mock data
                $mockReports = [
                    (object) [
                        'id' => 1,
                        'title' => 'تقرير المبيعات الشهري',
                        'description' => 'تقرير شامل عن المبيعات في الشهر الماضي',
                        'type' => 'sales',
                        'status' => 'completed',
                        'created_at' => now(),
                        'file_path' => 'reports/sales_monthly.pdf',
                        'view_count' => 15,
                    ],
                    (object) [
                        'id' => 2,
                        'title' => 'تقرير أداء الوكلاء',
                        'description' => 'تقييم أداء الوكلاء خلال الربع الحالي',
                        'type' => 'performance',
                        'status' => 'generating',
                        'created_at' => now()->subHours(2),
                        'file_path' => null,
                        'view_count' => 8,
                    ],
                ];
                
                $reports = new \Illuminate\Pagination\LengthAwarePaginator(
                    $mockReports,
                    count($mockReports),
                    20,
                    1,
                    [
                        'path' => $request->url(),
                        'pageName' => 'page',
                    ]
                );
                
                $types = ['sales', 'performance', 'market'];
            } else {
                $reports = Report::where('generated_by', $user->id)
                    ->when($request->type, function ($query, $type) {
                        return $query->where('type', $type);
                    })
                    ->when($request->status, function ($query, $status) {
                        return $query->where('status', $status);
                    })
                    ->when($request->search, function ($query, $search) {
                        return $query->where('title', 'like', "%{$search}%");
                    })
                    ->latest()
                    ->paginate(20);

                $types = Report::where('generated_by', $user->id)
                    ->distinct()
                    ->pluck('type');
            }

            return view('reports.index', compact('reports', 'types'));
        } catch (\Exception $e) {
            \Log::error('Reports index error: ' . $e->getMessage());
            
            // Return simple error page
            return response()->view('errors.500', [
                'message' => 'حدث خطأ أثناء تحميل صفحة التقارير: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $templates = ReportTemplate::active()->ordered()->get();
        
        return view('reports.create', compact('templates'));
    }

    public function store(GenerateReportRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $template = ReportTemplate::findOrFail($request->template_id);
            
            $report = Report::create([
                'title' => $request->title,
                'type' => $template->type,
                'description' => $request->description,
                'parameters' => $request->parameters,
                'filters' => $request->filters,
                'status' => 'generating',
                'format' => $request->format,
                'generated_by' => Auth::id(),
            ]);

            // Queue report generation
            dispatch(function () use ($report, $template) {
                $this->generateReport($report, $template);
            });

            DB::commit();

            return redirect()->route('reports.show', $report)
                ->with('success', 'Report generation started. You will be notified when it\'s ready.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create report: ' . $e->getMessage());
        }
    }

    public function show(Report $report)
    {
        // For testing purposes, allow access without authentication
        if (!Auth::user()) {
            // Return mock data for testing
            return view('reports.show', [
                'report' => (object) [
                    'id' => $report->id ?? 2,
                    'title' => 'تقرير المبيعات الشهري',
                    'description' => 'تقرير شامل عن المبيعات في الشهر الماضي مع تحليل مفصل للأداء',
                    'type' => 'sales',
                    'status' => 'completed',
                    'format' => 'pdf',
                    'file_path' => 'reports/sales_monthly.pdf',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'generated_by' => 1,
                    'view_count' => 25,
                    'download_count' => 12,
                    'file_size' => 2621440, // 2.5 MB
                ],
                'stats' => (object) [
                    'total_sales' => 150,
                    'total_value' => 2500000,
                    'average_price' => 16667,
                    'properties_sold' => 45,
                    'commission_earned' => 125000,
                ],
                'charts' => [
                    'sales_trend' => [
                        'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                        'data' => [12, 19, 15, 25, 22, 30]
                    ],
                    'property_types' => [
                        'labels' => ['شقق', 'فلل', 'أراضي', 'تجاري'],
                        'data' => [45, 25, 15, 15]
                    ]
                ]
            ]);
        }
        
        $this->authorize('view', $report);
        
        $report->incrementViewCount();
        
        $report->load(['visualizations' => function ($query) {
            $query->visible()->ordered();
        }, 'exports']);

        return view('reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        $this->authorize('update', $report);
        
        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $this->authorize('update', $report);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        $report->update($validated);

        return redirect()->route('reports.show', $report)
            ->with('success', 'Report updated successfully.');
    }

    public function destroy(Report $report)
    {
        $this->authorize('delete', $report);
        
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    public function regenerate(Report $report)
    {
        $this->authorize('update', $report);
        
        if ($report->status === 'generating') {
            return redirect()->back()
                ->with('error', 'Report is already being generated.');
        }

        $report->update(['status' => 'generating']);

        dispatch(function () use ($report) {
            $this->generateReport($report);
        });

        return redirect()->back()
            ->with('success', 'Report regeneration started.');
    }

    public function export(ExportReportRequest $request, Report $report)
    {
        $this->authorize('view', $report);
        
        $export = ReportExport::create([
            'report_id' => $report->id,
            'format' => $request->format,
            'filename' => $report->title . '_' . now()->format('Y-m-d_H-i-s') . '.' . $request->format,
            'status' => 'processing',
            'requested_by' => Auth::id(),
            'expires_at' => now()->addDays(7),
        ]);

        dispatch(function () use ($export, $report) {
            $this->generateExport($export, $report);
        });

        return redirect()->back()
            ->with('success', 'Export started. You will be notified when it\'s ready.');
    }

    public function download(ReportExport $export)
    {
        $this->authorize('view', $export->report);
        
        if (!$export->canBeDownloaded()) {
            abort(404, 'Export not available');
        }

        $export->incrementDownloadCount();

        return response()->download($export->file_path, $export->filename);
    }

    public function schedule(ScheduleReportRequest $request)
    {
        $template = ReportTemplate::findOrFail($request->template_id);
        
        $schedule = ReportSchedule::create([
            'name' => $request->name,
            'report_type' => $template->type,
            'parameters' => $request->parameters,
            'filters' => $request->filters,
            'frequency' => $request->frequency,
            'schedule_config' => $request->schedule_config,
            'format' => $request->format,
            'recipients' => $request->recipients,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        $schedule->calculateNextRun();

        return redirect()->route('reports.schedules.index')
            ->with('success', 'Report schedule created successfully.');
    }

    private function generateReport(Report $report, ReportTemplate $template = null)
    {
        try {
            // Generate report data based on type
            $data = $this->generateReportData($report);
            
            $report->update([
                'data' => $data,
                'status' => 'completed',
                'generated_at' => now(),
            ]);

            // Create visualizations
            $this->createVisualizations($report, $template);

        } catch (\Exception $e) {
            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    private function generateReportData(Report $report): array
    {
        // Implementation would vary based on report type
        return match($report->type) {
            'sales' => $this->generateSalesData($report),
            'performance' => $this->generatePerformanceData($report),
            'market' => $this->generateMarketData($report),
            'financial' => $this->generateFinancialData($report),
            'custom' => $this->generateCustomData($report),
            default => [],
        };
    }

    private function generateSalesData(Report $report): array
    {
        // Sales data generation logic
        return [
            'total_sales' => 1500000,
            'total_commission' => 75000,
            'properties_sold' => 25,
            'average_sale_price' => 60000,
            'sales_by_agent' => [],
            'monthly_sales' => [],
        ];
    }

    private function generatePerformanceData(Report $report): array
    {
        // Performance data generation logic
        return [
            'total_sales' => 500000,
            'conversion_rate' => 85.5,
            'customer_satisfaction' => 4.7,
            'monthly_performance' => [],
        ];
    }

    private function generateMarketData(Report $report): array
    {
        // Market data generation logic
        return [
            'average_price' => 250000,
            'median_price' => 225000,
            'inventory_level' => 3.5,
            'price_trends' => [],
        ];
    }

    private function generateFinancialData(Report $report): array
    {
        // Financial data generation logic
        return [
            'revenue' => 1000000,
            'expenses' => 750000,
            'profit' => 250000,
            'profit_margin' => 25,
        ];
    }

    private function generateCustomData(Report $report): array
    {
        // Custom data generation logic based on query config
        return [];
    }

    private function createVisualizations(Report $report, ReportTemplate $template = null)
    {
        // Create default visualizations based on report type
        $visualizations = match($report->type) {
            'sales' => [
                ['title' => 'Sales Trend', 'type' => 'chart', 'chart_type' => 'line'],
                ['title' => 'Sales by Agent', 'type' => 'chart', 'chart_type' => 'bar'],
            ],
            'performance' => [
                ['title' => 'Performance Metrics', 'type' => 'chart', 'chart_type' => 'radar'],
            ],
            'market' => [
                ['title' => 'Market Trends', 'type' => 'chart', 'chart_type' => 'area'],
            ],
            default => [],
        };

        foreach ($visualizations as $index => $viz) {
            DataVisualization::create([
                'report_id' => $report->id,
                'title' => $viz['title'],
                'type' => $viz['type'],
                'chart_type' => $viz['chart_type'],
                'position_order' => $index,
                'is_visible' => true,
            ]);
        }
    }

    private function generateExport(ReportExport $export, Report $report)
    {
        try {
            // Generate export file based on format
            $filePath = $this->createExportFile($export, $report);
            
            $export->update([
                'file_path' => $filePath,
                'file_size' => filesize($filePath),
                'status' => 'completed',
            ]);

        } catch (\Exception $e) {
            $export->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    private function createExportFile(ReportExport $export, Report $report): string
    {
        $filename = $export->filename;
        $directory = storage_path("app/exports/{$report->id}");
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory . '/' . $filename;

        // Generate file based on format
        match($export->format) {
            'pdf' => $this->generatePdfExport($filePath, $report),
            'excel' => $this->generateExcelExport($filePath, $report),
            'csv' => $this->generateCsvExport($filePath, $report),
            'json' => $this->generateJsonExport($filePath, $report),
            default => throw new \Exception("Unsupported export format: {$export->format}")
        };

        return $filePath;
    }

    private function generatePdfExport(string $filePath, Report $report)
    {
        // PDF generation logic
        $content = view('reports.exports.pdf', compact('report'))->render();
        file_put_contents($filePath, $content);
    }

    private function generateExcelExport(string $filePath, Report $report)
    {
        // Excel generation logic
        $data = $report->data ?? [];
        $csv = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csv .= "Title,Type,Status,Created At\n";
        $csv .= "\"{$report->title}\",\"{$report->type}\",\"{$report->status}\",\"{$report->created_at}\"\n";
        file_put_contents($filePath, $csv);
    }

    private function generateCsvExport(string $filePath, Report $report)
    {
        // CSV generation logic
        $data = $report->data ?? [];
        $csv = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csv .= "Title,Type,Status,Created At\n";
        $csv .= "\"{$report->title}\",\"{$report->type}\",\"{$report->status}\",\"{$report->created_at}\"\n";
        file_put_contents($filePath, $csv);
    }

    private function generateJsonExport(string $filePath, Report $report)
    {
        // JSON generation logic
        $data = [
            'report' => $report->toArray(),
            'data' => $report->data ?? [],
            'visualizations' => $report->visualizations->toArray(),
        ];
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
