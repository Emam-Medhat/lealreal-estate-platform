<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\SalesReport;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->period ?? 'month';
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $reports = SalesReport::with('report.generator')
            ->whereBetween('period_start', [$startDate, $endDate])
            ->latest('period_end')
            ->paginate(20);

        $stats = $this->getSalesStats($startDate, $endDate);

        return view('reports.sales.index', compact('reports', 'stats', 'period'));
    }

    public function create()
    {
        return view('reports.sales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'filters' => 'nullable|array',
            'format' => 'required|in:pdf,excel,csv',
        ]);

        $report = Report::create([
            'title' => $validated['title'],
            'type' => 'sales',
            'description' => 'Sales report from ' . $validated['period_start'] . ' to ' . $validated['period_end'],
            'parameters' => [
                'period_start' => $validated['period_start'],
                'period_end' => $validated['period_end'],
            ],
            'filters' => $validated['filters'] ?? [],
            'status' => 'generating',
            'format' => $validated['format'],
            'generated_by' => auth()->id(),
        ]);

        // Generate sales report data
        dispatch(function () use ($report, $validated) {
            $this->generateSalesReport($report, $validated);
        });

        return redirect()->route('reports.sales.show', $report)
            ->with('success', 'Sales report generation started.');
    }

    public function show(Report $report)
    {
        $salesReport = $report->salesReport;
        
        if (!$salesReport) {
            abort(404, 'Sales report data not found');
        }

        $report->load(['visualizations', 'exports']);

        return view('reports.sales.show', compact('report', 'salesReport'));
    }

    public function getSalesData(Request $request): JsonResponse
    {
        $startDate = $request->start_date ?? now()->subMonth();
        $endDate = $request->end_date ?? now();

        $data = [
            'total_sales' => $this->getTotalSales($startDate, $endDate),
            'total_commission' => $this->getTotalCommission($startDate, $endDate),
            'properties_sold' => $this->getPropertiesSold($startDate, $endDate),
            'average_sale_price' => $this->getAverageSalePrice($startDate, $endDate),
            'sales_by_agent' => $this->getSalesByAgent($startDate, $endDate),
            'sales_by_property_type' => $this->getSalesByPropertyType($startDate, $endDate),
            'monthly_sales' => $this->getMonthlySales($startDate, $endDate),
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    private function generateSalesReport(Report $report, array $validated)
    {
        try {
            $startDate = Carbon::parse($validated['period_start']);
            $endDate = Carbon::parse($validated['period_end']);

            $salesData = [
                'total_sales' => $this->getTotalSales($startDate, $endDate),
                'total_commission' => $this->getTotalCommission($startDate, $endDate),
                'properties_sold' => $this->getPropertiesSold($startDate, $endDate),
                'average_sale_price' => $this->getAverageSalePrice($startDate, $endDate),
                'average_days_on_market' => $this->getAverageDaysOnMarket($startDate, $endDate),
                'sales_by_agent' => $this->getSalesByAgent($startDate, $endDate),
                'sales_by_property_type' => $this->getSalesByPropertyType($startDate, $endDate),
                'sales_by_location' => $this->getSalesByLocation($startDate, $endDate),
                'monthly_sales' => $this->getMonthlySales($startDate, $endDate),
            ];

            $salesReport = SalesReport::create([
                'report_id' => $report->id,
                'total_sales' => $salesData['total_sales'],
                'total_commission' => $salesData['total_commission'],
                'properties_sold' => $salesData['properties_sold'],
                'average_sale_price' => $salesData['average_sale_price'],
                'average_days_on_market' => $salesData['average_days_on_market'],
                'sales_by_agent' => $salesData['sales_by_agent'],
                'sales_by_property_type' => $salesData['sales_by_property_type'],
                'sales_by_location' => $salesData['sales_by_location'],
                'monthly_sales' => $salesData['monthly_sales'],
                'period_start' => $startDate,
                'period_end' => $endDate,
            ]);

            $report->update([
                'data' => $salesData,
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

    private function getSalesStats(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'total_sales' => $this->getTotalSales($startDate, $endDate),
            'total_commission' => $this->getTotalCommission($startDate, $endDate),
            'properties_sold' => $this->getPropertiesSold($startDate, $endDate),
            'average_sale_price' => $this->getAverageSalePrice($startDate, $endDate),
        ];
    }

    private function getTotalSales(Carbon $startDate, Carbon $endDate): float
    {
        // Mock implementation - replace with actual database queries
        return DB::table('properties')
            ->where('status', 'sold')
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->sum('sale_price') ?? 1500000;
    }

    private function getTotalCommission(Carbon $startDate, Carbon $endDate): float
    {
        $totalSales = $this->getTotalSales($startDate, $endDate);
        return $totalSales * 0.05; // 5% commission rate
    }

    private function getPropertiesSold(Carbon $startDate, Carbon $endDate): int
    {
        return DB::table('properties')
            ->where('status', 'sold')
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->count() ?? 25;
    }

    private function getAverageSalePrice(Carbon $startDate, Carbon $endDate): float
    {
        $totalSales = $this->getTotalSales($startDate, $endDate);
        $propertiesSold = $this->getPropertiesSold($startDate, $endDate);
        
        return $propertiesSold > 0 ? $totalSales / $propertiesSold : 0;
    }

    private function getAverageDaysOnMarket(Carbon $startDate, Carbon $endDate): float
    {
        return DB::table('properties')
            ->where('status', 'sold')
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->avg('days_on_market') ?? 45.5;
    }

    private function getSalesByAgent(Carbon $startDate, Carbon $endDate): array
    {
        // Mock data - replace with actual database queries
        return [
            ['agent_id' => 1, 'agent_name' => 'John Doe', 'total_sales' => 500000, 'properties_sold' => 8],
            ['agent_id' => 2, 'agent_name' => 'Jane Smith', 'total_sales' => 750000, 'properties_sold' => 12],
            ['agent_id' => 3, 'agent_name' => 'Bob Johnson', 'total_sales' => 250000, 'properties_sold' => 5],
        ];
    }

    private function getSalesByPropertyType(Carbon $startDate, Carbon $endDate): array
    {
        return [
            ['type' => 'House', 'count' => 15, 'total_value' => 900000],
            ['type' => 'Apartment', 'count' => 8, 'total_value' => 400000],
            ['type' => 'Condo', 'count' => 2, 'total_value' => 200000],
        ];
    }

    private function getSalesByLocation(Carbon $startDate, Carbon $endDate): array
    {
        return [
            ['location' => 'Downtown', 'count' => 10, 'total_value' => 800000],
            ['location' => 'Suburbs', 'count' => 12, 'total_value' => 600000],
            ['location' => 'Waterfront', 'count' => 3, 'total_value' => 100000],
        ];
    }

    private function getMonthlySales(Carbon $startDate, Carbon $endDate): array
    {
        $monthlyData = [];
        $current = $startDate->copy()->startOfMonth();
        
        while ($current <= $endDate) {
            $monthEnd = $current->copy()->endOfMonth();
            if ($monthEnd > $endDate) {
                $monthEnd = $endDate;
            }
            
            $monthlyData[] = [
                'month' => $current->format('Y-m'),
                'sales' => $this->getTotalSales($current, $monthEnd),
                'properties' => $this->getPropertiesSold($current, $monthEnd),
            ];
            
            $current->addMonth();
        }
        
        return $monthlyData;
    }

    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };
    }
}
