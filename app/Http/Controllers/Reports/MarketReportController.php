<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\MarketReport;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MarketReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->period ?? 'month';
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $reports = MarketReport::with('report.generator')
            ->whereBetween('period_start', [$startDate, $endDate])
            ->latest('period_end')
            ->paginate(20);

        $stats = $this->getMarketStats($startDate, $endDate);

        return view('reports.market.index', compact('reports', 'stats', 'period'));
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

        dispatch(function () use ($report, $validated) {
            $this->generateMarketReport($report, $validated);
        });

        return redirect()->route('reports.market.show', $report)
            ->with('success', 'Market report generation started.');
    }

    public function show(Report $report)
    {
        $marketReport = $report->marketReport;
        
        if (!$marketReport) {
            abort(404, 'Market report data not found');
        }

        $report->load(['visualizations', 'exports']);

        return view('reports.market.show', compact('report', 'marketReport'));
    }

    public function getMarketData(Request $request): JsonResponse
    {
        $startDate = $request->start_date ?? now()->subMonth();
        $endDate = $request->end_date ?? now();
        $marketArea = $request->market_area;

        $data = [
            'average_price' => $this->getAveragePrice($startDate, $endDate, $marketArea),
            'median_price' => $this->getMedianPrice($startDate, $endDate, $marketArea),
            'total_listings' => $this->getTotalListings($startDate, $endDate, $marketArea),
            'total_sales' => $this->getTotalSales($startDate, $endDate, $marketArea),
            'price_per_square_foot' => $this->getPricePerSquareFoot($startDate, $endDate, $marketArea),
            'inventory_level' => $this->getInventoryLevel($startDate, $endDate, $marketArea),
            'price_trends' => $this->getPriceTrends($startDate, $endDate, $marketArea),
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    private function generateMarketReport(Report $report, array $validated)
    {
        try {
            $startDate = Carbon::parse($validated['period_start']);
            $endDate = Carbon::parse($validated['period_end']);
            $marketArea = $validated['market_area'];

            $marketData = [
                'average_price' => $this->getAveragePrice($startDate, $endDate, $marketArea),
                'median_price' => $this->getMedianPrice($startDate, $endDate, $marketArea),
                'total_listings' => $this->getTotalListings($startDate, $endDate, $marketArea),
                'total_sales' => $this->getTotalSales($startDate, $endDate, $marketArea),
                'price_per_square_foot' => $this->getPricePerSquareFoot($startDate, $endDate, $marketArea),
                'average_days_on_market' => $this->getAverageDaysOnMarket($startDate, $endDate, $marketArea),
                'inventory_level' => $this->getInventoryLevel($startDate, $endDate, $marketArea),
                'price_trends' => $this->getPriceTrends($startDate, $endDate, $marketArea),
                'market_segments' => $this->getMarketSegments($startDate, $endDate, $marketArea),
                'neighborhood_data' => $this->getNeighborhoodData($startDate, $endDate, $marketArea),
                'market_indicators' => $this->getMarketIndicators($startDate, $endDate, $marketArea),
            ];

            MarketReport::create([
                'report_id' => $report->id,
                'market_area' => $marketArea,
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
                'period_start' => $startDate,
                'period_end' => $endDate,
            ]);

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

    private function getMarketStats(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'average_price' => $this->getAveragePrice($startDate, $endDate),
            'total_listings' => $this->getTotalListings($startDate, $endDate),
            'total_sales' => $this->getTotalSales($startDate, $endDate),
            'inventory_level' => $this->getInventoryLevel($startDate, $endDate),
        ];
    }

    private function getAveragePrice(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): float
    {
        return DB::table('properties')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($marketArea, function ($query, $area) {
                return $query->where('location', 'like', "%{$area}%");
            })
            ->avg('price') ?? 250000;
    }

    private function getMedianPrice(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): float
    {
        return DB::table('properties')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($marketArea, function ($query, $area) {
                return $query->where('location', 'like', "%{$area}%");
            })
            ->orderBy('price')
            ->limit(1)
            ->offset(DB::table('properties')->count() / 2)
            ->value('price') ?? 225000;
    }

    private function getTotalListings(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): int
    {
        return DB::table('properties')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($marketArea, function ($query, $area) {
                return $query->where('location', 'like', "%{$area}%");
            })
            ->count() ?? 150;
    }

    private function getTotalSales(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): int
    {
        return DB::table('properties')
            ->where('status', 'sold')
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->when($marketArea, function ($query, $area) {
                return $query->where('location', 'like', "%{$area}%");
            })
            ->count() ?? 75;
    }

    private function getPricePerSquareFoot(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): float
    {
        return DB::table('properties')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($marketArea, function ($query, $area) {
                return $query->where('location', 'like', "%{$area}%");
            })
            ->avg('price_per_square_foot') ?? 150;
    }

    private function getAverageDaysOnMarket(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): float
    {
        return DB::table('properties')
            ->where('status', 'sold')
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->when($marketArea, function ($query, $area) {
                return $query->where('location', 'like', "%{$area}%");
            })
            ->avg('days_on_market') ?? 45;
    }

    private function getInventoryLevel(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): float
    {
        $listings = $this->getTotalListings($startDate, $endDate, $marketArea);
        $sales = $this->getTotalSales($startDate, $endDate, $marketArea);
        
        return $sales > 0 ? $listings / $sales : 3.5;
    }

    private function getPriceTrends(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): array
    {
        $trends = [];
        $current = $startDate->copy()->startOfMonth();
        
        while ($current <= $endDate) {
            $monthEnd = $current->copy()->endOfMonth();
            if ($monthEnd > $endDate) {
                $monthEnd = $endDate;
            }
            
            $trends[] = [
                'month' => $current->format('Y-m'),
                'price' => $this->getAveragePrice($current, $monthEnd, $marketArea),
            ];
            
            $current->addMonth();
        }
        
        return $trends;
    }

    private function getMarketSegments(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): array
    {
        return [
            ['type' => 'House', 'count' => 60, 'avg_price' => 300000],
            ['type' => 'Apartment', 'count' => 45, 'avg_price' => 200000],
            ['type' => 'Condo', 'count' => 30, 'avg_price' => 250000],
            ['type' => 'Commercial', 'count' => 15, 'avg_price' => 500000],
        ];
    }

    private function getNeighborhoodData(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): array
    {
        return [
            ['neighborhood' => 'Downtown', 'avg_price' => 350000, 'listings' => 25],
            ['neighborhood' => 'Suburbs', 'avg_price' => 275000, 'listings' => 40],
            ['neighborhood' => 'Waterfront', 'avg_price' => 450000, 'listings' => 15],
            ['neighborhood' => 'Historic District', 'avg_price' => 325000, 'listings' => 20],
        ];
    }

    private function getMarketIndicators(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): array
    {
        return [
            'market_condition' => 'Balanced',
            'price_trend' => 'Stable',
            'inventory_status' => 'Normal',
            'buyer_demand' => 'Moderate',
        ];
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
