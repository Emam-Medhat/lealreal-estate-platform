<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Lead;
use App\Models\Appointment;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('agent');
    }

    public function index()
    {
        $agent = Auth::user();
        
        // Get basic statistics
        $stats = [
            'active_properties' => Property::where('agent_id', $agent->id)->where('status', 'active')->count(),
            'total_properties' => Property::where('agent_id', $agent->id)->count(),
            'new_leads' => Lead::where('agent_id', $agent->id)->where('lead_status', 'new')->count(),
            'total_leads' => Lead::where('agent_id', $agent->id)->count(),
            'today_appointments' => Appointment::where('agent_id', $agent->id)->whereDate('start_datetime', today())->count(),
            'week_appointments' => Appointment::where('agent_id', $agent->id)->whereBetween('start_datetime', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'expected_commissions' => Commission::where('agent_id', $agent->id)->where('status', 'approved')->sum('amount'),
            'collected_commissions' => Commission::where('agent_id', $agent->id)->where('status', 'paid')->sum('amount'),
        ];

        // Property performance
        $propertyPerformance = Property::where('agent_id', $agent->id)
            ->withCount(['views', 'favorites'])
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        // Recent activities
        $recentActivities = [
            'recent_appointments' => Appointment::where('agent_id', $agent->id)
                ->with(['lead', 'property'])
                ->orderBy('start_datetime', 'desc')
                ->take(5)
                ->get(),
            'recent_leads' => Lead::where('agent_id', $agent->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'recent_commissions' => Commission::where('agent_id', $agent->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];

        // Monthly performance data for charts
        $monthlyData = $this->getMonthlyPerformanceData($agent->id);

        // Today's tasks
        $todayTasks = $this->getTodayTasks($agent->id);

        return view('agent.dashboard.index', compact(
            'stats',
            'propertyPerformance',
            'recentActivities',
            'monthlyData',
            'todayTasks'
        ));
    }

    private function getMonthlyPerformanceData($agentId)
    {
        $months = [];
        $propertyData = [];
        $leadData = [];
        $commissionData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');
            
            $propertyData[] = Property::where('agent_id', $agentId)
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
                
            $leadData[] = Lead::where('agent_id', $agentId)
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
                
            $commissionData[] = Commission::where('agent_id', $agentId)
                ->whereMonth('paid_date', $month->month)
                ->whereYear('paid_date', $month->year)
                ->where('status', 'paid')
                ->sum('amount');
        }

        return [
            'months' => $months,
            'properties' => $propertyData,
            'leads' => $leadData,
            'commissions' => $commissionData,
        ];
    }

    private function getTodayTasks($agentId)
    {
        $tasks = [];

        // Today's appointments
        $appointments = Appointment::where('agent_id', $agentId)
            ->whereDate('start_datetime', today())
            ->with(['lead', 'property'])
            ->orderBy('start_datetime')
            ->get();

        foreach ($appointments as $appointment) {
            $tasks[] = [
                'type' => 'appointment',
                'title' => $appointment->title,
                'time' => $appointment->start_datetime->format('h:i A'),
                'description' => $appointment->lead->full_name ?? 'No lead assigned',
                'status' => $appointment->status,
            ];
        }

        // Follow-up reminders
        $followUps = Lead::where('agent_id', $agentId)
            ->whereDate('next_follow_up_at', today())
            ->get();

        foreach ($followUps as $lead) {
            $tasks[] = [
                'type' => 'follow_up',
                'title' => 'Follow up with ' . $lead->full_name,
                'time' => $lead->next_follow_up_at->format('h:i A'),
                'description' => $lead->lead_type . ' - ' . $lead->lead_source,
                'status' => 'pending',
            ];
        }

        // Overdue commissions
        $overdueCommissions = Commission::where('agent_id', $agentId)
            ->where('due_date', '<', now())
            ->where('status', '!=', 'paid')
            ->get();

        foreach ($overdueCommissions as $commission) {
            $tasks[] = [
                'type' => 'commission',
                'title' => 'Overdue Commission',
                'time' => 'All day',
                'description' => 'Commission from ' . ($commission->property->title ?? 'Unknown property'),
                'status' => 'overdue',
            ];
        }

        return collect($tasks)->sortBy('time')->values();
    }

    public function getStats(Request $request)
    {
        $agent = Auth::user();
        $period = $request->get('period', 'month');

        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            default:
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
        }

        $stats = [
            'properties_added' => Property::where('agent_id', $agent->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'leads_generated' => Lead::where('agent_id', $agent->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'appointments_completed' => Appointment::where('agent_id', $agent->id)
                ->whereBetween('start_datetime', [$startDate, $endDate])
                ->where('status', 'completed')
                ->count(),
            'commissions_earned' => Commission::where('agent_id', $agent->id)
                ->whereBetween('paid_date', [$startDate, $endDate])
                ->where('status', 'paid')
                ->sum('amount'),
            'conversion_rate' => $this->calculateConversionRate($agent->id, $startDate, $endDate),
            'average_response_time' => $this->calculateAverageResponseTime($agent->id, $startDate, $endDate),
        ];

        return response()->json($stats);
    }

    private function calculateConversionRate($agentId, $startDate, $endDate)
    {
        $totalLeads = Lead::where('agent_id', $agentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $convertedLeads = Lead::where('agent_id', $agentId)
            ->whereBetween('converted_at', [$startDate, $endDate])
            ->count();

        if ($totalLeads == 0) {
            return 0;
        }

        return round(($convertedLeads / $totalLeads) * 100, 2);
    }

    private function calculateAverageResponseTime($agentId, $startDate, $endDate)
    {
        // This would need to be implemented based on your actual response tracking
        // For now, return a placeholder value
        return '2 hours';
    }
}
