<?php

namespace App\Services;

use App\Models\Developer;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class DeveloperDashboardService
{
    /**
     * Get recent projects for a developer.
     *
     * @param Developer $developer
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentProjects(Developer $developer, int $limit = 5)
    {
        $cacheKey = "developer_recent_projects_{$developer->id}_{$limit}";

        return Cache::remember($cacheKey, 600, function () use ($developer, $limit) {
            return $developer->projects()
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get upcoming milestones for a developer.
     *
     * @param Developer $developer
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingMilestones(Developer $developer, int $limit = 5)
    {
        $cacheKey = "developer_upcoming_milestones_{$developer->id}_{$limit}";

        return Cache::remember($cacheKey, 600, function () use ($developer, $limit) {
            return $developer->projects()
                ->whereHas('phases', function ($query) {
                    $query->where('end_date', '>', now())
                        ->where('end_date', '<=', now()->addDays(30));
                })
                ->with(['phases' => function ($query) {
                    $query->where('end_date', '>', now())
                        ->where('end_date', '<=', now()->addDays(30))
                        ->orderBy('end_date');
                }])
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get recent activities for a user.
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentActivities(int $userId, int $limit = 10)
    {
        $cacheKey = "developer_recent_activities_{$userId}_{$limit}";

        return Cache::remember($cacheKey, 300, function () use ($userId, $limit) {
            return UserActivityLog::where('user_id', $userId)
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get quick stats for a developer with caching.
     *
     * @param Developer $developer
     * @return array
     */
    public function getQuickStats(Developer $developer): array
    {
        $cacheKey = "developer_quick_stats_{$developer->id}";

        return Cache::remember($cacheKey, 600, function () use ($developer) {
            $projects = $developer->projects();
            
            return [
                'total_projects' => $projects->count(),
                'active_projects' => $projects->where('status', 'active')->count(),
                'completed_projects' => $projects->where('status', 'completed')->count(),
                'total_units' => $projects->withCount('units')->get()->sum('units_count'),
                'sold_units' => $projects->withCount(['units' => function ($query) {
                    $query->where('status', 'sold');
                }])->get()->sum('units_count'),
                'total_revenue' => $projects->sum('total_value'),
                'ongoing_phases' => $projects
                    ->whereHas('phases', function ($query) {
                        $query->where('status', 'in_progress');
                    })
                    ->count(),
                'this_month_revenue' => $projects
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total_value'),
            ];
        });
    }

    /**
     * Get project progress for a developer with caching.
     *
     * @param Developer $developer
     * @return array
     */
    public function getProjectProgress(Developer $developer): array
    {
        $cacheKey = "developer_project_progress_{$developer->id}";

        return Cache::remember($cacheKey, 1200, function () use ($developer) {
            return $developer->projects()
                ->with(['phases' => function ($query) {
                    $query->orderBy('start_date');
                }])
                ->get()
                ->map(function ($project) {
                    $totalPhases = $project->phases->count();
                    $completedPhases = $project->phases->where('status', 'completed')->count();
                    $inProgressPhases = $project->phases->where('status', 'in_progress')->count();
                    
                    return [
                        'id' => $project->id,
                        'name' => $project->name,
                        'status' => $project->status,
                        'total_phases' => $totalPhases,
                        'completed_phases' => $completedPhases,
                        'in_progress_phases' => $inProgressPhases,
                        'progress_percentage' => $totalPhases > 0 
                            ? round(($completedPhases / $totalPhases) * 100, 2)
                            : 0,
                        'current_phase' => $project->phases
                            ->where('status', 'in_progress')
                            ->first(),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get financial overview for a developer with caching.
     *
     * @param Developer $developer
     * @return array
     */
    public function getFinancialOverview(Developer $developer): array
    {
        $cacheKey = "developer_financial_overview_{$developer->id}";

        return Cache::remember($cacheKey, 1800, function () use ($developer) {
            $projects = $developer->projects()->get();
            
            return [
                'total_project_value' => $projects->sum('total_value'),
                'total_investment' => $projects->sum('total_investment'),
                'expected_roi' => $projects->avg('expected_roi'),
                'total_units_sold' => $projects->sum('units_sold'),
                'total_revenue' => $projects->sum('total_revenue'),
                'monthly_revenue' => $projects
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total_revenue'),
                'revenue_by_month' => $projects
                    ->groupBy(function ($project) {
                        return $project->created_at->format('Y-m');
                    })
                    ->map(function ($group) {
                        return $group->sum('total_revenue');
                    }),
            ];
        });
    }

    /**
     * Get unit sales stats for a developer with caching.
     *
     * @param Developer $developer
     * @return array
     */
    public function getUnitSalesStats(Developer $developer): array
    {
        $cacheKey = "developer_unit_sales_stats_{$developer->id}";

        return Cache::remember($cacheKey, 1800, function () use ($developer) {
            $units = $developer->projects()
                ->with(['units'])
                ->get()
                ->pluck('units')
                ->flatten();

            return [
                'total_units' => $units->count(),
                'available_units' => $units->where('status', 'available')->count(),
                'reserved_units' => $units->where('status', 'reserved')->count(),
                'sold_units' => $units->where('status', 'sold')->count(),
                'under_construction_units' => $units->where('status', 'under_construction')->count(),
                'ready_units' => $units->where('status', 'ready')->count(),
                'total_sold_value' => $units->where('status', 'sold')->sum('price'),
                'average_unit_price' => $units->where('status', 'sold')->avg('price'),
            ];
        });
    }

    /**
     * Get construction updates for a developer with caching.
     *
     * @param Developer $developer
     * @return array
     */
    public function getConstructionUpdates(Developer $developer): array
    {
        $cacheKey = "developer_construction_updates_{$developer->id}";

        return Cache::remember($cacheKey, 1200, function () use ($developer) {
            return $developer->projects()
                ->whereHas('constructionUpdates')
                ->with(['constructionUpdates' => function ($query) {
                    $query->latest()->limit(10);
                }])
                ->get()
                ->pluck('constructionUpdates')
                ->flatten()
                ->sortByDesc('created_at')
                ->take(20)
                ->values()
                ->toArray();
        });
    }

    /**
     * Get upcoming deadlines for a developer with caching.
     *
     * @param Developer $developer
     * @return array
     */
    public function getUpcomingDeadlines(Developer $developer): array
    {
        $cacheKey = "developer_upcoming_deadlines_{$developer->id}";

        return Cache::remember($cacheKey, 3600, function () use ($developer) {
            return $developer->projects()
                ->whereHas('phases', function ($query) {
                    $query->where('end_date', '>', now())
                        ->where('end_date', '<=', now()->addDays(60));
                })
                ->with(['phases' => function ($query) {
                    $query->where('end_date', '>', now())
                        ->where('end_date', '<=', now()->addDays(60))
                        ->orderBy('end_date');
                }])
                ->get()
                ->map(function ($project) {
                    return [
                        'project_id' => $project->id,
                        'project_name' => $project->name,
                        'phases' => $project->phases->map(function ($phase) {
                            return [
                                'id' => $phase->id,
                                'name' => $phase->name,
                                'end_date' => $phase->end_date,
                                'days_remaining' => now()->diffInDays($phase->end_date, false),
                                'status' => $phase->status,
                            ];
                        }),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get recent activities for a user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getRecentActivitiesV2(int $userId, int $limit = 10): array
    {
        return UserActivityLog::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get(['action', 'details', 'created_at'])
            ->toArray();
    }
}
