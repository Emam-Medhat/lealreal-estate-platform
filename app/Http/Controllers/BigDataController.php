<?php

namespace App\Http\Controllers;

use App\Models\AnalyticEvent;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BigDataController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        return view('analytics.bigdata.index');
    }

    public function processData(Request $request)
    {
        $request->validate([
            'data_source' => 'required|string',
            'processing_type' => 'required|string|in:aggregate,filter,transform',
            'time_range' => 'required|string|in:hour,day,week,month'
        ]);

        $results = $this->processBigData($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $results,
            'processing_time' => microtime(true) - LARAVEL_START
        ]);
    }

    public function aggregateData(Request $request)
    {
        $timeRange = $request->time_range ?? 'day';
        $startDate = $request->start_date ?? now()->subDays(30);
        $endDate = $request->end_date ?? now();

        $aggregatedData = AnalyticEvent::selectRaw($this->getAggregateSelect($timeRange))
            ->selectRaw('COUNT(*) as total_events')
            ->selectRaw('COUNT(DISTINCT user_session_id) as unique_sessions')
            ->selectRaw('COUNT(DISTINCT ip_address) as unique_visitors')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy($this->getGroupByField($timeRange))
            ->orderBy($this->getGroupByField($timeRange))
            ->get();

        return response()->json($aggregatedData);
    }

    public function realTimeStream()
    {
        return response()->stream(function() {
            while (true) {
                $data = [
                    'timestamp' => now()->toISOString(),
                    'active_sessions' => UserSession::where('updated_at', '>', now()->subMinutes(5))->count(),
                    'events_per_minute' => AnalyticEvent::where('created_at', '>', now()->subMinute())->count(),
                    'conversions' => AnalyticEvent::where('event_name', 'conversion')->where('created_at', '>', now()->subMinute())->count()
                ];

                echo "data: " . json_encode($data) . "\n\n";
                ob_flush();
                flush();
                sleep(5);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    public function dataInsights(Request $request)
    {
        $insights = [
            'peak_hours' => $this->getPeakHours(),
            'top_pages' => $this->getTopPages(),
            'user_patterns' => $this->getUserPatterns(),
            'conversion_funnel' => $this->getConversionFunnel(),
            'geographic_distribution' => $this->getGeographicDistribution()
        ];

        return response()->json($insights);
    }

    public function exportBigData(Request $request)
    {
        $format = $request->format ?? 'json';
        $chunkSize = $request->chunk_size ?? 1000;
        
        $events = AnalyticEvent::orderBy('created_at')
            ->chunk($chunkSize, function($events) use ($format) {
                if ($format === 'json') {
                    echo json_encode($events->toArray()) . "\n";
                }
            });

        return response()->json(['status' => 'export_completed']);
    }

    private function processBigData($params)
    {
        $dataSource = $params['data_source'];
        $processingType = $params['processing_type'];
        $timeRange = $params['time_range'];

        switch ($processingType) {
            case 'aggregate':
                return $this->aggregateData(new Request($params));
            case 'filter':
                return $this->filterData($params);
            case 'transform':
                return $this->transformData($params);
            default:
                return [];
        }
    }

    private function filterData($params)
    {
        $query = AnalyticEvent::query();

        if (isset($params['event_type'])) {
            $query->where('event_name', $params['event_type']);
        }

        if (isset($params['date_from'])) {
            $query->where('created_at', '>=', $params['date_from']);
        }

        if (isset($params['date_to'])) {
            $query->where('created_at', '<=', $params['date_to']);
        }

        return $query->get();
    }

    private function transformData($params)
    {
        $data = AnalyticEvent::all();

        return $data->map(function ($event) {
            return [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'page_url' => $event->page_url,
                'timestamp' => $event->created_at->timestamp,
                'hour_of_day' => $event->created_at->hour,
                'day_of_week' => $event->created_at->dayOfWeek,
                'is_weekend' => $event->created_at->isWeekend()
            ];
        });
    }

    private function getAggregateSelect($timeRange)
    {
        return match($timeRange) {
            'hour' => 'DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as period',
            'day' => 'DATE(created_at) as period',
            'week' => 'YEARWEEK(created_at) as period',
            'month' => 'DATE_FORMAT(created_at, "%Y-%m-01") as period',
            default => 'DATE(created_at) as period'
        };
    }

    private function getGroupByField($timeRange)
    {
        return match($timeRange) {
            'hour' => DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00")'),
            'day' => DB::raw('DATE(created_at)'),
            'week' => DB::raw('YEARWEEK(created_at)'),
            'month' => DB::raw('DATE_FORMAT(created_at, "%Y-%m-01")'),
            default => DB::raw('DATE(created_at)')
        };
    }

    private function getPeakHours()
    {
        return AnalyticEvent::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('created_at', '>', now()->subDays(30))
            ->groupBy('hour')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
    }

    private function getTopPages()
    {
        return AnalyticEvent::select('page_url')
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('COUNT(DISTINCT user_session_id) as unique_visitors')
            ->where('created_at', '>', now()->subDays(30))
            ->groupBy('page_url')
            ->orderByDesc('views')
            ->limit(10)
            ->get();
    }

    private function getUserPatterns()
    {
        return UserSession::selectRaw('AVG(duration) as avg_duration')
            ->selectRaw('AVG(events_count) as avg_events')
            ->selectRaw('COUNT(*) as total_sessions')
            ->where('created_at', '>', now()->subDays(30))
            ->first();
    }

    private function getConversionFunnel()
    {
        $steps = ['page_view', 'add_to_cart', 'checkout', 'purchase'];
        $funnel = [];

        foreach ($steps as $step) {
            $count = AnalyticEvent::where('event_name', $step)
                ->where('created_at', '>', now()->subDays(30))
                ->distinct('user_session_id')
                ->count();
            
            $funnel[] = [
                'step' => $step,
                'count' => $count,
                'conversion_rate' => $this->calculateStepConversion($step, $count)
            ];
        }

        return $funnel;
    }

    private function calculateStepConversion($step, $count)
    {
        $totalSessions = UserSession::where('created_at', '>', now()->subDays(30))->count();
        return $totalSessions > 0 ? ($count / $totalSessions) * 100 : 0;
    }

    private function getGeographicDistribution()
    {
        return AnalyticEvent::select('ip_address')
            ->selectRaw('COUNT(*) as visits')
            ->where('created_at', '>', now()->subDays(30))
            ->groupBy('ip_address')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();
    }
}
