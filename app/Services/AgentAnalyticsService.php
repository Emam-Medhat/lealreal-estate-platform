<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Property;
use App\Models\Lead;
use App\Models\AgentCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AgentAnalyticsService
{
    protected $cacheTTL = 3600; // Cache Time To Live in seconds (1 hour)

    public function getOverviewStats(Agent $agent): array
    {
        $cacheKey = 'agent_overview_stats_' . $agent->id;
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
            $startOfMonth = now()->startOfMonth();

            // Consolidate property stats into a single query
            $propertyStats = $agent->properties()
                ->selectRaw("
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
                    COUNT(CASE WHEN status = 'sold' THEN 1 END) as sold,
                    SUM(CASE WHEN status = 'sold' AND updated_at >= ? THEN price ELSE 0 END) as monthly_sales
                ", [$startOfMonth])
                ->first();

            // Consolidate lead stats
            $leadStats = $agent->leads()
                ->selectRaw("
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'converted' THEN 1 END) as converted
                ")
                ->first();

            // Consolidate appointment stats
            $appointmentStats = $agent->appointments()
                ->selectRaw("
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed
                ")
                ->first();

            // Commission stats
            $commissionStats = $agent->commissions()
                ->selectRaw("
                    SUM(amount) as total,
                    SUM(CASE WHEN commission_date >= ? THEN amount ELSE 0 END) as monthly
                ", [$startOfMonth])
                ->first();

            return [
                'total_properties' => (int)($propertyStats->total ?? 0),
                'active_properties' => (int)($propertyStats->active ?? 0),
                'sold_properties' => (int)($propertyStats->sold ?? 0),
                'total_leads' => (int)($leadStats->total ?? 0),
                'converted_leads' => (int)($leadStats->converted ?? 0),
                'total_appointments' => (int)($appointmentStats->total ?? 0),
                'completed_appointments' => (int)($appointmentStats->completed ?? 0),
                'total_commissions' => (float)($commissionStats->total ?? 0),
                'this_month_sales' => (float)($propertyStats->monthly_sales ?? 0),
                'this_month_commissions' => (float)($commissionStats->monthly ?? 0),
                'conversion_rate' => $leadStats->total > 0 ? round(($leadStats->converted / $leadStats->total) * 100, 2) : 0,
                'average_rating' => (float)($agent->reviews()->avg('rating') ?? 0),
            ];
        });
    }

    public function getPerformanceTrends(Agent $agent): array
    {
        $cacheKey = 'agent_performance_trends_' . $agent->id;
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
            $twelveMonthsAgo = now()->subMonths(11)->startOfMonth();
            $driver = \DB::connection()->getDriverName();

            // Database-agnostic month formatting
            $monthFormat = $driver === 'sqlite'
                ? "strftime('%Y-%m', updated_at)"
                : "DATE_FORMAT(updated_at, '%Y-%m')";

            $commissionMonthFormat = $driver === 'sqlite'
                ? "strftime('%Y-%m', commission_date)"
                : "DATE_FORMAT(commission_date, '%Y-%m')";

            $leadMonthFormat = $driver === 'sqlite'
                ? "strftime('%Y-%m', created_at)"
                : "DATE_FORMAT(created_at, '%Y-%m')";

            // Sales Trend: Grouped by month
            $salesTrendData = $agent->properties()
                ->where('status', 'sold')
                ->where('updated_at', '>=', $twelveMonthsAgo)
                ->selectRaw("
                    {$monthFormat} as month,
                    SUM(price) as sales,
                    COUNT(*) as properties_sold
                ")
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            // Commission Trend: Grouped by month
            $commissionTrendData = $agent->commissions()
                ->where('commission_date', '>=', $twelveMonthsAgo)
                ->selectRaw("
                    {$commissionMonthFormat} as month,
                    SUM(amount) as commissions
                ")
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            // Lead Trend: Grouped by month
            $leadTrendData = $agent->leads()
                ->where('created_at', '>=', $twelveMonthsAgo)
                ->selectRaw("
                    {$leadMonthFormat} as month,
                    COUNT(*) as leads,
                    COUNT(CASE WHEN status = 'converted' THEN 1 END) as converted
                ")
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            // Fill in missing months to ensure a continuous 12-month trend
            $salesTrend = [];
            $commissionTrend = [];
            $leadTrend = [];

            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i)->format('Y-m');

                $salesTrend[] = [
                    'month' => $month,
                    'sales' => (float)($salesTrendData->get($month)->sales ?? 0),
                    'properties_sold' => (int)($salesTrendData->get($month)->properties_sold ?? 0),
                ];

                $commissionTrend[] = [
                    'month' => $month,
                    'commissions' => (float)($commissionTrendData->get($month)->commissions ?? 0),
                ];

                $leadTrend[] = [
                    'month' => $month,
                    'leads' => (int)($leadTrendData->get($month)->leads ?? 0),
                    'converted' => (int)($leadTrendData->get($month)->converted ?? 0),
                ];
            }

            return [
                'sales_trend' => $salesTrend,
                'commission_trend' => $commissionTrend,
                'lead_trend' => $leadTrend,
            ];
        });
    }

    public function getPropertyAnalytics(Agent $agent): array
    {
        $cacheKey = 'agent_property_analytics_' . $agent->id;
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
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
        });
    }

    public function getLeadAnalytics(Agent $agent): array
    {
        $cacheKey = 'agent_lead_analytics_' . $agent->id;
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
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
        });
    }

    public function getCommissionAnalytics(Agent $agent): array
    {
        $cacheKey = 'agent_commission_analytics_' . $agent->id;
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
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
        });
    }

    public function invalidateCache(int $agentId)
    {
        Cache::forget('agent_overview_stats_' . $agentId);
        Cache::forget('agent_performance_trends_' . $agentId);
        Cache::forget('agent_property_analytics_' . $agentId);
        Cache::forget('agent_lead_analytics_' . $agentId);
        Cache::forget('agent_commission_analytics_' . $agentId);
    }

    private function calculateConversionRate(Agent $agent): float
    {
        $totalLeads = $agent->leads()->count();
        $convertedLeads = $agent->leads()->where('status', 'converted')->count();
        
        return $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;
    }

    private function getPropertyPriceRanges($properties): array
    {
        $results = (clone $properties)
            ->selectRaw("
                COUNT(CASE WHEN price < 100000 THEN 1 END) as range1,
                COUNT(CASE WHEN price >= 100000 AND price < 250000 THEN 1 END) as range2,
                COUNT(CASE WHEN price >= 250000 AND price < 500000 THEN 1 END) as range3,
                COUNT(CASE WHEN price >= 500000 AND price < 1000000 THEN 1 END) as range4,
                COUNT(CASE WHEN price >= 1000000 THEN 1 END) as range5
            ")
            ->first();

        return [
            '0-100k' => (int)($results->range1 ?? 0),
            '100k-250k' => (int)($results->range2 ?? 0),
            '250k-500k' => (int)($results->range3 ?? 0),
            '500k-1m' => (int)($results->range4 ?? 0),
            '1m+' => (int)($results->range5 ?? 0),
        ];
    }

    private function getLeadBudgetRanges($leads): array
    {
        $results = (clone $leads)
            ->selectRaw("
                COUNT(CASE WHEN budget_max < 100000 THEN 1 END) as range1,
                COUNT(CASE WHEN budget_max >= 100000 AND budget_max < 250000 THEN 1 END) as range2,
                COUNT(CASE WHEN budget_max >= 250000 AND budget_max < 500000 THEN 1 END) as range3,
                COUNT(CASE WHEN budget_max >= 500000 AND budget_max < 1000000 THEN 1 END) as range4,
                COUNT(CASE WHEN budget_max >= 1000000 THEN 1 END) as range5
            ")
            ->first();

        return [
            '0-100k' => (int)($results->range1 ?? 0),
            '100k-250k' => (int)($results->range2 ?? 0),
            '250k-500k' => (int)($results->range3 ?? 0),
            '500k-1m' => (int)($results->range4 ?? 0),
            '1m+' => (int)($results->range5 ?? 0),
        ];
    }

    public function getDateRange(string $period): array
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
