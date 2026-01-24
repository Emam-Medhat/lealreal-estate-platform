<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\PerformanceReport;
use App\Models\Report;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->period ?? 'month';
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $reports = PerformanceReport::with(['report.generator', 'agent'])
            ->whereBetween('period_start', [$startDate, $endDate])
            ->latest('period_end')
            ->paginate(20);

        $stats = $this->getPerformanceStats($startDate, $endDate);
        $topAgents = $this->getTopPerformingAgents($startDate, $endDate, 5);

        return view('reports.performance.index', compact('reports', 'stats', 'topAgents', 'period'));
    }

    public function create()
    {
        $agents = Agent::with('user')->where('status', 'active')->get();
        
        return view('reports.performance.create', compact('agents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'agent_id' => 'nullable|exists:agents,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'filters' => 'nullable|array',
            'format' => 'required|in:pdf,excel,csv',
        ]);

        $report = Report::create([
            'title' => $validated['title'],
            'type' => 'performance',
            'description' => 'Performance report from ' . $validated['period_start'] . ' to ' . $validated['period_end'],
            'parameters' => [
                'agent_id' => $validated['agent_id'],
                'period_start' => $validated['period_start'],
                'period_end' => $validated['period_end'],
            ],
            'filters' => $validated['filters'] ?? [],
            'status' => 'generating',
            'format' => $validated['format'],
            'generated_by' => auth()->id(),
        ]);

        // Generate performance report data
        dispatch(function () use ($report, $validated) {
            $this->generatePerformanceReport($report, $validated);
        });

        return redirect()->route('reports.performance.show', $report)
            ->with('success', 'Performance report generation started.');
    }

    public function show(Report $report)
    {
        $performanceReport = $report->performanceReport;
        
        if (!$performanceReport) {
            abort(404, 'Performance report data not found');
        }

        $report->load(['visualizations', 'exports']);

        return view('reports.performance.show', compact('report', 'performanceReport'));
    }

    public function getPerformanceData(Request $request): JsonResponse
    {
        $startDate = $request->start_date ?? now()->subMonth();
        $endDate = $request->end_date ?? now();
        $agentId = $request->agent_id;

        $data = [
            'total_sales' => $this->getTotalSales($startDate, $endDate, $agentId),
            'total_commission' => $this->getTotalCommission($startDate, $endDate, $agentId),
            'properties_listed' => $this->getPropertiesListed($startDate, $endDate, $agentId),
            'properties_sold' => $this->getPropertiesSold($startDate, $endDate, $agentId),
            'conversion_rate' => $this->getConversionRate($startDate, $endDate, $agentId),
            'average_sale_price' => $this->getAverageSalePrice($startDate, $endDate, $agentId),
            'customer_satisfaction' => $this->getCustomerSatisfaction($startDate, $endDate, $agentId),
            'leads_generated' => $this->getLeadsGenerated($startDate, $endDate, $agentId),
            'monthly_performance' => $this->getMonthlyPerformance($startDate, $endDate, $agentId),
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    private function generatePerformanceReport(Report $report, array $validated)
    {
        try {
            $startDate = Carbon::parse($validated['period_start']);
            $endDate = Carbon::parse($validated['period_end']);
            $agentId = $validated['agent_id'];

            $performanceData = [
                'total_sales' => $this->getTotalSales($startDate, $endDate, $agentId),
                'total_commission' => $this->getTotalCommission($startDate, $endDate, $agentId),
                'properties_listed' => $this->getPropertiesListed($startDate, $endDate, $agentId),
                'properties_sold' => $this->getPropertiesSold($startDate, $endDate, $agentId),
                'conversion_rate' => $this->getConversionRate($startDate, $endDate, $agentId),
                'average_sale_price' => $this->getAverageSalePrice($startDate, $endDate, $agentId),
                'customer_satisfaction' => $this->getCustomerSatisfaction($startDate, $endDate, $agentId),
                'leads_generated' => $this->getLeadsGenerated($startDate, $endDate, $agentId),
                'appointments_scheduled' => $this->getAppointmentsScheduled($startDate, $endDate, $agentId),
                'monthly_performance' => $this->getMonthlyPerformance($startDate, $endDate, $agentId),
                'performance_metrics' => $this->getPerformanceMetrics($startDate, $endDate, $agentId),
            ];

            PerformanceReport::create([
                'report_id' => $report->id,
                'agent_id' => $agentId,
                'total_sales' => $performanceData['total_sales'],
                'total_commission' => $performanceData['total_commission'],
                'properties_listed' => $performanceData['properties_listed'],
                'properties_sold' => $performanceData['properties_sold'],
                'conversion_rate' => $performanceData['conversion_rate'],
                'average_sale_price' => $performanceData['average_sale_price'],
                'customer_satisfaction' => $performanceData['customer_satisfaction'],
                'leads_generated' => $performanceData['leads_generated'],
                'appointments_scheduled' => $performanceData['appointments_scheduled'],
                'monthly_performance' => $performanceData['monthly_performance'],
                'performance_metrics' => $performanceData['performance_metrics'],
                'period_start' => $startDate,
                'period_end' => $endDate,
            ]);

            $report->update([
                'data' => $performanceData,
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

    private function getPerformanceStats(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'total_sales' => $this->getTotalSales($startDate, $endDate),
            'total_commission' => $this->getTotalCommission($startDate, $endDate),
            'properties_sold' => $this->getPropertiesSold($startDate, $endDate),
            'average_conversion_rate' => $this->getAverageConversionRate($startDate, $endDate),
        ];
    }

    private function getTopPerformingAgents(Carbon $startDate, Carbon $endDate, int $limit): array
    {
        // Mock implementation - replace with actual database queries
        return [
            ['agent_id' => 2, 'agent_name' => 'Jane Smith', 'total_sales' => 750000, 'conversion_rate' => 85.5, 'score' => 92],
            ['agent_id' => 1, 'agent_name' => 'John Doe', 'total_sales' => 500000, 'conversion_rate' => 78.2, 'score' => 85],
            ['agent_id' => 3, 'agent_name' => 'Bob Johnson', 'total_sales' => 250000, 'conversion_rate' => 92.1, 'score' => 78],
        ];
    }

    private function getTotalSales(Carbon $startDate, Carbon $endDate, ?int $agentId = null): float
    {
        $query = DB::table('properties')
            ->where('status', 'sold')
            ->whereBetween('sold_at', [$startDate, $endDate]);

        if ($agentId) {
            $query->where('agent_id', $agentId);
        }

        return $query->sum('sale_price') ?? 500000;
    }

    private function getTotalCommission(Carbon $startDate, Carbon $endDate, ?int $agentId = null): float
    {
        $totalSales = $this->getTotalSales($startDate, $endDate, $agentId);
        return $totalSales * 0.05;
    }

    private function getPropertiesListed(Carbon $startDate, Carbon $endDate, ?int $agentId = null): int
    {
        $query = DB::table('properties')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($agentId) {
            $query->where('agent_id', $agentId);
        }

        return $query->count() ?? 35;
    }

    private function getPropertiesSold(Carbon $startDate, Carbon $endDate, ?int $agentId = null): int
    {
        $query = DB::table('properties')
            ->where('status', 'sold')
            ->whereBetween('sold_at', [$startDate, $endDate]);

        if ($agentId) {
            $query->where('agent_id', $agentId);
        }

        return $query->count() ?? 25;
    }

    private function getConversionRate(Carbon $startDate, Carbon $endDate, ?int $agentId = null): float
    {
        $listed = $this->getPropertiesListed($startDate, $endDate, $agentId);
        $sold = $this->getPropertiesSold($startDate, $endDate, $agentId);
        
        return $listed > 0 ? ($sold / $listed) * 100 : 0;
    }

    private function getAverageConversionRate(Carbon $startDate, Carbon $endDate): float
    {
        return $this->getConversionRate($startDate, $endDate);
    }

    private function getAverageSalePrice(Carbon $startDate, Carbon $endDate, ?int $agentId = null): float
    {
        $totalSales = $this->getTotalSales($startDate, $endDate, $agentId);
        $propertiesSold = $this->getPropertiesSold($startDate, $endDate, $agentId);
        
        return $propertiesSold > 0 ? $totalSales / $propertiesSold : 0;
    }

    private function getCustomerSatisfaction(Carbon $startDate, Carbon $endDate, ?int $agentId = null): float
    {
        $query = DB::table('reviews')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($agentId) {
            $query->where('agent_id', $agentId);
        }

        return $query->avg('rating') ?? 4.5;
    }

    private function getLeadsGenerated(Carbon $startDate, Carbon $endDate, ?int $agentId = null): int
    {
        $query = DB::table('leads')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($agentId) {
            $query->where('agent_id', $agentId);
        }

        return $query->count() ?? 150;
    }

    private function getAppointmentsScheduled(Carbon $startDate, Carbon $endDate, ?int $agentId = null): int
    {
        $query = DB::table('appointments')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($agentId) {
            $query->where('agent_id', $agentId);
        }

        return $query->count() ?? 75;
    }

    private function getMonthlyPerformance(Carbon $startDate, Carbon $endDate, ?int $agentId = null): array
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
                'sales' => $this->getTotalSales($current, $monthEnd, $agentId),
                'properties_sold' => $this->getPropertiesSold($current, $monthEnd, $agentId),
                'conversion_rate' => $this->getConversionRate($current, $monthEnd, $agentId),
                'leads' => $this->getLeadsGenerated($current, $monthEnd, $agentId),
            ];
            
            $current->addMonth();
        }
        
        return $monthlyData;
    }

    private function getPerformanceMetrics(Carbon $startDate, Carbon $endDate, ?int $agentId = null): array
    {
        return [
            'productivity_score' => $this->calculateProductivityScore($startDate, $endDate, $agentId),
            'efficiency_rating' => $this->calculateEfficiencyRating($startDate, $endDate, $agentId),
            'client_satisfaction' => $this->getCustomerSatisfaction($startDate, $endDate, $agentId),
            'lead_conversion' => $this->getConversionRate($startDate, $endDate, $agentId),
        ];
    }

    private function calculateProductivityScore(Carbon $startDate, Carbon $endDate, ?int $agentId = null): float
    {
        $sales = $this->getTotalSales($startDate, $endDate, $agentId);
        $appointments = $this->getAppointmentsScheduled($startDate, $endDate, $agentId);
        
        // Simple productivity calculation
        return min(($sales / 10000) + ($appointments * 2), 100);
    }

    private function calculateEfficiencyRating(Carbon $startDate, Carbon $endDate, ?int $agentId = null): float
    {
        $conversionRate = $this->getConversionRate($startDate, $endDate, $agentId);
        $satisfaction = $this->getCustomerSatisfaction($startDate, $endDate, $agentId);
        
        return ($conversionRate * 0.6) + ($satisfaction * 20 * 0.4); // Scale satisfaction to 100
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
