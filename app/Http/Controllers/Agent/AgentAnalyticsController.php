<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\AgentAnalyticsService;

class AgentAnalyticsController extends Controller
{
    private AgentAnalyticsService $agentAnalyticsService;

    public function __construct(AgentAnalyticsService $agentAnalyticsService)
    {
        $this->agentAnalyticsService = $agentAnalyticsService;
    }

    public function index()
    {
        $agent = Auth::user()->agent;
        
        // Get overview stats
        $overview = $this->agentAnalyticsService->getOverviewStats($agent);
        
        // Get performance trends
        $trends = $this->agentAnalyticsService->getPerformanceTrends($agent);
        
        // Get property analytics
        $propertyAnalytics = $this->agentAnalyticsService->getPropertyAnalytics($agent);
        
        // Get lead analytics
        $leadAnalytics = $this->agentAnalyticsService->getLeadAnalytics($agent);
        
        // Get commission analytics
        $commissionAnalytics = $this->agentAnalyticsService->getCommissionAnalytics($agent);

        return view('agent.analytics.index', compact(
            'overview',
            'trends',
            'propertyAnalytics',
            'leadAnalytics',
            'commissionAnalytics'
        ));
    }

    public function getDetailedAnalytics(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $period = $request->period ?? 'monthly';
        $dateRange = $this->agentAnalyticsService->getDateRange($period);

        $analytics = [
            'overview' => $this->agentAnalyticsService->getOverviewStats($agent),
            'performance_trends' => $this->agentAnalyticsService->getPerformanceTrends($agent),
            'property_analytics' => $this->agentAnalyticsService->getPropertyAnalytics($agent),
            'lead_analytics' => $this->agentAnalyticsService->getLeadAnalytics($agent),
            'commission_analytics' => $this->agentAnalyticsService->getCommissionAnalytics($agent),
        ];

        return response()->json([
            'success' => true,
            'analytics' => $analytics,
            'period' => $period,
            'date_range' => $dateRange,
        ]);
    }

    public function getRealTimeStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $stats = [
            'active_properties' => $agent->properties()->where('status', 'active')->count(),
            'pending_leads' => $agent->leads()->where('status', 'pending')->count(),
            'today_appointments' => $agent->appointments()
                ->whereDate('appointment_date', today())
                ->where('status', 'scheduled')
                ->count(),
            'unread_messages' => $agent->conversations()
                ->whereHas('messages', function ($query) {
                    $query->where('receiver_id', $agent->user_id)
                          ->where('is_read', false);
                })
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'updated_at' => now()->toISOString(),
        ]);
    }

    public function exportAnalytics(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'period' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $agent = Auth::user()->agent;
        
        $analytics = [
            'overview' => $this->agentAnalyticsService->getOverviewStats($agent),
            'performance_trends' => $this->agentAnalyticsService->getPerformanceTrends($agent),
            'property_analytics' => $this->agentAnalyticsService->getPropertyAnalytics($agent),
            'lead_analytics' => $this->agentAnalyticsService->getLeadAnalytics($agent),
            'commission_analytics' => $this->agentAnalyticsService->getCommissionAnalytics($agent),
        ];

        $filename = "agent_analytics_export_{$request->period}_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $analytics,
            'filename' => $filename,
            'message' => 'Analytics exported successfully'
        ]);
    }
}
