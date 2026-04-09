<?php

namespace App\Http\Controllers;

use App\Models\AnalyticEvent;
use App\Models\UserSession;
use App\Models\Conversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        $totalEvents = AnalyticEvent::count();
        $totalSessions = UserSession::count();
        $totalConversions = Conversion::count();
        $conversionRate = $totalSessions > 0 ? ($totalConversions / $totalSessions) * 100 : 0;

        $recentEvents = AnalyticEvent::with('userSession')
            ->latest()
            ->take(10)
            ->get();

        $topPages = AnalyticEvent::select('page_url')
            ->selectRaw('COUNT(*) as views')
            ->groupBy('page_url')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        return view('analytics.dashboard', compact(
            'totalEvents',
            'totalSessions', 
            'totalConversions',
            'conversionRate',
            'recentEvents',
            'topPages'
        ));
    }

    public function dashboard()
    {
        return view('analytics.dashboard');
    }

    public function overview(Request $request)
    {
        // Sample data for the overview page
        $totalUsers = 1250;
        $totalProperties = 850;
        $totalInteractions = 3200;
        $conversionRate = 3.5;
        
        $recentActivities = collect([
            (object) ['description' => 'مستخدم جديد سجل', 'user' => (object) ['name' => 'أحمد محمد'], 'created_at' => now()->subMinutes(5)],
            (object) ['description' => 'عقار جديد تم إضافته', 'user' => (object) ['name' => 'سارة أحمد'], 'created_at' => now()->subMinutes(15)],
            (object) ['description' => 'طلب تم استكماله', 'user' => (object) ['name' => 'محمد علي'], 'created_at' => now()->subHour()],
        ]);

        return view('analytics.overview', compact(
            'totalUsers',
            'totalProperties', 
            'totalInteractions',
            'conversionRate',
            'recentActivities'
        ));
    }

    public function realTime()
    {
        // Sample data for real-time page
        $activeUsers = 45;
        $currentSessions = 32;
        $requestsPerMinute = 120;
        $hitRate = 94.5;
        $cpuUsage = 45;
        $memoryUsage = 67;
        $diskUsage = 23;
        $uptime = '15d 8h';
        
        $liveActivities = collect([
            (object) ['description' => 'صفحة الرئيسية تم زيارتها', 'user' => (object) ['name' => 'خالد سالم'], 'created_at' => now()->subSeconds(30)],
            (object) ['description' => 'بحث عن عقارات', 'user' => (object) ['name' => 'نورا أحمد'], 'created_at' => now()->subMinute()],
            (object) ['description' => 'تم تسجيل الدخول', 'user' => (object) ['name' => 'عمر محمد'], 'created_at' => now()->subMinutes(2)],
        ]);

        return view('analytics.real-time', compact(
            'activeUsers',
            'currentSessions',
            'requestsPerMinute',
            'hitRate',
            'cpuUsage',
            'memoryUsage',
            'diskUsage',
            'uptime',
            'liveActivities'
        ));
    }

    public function trackEvent(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string',
            'page_url' => 'required|string',
            'user_agent' => 'nullable|string',
            'ip_address' => 'nullable|string',
            'properties' => 'nullable|array'
        ]);

        $session = $this->getOrCreateSession($request);

        AnalyticEvent::create([
            'user_session_id' => $session->id,
            'event_name' => $request->event_name,
            'page_url' => $request->page_url,
            'user_agent' => $request->user_agent,
            'ip_address' => $request->ip_address,
            'properties' => $request->properties ?? [],
            'created_at' => now()
        ]);

        return response()->json(['status' => 'success']);
    }

    public function getMetrics(Request $request)
    {
        $period = $request->period ?? '7d';
        $startDate = $this->getStartDate($period);

        $metrics = [
            'page_views' => $this->getPageViews($startDate),
            'unique_visitors' => $this->getUniqueVisitors($startDate),
            'sessions' => $this->getSessions($startDate),
            'bounce_rate' => $this->getBounceRate($startDate),
            'avg_session_duration' => $this->getAvgSessionDuration($startDate),
            'conversion_rate' => $this->getConversionRate($startDate)
        ];

        return response()->json($metrics);
    }

    public function reports()
    {
        // Sample data for reports page
        $userReportsCount = 12;
        $propertyReportsCount = 8;
        $financialReportsCount = 5;
        $performanceReportsCount = 7;
        
        $recentReports = collect([
            (object) [
                'name' => 'تقرير المستخدمين الشهري',
                'description' => 'تحليل شامل لنمو المستخدمين',
                'type' => 'المستخدمون',
                'created_at' => now()->subDay(),
                'size' => '2.3 MB'
            ],
            (object) [
                'name' => 'تقرير العقارات',
                'description' => 'إحصائيات العقارات المباعة',
                'type' => 'العقارات',
                'created_at' => now()->subDays(2),
                'size' => '1.8 MB'
            ],
        ]);

        return view('analytics.reports', compact(
            'userReportsCount',
            'propertyReportsCount',
            'financialReportsCount',
            'performanceReportsCount',
            'recentReports'
        ));
    }

    public function insights()
    {
        // Sample data for insights page
        $performanceInsights = 8;
        $marketTrends = 15;
        $userBehaviorInsights = 12;
        
        $keyInsights = collect([
            (object) [
                'title' => 'زيادة بنسبة 25% في التفاعلات',
                'category' => 'الأداء',
                'description' => 'شهدت المنصة زيادة كبيرة في التفاعلات خلال الأسبوع الماضي',
                'confidence' => 95,
                'impact' => 'عالي',
                'created_at' => now()->subHours(6)
            ],
            (object) [
                'title' => 'تراجع في معدل التحويل',
                'category' => 'التحويلات',
                'description' => 'انخفض معدل التحويل بنسبة 3% ويحتاج لتدخل',
                'confidence' => 88,
                'impact' => 'متوسط',
                'created_at' => now()->subHours(12)
            ],
        ]);
        
        $recommendations = collect([
            (object) [
                'title' => 'تحسين صفحة الهبوط',
                'description' => 'تحسين تصميم صفحة الهبوط لزيادة التحويلات',
                'priority' => 'عالية'
            ],
            (object) [
                'title' => 'حملة تسويقية جديدة',
                'description' => 'إطلاق حملة تسويقية لجذب مستخدمين جدد',
                'priority' => 'متوسطة'
            ],
        ]);

        return view('analytics.insights', compact(
            'performanceInsights',
            'marketTrends',
            'userBehaviorInsights',
            'keyInsights',
            'recommendations'
        ));
    }

    public function segmentation()
    {
        // Sample data for segmentation page
        $totalSegments = 8;
        $activeSegments = 6;
        $avgSegmentSize = 156;
        $conversionRate = 4.2;
        
        $segments = collect([
            (object) [
                'name' => 'المستخدمون النشطون',
                'type' => 'سلوكية',
                'description' => 'المستخدمون الذين سجلوا دخولهم في آخر 7 أيام',
                'user_count' => 450,
                'conversion_rate' => 5.8,
                'is_active' => true,
                'updated_at' => now()->subHours(2)
            ],
            (object) [
                'name' => 'المشترون المحتملون',
                'type' => 'ديموغرافية',
                'description' => 'المستخدمون الذين أبدوا اهتماماً بالشراء',
                'user_count' => 230,
                'conversion_rate' => 12.5,
                'is_active' => true,
                'updated_at' => now()->subDay()
            ],
        ]);

        return view('analytics.segmentation', compact(
            'totalSegments',
            'activeSegments',
            'avgSegmentSize',
            'conversionRate',
            'segments'
        ));
    }

    public function export(Request $request)
    {
        $format = $request->format ?? 'csv';
        $startDate = $request->start_date ?? now()->subDays(30);
        $endDate = $request->end_date ?? now();

        $events = AnalyticEvent::whereBetween('created_at', [$startDate, $endDate])
            ->get();

        if ($format === 'csv') {
            return $this->exportToCsv($events);
        } elseif ($format === 'json') {
            return response()->json($events);
        }

        return response()->json(['error' => 'Unsupported format'], 400);
    }

    private function getOrCreateSession($request)
    {
        $sessionId = $request->cookie('analytics_session') ?? session()->getId();
        
        return UserSession::firstOrCreate([
            'session_id' => $sessionId
        ], [
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function calculateBounceRate($sessions)
    {
        $bouncedSessions = $sessions->filter(function ($session) {
            return $session->events->count() <= 1;
        });

        return $sessions->count() > 0 ? ($bouncedSessions->count() / $sessions->count()) * 100 : 0;
    }

    private function getStartDate($period)
    {
        return match($period) {
            '1d' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(7)
        };
    }

    private function getPageViews($startDate)
    {
        return AnalyticEvent::where('event_name', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->count();
    }

    private function getUniqueVisitors($startDate)
    {
        return UserSession::where('created_at', '>=', $startDate)
            ->distinct('session_id')
            ->count();
    }

    private function getSessions($startDate)
    {
        return UserSession::where('created_at', '>=', $startDate)->count();
    }

    private function getBounceRate($startDate)
    {
        $sessions = UserSession::where('created_at', '>=', $startDate)->get();
        return $this->calculateBounceRate($sessions);
    }

    private function getAvgSessionDuration($startDate)
    {
        return UserSession::where('created_at', '>=', $startDate)
            ->avg('duration') ?? 0;
    }

    private function getConversionRate($startDate)
    {
        $sessions = UserSession::where('created_at', '>=', $startDate)->count();
        $conversions = Conversion::where('created_at', '>=', $startDate)->count();
        
        return $sessions > 0 ? ($conversions / $sessions) * 100 : 0;
    }

    private function exportToCsv($events)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="analytics.csv"'
        ];

        $callback = function() use ($events) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['ID', 'Event Name', 'Page URL', 'User Agent', 'IP Address', 'Created At']);
            
            foreach ($events as $event) {
                fputcsv($file, [
                    $event->id,
                    $event->event_name,
                    $event->page_url,
                    $event->user_agent,
                    $event->ip_address,
                    $event->created_at
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
