<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
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

class AgentAnalyticsController extends Controller
{
    public function index()
    {
        $agent = Auth::user()->agent;
        
        // Get overview stats
        $overview = $this->getOverviewStats($agent);
        
        // Get performance trends
        $trends = $this->getPerformanceTrends($agent);
        
        // Get property analytics
        $propertyAnalytics = $this->getPropertyAnalytics($agent);
        
        // Get lead analytics
        $leadAnalytics = $this->getLeadAnalytics($agent);
        
        // Get commission analytics
        $commissionAnalytics = $this->getCommissionAnalytics($agent);

        return view('agent.analytics.index', compact(
            'overview',
            'trends',
            'propertyAnalytics',
            'leadAnalytics',
            'commissionAnalytics'
        ));
    }

    public function getOverviewStats(Agent $agent): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        return [
            'total_properties' => $agent->properties()->count(),
            'active_properties' => $agent->properties()->where('status', 'active')->count(),
            'sold_properties' => $agent->properties()->where('status', 'sold')->count(),
            'total_leads' => $agent->leads()->count(),
            'converted_leads' => $agent->leads()->where('status', 'converted')->count(),
            'total_appointments' => $agent->appointments()->count(),
            'completed_appointments' => $agent->appointments()->where('status', 'completed')->count(),
            'total_commissions' => $agent->commissions()->sum('amount'),
            'this_month_sales' => $agent->properties()->where('status', 'sold')
                ->whereMonth('updated_at', $currentMonth)
                ->whereYear('updated_at', $currentYear)
                ->with('price')
                ->get()
                ->sum(function ($property) {
                    return $property->price?->price ?? 0;
                }),
            'this_month_commissions' => $agent->commissions()
                ->whereMonth('commission_date', $currentMonth)
                ->whereYear('commission_date', $currentYear)
                ->sum('amount'),
            'conversion_rate' => $this->calculateConversionRate($agent),
            'average_rating' => $agent->reviews()->avg('rating') ?? 0,
        ];
    }

    public function getPerformanceTrends(Agent $agent): array
    {
        // Last 12 months data
        $months = collect(range(11, 0))->map(function ($month) {
            return now()->subMonths($month);
        });

        $salesTrend = $months->map(function ($date) use ($agent) {
            return [
                'month' => $date->format('Y-m'),
                'sales' => $agent->properties()->where('status', 'sold')
                    ->whereMonth('updated_at', $date->month)
                    ->whereYear('updated_at', $date->year)
                    ->with('price')
                    ->get()
                    ->sum(function ($property) {
                        return $property->price?->price ?? 0;
                    }),
                'properties_sold' => $agent->properties()->where('status', 'sold')
                    ->whereMonth('updated_at', $date->month)
                    ->whereYear('updated_at', $date->year)
                    ->count(),
            ];
        });

        $commissionTrend = $months->map(function ($date) use ($agent) {
            return [
                'month' => $date->format('Y-m'),
                'commissions' => $agent->commissions()
                    ->whereMonth('commission_date', $date->month)
                    ->whereYear('commission_date', $date->year)
                    ->sum('amount'),
            ];
        });

        $leadTrend = $months->map(function ($date) use ($agent) {
            return [
                'month' => $date->format('Y-m'),
                'leads' => $agent->leads()
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'converted' => $agent->leads()->where('status', 'converted')
                    ->whereMonth('updated_at', $date->month)
                    ->whereYear('updated_at', $date->year)
                    ->count(),
            ];
        });

        return [
            'sales_trend' => $salesTrend,
            'commission_trend' => $commissionTrend,
            'lead_trend' => $leadTrend,
        ];
    }

    public function getPropertyAnalytics(Agent $agent): array
    {
        $properties = $agent->properties();

        return [
            'status_distribution' => $properties
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            
            'type_distribution' => $properties
                ->with('propertyType')
                ->selectRaw('property_type_id, COUNT(*) as count')
                ->groupBy('property_type_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => $item->propertyType?->name ?? 'Unknown',
                        'count' => $item->count,
                    ];
                }),
            
            'price_ranges' => $this->getPropertyPriceRanges($properties),
            
            'top_performing_properties' => $properties->where('status', 'sold')
                ->with('price')
                ->orderByDesc(function ($property) {
                    return $property->price?->price ?? 0;
                })
                ->limit(5)
                ->get(['id', 'title', 'updated_at']),
            
            'average_days_on_market' => $properties->where('status', 'sold')
                ->avg('days_on_market'),
            
            'total_views' => $properties->sum('views_count'),
            'total_inquiries' => $properties->sum('inquiries_count'),
        ];
    }

    public function getLeadAnalytics(Agent $agent): array
    {
        $leads = $agent->leads();

        return [
            'status_distribution' => $leads
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            
            'priority_distribution' => $leads
                ->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
            
            'source_distribution' => $leads->with('source')
                ->selectRaw('source_id, COUNT(*) as count')
                ->groupBy('source_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'source' => $item->source?->name ?? 'Unknown',
                        'count' => $item->count,
                    ];
                }),
            
            'conversion_funnel' => [
                'new' => $leads->where('status', 'new')->count(),
                'contacted' => $leads->where('status', 'contacted')->count(),
                'qualified' => $leads->where('status', 'qualified')->count(),
                'converted' => $leads->where('status', 'converted')->count(),
                'lost' => $leads->where('status', 'lost')->count(),
            ],
            
            'average_response_time' => $leads->avg('response_time_hours'),
            
            'budget_ranges' => $this->getLeadBudgetRanges($leads),
        ];
    }

    public function getCommissionAnalytics(Agent $agent): array
    {
        $commissions = $agent->commissions();

        return [
            'status_distribution' => $commissions
                ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('status')
                ->get()
                ->map(function ($item) {
                    return [
                        'status' => $item->status,
                        'count' => $item->count,
                        'total' => $item->total,
                    ];
                }),
            
            'type_distribution' => $commissions
                ->selectRaw('type, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('type')
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => $item->type,
                        'count' => $item->count,
                        'total' => $item->total,
                    ];
                }),
            
            'monthly_commissions' => $commissions
                ->where('commission_date', '>=', now()->subMonths(12))
                ->selectRaw('DATE_FORMAT(commission_date, "%Y-%m") as month, SUM(amount) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            
            'average_commission' => $commissions->avg('amount'),
            
            'largest_commission' => $commissions->max('amount'),
        ];
    }

    public function getDetailedAnalytics(Request $request): JsonResponse
    {
        $agent = Auth::user()->agent;
        $period = $request->period ?? 'monthly';
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'overview' => $this->getOverviewStats($agent),
            'performance_trends' => $this->getPerformanceTrends($agent),
            'property_analytics' => $this->getPropertyAnalytics($agent),
            'lead_analytics' => $this->getLeadAnalytics($agent),
            'commission_analytics' => $this->getCommissionAnalytics($agent),
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
            'overview' => $this->getOverviewStats($agent),
            'performance_trends' => $this->getPerformanceTrends($agent),
            'property_analytics' => $this->getPropertyAnalytics($agent),
            'lead_analytics' => $this->getLeadAnalytics($agent),
            'commission_analytics' => $this->getCommissionAnalytics($agent),
        ];

        $filename = "agent_analytics_export_{$request->period}_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $analytics,
            'filename' => $filename,
            'message' => 'Analytics exported successfully'
        ]);
    }

    private function calculateConversionRate(Agent $agent): float
    {
        $totalLeads = $agent->leads()->count();
        $convertedLeads = $agent->leads()->where('status', 'converted')->count();
        
        return $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;
    }

    private function getPropertyPriceRanges($properties): array
    {
        $ranges = [
            '0-100k' => 0,
            '100k-250k' => 0,
            '250k-500k' => 0,
            '500k-1m' => 0,
            '1m+' => 0,
        ];

        $properties->with('price')->get()->each(function ($property) use (&$ranges) {
            $price = $property->price?->price ?? 0;
            
            if ($price < 100000) {
                $ranges['0-100k']++;
            } elseif ($price < 250000) {
                $ranges['100k-250k']++;
            } elseif ($price < 500000) {
                $ranges['250k-500k']++;
            } elseif ($price < 1000000) {
                $ranges['500k-1m']++;
            } else {
                $ranges['1m+']++;
            }
        });

        return $ranges;
    }

    private function getLeadBudgetRanges($leads): array
    {
        $ranges = [
            '0-100k' => 0,
            '100k-250k' => 0,
            '250k-500k' => 0,
            '500k-1m' => 0,
            '1m+' => 0,
        ];

        $leads->get()->each(function ($lead) use (&$ranges) {
            $budget = $lead->budget_max ?? 0;
            
            if ($budget < 100000) {
                $ranges['0-100k']++;
            } elseif ($budget < 250000) {
                $ranges['100k-250k']++;
            } elseif ($budget < 500000) {
                $ranges['250k-500k']++;
            } elseif ($budget < 1000000) {
                $ranges['500k-1m']++;
            } else {
                $ranges['1m+']++;
            }
        });

        return $ranges;
    }

    private function getDateRange(string $period): array
    {
        return match($period) {
            'daily' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'weekly' => [
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
            ],
            'monthly' => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
            ],
            'quarterly' => [
                'start' => now()->startOfQuarter(),
                'end' => now()->endOfQuarter(),
            ],
            'yearly' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear(),
            ],
            default => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
            ],
        };
    }
}
