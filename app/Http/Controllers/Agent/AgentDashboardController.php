<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Agent\BaseAgentController;
use App\Models\Agent;
use App\Models\Property;
use App\Models\AgentLead;
use App\Models\AgentAppointment;
use App\Models\AgentCommission;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentDashboardController extends BaseAgentController
{
    public function index()
    {
        $agent = $this->getAgent();
        
        // Check if user has an agent profile
        if (!$agent) {
            return redirect()->route('dashboard')->with('error', 'Agent profile not found. Please contact administrator.');
        }
        
        // Get dashboard statistics
        $stats = [
            'total_properties' => $agent->properties()->count(),
            'active_properties' => $agent->properties()->where('status', 'active')->count(),
            'sold_properties' => $agent->properties()->where('status', 'sold')->count(),
            'pending_leads' => $agent->leads()->where('status', 'pending')->count(),
            'today_appointments' => $agent->appointments()
                ->whereDate('appointment_date', today())
                ->where('status', 'scheduled')
                ->count(),
            'this_month_commissions' => $agent->commissions()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'total_commissions' => $agent->commissions()->sum('amount'),
            'average_rating' => $agent->reviews()->avg('rating') ?? 0,
        ];

        // Get recent activities
        $recentActivities = UserActivityLog::where('user_id', Auth::id())
            ->latest()
            ->limit(10)
            ->get();

        // Get recent properties
        $recentProperties = $agent->properties()
            ->with(['location', 'price'])
            ->latest()
            ->limit(6)
            ->get();

        // Get upcoming appointments
        $upcomingAppointments = $agent->appointments()
            ->with('lead')
            ->where('appointment_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->limit(5)
            ->get();

        // Get recent leads
        $recentLeads = $agent->leads()
            ->latest()
            ->limit(5)
            ->get();

        // Get performance data
        $performanceData = $this->getPerformanceData($agent);

        return view('agent.dashboard', compact(
            'agent',
            'stats',
            'recentActivities',
            'recentProperties',
            'upcomingAppointments',
            'recentLeads',
            'performanceData'
        ));
    }

    public function getQuickStats(): JsonResponse
    {
        $agent = $this->getAgentOrError();
        
        if ($agent instanceof JsonResponse) {
            return $agent;
        }
        
        $stats = [
            'total_properties' => $agent->properties()->count(),
            'active_properties' => $agent->properties()->where('status', 'active')->count(),
            'pending_leads' => $agent->leads()->where('status', 'pending')->count(),
            'today_appointments' => $agent->appointments()
                ->whereDate('appointment_date', today())
                ->where('status', 'scheduled')
                ->count(),
            'this_month_commissions' => $agent->commissions()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getRecentProperties(Request $request): JsonResponse
    {
        $agent = $this->getAgentOrError();
        
        if ($agent instanceof JsonResponse) {
            return $agent;
        }
        
        $properties = $agent->properties()
            ->with(['location', 'price'])
            ->latest()
            ->limit($request->limit ?? 6)
            ->get();

        return response()->json([
            'success' => true,
            'properties' => $properties
        ]);
    }

    public function getUpcomingAppointments(Request $request): JsonResponse
    {
        $agent = $this->getAgentOrError();
        
        if ($agent instanceof JsonResponse) {
            return $agent;
        }
        
        $appointments = $agent->appointments()
            ->with('lead')
            ->where('appointment_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->limit($request->limit ?? 5)
            ->get();

        return response()->json([
            'success' => true,
            'appointments' => $appointments
        ]);
    }

    public function getRecentLeads(Request $request): JsonResponse
    {
        $agent = $this->getAgentOrError();
        
        if ($agent instanceof JsonResponse) {
            return $agent;
        }
        
        $leads = $agent->leads()
            ->latest()
            ->limit($request->limit ?? 5)
            ->get();

        return response()->json([
            'success' => true,
            'leads' => $leads
        ]);
    }

    public function getPerformanceData(Agent $agent): array
    {
        // Property sales over last 6 months
        $salesData = $agent->properties()
            ->where('status', 'sold')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');

        // Commission earnings over last 6 months
        $commissionData = $agent->commissions()
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        // Lead conversion rates
        $conversionData = [
            'total_leads' => $agent->leads()->count(),
            'converted_leads' => $agent->leads()->where('status', 'converted')->count(),
            'conversion_rate' => 0,
        ];

        if ($conversionData['total_leads'] > 0) {
            $conversionData['conversion_rate'] = round(
                ($conversionData['converted_leads'] / $conversionData['total_leads']) * 100, 
                2
            );
        }

        // Property status distribution
        $statusData = $agent->properties()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'sales_data' => $salesData,
            'commission_data' => $commissionData,
            'conversion_data' => $conversionData,
            'status_data' => $statusData,
        ];
    }

    public function getPerformanceMetrics(): JsonResponse
    {
        $agent = $this->getAgentOrError();
        
        if ($agent instanceof JsonResponse) {
            return $agent;
        }
        
        $performanceData = $this->getPerformanceData($agent);

        return response()->json([
            'success' => true,
            'performance_data' => $performanceData
        ]);
    }

    public function getCalendarEvents(Request $request): JsonResponse
    {
        $agent = $this->getAgentOrError();
        
        if ($agent instanceof JsonResponse) {
            return $agent;
        }
        
        $start = $request->start;
        $end = $request->end;

        // Get appointments
        $appointments = $agent->appointments()
            ->whereBetween('appointment_date', [$start, $end])
            ->where('status', 'scheduled')
            ->get(['id', 'appointment_date as start', 'appointment_end_date as end', 'title', 'type'])
            ->map(function ($appointment) {
                $appointment['backgroundColor'] = $appointment->type === 'viewing' ? '#007bff' : '#28a745';
                $appointment['borderColor'] = $appointment['backgroundColor'];
                $appointment['url'] = route('agent.appointments.show', $appointment->id);
                return $appointment;
            });

        // Get important dates (property closings, etc.)
        $importantDates = $agent->properties()
            ->whereBetween('closing_date', [$start, $end])
            ->where('status', 'pending')
            ->get(['id', 'closing_date as start', 'title', 'status'])
            ->map(function ($property) {
                $property['backgroundColor'] = '#ffc107';
                $property['borderColor'] = '#ffc107';
                $property['title'] = 'Closing: ' . $property['title'];
                $property['url'] = route('agent.properties.show', $property->id);
                return $property;
            });

        $events = $appointments->merge($importantDates);

        return response()->json($events);
    }

    public function getNotifications(): JsonResponse
    {
        $agent = $this->getAgentOrError();
        
        if ($agent instanceof JsonResponse) {
            return $agent;
        }
        
        $notifications = [
            'pending_leads' => $agent->leads()->where('status', 'pending')->count(),
            'today_appointments' => $agent->appointments()
                ->whereDate('appointment_date', today())
                ->where('status', 'scheduled')
                ->count(),
            'expiring_listings' => $agent->properties()
                ->where('status', 'active')
                ->where('listing_expires_at', '<=', now()->addDays(7))
                ->count(),
            'new_reviews' => $agent->reviews()
                ->where('created_at', '>=', now()->subDays(3))
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    public function exportDashboardData(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'data_type' => 'required|in:properties,leads,appointments,commissions,all',
        ]);

        $agent = $this->getAgentOrError();
        
        if ($agent instanceof JsonResponse) {
            return $agent;
        }
        
        $data = [];

        switch ($request->data_type) {
            case 'properties':
                $data['properties'] = $agent->properties()->get();
                break;
            case 'leads':
                $data['leads'] = $agent->leads()->get();
                break;
            case 'appointments':
                $data['appointments'] = $agent->appointments()->get();
                break;
            case 'commissions':
                $data['commissions'] = $agent->commissions()->get();
                break;
            case 'all':
                $data['properties'] = $agent->properties()->get();
                $data['leads'] = $agent->leads()->get();
                $data['appointments'] = $agent->appointments()->get();
                $data['commissions'] = $agent->commissions()->get();
                break;
        }

        $filename = "agent_dashboard_{$request->data_type}_" . now()->format('Y-m-d');

        return response()->json([
            'success' => true,
            'data' => $data,
            'filename' => $filename,
            'message' => 'Dashboard data exported successfully'
        ]);
    }
}
