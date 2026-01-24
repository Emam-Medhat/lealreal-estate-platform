<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\DeveloperProjectPhase;
use App\Models\DeveloperProjectUnit;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DeveloperDashboardController extends Controller
{
    public function index()
    {
        $developer = Auth::user()->developer;
        
        if (!$developer) {
            return redirect()->route('developer.create')
                ->with('error', 'Please create your developer profile first.');
        }

        // Quick stats
        $stats = [
            'total_projects' => $developer->projects()->count(),
            'active_projects' => $developer->projects()->where('status', 'active')->count(),
            'completed_projects' => $developer->projects()->where('status', 'completed')->count(),
            'total_units' => $developer->projects()->withCount('units')->get()->sum('units_count'),
            'sold_units' => $developer->projects()->withCount(['units' => function ($query) {
                $query->where('status', 'sold');
            }])->get()->sum('units_count'),
            'total_revenue' => $developer->projects()->sum('total_value'),
            'ongoing_phases' => $developer->projects()
                ->whereHas('phases', function ($query) {
                    $query->where('status', 'in_progress');
                })
                ->count(),
        ];

        // Recent activities
        $recentActivities = UserActivityLog::where('user_id', Auth::id())
            ->latest()
            ->limit(10)
            ->get();

        // Recent projects
        $recentProjects = $developer->projects()
            ->latest()
            ->limit(5)
            ->get();

        // Upcoming milestones
        $upcomingMilestones = $developer->projects()
            ->whereHas('phases', function ($query) {
                $query->where('end_date', '>', now())
                    ->where('end_date', '<=', now()->addDays(30));
            })
            ->with(['phases'])
            ->limit(5)
            ->get();

        return view('developer.dashboard.index', compact(
            'developer',
            'stats',
            'recentActivities',
            'recentProjects',
            'upcomingMilestones'
        ));
    }

    public function getQuickStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        if (!$developer) {
            return response()->json([
                'success' => false,
                'message' => 'Developer profile not found'
            ]);
        }

        $stats = [
            'total_projects' => $developer->projects()->count(),
            'active_projects' => $developer->projects()->where('status', 'active')->count(),
            'completed_projects' => $developer->projects()->where('status', 'completed')->count(),
            'total_units' => $developer->projects()->withCount('units')->get()->sum('units_count'),
            'sold_units' => $developer->projects()->withCount(['units' => function ($query) {
                $query->where('status', 'sold');
            }])->get()->sum('units_count'),
            'total_revenue' => $developer->projects()->sum('total_value'),
            'this_month_revenue' => $developer->projects()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_value'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getRecentActivities(): JsonResponse
    {
        $activities = UserActivityLog::where('user_id', Auth::id())
            ->latest()
            ->limit(20)
            ->get(['action', 'details', 'created_at']);

        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    }

    public function getProjectProgress(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $projects = $developer->projects()
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
            });

        return response()->json([
            'success' => true,
            'projects' => $projects
        ]);
    }

    public function getUpcomingDeadlines(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $deadlines = $developer->projects()
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
            });

        return response()->json([
            'success' => true,
            'deadlines' => $deadlines
        ]);
    }

    public function getFinancialOverview(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $projects = $developer->projects()->get();
        
        $overview = [
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

        return response()->json([
            'success' => true,
            'overview' => $overview
        ]);
    }

    public function getUnitSalesStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $units = $developer->projects()
            ->with(['units'])
            ->get()
            ->pluck('units')
            ->flatten();

        $stats = [
            'total_units' => $units->count(),
            'available_units' => $units->where('status', 'available')->count(),
            'reserved_units' => $units->where('status', 'reserved')->count(),
            'sold_units' => $units->where('status', 'sold')->count(),
            'under_construction_units' => $units->where('status', 'under_construction')->count(),
            'ready_units' => $units->where('status', 'ready')->count(),
            'total_sold_value' => $units->where('status', 'sold')->sum('price'),
            'average_unit_price' => $units->where('status', 'sold')->avg('price'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getConstructionUpdates(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $updates = $developer->projects()
            ->whereHas('constructionUpdates')
            ->with(['constructionUpdates' => function ($query) {
                $query->latest()->limit(10);
            }])
            ->get()
            ->pluck('constructionUpdates')
            ->flatten()
            ->sortByDesc('created_at')
            ->take(20);

        return response()->json([
            'success' => true,
            'updates' => $updates
        ]);
    }

    public function exportDashboardData(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'data_type' => 'required|in:stats,projects,units,revenue',
        ]);

        $developer = Auth::user()->developer;
        
        switch ($request->data_type) {
            case 'stats':
                $data = $this->getQuickStats()->getData();
                break;
            case 'projects':
                $data = $developer->projects()->with(['phases', 'units'])->get();
                break;
            case 'units':
                $data = $developer->projects()->with(['units'])->get()->pluck('units')->flatten();
                break;
            case 'revenue':
                $data = $this->getFinancialOverview()->getData();
                break;
        }

        $filename = "developer_dashboard_{$request->data_type}_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $data,
            'filename' => $filename,
            'message' => 'Dashboard data exported successfully'
        ]);
    }
}
