<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::latest()
            ->paginate(15);
            
        return view('reports.index', compact('reports'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // Quick stats
        $totalReports = Report::count();
        $recentReportsCount = Report::where('created_at', '>=', now()->subDays(30))
            ->count();
        $scheduledReports = 0; // $user->reportSchedules()->where('is_active', true)->count();
        
        // Recent reports
        $recentReports = Report::latest()
            ->take(5)
            ->get();
            
        // Popular templates
        $popularTemplates = []; // ReportTemplate::withCount(['reports' => function($query) use ($user) {
        //     $query->where('user_id', $user->id);
        // }])
        // ->orderBy('reports_count', 'desc')
        // ->take(5)
        // ->get();
        
        return view('reports.dashboard', compact(
            'totalReports', 'recentReportsCount', 'scheduledReports',
            'recentReports', 'popularTemplates'
        ));
    }

    public function create()
    {
        $templates = ReportTemplate::where('is_active', true)->get();
        return view('reports.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parameters' => 'nullable|array',
            'filters' => 'nullable|array',
            'date_range' => 'nullable|array',
            'format' => 'required|in:pdf,excel,csv,html'
        ]);

        $report = Report::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => 'custom',
            'generated_by' => Auth::user()->email,
            'user_id' => Auth::id(),
            'parameters' => $validated['parameters'] ?? [],
            'filters' => $validated['filters'] ?? [],
            'data' => $validated['date_range'] ?? [],
            'format' => $validated['format'],
            'status' => 'pending',
            'generated_at' => null
        ]);

        // Queue report generation
        $this->generateReport($report);

        return redirect()->route('reports.show', $report->id)
            ->with('success', 'تم إنشاء التقرير بنجاح وجاري معالجته');
    }

    public function show(Report $report)
    {
        $report->load(['exports']);
        
        return view('reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        $this->authorize('update', $report);
        
        $templates = ReportTemplate::where('active', true)->get();
        
        return view('reports.edit', compact('report', 'templates'));
    }

    public function update(Request $request, Report $report)
    {
        $this->authorize('update', $report);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parameters' => 'nullable|array',
            'filters' => 'nullable|array',
            'date_range' => 'nullable|array'
        ]);

        $report->update($validated);

        return redirect()->route('reports.show', $report->id)
            ->with('success', 'تم تحديث التقرير بنجاح');
    }

    public function destroy(Report $report)
    {
        $this->authorize('delete', $report);
        
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'تم حذف التقرير بنجاح');
    }

    public function regenerate(Report $report)
    {
        $this->authorize('update', $report);
        
        $report->update(['status' => 'pending', 'generated_at' => null]);
        
        $this->generateReport($report);

        return back()->with('success', 'جاري إعادة إنشاء التقرير');
    }

    public function download(Report $report, $format = null)
    {
        $this->authorize('view', $report);
        
        if ($report->status !== 'completed') {
            return back()->with('error', 'التقرير لم يكتمل بعد');
        }

        $format = $format ?: $report->format;
        $filePath = storage_path("app/reports/{$report->id}/report.{$format}");
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'ملف التقرير غير موجود');
        }

        return response()->download($filePath, "{$report->title}.{$format}");
    }

    public function preview(Report $report)
    {
        $this->authorize('view', $report);
        
        if ($report->status !== 'completed') {
            return response()->json(['error' => 'التقرير لم يكتمل بعد'], 422);
        }

        $htmlPath = storage_path("app/reports/{$report->id}/report.html");
        
        if (!file_exists($htmlPath)) {
            return response()->json(['error' => 'معاينة التقرير غير متوفرة'], 422);
        }

        return response()->json([
            'html' => file_get_contents($htmlPath)
        ]);
    }

    private function generateReport(Report $report)
    {
        // Create report directory
        $reportDir = storage_path("app/reports/{$report->id}");
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0755, true);
        }

        // Update status to processing
        $report->update(['status' => 'processing']);

        // Queue the actual generation
        dispatch(function () use ($report) {
            try {
                $template = $report->template;
                $data = $this->collectReportData($report);
                
                // Generate HTML version
                $html = $this->generateHtmlReport($template, $data, $report);
                file_put_contents(storage_path("app/reports/{$report->id}/report.html"), $html);
                
                // Generate other formats based on request
                if ($report->format === 'pdf') {
                    $this->generatePdfReport($html, $report);
                } elseif ($report->format === 'excel') {
                    $this->generateExcelReport($data, $report);
                } elseif ($report->format === 'csv') {
                    $this->generateCsvReport($data, $report);
                }
                
                $report->update([
                    'status' => 'completed',
                    'generated_at' => now(),
                    'file_size' => $this->getReportFileSize($report)
                ]);
                
            } catch (\Exception $e) {
                $report->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }
        });
    }

    private function collectReportData(Report $report)
    {
        $template = $report->template;
        $parameters = $report->parameters ?? [];
        $filters = $report->filters ?? [];
        $dateRange = $report->date_range ?? [];

        $data = [];

        switch ($template->type) {
            case 'sales':
                $data = $this->getSalesData($parameters, $filters, $dateRange);
                break;
            case 'performance':
                $data = $this->getPerformanceData($parameters, $filters, $dateRange);
                break;
            case 'market':
                $data = $this->getMarketData($parameters, $filters, $dateRange);
                break;
            default:
                $data = $this->getGeneralData($parameters, $filters, $dateRange);
        }

        return $data;
    }

    private function getSalesData($parameters, $filters, $dateRange)
    {
        $query = DB::table('properties')
            ->join('transactions', 'properties.id', '=', 'transactions.property_id')
            ->where('transactions.status', 'completed');

        if (isset($dateRange['start'])) {
            $query->where('transactions.created_at', '>=', $dateRange['start']);
        }
        if (isset($dateRange['end'])) {
            $query->where('transactions.created_at', '<=', $dateRange['end']);
        }

        return [
            'total_sales' => $query->sum('transactions.amount'),
            'total_transactions' => $query->count(),
            'average_price' => $query->avg('transactions.amount'),
            'sales_by_month' => $query->selectRaw('YEAR(transactions.created_at) as year, MONTH(transactions.created_at) as month, SUM(transactions.amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get(),
            'top_properties' => $query->select('properties.title', 'properties.location', 'transactions.amount')
                ->orderBy('transactions.amount', 'desc')
                ->limit(10)
                ->get()
        ];
    }

    private function getPerformanceData($parameters, $filters, $dateRange)
    {
        $query = DB::table('properties')
            ->where('properties.user_id', Auth::id());

        if (isset($dateRange['start'])) {
            $query->where('properties.created_at', '>=', $dateRange['start']);
        }
        if (isset($dateRange['end'])) {
            $query->where('properties.created_at', '<=', $dateRange['end']);
        }

        return [
            'total_properties' => $query->count(),
            'active_listings' => $query->where('status', 'active')->count(),
            'sold_properties' => $query->where('status', 'sold')->count(),
            'average_days_on_market' => $query->avg('days_on_market'),
            'views_by_property' => $query->select('properties.title', 'properties.views_count')
                ->orderBy('properties.views_count', 'desc')
                ->limit(10)
                ->get()
        ];
    }

    private function getMarketData($parameters, $filters, $dateRange)
    {
        return [
            'market_trends' => $this->getMarketTrends($dateRange),
            'price_analysis' => $this->getPriceAnalysis($filters),
            'inventory_levels' => $this->getInventoryLevels(),
            'demand_indicators' => $this->getDemandIndicators($dateRange)
        ];
    }

    private function getGeneralData($parameters, $filters, $dateRange)
    {
        return [
            'summary' => $this->getGeneralSummary($dateRange),
            'charts' => $this->getGeneralCharts($parameters, $dateRange),
            'tables' => $this->getGeneralTables($filters, $dateRange)
        ];
    }

    private function generateHtmlReport($template, $data, $report)
    {
        $view = 'reports.templates.' . $template->slug;
        
        if (!view()->exists($view)) {
            $view = 'reports.templates.default';
        }

        return view($view, [
            'report' => $report,
            'template' => $template,
            'data' => $data,
            'generated_at' => now()
        ])->render();
    }

    private function generatePdfReport($html, $report)
    {
        // Implementation for PDF generation
        // This would use a library like DomPDF or TCPDF
    }

    private function generateExcelReport($data, $report)
    {
        // Implementation for Excel generation
        // This would use a library like Laravel Excel
    }

    private function generateCsvReport($data, $report)
    {
        // Implementation for CSV generation
    }

    private function getReportFileSize($report)
    {
        $file = storage_path("app/reports/{$report->id}/report.{$report->format}");
        return file_exists($file) ? filesize($file) : 0;
    }

    // Additional helper methods for market data collection
    private function getMarketTrends($dateRange)
    {
        // Implementation for market trends
        return [];
    }

    private function getPriceAnalysis($filters)
    {
        // Implementation for price analysis
        return [];
    }

    private function getInventoryLevels()
    {
        // Implementation for inventory levels
        return [];
    }

    private function getDemandIndicators($dateRange)
    {
        // Implementation for demand indicators
        return [];
    }

    private function getGeneralSummary($dateRange)
    {
        // Implementation for general summary
        return [];
    }

    private function getGeneralCharts($parameters, $dateRange)
    {
        // Implementation for general charts
        return [];
    }

    private function getGeneralTables($filters, $dateRange)
    {
        // Implementation for general tables
        return [];
    }
}
