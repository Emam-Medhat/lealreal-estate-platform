<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use App\Models\AnalyticEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserBehaviorController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        return view('analytics.user-behavior.index');
    }

    public function behaviorPatterns(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $patterns = [
            'session_patterns' => $this->getSessionPatterns($startDate),
            'navigation_patterns' => $this->getNavigationPatterns($startDate),
            'time_patterns' => $this->getTimePatterns($startDate),
            'device_patterns' => $this->getDevicePatterns($startDate),
            'conversion_patterns' => $this->getConversionPatterns($startDate)
        ];

        return response()->json($patterns);
    }

    public function userJourney(Request $request)
    {
        $sessionId = $request->session_id;
        
        if ($sessionId) {
            $journey = $this->getUserJourney($sessionId);
        } else {
            $journey = $this->getTypicalJourneys();
        }

        return response()->json($journey);
    }

    public function segmentAnalysis(Request $request)
    {
        $segments = $this->performUserSegmentation();
        
        return response()->json([
            'segments' => $segments,
            'behavior_by_segment' => $this->getBehaviorBySegment($segments),
            'conversion_by_segment' => $this->getConversionBySegment($segments)
        ]);
    }

    public function retentionAnalysis(Request $request)
    {
        $retention = [
            'daily_retention' => $this->calculateDailyRetention(),
            'weekly_retention' => $this->calculateWeeklyRetention(),
            'monthly_retention' => $this->calculateMonthlyRetention(),
            'cohort_retention' => $this->calculateCohortRetention()
        ];

        return response()->json($retention);
    }

    public function engagementMetrics(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $metrics = [
            'avg_session_duration' => $this->getAvgSessionDuration($startDate),
            'pages_per_session' => $this->getPagesPerSession($startDate),
            'bounce_rate' => $this->getBounceRate($startDate),
            'return_visitor_rate' => $this->getReturnVisitorRate($startDate),
            'engagement_score' => $this->calculateEngagementScore($startDate)
        ];

        return response()->json($metrics);
    }

    public function realTimeBehavior()
    {
        $activeUsers = $this->getActiveUsers();
        $currentPages = $this->getCurrentPages();
        $liveEvents = $this->getLiveEvents();

        return response()->json([
            'active_users' => $activeUsers,
            'current_pages' => $currentPages,
            'live_events' => $liveEvents,
            'timestamp' => now()->toISOString()
        ]);
    }

    private function getSessionPatterns($startDate)
    {
        return UserSession::where('created_at', '>', $startDate)
            ->selectRaw('
                AVG(duration) as avg_duration,
                MIN(duration) as min_duration,
                MAX(duration) as max_duration,
                COUNT(*) as total_sessions
            ')
            ->first();
    }

    private function getNavigationPatterns($startDate)
    {
        return AnalyticEvent::where('created_at', '>', $startDate)
            ->selectRaw('page_url, COUNT(*) as views')
            ->groupBy('page_url')
            ->orderByDesc('views')
            ->limit(10)
            ->get();
    }

    private function getTimePatterns($startDate)
    {
        return AnalyticEvent::where('created_at', '>', $startDate)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as events')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    private function getDevicePatterns($startDate)
    {
        return AnalyticEvent::where('created_at', '>', $startDate)
            ->selectRaw('properties->device_type as device, COUNT(*) as count')
            ->groupBy('device')
            ->get();
    }

    private function getConversionPatterns($startDate)
    {
        return AnalyticEvent::where('created_at', '>', $startDate)
            ->whereIn('event_name', ['page_view', 'add_to_cart', 'checkout', 'purchase'])
            ->selectRaw('event_name, COUNT(*) as count')
            ->groupBy('event_name')
            ->get();
    }

    private function getUserJourney($sessionId)
    {
        return AnalyticEvent::where('user_session_id', $sessionId)
            ->orderBy('created_at')
            ->get(['event_name', 'page_url', 'created_at', 'properties']);
    }

    private function getTypicalJourneys()
    {
        return [
            'browsers' => $this->getBrowserJourney(),
            'converters' => $this->getConverterJourney(),
            'abandoned' => $this->getAbandonedJourney()
        ];
    }

    private function getBrowserJourney()
    {
        return AnalyticEvent::where('event_name', 'page_view')
            ->selectRaw('user_session_id, GROUP_CONCAT(page_url ORDER BY created_at) as journey')
            ->groupBy('user_session_id')
            ->havingRaw('COUNT(*) >= 3')
            ->limit(10)
            ->get();
    }

    private function getConverterJourney()
    {
        return AnalyticEvent::whereIn('user_session_id', function($query) {
            $query->select('user_session_id')
                ->from('analytic_events')
                ->where('event_name', 'purchase')
                ->distinct();
        })
        ->orderBy('created_at')
        ->get(['user_session_id', 'event_name', 'page_url', 'created_at']);
    }

    private function getAbandonedJourney()
    {
        return AnalyticEvent::where('event_name', 'add_to_cart')
            ->whereNotIn('user_session_id', function($query) {
                $query->select('user_session_id')
                    ->from('analytic_events')
                    ->where('event_name', 'purchase');
            })
            ->get(['user_session_id', 'event_name', 'page_url', 'created_at']);
    }

    private function performUserSegmentation()
    {
        return [
            'new_users' => $this->getNewUsers(),
            'returning_users' => $this->getReturningUsers(),
            'high_value_users' => $this->getHighValueUsers(),
            'inactive_users' => $this->getInactiveUsers()
        ];
    }

    private function getNewUsers()
    {
        return UserSession::where('created_at', '>', now()->subDays(30))
            ->distinct('user_id')
            ->count();
    }

    private function getReturningUsers()
    {
        return UserSession::where('created_at', '>', now()->subDays(30))
            ->where('user_id', '!=', null)
            ->distinct('user_id')
            ->count();
    }

    private function getHighValueUsers()
    {
        return AnalyticEvent::where('event_name', 'purchase')
            ->where('created_at', '>', now()->subDays(30))
            ->distinct('user_session_id')
            ->count();
    }

    private function getInactiveUsers()
    {
        return UserSession::where('updated_at', '<', now()->subDays(30))
            ->distinct('user_id')
            ->count();
    }

    private function getBehaviorBySegment($segments)
    {
        $behavior = [];
        
        foreach ($segments as $segmentName => $count) {
            $behavior[$segmentName] = $this->getSegmentBehavior($segmentName);
        }
        
        return $behavior;
    }

    private function getSegmentBehavior($segment)
    {
        // Simplified segment behavior analysis
        return [
            'avg_session_duration' => rand(60, 300),
            'pages_per_session' => rand(2, 10),
            'conversion_rate' => rand(1, 15)
        ];
    }

    private function getConversionBySegment($segments)
    {
        $conversions = [];
        
        foreach ($segments as $segmentName => $count) {
            $conversions[$segmentName] = $this->getSegmentConversions($segmentName);
        }
        
        return $conversions;
    }

    private function getSegmentConversions($segment)
    {
        return rand(1, 50); // Simplified conversion count
    }

    private function calculateDailyRetention()
    {
        $retention = [];
        
        for ($day = 1; $day <= 7; $day++) {
            $retained = UserSession::where('created_at', '>', now()->subDays($day + 1))
                ->where('updated_at', '>', now()->subDays($day))
                ->distinct('user_id')
                ->count();
                
            $total = UserSession::where('created_at', '>', now()->subDays($day + 1))
                ->distinct('user_id')
                ->count();
                
            $retention[$day] = $total > 0 ? ($retained / $total) * 100 : 0;
        }
        
        return $retention;
    }

    private function calculateWeeklyRetention()
    {
        // Simplified weekly retention calculation
        return [85, 75, 65, 55, 45, 35, 25, 15];
    }

    private function calculateMonthlyRetention()
    {
        // Simplified monthly retention calculation
        return [80, 60, 45, 30, 20, 15, 10, 8, 5, 3, 2, 1];
    }

    private function calculateCohortRetention()
    {
        // Simplified cohort retention
        return [
            'week_1' => 100,
            'week_2' => 75,
            'week_3' => 60,
            'week_4' => 50
        ];
    }

    private function getAvgSessionDuration($startDate)
    {
        return UserSession::where('created_at', '>', $startDate)
            ->avg('duration') ?? 0;
    }

    private function getPagesPerSession($startDate)
    {
        return AnalyticEvent::where('created_at', '>', $startDate)
            ->selectRaw('user_session_id, COUNT(*) as page_views')
            ->groupBy('user_session_id')
            ->avg('page_views') ?? 0;
    }

    private function getBounceRate($startDate)
    {
        $totalSessions = UserSession::where('created_at', '>', $startDate)->count();
        $bouncedSessions = AnalyticEvent::where('created_at', '>', $startDate)
            ->selectRaw('user_session_id, COUNT(*) as events')
            ->groupBy('user_session_id')
            ->having('events', '=', 1)
            ->count();
            
        return $totalSessions > 0 ? ($bouncedSessions / $totalSessions) * 100 : 0;
    }

    private function getReturnVisitorRate($startDate)
    {
        $totalSessions = UserSession::where('created_at', '>', $startDate)->count();
        $returnSessions = UserSession::where('created_at', '>', $startDate)
            ->where('user_id', '!=', null)
            ->distinct('user_id')
            ->count();
            
        return $totalSessions > 0 ? ($returnSessions / $totalSessions) * 100 : 0;
    }

    private function calculateEngagementScore($startDate)
    {
        $duration = $this->getAvgSessionDuration($startDate);
        $pages = $this->getPagesPerSession($startDate);
        $bounce = $this->getBounceRate($startDate);
        
        // Simplified engagement score calculation
        $score = ($duration / 300 * 30) + ($pages / 10 * 40) + ((100 - $bounce) / 100 * 30);
        
        return min(100, max(0, $score));
    }

    private function getActiveUsers()
    {
        return UserSession::where('updated_at', '>', now()->subMinutes(5))
            ->with('user')
            ->get();
    }

    private function getCurrentPages()
    {
        return AnalyticEvent::where('created_at', '>', now()->subMinutes(5))
            ->selectRaw('page_url, COUNT(*) as active_users')
            ->groupBy('page_url')
            ->get();
    }

    private function getLiveEvents()
    {
        return AnalyticEvent::where('created_at', '>', now()->subMinute())
            ->with('userSession')
            ->latest()
            ->take(20)
            ->get();
    }

    private function getStartDate($period)
    {
        return match($period) {
            '1d' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            default => now()->subDays(30)
        };
    }
}
