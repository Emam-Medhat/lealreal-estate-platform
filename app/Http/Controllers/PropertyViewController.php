<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyView;
use App\Models\PropertyAnalytic;
use App\Models\PropertyPriceHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PropertyViewController extends Controller
{
    public function recordView(Request $request, Property $property): JsonResponse
    {
        $viewData = [
            'property_id' => $property->id,
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => Auth::id(),
            'referrer' => $request->header('referer'),
            'view_type' => $request->view_type ?? 'detail',
            'duration_seconds' => $request->duration_seconds,
            'metadata' => $request->metadata ?? [],
        ];

        $view = PropertyView::create($viewData);

        // Increment property view count
        $property->increment('views_count');

        // Record analytics
        PropertyAnalytic::recordMetric($property->id, 'views');

        return response()->json([
            'success' => true,
            'message' => 'View recorded successfully',
            'view_id' => $view->id,
            'total_views' => $property->fresh()->views_count,
        ]);
    }

    public function getViewStats(Request $request, Property $property): JsonResponse
    {
        $this->authorize('viewStats', $property);

        $stats = [
            'total_views' => $property->views_count,
            'unique_views' => PropertyView::where('property_id', $property->id)
                ->distinct('ip_address')
                ->count(),
            'today_views' => PropertyView::where('property_id', $property->id)
                ->whereDate('created_at', today())
                ->count(),
            'this_week_views' => PropertyView::where('property_id', $property->id)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month_views' => PropertyView::where('property_id', $property->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'views_by_type' => PropertyView::where('property_id', $property->id)
                ->groupBy('view_type')
                ->selectRaw('view_type, count(*) as count')
                ->pluck('count', 'view_type'),
            'views_by_hour' => PropertyView::where('property_id', $property->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->selectRaw('HOUR(created_at) as hour, count(*) as count')
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('count', 'hour'),
            'top_referrers' => PropertyView::where('property_id', $property->id)
                ->whereNotNull('referrer')
                ->where('referrer', '!=', '')
                ->selectRaw('referrer, count(*) as count')
                ->groupBy('referrer')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function getAnalytics(Request $request, Property $property): JsonResponse
    {
        $this->authorize('viewStats', $property);

        $period = $request->period ?? '30'; // days
        $startDate = now()->subDays($period);

        $analytics = PropertyAnalytic::where('property_id', $property->id)
            ->where('date', '>=', $startDate)
            ->orderBy('date')
            ->get()
            ->groupBy('metric_type');

        $chartData = [
            'views' => $analytics->get('views', collect())->map(function($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            }),
            'inquiries' => $analytics->get('inquiries', collect())->map(function($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            }),
            'favorites' => $analytics->get('favorites', collect())->map(function($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            }),
            'shares' => $analytics->get('shares', collect())->map(function($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'chart_data' => $chartData,
                'period' => $period,
                'start_date' => $startDate->toDateString(),
                'end_date' => now()->toDateString(),
            ],
        ]);
    }

    public function trackEngagement(Request $request, Property $property): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:phone_call,email_click,whatsapp_share,map_view,gallery_view,virtual_tour',
            'duration' => 'nullable|integer',
        ]);

        $action = $request->action;
        $duration = $request->duration;

        // Record the engagement
        $metadata = [
            'action' => $action,
            'duration' => $duration,
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
        ];

        PropertyView::create([
            'property_id' => $property->id,
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => Auth::id(),
            'view_type' => $action,
            'duration_seconds' => $duration,
            'metadata' => $metadata,
        ]);

        // Record analytics
        PropertyAnalytic::recordMetric($property->id, $action);

        // Increment specific counters
        switch ($action) {
            case 'phone_call':
                $property->increment('phone_calls_count');
                break;
            case 'email_click':
                $property->increment('email_clicks_count');
                break;
            case 'whatsapp_share':
                $property->increment('whatsapp_shares_count');
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Engagement tracked successfully',
        ]);
    }

    public function getHeatmapData(Request $request, Property $property): JsonResponse
    {
        $this->authorize('viewStats', $property);

        $views = PropertyView::where('property_id', $property->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->get(['created_at', 'view_type']);

        $heatmapData = $views->groupBy(function($view) {
            return $view->created_at->format('Y-m-d H');
        })->map(function($hourlyViews) {
            return [
                'date' => $hourlyViews->first()->created_at->format('Y-m-d H:00'),
                'count' => $hourlyViews->count(),
                'types' => $hourlyViews->groupBy('view_type')->map->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $heatmapData->values(),
        ]);
    }

    public function getPerformanceMetrics(Request $request, Property $property): JsonResponse
    {
        $this->authorize('viewStats', $property);

        $metrics = [
            'view_to_inquiry_rate' => $this->calculateViewToInquiryRate($property),
            'average_view_duration' => $this->calculateAverageViewDuration($property),
            'bounce_rate' => $this->calculateBounceRate($property),
            'engagement_score' => $this->calculateEngagementScore($property),
            'conversion_rate' => $this->calculateConversionRate($property),
            'popularity_ranking' => $this->getPopularityRanking($property),
            'price_performance' => $this->calculatePricePerformance($property),
        ];

        return response()->json([
            'success' => true,
            'data' => $metrics,
        ]);
    }

    public function exportViews(Request $request, Property $property)
    {
        $this->authorize('viewStats', $property);

        $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ?? now()->subDays(30);
        $endDate = $request->end_date ?? now();

        $views = PropertyView::where('property_id', $property->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['user:id,name,email'])
            ->get();

        // Export logic would go here based on format
        return response()->json([
            'success' => true,
            'message' => 'Views exported successfully',
            'data' => $views,
        ]);
    }

    private function calculateViewToInquiryRate(Property $property): float
    {
        $totalViews = $property->views_count;
        $totalInquiries = $property->inquiries_count;

        if ($totalViews === 0) return 0;

        return round(($totalInquiries / $totalViews) * 100, 2);
    }

    private function calculateAverageViewDuration(Property $property): ?float
    {
        $views = PropertyView::where('property_id', $property->id)
            ->whereNotNull('duration_seconds')
            ->where('view_type', 'detail')
            ->get();

        if ($views->isEmpty()) return null;

        return round($views->avg('duration_seconds'), 2);
    }

    private function calculateBounceRate(Property $property): float
    {
        $totalSessions = PropertyView::where('property_id', $property->id)
            ->distinct('session_id')
            ->count();

        $bouncedSessions = PropertyView::where('property_id', $property->id)
            ->where('duration_seconds', '<', 30) // Less than 30 seconds
            ->orWhereNull('duration_seconds')
            ->distinct('session_id')
            ->count();

        if ($totalSessions === 0) return 0;

        return round(($bouncedSessions / $totalSessions) * 100, 2);
    }

    private function calculateEngagementScore(Property $property): float
    {
        $score = 0;

        // Base score from views
        $score += min($property->views_count / 10, 10);

        // Bonus for favorites
        $score += min($property->favorites_count * 2, 10);

        // Bonus for inquiries
        $score += min($property->inquiries_count * 5, 20);

        // Bonus for premium/featured
        if ($property->featured) $score += 5;
        if ($property->premium) $score += 5;

        // Bonus for media richness
        $mediaCount = $property->media()->count();
        $score += min($mediaCount / 2, 5);

        return min($score, 100);
    }

    private function calculateConversionRate(Property $property): float
    {
        // This would depend on how you define conversion
        // For now, let's say conversion = inquiries / (views + favorites)
        $totalViews = $property->views_count;
        $totalFavorites = $property->favorites_count;
        $totalInquiries = $property->inquiries_count;

        $totalInteractions = $totalViews + $totalFavorites;

        if ($totalInteractions === 0) return 0;

        return round(($totalInquiries / $totalInteractions) * 100, 2);
    }

    private function getPopularityRanking(Property $property): array
    {
        $allProperties = Property::where('status', 'active')
            ->orderBy('views_count', 'desc')
            ->get();

        $rank = $allProperties->search(function($p) use ($property) {
            return $p->id === $property->id;
        }) + 1;

        $total = $allProperties->count();

        return [
            'rank' => $rank,
            'total' => $total,
            'percentile' => round((($total - $rank) / $total) * 100, 2),
        ];
    }

    private function calculatePricePerformance(Property $property): array
    {
        $priceHistory = PropertyPriceHistory::where('property_id', $property->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($priceHistory->isEmpty()) {
            return [
                'trend' => 'stable',
                'change_percentage' => 0,
                'last_change' => null,
            ];
        }

        $lastChange = $priceHistory->first();
        $trend = $lastChange->change_type;
        $changePercentage = $lastChange->change_percentage;

        return [
            'trend' => $trend,
            'change_percentage' => $changePercentage,
            'last_change' => $lastChange->created_at,
        ];
    }
}
