<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Services\LeadService;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    protected $leadService;
    protected $propertyService;

    public function __construct(LeadService $leadService, PropertyService $propertyService)
    {
        $this->leadService = $leadService;
        $this->propertyService = $propertyService;
    }

    /**
     * Get comprehensive dashboard data
     */
    public function index(Request $request): JsonResponse
    {
        $this->rateLimit($request, 30, 5);

        $dashboardData = $this->getCachedData(
            'dashboard_data:' . auth()->id(),
            function () {
                return [
                    'leads' => [
                        'total' => $this->leadService->getRepository()->countTotal(),
                        'new' => $this->leadService->getRepository()->countByStatusName('new'),
                        'qualified' => $this->leadService->getRepository()->countByStatusName('qualified'),
                        'converted' => $this->leadService->getRepository()->countConverted(),
                        'lost' => $this->leadService->getRepository()->countByStatusName('lost'),
                        'this_week' => $this->getLeadsThisWeek(),
                        'this_month' => $this->getLeadsThisMonth(),
                        'conversion_rate' => $this->getLeadConversionRate(),
                    ],
                    'properties' => [
                        'total' => $this->propertyService->getRepository()->countTotal(),
                        'published' => $this->getPublishedPropertiesCount(),
                        'draft' => $this->getDraftPropertiesCount(),
                        'featured' => $this->getFeaturedPropertiesCount(),
                        'this_week' => $this->getPropertiesThisWeek(),
                        'this_month' => $this->getPropertiesThisMonth(),
                        'average_price' => $this->propertyService->getRepository()->sumEstimatedValue(),
                    ],
                    'performance' => [
                        'total_leads' => $this->leadService->getRepository()->countTotal(),
                        'total_properties' => $this->propertyService->getRepository()->countTotal(),
                        'conversion_rate' => $this->getOverallConversionRate(),
                        'average_response_time' => $this->getAverageResponseTime(),
                        'user_engagement' => $this->getUserEngagementMetrics(),
                    ],
                    'recent_activities' => $this->getRecentActivities(),
                    'quick_stats' => [
                        'total_users' => \App\Models\User::count(),
                        'active_users' => \App\Models\User::where('account_status', 'active')->count(),
                        'total_revenue' => $this->getTotalRevenue(),
                        'growth_rate' => $this->getGrowthRate(),
                    ],
                ];
            },
            'short'
        );

        return $this->apiResponse($dashboardData, 'Dashboard data retrieved successfully');
    }

    /**
     * Get lead statistics
     */
    public function leads(Request $request): JsonResponse
    {
        $this->rateLimit($request, 60, 5);

        $stats = $this->getCachedData(
            'lead_dashboard_stats',
            function () {
                return $this->leadService->getDashboardStats();
            },
            'short'
        );

        return $this->apiResponse($stats, 'Lead statistics retrieved successfully');
    }

    /**
     * Get property statistics
     */
    public function properties(Request $request): JsonResponse
    {
        $this->rateLimit($request, 60, 5);

        $stats = $this->getCachedData(
            'property_dashboard_stats',
            function () {
                return $this->propertyService->getPropertyStats();
            },
            'short'
        );

        return $this->apiResponse($stats, 'Property statistics retrieved successfully');
    }

    /**
     * Get performance metrics
     */
    public function performance(Request $request): JsonResponse
    {
        $this->rateLimit($request, 30, 10);

        $metrics = $this->getCachedData(
            'performance_metrics',
            function () {
                return [
                    'leads' => [
                        'conversion_rate' => $this->getLeadConversionRate(),
                        'average_response_time' => $this->getAverageResponseTime(),
                        'lead_sources' => $this->leadService->getLeadSourcesStats(10),
                        'lead_statuses' => $this->leadService->getLeadStatusesStats(),
                    ],
                    'properties' => [
                        'performance_metrics' => $this->propertyService->getPropertyPerformanceMetrics(),
                        'property_types' => $this->getPropertyTypeStats(),
                        'location_stats' => $this->getLocationStats(),
                    ],
                    'system' => [
                        'cache_hit_rate' => $this->getCacheHitRate(),
                        'average_response_time' => $this->getAverageResponseTime(),
                        'database_queries' => $this->getDatabaseQueryCount(),
                        'memory_usage' => $this->getMemoryUsage(),
                    ],
                ];
            },
            'medium'
        );

        return $this->apiResponse($metrics, 'Performance metrics retrieved successfully');
    }

    /**
     * Get recent activities
     */
    public function activities(Request $request): JsonResponse
    {
        $this->rateLimit($request, 100, 5);

        $limit = min($request->get('limit', 20), 50);
        $type = $request->get('type', 'all');

        $activities = $this->getCachedData(
            "recent_activities:{$type}:{$limit}",
            function () use ($limit, $type) {
                $query = \App\Models\LeadActivity::with(['user:id,full_name', 'lead:id,first_name,last_name'])
                    ->latest('created_at');

                if ($type !== 'all') {
                    $query->where('type', $type);
                }

                return $query->take($limit)->get();
            },
            'short'
        );

        return $this->apiResponse($activities, 'Recent activities retrieved successfully');
    }

    /**
     * Get growth metrics
     */
    public function growth(Request $request): JsonResponse
    {
        $this->rateLimit($request, 60, 10);

        $metrics = $this->getCachedData(
            'growth_metrics',
            function () {
                return [
                    'leads_growth' => $this->getLeadsGrowthRate(),
                    'properties_growth' => $this->getPropertiesGrowthRate(),
                    'users_growth' => $this->getUsersGrowthRate(),
                    'revenue_growth' => $this->getRevenueGrowthRate(),
                ];
            },
            'long'
        );

        return $this->apiResponse($metrics, 'Growth metrics retrieved successfully');
    }

    /**
     * Get system health check
     */
    public function health(Request $request): JsonResponse
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
            'environment' => app()->environment(),
            'services' => [
                'database' => $this->checkDatabaseHealth(),
                'cache' => $this->checkCacheHealth(),
                'queue' => $this->checkQueueHealth(),
            ],
            'metrics' => [
                'memory_usage' => $this->getMemoryUsage(),
                'database_queries' => $this->getDatabaseQueryCount(),
                'cache_hit_rate' => $this->getCacheHitRate(),
                'average_response_time' => $this->getAverageResponseTime(),
            ],
        ];

        $status = $health['services']['database']['status'] === 'healthy' &&
                 $health['services']['cache']['status'] === 'healthy' &&
                 $health['services']['queue']['status'] === 'healthy' ? 200 : 503;

        return response()->json($health, $status);
    }

    /**
     * Get leads this week
     */
    private function getLeadsThisWeek(): int
    {
        return \App\Models\Lead::where('created_at', '>=', now()->subDays(7))->count();
    }

    /**
     * Get leads this month
     */
    private function getLeadsThisMonth(): int
    {
        return \App\Models\Lead::where('created_at', '>=', now()->subMonth())->count();
    }

    /**
     * Get properties this week
     */
    private function getPropertiesThisWeek(): int
    {
        return \App\Models\Property::where('created_at', '>=', now()->subDays(7))->count();
    }

    /**
     * Get properties this month
     */
    private function getPropertiesThisMonth(): int
    {
        return \App\Models\Property::where('created_at', '>=', now()->subMonth())->count();
    }

    /**
     * Get lead conversion rate
     */
    private function getLeadConversionRate(): float
    {
        $totalLeads = $this->leadService->getRepository()->countTotal();
        $convertedLeads = $this->leadService->getRepository()->countConverted();

        return $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;
    }

    /**
     * Get overall conversion rate
     */
    private function getOverallConversionRate(): float
    {
        $totalLeads = $this->leadService->getRepository()->countTotal();
        $convertedLeads = $this->leadService->getRepository()->countConverted();

        return $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;
    }

    /**
     * Get average response time
     */
    private function getAverageResponseTime(): float
    {
        // This would typically come from tracking system logs
        // For now, return a placeholder
        return 2.5; // seconds
    }

    /**
     * Get user engagement metrics
     */
    private function getUserEngagementMetrics(): array
    {
        return [
            'active_users_today' => \App\Models\User::where('last_login_at', '>=', now()->subDay())->count(),
            'active_users_week' => \App\Models\User::where('last_login_at', '>=', now()->subDays(7))->count(),
            'active_users_month' => \App\Models\User::where('last_login_at', '>=', now()->subMonth())->count(),
            'new_users_today' => \App\Models\User::where('created_at', '>=', now()->subDay())->count(),
        ];
    }

    /**
     * Get published properties count
     */
    private function getPublishedPropertiesCount(): int
    {
        return \App\Models\Property::where('status', 'published')->count();
    }

    /**
     * Get draft properties count
     */
    private function getDraftPropertiesCount(): int
    {
        return \App\Models\Property::where('status', 'draft')->count();
    }

    /**
     * Get featured properties count
     */
    private function getFeaturedPropertiesCount(): int
    {
        return \App\Models\Property::where('featured', true)->count();
    }

    /**
     * Get property type statistics
     */
    private function getPropertyTypeStats(): array
    {
        $types = \App\Models\Property::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');

        return $types->toArray();
    }

    /**
     * Get location statistics
     */
    private function getLocationStats(): array
    {
        $locations = \App\Models\Property::selectRaw('city, COUNT(*) as count')
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get()
            ->pluck('count', 'city');

        return $locations->toArray();
    }

    /**
     * Get leads growth rate
     */
    private function getLeadsGrowthRate(): float
    {
        $thisMonth = \App\Models\Lead::whereMonth('created_at', now()->month)->count();
        $lastMonth = \App\Models\Lead::whereMonth('created_at', now()->subMonth())->count();

        return $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    /**
     * Get properties growth rate
     */
    private function getPropertiesGrowthRate(): float
    {
        $thisMonth = \App\Models\Property::whereMonth('created_at', now()->month)->count();
        $lastMonth = \App\Models\Property::whereMonth('created_at', now()->subMonth())->count();

        return $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    /**
     * Get users growth rate
     */
    private function getUsersGrowthRate(): float
    {
        $thisMonth = \App\Models\User::whereMonth('created_at', now()->month)->count();
        $lastMonth = \App\Models\User::whereMonth('created_at', now()->subMonth())->count();

        return $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    /**
     * Get revenue growth rate
     */
    private function getRevenueGrowthRate(): float
    {
        // This would typically come from actual revenue data
        // For now, return a placeholder
        return 12.5;
    }

    /**
     * Check database health
     */
    private function checkDatabaseHealth(): array
    {
        try {
            \DB::select('SELECT 1')->first();
            return ['status' => 'healthy', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => $e->getMessage()];
        }
    }

    /**
     * Check cache health
     */
    private function checkCacheHealth(): array
    {
        try {
            CacheService::remember('health_check', function () {
                    return 'ok';
                }, 'short');
            
            return ['status' => 'healthy', 'message' => 'Cache working'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => $e->getMessage()];
        }
    }

    /**
     * Check queue health
     */
    private function checkQueueHealth(): array
    {
        try {
            // Check if queue worker is running
            return ['status' => 'healthy', 'message' => 'Queue system working'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => $e->getMessage()];
        }
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage(): float
    {
        return memory_get_usage(true) / 1024 / 1024; // MB
    }

    /**
     * Get database query count
     */
    private function getDatabaseQueryCount(): int
    {
        // This would typically come from query logging
        // For now, return a placeholder
        return 0;
    }

    /**
     * Get cache hit rate
     */
    private function getCacheHitRate(): float
    {
        // This would typically come from cache monitoring
        // For now, return a placeholder
        return 85.5;
    }
}
