<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Property;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\CompanyAnalytic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CompanyDashboardController extends Controller
{
    public function index(Company $company)
    {
        $this->authorize('view', $company);
        
        // Get company statistics
        $stats = [
            'total_properties' => $company->properties()->count(),
            'published_properties' => $company->properties()->where('status', 'published')->count(),
            'pending_properties' => $company->properties()->where('status', 'pending')->count(),
            'total_members' => $company->members()->count(),
            'active_members' => $company->members()->where('status', 'active')->count(),
            'total_branches' => $company->branches()->count(),
            'total_views' => $company->properties()->sum('views_count'),
            'total_inquiries' => $company->properties()->sum('inquiries_count'),
        ];

        // Get recent activities
        $recentActivities = UserActivityLog::whereHas('user', function ($query) use ($company) {
            $query->whereHas('companyMemberships', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            });
        })
        ->latest()
        ->limit(10)
        ->get();

        // Get recent properties
        $recentProperties = $company->properties()
            ->with(['location', 'price'])
            ->latest()
            ->limit(6)
            ->get();

        // Get top performing properties
        $topProperties = $company->properties()
            ->with(['location', 'price'])
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get();

        // Get analytics data for charts
        $analytics = $this->getAnalyticsData($company);

        return view('company.dashboard', compact(
            'company',
            'stats',
            'recentActivities',
            'recentProperties',
            'topProperties',
            'analytics'
        ));
    }

    public function getQuickStats(Company $company): JsonResponse
    {
        $this->authorize('view', $company);
        
        $stats = [
            'total_properties' => $company->properties()->count(),
            'published_properties' => $company->properties()->where('status', 'published')->count(),
            'total_members' => $company->members()->count(),
            'total_branches' => $company->branches()->count(),
            'total_views' => $company->properties()->sum('views_count'),
            'total_inquiries' => $company->properties()->sum('inquiries_count'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getRecentProperties(Company $company, Request $request): JsonResponse
    {
        $this->authorize('view', $company);
        
        $properties = $company->properties()
            ->with(['location', 'price'])
            ->latest()
            ->limit($request->limit ?? 10)
            ->get();

        return response()->json([
            'success' => true,
            'properties' => $properties
        ]);
    }

    public function getRecentActivities(Company $company, Request $request): JsonResponse
    {
        $this->authorize('view', $company);
        
        $activities = UserActivityLog::whereHas('user', function ($query) use ($company) {
            $query->whereHas('companyMemberships', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            });
        })
        ->latest()
        ->limit($request->limit ?? 10)
        ->get();

        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    }

    public function getTopProperties(Company $company, Request $request): JsonResponse
    {
        $this->authorize('view', $company);
        
        $properties = $company->properties()
            ->with(['location', 'price'])
            ->orderBy('views_count', 'desc')
            ->limit($request->limit ?? 5)
            ->get();

        return response()->json([
            'success' => true,
            'properties' => $properties
        ]);
    }

    public function getAnalyticsData(Company $company): JsonResponse
    {
        $this->authorize('view', $company);
        
        $analytics = $this->getAnalyticsData($company);

        return response()->json([
            'success' => true,
            'analytics' => $analytics
        ]);
    }

    private function getAnalyticsData(Company $company): array
    {
        // Property views over last 30 days
        $viewsData = CompanyAnalytic::where('company_id', $company->id)
            ->where('metric_type', 'property_views')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            })
            ->map(function ($day) {
                return $day->sum('value');
            });

        // Inquiries over last 30 days
        $inquiriesData = CompanyAnalytic::where('company_id', $company->id)
            ->where('metric_type', 'property_inquiries')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            })
            ->map(function ($day) {
                return $day->sum('value');
            });

        // Property status distribution
        $statusData = $company->properties()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'views_data' => $viewsData,
            'inquiries_data' => $inquiriesData,
            'status_data' => $statusData,
        ];
    }

    public function getPerformanceMetrics(Company $company): JsonResponse
    {
        $this->authorize('view', $company);
        
        $metrics = [
            'average_property_views' => $company->properties()->avg('views_count') ?? 0,
            'average_property_inquiries' => $company->properties()->avg('inquiries_count') ?? 0,
            'conversion_rate' => $this->calculateConversionRate($company),
            'listings_growth' => $this->calculateListingsGrowth($company),
            'member_productivity' => $this->calculateMemberProductivity($company),
        ];

        return response()->json([
            'success' => true,
            'metrics' => $metrics
        ]);
    }

    private function calculateConversionRate(Company $company): float
    {
        $totalViews = $company->properties()->sum('views_count');
        $totalInquiries = $company->properties()->sum('inquiries_count');
        
        return $totalViews > 0 ? round(($totalInquiries / $totalViews) * 100, 2) : 0;
    }

    private function calculateListingsGrowth(Company $company): float
    {
        $now = now();
        $lastMonth = $now->copy()->subMonth();
        $twoMonthsAgo = $now->copy()->subMonths(2);

        $currentMonthCount = $company->properties()
            ->where('created_at', '>=', $lastMonth)
            ->count();
        
        $previousMonthCount = $company->properties()
            ->where('created_at', '>=', $twoMonthsAgo)
            ->where('created_at', '<', $lastMonth)
            ->count();

        return $previousMonthCount > 0 
            ? round((($currentMonthCount - $previousMonthCount) / $previousMonthCount) * 100, 2)
            : 0;
    }

    private function calculateMemberProductivity(Company $company): float
    {
        $activeMembers = $company->members()->where('status', 'active')->count();
        $totalProperties = $company->properties()->count();
        
        return $activeMembers > 0 ? round($totalProperties / $activeMembers, 2) : 0;
    }

    public function exportDashboardData(Company $company, Request $request): JsonResponse
    {
        $this->authorize('view', $company);
        
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'data_type' => 'required|in:properties,members,analytics,all',
        ]);

        $data = [];

        switch ($request->data_type) {
            case 'properties':
                $data['properties'] = $company->properties()->get();
                break;
            case 'members':
                $data['members'] = $company->members()->with('user.profile')->get();
                break;
            case 'analytics':
                $data['analytics'] = $this->getAnalyticsData($company);
                break;
            case 'all':
                $data['properties'] = $company->properties()->get();
                $data['members'] = $company->members()->with('user.profile')->get();
                $data['analytics'] = $this->getAnalyticsData($company);
                break;
        }

        $filename = "company_{$company->id}_dashboard_{$request->data_type}_" . now()->format('Y-m-d');

        return response()->json([
            'success' => true,
            'data' => $data,
            'filename' => $filename,
            'message' => 'Dashboard data exported successfully'
        ]);
    }
}
