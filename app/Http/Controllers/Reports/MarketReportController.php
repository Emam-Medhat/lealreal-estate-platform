<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\MarketReport;
use App\Models\Report;
use App\Services\MarketService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class MarketReportController extends Controller
{
    protected $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    public function index(Request $request)
    {
        $period = $request->period ?? 'month';
        $startDate = $this->getStartDate($period);
        $endDate = now();

        try {
            $reports = MarketReport::with('report.generator')
                ->whereBetween('period_start', [$startDate, $endDate])
                ->latest('period_end')
                ->paginate(20);

            $stats = $this->marketService->getMarketMetrics($startDate, $endDate);
            // Adapt stats to view expected format
            $stats['total_reports'] = $reports->total();
            $stats['avg_growth'] = '0%'; // Placeholder
            $stats['available_properties'] = $stats['total_listings'];
            $stats['avg_price'] = $stats['average_price'];

        } catch (\Exception $e) {
             // Fallback
             $reports = collect();
             $stats = [
                'total_reports' => 0,
                'avg_growth' => '0%',
                'available_properties' => 0,
                'avg_price' => 0,
            ];
        }
        
        return view('reports.market.index', compact('reports', 'stats', 'period'));
    }

    private function getStartDate($period)
    {
        return match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };
    }

    public function create()
    {
        return view('reports.market.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'market_area' => 'required|string|max:255',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'filters' => 'nullable|array',
            'format' => 'required|in:pdf,excel,csv',
        ]);

        $report = Report::create([
            'title' => $validated['title'],
            'type' => 'market',
            'description' => 'Market report for ' . $validated['market_area'] . ' from ' . $validated['period_start'] . ' to ' . $validated['period_end'],
            'parameters' => [
                'market_area' => $validated['market_area'],
                'period_start' => $validated['period_start'],
                'period_end' => $validated['period_end'],
            ],
            'filters' => $validated['filters'] ?? [],
            'status' => 'generating',
            'format' => $validated['format'],
            'generated_by' => auth()->id(),
        ]);

        // In a real app, dispatch to queue
        // dispatch(function () use ($report, $validated) {
             $this->generateMarketReport($report, $validated);
        // });

        return redirect()->route('reports.market.show', $report)
            ->with('success', 'Market report generation started.');
    }

    public function show($id)
    {
        $report = Report::findOrFail($id);
        
        if ($report->type !== 'market') {
            abort(404, 'This is not a market report');
        }

        $report->load(['exports']);

        return view('reports.market.show', compact('report'));
    }

    public function getMarketData(Request $request): JsonResponse
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->subMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now();
        $marketArea = $request->market_area;

        $data = $this->marketService->getMarketMetrics($startDate, $endDate, $marketArea);

        return response()->json(['success' => true, 'data' => $data]);
    }

    private function generateMarketReport(Report $report, array $validated)
    {
        try {
            $startDate = Carbon::parse($validated['period_start']);
            $endDate = Carbon::parse($validated['period_end']);
            $marketArea = $validated['market_area'];

            $marketData = $this->marketService->getMarketMetrics($startDate, $endDate, $marketArea);

            MarketReport::create(array_merge([
                'report_id' => $report->id,
                'market_area' => $marketArea,
                'period_start' => $startDate,
                'period_end' => $endDate,
                // Map fields from marketData to Model fields
                'average_property_price' => $marketData['average_price'],
                'median_property_price' => $marketData['median_price'],
                'total_listings' => $marketData['total_listings'],
                'total_sales' => $marketData['total_sales'],
                'price_per_square_foot' => $marketData['price_per_square_foot'],
                'average_days_on_market' => $marketData['average_days_on_market'],
                'inventory_level' => $marketData['inventory_level'],
                'price_trends' => $marketData['price_trends'],
                'market_segments' => $marketData['market_segments'],
                'neighborhood_data' => $marketData['neighborhood_data'],
                'market_indicators' => $marketData['market_indicators'],
            ]));

            $report->update([
                'data' => $marketData,
                'status' => 'completed',
                'generated_at' => now(),
            ]);

        } catch (\Exception $e) {
            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
