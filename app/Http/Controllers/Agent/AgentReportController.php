<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Property;
use App\Models\AgentLead;
use App\Models\AgentAppointment;
use App\Models\AgentCommission;
use App\Models\AgentPerformance;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgentReportController extends Controller
{
    public function index()
    {
        $agent = Auth::user()->agent;
        
        return view('agent.reports.index', compact('agent'));
    }

    public function performanceReport(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $period = $request->period ?? 'monthly';
        $dateFrom = $request->date_from ?? now()->subMonths(12);
        $dateTo = $request->date_to ?? now();

        $report = [
            'period' => $period,
            'date_range' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
            'summary' => [
                'total_properties' => $agent->properties()
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->count(),
                'properties_sold' => $agent->properties()
                    ->where('status', 'sold')
                    ->whereBetween('updated_at', [$dateFrom, $dateTo])
                    ->count(),
                'total_sales' => $agent->properties()
                    ->where('status', 'sold')
                    ->whereBetween('updated_at', [$dateFrom, $dateTo])
                    ->with('price')
                    ->get()
                    ->sum(function ($property) {
                        return $property->price?->price ?? 0;
                    }),
                'total_leads' => $agent->leads()
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->count(),
                'converted_leads' => $agent->leads()
                    ->where('status', 'converted')
                    ->whereBetween('updated_at', [$dateFrom, $dateTo])
                    ->count(),
                'total_appointments' => $agent->appointments()
                    ->whereBetween('appointment_date', [$dateFrom, $dateTo])
                    ->count(),
                'completed_appointments' => $agent->appointments()
                    ->where('status', 'completed')
                    ->whereBetween('appointment_date', [$dateFrom, $dateTo])
                    ->count(),
                'total_commissions' => $agent->commissions()
                    ->whereBetween('commission_date', [$dateFrom, $dateTo])
                    ->sum('amount'),
            ],
        ];

        // Calculate metrics
        $report['metrics'] = [
            'conversion_rate' => $report['summary']['total_leads'] > 0 
                ? round(($report['summary']['converted_leads'] / $report['summary']['total_leads']) * 100, 2)
                : 0,
            'appointment_completion_rate' => $report['summary']['total_appointments'] > 0
                ? round(($report['summary']['completed_appointments'] / $report['summary']['total_appointments']) * 100, 2)
                : 0,
            'average_sale_price' => $report['summary']['properties_sold'] > 0
                ? round($report['summary']['total_sales'] / $report['summary']['properties_sold'], 2)
                : 0,
            'commission_per_property' => $report['summary']['properties_sold'] > 0
                ? round($report['summary']['total_commissions'] / $report['summary']['properties_sold'], 2)
                : 0,
        ];

        // Time series data
        $report['time_series'] = $this->getTimeSeriesData($agent, $period, $dateFrom, $dateTo);

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }

    public function salesReport(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $dateFrom = $request->date_from ?? now()->subMonths(12);
        $dateTo = $request->date_to ?? now();

        $soldProperties = $agent->properties()
            ->where('status', 'sold')
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->with(['price', 'propertyType', 'location'])
            ->get();

        $report = [
            'summary' => [
                'total_properties_sold' => $soldProperties->count(),
                'total_sales_value' => $soldProperties->sum(function ($property) {
                    return $property->price?->price ?? 0;
                }),
                'average_sale_price' => $soldProperties->count() > 0
                    ? $soldProperties->avg(function ($property) {
                        return $property->price?->price ?? 0;
                    })
                    : 0,
                'average_days_on_market' => $soldProperties->avg('days_on_market'),
            ],
            'by_property_type' => $soldProperties
                ->groupBy('property_type_id')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_value' => $group->sum(function ($property) {
                            return $property->price?->price ?? 0;
                        }),
                        'average_price' => $group->avg(function ($property) {
                            return $property->price?->price ?? 0;
                        }),
                    ];
                }),
            'by_location' => $soldProperties
                ->groupBy('location_id')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_value' => $group->sum(function ($property) {
                            return $property->price?->price ?? 0;
                        }),
                        'average_price' => $group->avg(function ($property) {
                            return $property->price?->price ?? 0;
                        }),
                    ];
                }),
            'properties' => $soldProperties->map(function ($property) {
                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'sale_price' => $property->price?->price,
                    'sale_date' => $property->updated_at,
                    'days_on_market' => $property->days_on_market,
                    'property_type' => $property->propertyType?->name,
                    'location' => $property->location?->full_address,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }

    public function leadReport(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $dateFrom = $request->date_from ?? now()->subMonths(12);
        $dateTo = $request->date_to ?? now();

        $leads = $agent->leads()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['source', 'property'])
            ->get();

        $report = [
            'summary' => [
                'total_leads' => $leads->count(),
                'new_leads' => $leads->where('status', 'new')->count(),
                'contacted_leads' => $leads->where('status', 'contacted')->count(),
                'qualified_leads' => $leads->where('status', 'qualified')->count(),
                'converted_leads' => $leads->where('status', 'converted')->count(),
                'lost_leads' => $leads->where('status', 'lost')->count(),
            ],
            'conversion_funnel' => [
                ['stage' => 'New', 'count' => $leads->where('status', 'new')->count()],
                ['stage' => 'Contacted', 'count' => $leads->where('status', 'contacted')->count()],
                ['stage' => 'Qualified', 'count' => $leads->where('status', 'qualified')->count()],
                ['stage' => 'Converted', 'count' => $leads->where('status', 'converted')->count()],
                ['stage' => 'Lost', 'count' => $leads->where('status', 'lost')->count()],
            ],
            'by_source' => $leads
                ->groupBy('source_id')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'converted' => $group->where('status', 'converted')->count(),
                        'conversion_rate' => $group->count() > 0
                            ? round(($group->where('status', 'converted')->count() / $group->count()) * 100, 2)
                            : 0,
                    ];
                }),
            'by_priority' => $leads
                ->groupBy('priority')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'converted' => $group->where('status', 'converted')->count(),
                        'conversion_rate' => $group->count() > 0
                            ? round(($group->where('status', 'converted')->count() / $group->count()) * 100, 2)
                            : 0,
                    ];
                }),
            'average_response_time' => $leads->avg('response_time_hours'),
            'leads' => $leads->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'status' => $lead->status,
                    'priority' => $lead->priority,
                    'source' => $lead->source?->name,
                    'created_at' => $lead->created_at,
                    'budget_max' => $lead->budget_max,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }

    public function commissionReport(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $dateFrom = $request->date_from ?? now()->subMonths(12);
        $dateTo = $request->date_to ?? now();

        $commissions = $agent->commissions()
            ->whereBetween('commission_date', [$dateFrom, $dateTo])
            ->with(['property'])
            ->get();

        $report = [
            'summary' => [
                'total_commissions' => $commissions->sum('amount'),
                'total_transactions' => $commissions->count(),
                'average_commission' => $commissions->avg('amount'),
                'highest_commission' => $commissions->max('amount'),
                'lowest_commission' => $commissions->min('amount'),
            ],
            'by_status' => $commissions
                ->groupBy('status')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount'),
                        'average_amount' => $group->avg('amount'),
                    ];
                }),
            'by_type' => $commissions
                ->groupBy('type')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount'),
                        'average_amount' => $group->avg('amount'),
                    ];
                }),
            'monthly_breakdown' => $commissions
                ->groupBy(function ($commission) {
                    return $commission->commission_date->format('Y-m');
                })
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount'),
                        'average_amount' => $group->avg('amount'),
                    ];
                }),
            'commissions' => $commissions->map(function ($commission) {
                return [
                    'id' => $commission->id,
                    'amount' => $commission->amount,
                    'type' => $commission->type,
                    'status' => $commission->status,
                    'commission_date' => $commission->commission_date,
                    'property_id' => $commission->property_id,
                    'property_title' => $commission->property?->title,
                    'description' => $commission->description,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }

    public function exportReport(Request $request): JsonResponse
    {
        $request->validate([
            'report_type' => 'required|in:performance,sales,leads,commissions',
            'format' => 'required|in:json,csv,xlsx',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $agent = Auth::user()->agent;
        $reportType = $request->report_type;
        $format = $request->format;

        switch ($reportType) {
            case 'performance':
                $data = $this->getPerformanceReportData($agent, $request->date_from, $request->date_to);
                break;
            case 'sales':
                $data = $this->getSalesReportData($agent, $request->date_from, $request->date_to);
                break;
            case 'leads':
                $data = $this->getLeadReportData($agent, $request->date_from, $request->date_to);
                break;
            case 'commissions':
                $data = $this->getCommissionReportData($agent, $request->date_from, $request->date_to);
                break;
        }

        $filename = "agent_{$reportType}_report_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $data,
            'filename' => $filename,
            'format' => $format,
            'message' => 'Report exported successfully'
        ]);
    }

    private function getTimeSeriesData(Agent $agent, string $period, $dateFrom, $dateTo): array
    {
        $groupBy = match($period) {
            'daily' => 'DATE_FORMAT(created_at, "%Y-%m-%d")',
            'weekly' => 'DATE_FORMAT(created_at, "%Y-%u")',
            'monthly' => 'DATE_FORMAT(created_at, "%Y-%m")',
            'yearly' => 'DATE_FORMAT(created_at, "%Y")',
            default => 'DATE_FORMAT(created_at, "%Y-%m")',
        };

        return [
            'properties' => $agent->properties()
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw("{$groupBy} as period, COUNT(*) as count")
                ->groupBy('period')
                ->orderBy('period')
                ->get(),
            'leads' => $agent->leads()
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw("{$groupBy} as period, COUNT(*) as count")
                ->groupBy('period')
                ->orderBy('period')
                ->get(),
            'appointments' => $agent->appointments()
                ->whereBetween('appointment_date', [$dateFrom, $dateTo])
                ->selectRaw("{$groupBy} as period, COUNT(*) as count")
                ->groupBy('period')
                ->orderBy('period')
                ->get(),
            'commissions' => $agent->commissions()
                ->whereBetween('commission_date', [$dateFrom, $dateTo])
                ->selectRaw("{$groupBy} as period, SUM(amount) as amount, COUNT(*) as count")
                ->groupBy('period')
                ->orderBy('period')
                ->get(),
        ];
    }

    private function getPerformanceReportData(Agent $agent, $dateFrom, $dateTo): array
    {
        // Implementation for performance report data
        return [];
    }

    private function getSalesReportData(Agent $agent, $dateFrom, $dateTo): array
    {
        // Implementation for sales report data
        return [];
    }

    private function getLeadReportData(Agent $agent, $dateFrom, $dateTo): array
    {
        // Implementation for lead report data
        return [];
    }

    private function getCommissionReportData(Agent $agent, $dateFrom, $dateTo): array
    {
        // Implementation for commission report data
        return [];
    }
}
