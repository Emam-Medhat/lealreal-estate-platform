<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\AdCampaign;
use App\Models\AdPlacement;
use App\Models\AdClick;
use App\Models\AdImpression;
use App\Models\AdConversion;
use App\Models\AdBudget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\AdAnalyticsService;

class AdAnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AdAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    protected function authorizeAdmin()
    {
        if (Auth::user()->user_type !== 'admin') {
            abort(403);
        }
    }

    public function dashboard()
    {
        $this->authorizeAdmin();
        $data = $this->analyticsService->getDashboardData();

        return view('ads.analytics-dashboard', $data);
    }

    public function platformOverview()
    {
        $this->authorizeAdmin();
        $timeframe = request('timeframe', '30days');
        $analytics = $this->analyticsService->getPlatformAnalytics($timeframe);

        return view('ads.platform-analytics', compact('analytics', 'timeframe'));
    }

    public function realTimeAnalytics()
    {
        $this->authorizeAdmin();
        $metrics = $this->analyticsService->getRealTimeMetrics();

        return response()->json($metrics);
    }

    public function platformAnalytics()
    {
        return $this->platformOverview();
    }

    // Additional methods can be refactored similarly as the service grows
}
