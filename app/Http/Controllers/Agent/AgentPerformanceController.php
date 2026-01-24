<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AgentPerformance;
use App\Models\Property;
use App\Models\AgentLead;
use App\Models\AgentCommission;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentPerformanceController extends Controller
{
    public function index()
    {
        $agent = Auth::user()->agent;
        
        // Get current month performance
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $performance = [
            'properties_sold' => $agent->properties()->where('status', 'sold')
                ->whereMonth('updated_at', $currentMonth)
                ->whereYear('updated_at', $currentYear)
                ->count(),
            'total_sales' => $agent->properties()->where('status', 'sold')
                ->whereMonth('updated_at', $currentMonth)
                ->whereYear('updated_at', $currentYear)
                ->with('price')
                ->get()
                ->sum(function ($property) {
                    return $property->price?->price ?? 0;
                }),
            'leads_generated' => $agent->leads()
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count(),
            'leads_converted' => $agent->leads()->where('status', 'converted')
                ->whereMonth('updated_at', $currentMonth)
                ->whereYear('updated_at', $currentYear)
                ->count(),
            'commissions_earned' => $agent->commissions()
                ->whereMonth('commission_date', $currentMonth)
                ->whereYear('commission_date', $currentYear)
                ->sum('amount'),
            'appointments_completed' => $agent->appointments()->where('status', 'completed')
                ->whereMonth('appointment_date', $currentMonth)
                ->whereYear('appointment_date', $currentYear)
                ->count(),
        ];

        // Calculate metrics
        $performance['conversion_rate'] = $performance['leads_generated'] > 0 
            ? round(($performance['leads_converted'] / $performance['leads_generated']) * 100, 2)
            : 0;
        
        $performance['average_sale_price'] = $performance['properties_sold'] > 0
            ? round($performance['total_sales'] / $performance['properties_sold'], 2)
            : 0;

        // Get performance history
        $performanceHistory = $this->getPerformanceHistory($agent);

        return view('agent.performance.index', compact('performance', 'performanceHistory'));
    }

    public function show(AgentPerformance $performance)
    {
        $this->authorize('view', $performance);
        
        $performance->load(['agent.profile']);
        
        return view('agent.performance.show', compact('performance'));
    }

    public function create()
    {
        $agent = Auth::user()->agent;
        
        return view('agent.performance.create', compact('agent'));
    }

    public function store(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $request->validate([
            'period' => 'required|in:weekly,monthly,quarterly,yearly',
            'properties_sold' => 'required|integer|min:0',
            'total_sales' => 'required|numeric|min:0',
            'leads_generated' => 'required|integer|min:0',
            'leads_converted' => 'required|integer|min:0',
            'commissions_earned' => 'required|numeric|min:0',
            'appointments_completed' => 'required|integer|min:0',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'notes' => 'nullable|string|max:1000',
        ]);

        $performance = AgentPerformance::create([
            'agent_id' => $agent->id,
            'period' => $request->period,
            'properties_sold' => $request->properties_sold,
            'total_sales' => $request->total_sales,
            'leads_generated' => $request->leads_generated,
            'leads_converted' => $request->leads_converted,
            'commissions_earned' => $request->commissions_earned,
            'appointments_completed' => $request->appointments_completed,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'notes' => $request->notes,
        ]);

        // Calculate derived metrics
        $performance->update([
            'conversion_rate' => $request->leads_generated > 0 
                ? round(($request->leads_converted / $request->leads_generated) * 100, 2)
                : 0,
            'average_sale_price' => $request->properties_sold > 0
                ? round($request->total_sales / $request->properties_sold, 2)
                : 0,
            'performance_score' => $this->calculatePerformanceScore($performance),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_performance_record',
            'details' => "Created performance record for {$request->period}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.performance.show', $performance)
            ->with('success', 'Performance record created successfully.');
    }

    public function edit(AgentPerformance $performance)
    {
        $this->authorize('update', $performance);
        
        return view('agent.performance.edit', compact('performance'));
    }

    public function update(Request $request, AgentPerformance $performance)
    {
        $this->authorize('update', $performance);
        
        $request->validate([
            'properties_sold' => 'required|integer|min:0',
            'total_sales' => 'required|numeric|min:0',
            'leads_generated' => 'required|integer|min:0',
            'leads_converted' => 'required|integer|min:0',
            'commissions_earned' => 'required|numeric|min:0',
            'appointments_completed' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $performance->update([
            'properties_sold' => $request->properties_sold,
            'total_sales' => $request->total_sales,
            'leads_generated' => $request->leads_generated,
            'leads_converted' => $request->leads_converted,
            'commissions_earned' => $request->commissions_earned,
            'appointments_completed' => $request->appointments_completed,
            'notes' => $request->notes,
        ]);

        // Recalculate derived metrics
        $performance->update([
            'conversion_rate' => $request->leads_generated > 0 
                ? round(($request->leads_converted / $request->leads_generated) * 100, 2)
                : 0,
            'average_sale_price' => $request->properties_sold > 0
                ? round($request->total_sales / $request->properties_sold, 2)
                : 0,
            'performance_score' => $this->calculatePerformanceScore($performance),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_performance_record',
            'details' => "Updated performance record for {$performance->period}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.performance.show', $performance)
            ->with('success', 'Performance record updated successfully.');
    }

    public function destroy(AgentPerformance $performance)
    {
        $this->authorize('delete', $performance);
        
        $period = $performance->period;
        $performance->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_performance_record',
            'details' => "Deleted performance record for {$period}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('agent.performance.index')
            ->with('success', 'Performance record deleted successfully.');
    }

    public function getPerformanceMetrics(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $period = $request->period ?? 'monthly';
        $months = $request->months ?? 12;

        $startDate = now()->subMonths($months);
        $endDate = now();

        $query = $agent->performances()
            ->where('period', $period)
            ->whereBetween('period_start', [$startDate, $endDate])
            ->orderBy('period_start');

        $performances = $query->get();

        return response()->json([
            'success' => true,
            'performances' => $performances,
            'summary' => [
                'total_properties_sold' => $performances->sum('properties_sold'),
                'total_sales' => $performances->sum('total_sales'),
                'total_commissions' => $performances->sum('commissions_earned'),
                'average_conversion_rate' => $performances->avg('conversion_rate'),
                'average_performance_score' => $performances->avg('performance_score'),
            ]
        ]);
    }

    public function getPerformanceChart(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $period = $request->period ?? 'monthly';
        $months = $request->months ?? 12;

        $startDate = now()->subMonths($months);
        
        $data = $agent->performances()
            ->where('period', $period)
            ->where('period_start', '>=', $startDate)
            ->orderBy('period_start')
            ->get(['period_start', 'properties_sold', 'total_sales', 'commissions_earned', 'conversion_rate', 'performance_score']);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getPerformanceComparison(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        // Get agent's current month performance
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $agentPerformance = [
            'properties_sold' => $agent->properties()->where('status', 'sold')
                ->whereMonth('updated_at', $currentMonth)
                ->whereYear('updated_at', $currentYear)
                ->count(),
            'total_sales' => $agent->properties()->where('status', 'sold')
                ->whereMonth('updated_at', $currentMonth)
                ->whereYear('updated_at', $currentYear)
                ->with('price')
                ->get()
                ->sum(function ($property) {
                    return $property->price?->price ?? 0;
                }),
        ];

        // Get average performance for all agents
        $averagePerformance = AgentPerformance::where('period', 'monthly')
            ->whereMonth('period_start', $currentMonth)
            ->whereYear('period_start', $currentYear)
            ->avg([
                'properties_sold' => 'properties_sold',
                'total_sales' => 'total_sales',
            ]);

        return response()->json([
            'success' => true,
            'agent_performance' => $agentPerformance,
            'average_performance' => $averagePerformance,
            'comparison' => [
                'properties_sold_diff' => $agentPerformance['properties_sold'] - ($averagePerformance['properties_sold'] ?? 0),
                'total_sales_diff' => $agentPerformance['total_sales'] - ($averagePerformance['total_sales'] ?? 0),
            ]
        ]);
    }

    public function exportPerformance(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'period' => 'nullable|in:weekly,monthly,quarterly,yearly',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $agent = Auth::user()->agent;
        
        $query = $agent->performances();

        if ($request->period) {
            $query->where('period', $request->period);
        }

        if ($request->date_from) {
            $query->whereDate('period_start', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('period_start', '<=', $request->date_to);
        }

        $performances = $query->orderBy('period_start')->get();

        $filename = "agent_performance_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $performances,
            'filename' => $filename,
            'message' => 'Performance data exported successfully'
        ]);
    }

    private function getPerformanceHistory(Agent $agent): array
    {
        return $agent->performances()
            ->where('period', 'monthly')
            ->orderBy('period_start', 'desc')
            ->limit(12)
            ->get(['period_start', 'properties_sold', 'total_sales', 'conversion_rate', 'performance_score'])
            ->toArray();
    }

    private function calculatePerformanceScore(AgentPerformance $performance): float
    {
        // Weight factors for different metrics
        $weights = [
            'sales' => 0.3,
            'conversions' => 0.25,
            'commissions' => 0.2,
            'appointments' => 0.15,
            'consistency' => 0.1,
        ];

        // Normalize scores (0-100 scale)
        $salesScore = min(($performance->total_sales / 1000000) * 100, 100);
        $conversionScore = min($performance->conversion_rate * 2, 100); // 50% conversion = 100 points
        $commissionScore = min(($performance->commissions_earned / 10000) * 100, 100);
        $appointmentScore = min($performance->appointments_completed * 5, 100);
        $consistencyScore = 100; // Could be calculated based on historical consistency

        $totalScore = (
            $salesScore * $weights['sales'] +
            $conversionScore * $weights['conversions'] +
            $commissionScore * $weights['commissions'] +
            $appointmentScore * $weights['appointments'] +
            $consistencyScore * $weights['consistency']
        );

        return round($totalScore, 2);
    }
}
