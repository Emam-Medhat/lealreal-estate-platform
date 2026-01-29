<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\DeveloperProjectPhase;
use App\Models\DeveloperProjectUnit;
use App\Models\UserActivityLog;
use App\Services\DeveloperDashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DeveloperDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DeveloperDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = Auth::user();
        $developer = $user->developer;
        
        if (!$developer) {
            return redirect()->route('developer.create')
                ->with('error', 'Please create your developer profile first.');
        }

        // Get optimized stats from service
        $stats = $this->dashboardService->getQuickStats($developer);

        // Recent activities
        $recentActivities = $this->dashboardService->getRecentActivities($user->id);

        // Recent projects
        $recentProjects = $this->dashboardService->getRecentProjects($developer);

        // Upcoming milestones
        $upcomingMilestones = $this->dashboardService->getUpcomingMilestones($developer);

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

        $stats = $this->dashboardService->getQuickStats($developer);

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getRecentActivities(): JsonResponse
    {
        $activities = $this->dashboardService->getRecentActivities(Auth::id(), 20);

        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    }

    public function getProjectProgress(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $projects = $this->dashboardService->getProjectProgress($developer);

        return response()->json([
            'success' => true,
            'projects' => $projects
        ]);
    }


    public function getUpcomingDeadlines(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $deadlines = $this->dashboardService->getUpcomingDeadlines($developer);

        return response()->json([
            'success' => true,
            'deadlines' => $deadlines
        ]);
    }

    public function getFinancialOverview(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $overview = $this->dashboardService->getFinancialOverview($developer);

        return response()->json([
            'success' => true,
            'overview' => $overview
        ]);
    }

    public function getUnitSalesStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $stats = $this->dashboardService->getUnitSalesStats($developer);

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getConstructionUpdates(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $updates = $this->dashboardService->getConstructionUpdates($developer);

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
