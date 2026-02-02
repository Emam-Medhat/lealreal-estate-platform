<?php

namespace App\Http\Controllers;

use App\Models\Heatmap;
use App\Models\AnalyticEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HeatmapController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        try {
            // Calculate heatmap statistics
            $totalClicks = $this->getTotalClicks();
            $activePages = $this->getActivePages();
            $hotspots = $this->getTotalHotspots();
            $avgInteraction = $this->getAvgInteractionPerPage();

            return view('analytics.heatmaps.index', compact(
                'totalClicks',
                'activePages',
                'hotspots',
                'avgInteraction'
            ));
        } catch (\Exception $e) {
            // Return view with default values if there's an error
            return view('analytics.heatmaps.index', [
                'totalClicks' => 0,
                'activePages' => 0,
                'hotspots' => 0,
                'avgInteraction' => 0,
                'error' => 'Failed to fetch heatmap data: ' . $e->getMessage()
            ]);
        }
    }

    public function generateHeatmap(Request $request)
    {
        $request->validate([
            'page_url' => 'required|string',
            'heatmap_type' => 'required|string|in:click,movement,scroll,attention',
            'time_range' => 'required|string|in:1d,7d,30d,90d'
        ]);

        $heatmapData = $this->generateHeatmapData($request->all());

        Heatmap::create([
            'page_url' => $request->page_url,
            'heatmap_type' => $request->heatmap_type,
            'time_range' => $request->time_range,
            'data' => $heatmapData,
            'created_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'heatmap_data' => $heatmapData
        ]);
    }

    public function getHeatmap(Request $request)
    {
        $pageUrl = $request->page_url;
        $type = $request->type ?? 'click';
        $timeRange = $request->time_range ?? '30d';

        $heatmap = Heatmap::where('page_url', $pageUrl)
            ->where('heatmap_type', $type)
            ->where('time_range', $timeRange)
            ->latest()
            ->first();

        if (!$heatmap) {
            $data = $this->generateRealtimeHeatmap($pageUrl, $type, $timeRange);
        } else {
            $data = $heatmap->data;
        }

        return response()->json($data);
    }

    public function clickHeatmap(Request $request)
    {
        $pageUrl = $request->page_url;
        $timeRange = $request->time_range ?? '30d';

        $clickData = AnalyticEvent::where('event_name', 'click')
            ->where('page_url', $pageUrl)
            ->where('created_at', '>', $this->getStartDate($timeRange))
            ->get(['properties']);

        $heatmapData = $this->processClickData($clickData);

        return response()->json($heatmapData);
    }

    public function movementHeatmap(Request $request)
    {
        $pageUrl = $request->page_url;
        $timeRange = $request->time_range ?? '30d';

        $movementData = AnalyticEvent::where('event_name', 'mouse_move')
            ->where('page_url', $pageUrl)
            ->where('created_at', '>', $this->getStartDate($timeRange))
            ->get(['properties']);

        $heatmapData = $this->processMovementData($movementData);

        return response()->json($heatmapData);
    }

    public function scrollHeatmap(Request $request)
    {
        $pageUrl = $request->page_url;
        $timeRange = $request->time_range ?? '30d';

        $scrollData = AnalyticEvent::where('event_name', 'scroll')
            ->where('page_url', $pageUrl)
            ->where('created_at', '>', $this->getStartDate($timeRange))
            ->get(['properties']);

        $heatmapData = $this->processScrollData($scrollData);

        return response()->json($heatmapData);
    }

    public function attentionHeatmap(Request $request)
    {
        $pageUrl = $request->page_url;
        $timeRange = $request->time_range ?? '30d';

        $attentionData = AnalyticEvent::where('page_url', $pageUrl)
            ->where('created_at', '>', $this->getStartDate($timeRange))
            ->whereIn('event_name', ['click', 'mouse_move', 'scroll'])
            ->get(['properties']);

        $heatmapData = $this->processAttentionData($attentionData);

        return response()->json($heatmapData);
    }

    public function compareHeatmaps(Request $request)
    {
        $pageUrl = $request->page_url;
        $period1 = $request->period1 ?? '30d';
        $period2 = $request->period2 ?? '30d';
        $type = $request->type ?? 'click';

        $heatmap1 = $this->getHistoricalHeatmap($pageUrl, $type, $period1, $request->date1);
        $heatmap2 = $this->getHistoricalHeatmap($pageUrl, $type, $period2, $request->date2);

        $comparison = $this->compareHeatmapData($heatmap1, $heatmap2);

        return response()->json($comparison);
    }

    public function heatmapAnalytics(Request $request)
    {
        $pageUrl = $request->page_url;
        $timeRange = $request->time_range ?? '30d';

        $analytics = [
            'total_interactions' => $this->getTotalInteractions($pageUrl, $timeRange),
            'hotspots' => $this->identifyHotspots($pageUrl, $timeRange),
            'cold_zones' => $this->identifyColdZones($pageUrl, $timeRange),
            'interaction_density' => $this->calculateInteractionDensity($pageUrl, $timeRange),
            'user_paths' => $this->analyzeUserPaths($pageUrl, $timeRange)
        ];

        return response()->json($analytics);
    }

    public function exportHeatmap(Request $request)
    {
        $format = $request->format ?? 'json';
        $pageUrl = $request->page_url;
        $type = $request->type ?? 'click';

        $heatmapData = $this->getHeatmapData($pageUrl, $type);

        if ($format === 'csv') {
            return $this->exportHeatmapToCsv($heatmapData);
        }

        return response()->json($heatmapData);
    }

    private function generateHeatmapData($params)
    {
        $pageUrl = $params['page_url'];
        $type = $params['heatmap_type'];
        $timeRange = $params['time_range'];

        return match($type) {
            'click' => $this->generateClickHeatmap($pageUrl, $timeRange),
            'movement' => $this->generateMovementHeatmap($pageUrl, $timeRange),
            'scroll' => $this->generateScrollHeatmap($pageUrl, $timeRange),
            'attention' => $this->generateAttentionHeatmap($pageUrl, $timeRange),
            default => []
        };
    }

    private function generateClickHeatmap($pageUrl, $timeRange)
    {
        $clicks = AnalyticEvent::where('event_name', 'click')
            ->where('page_url', $pageUrl)
            ->where('created_at', '>', $this->getStartDate($timeRange))
            ->get(['properties']);

        return $this->processClickData($clicks);
    }

    private function generateMovementHeatmap($pageUrl, $timeRange)
    {
        $movements = AnalyticEvent::where('event_name', 'mouse_move')
            ->where('page_url', $pageUrl)
            ->where('created_at', '>', $this->getStartDate($timeRange))
            ->get(['properties']);

        return $this->processMovementData($movements);
    }

    private function generateScrollHeatmap($pageUrl, $timeRange)
    {
        $scrolls = AnalyticEvent::where('event_name', 'scroll')
            ->where('page_url', $pageUrl)
            ->where('created_at', '>', $this->getStartDate($timeRange))
            ->get(['properties']);

        return $this->processScrollData($scrolls);
    }

    private function generateAttentionHeatmap($pageUrl, $timeRange)
    {
        $interactions = AnalyticEvent::where('page_url', $pageUrl)
            ->where('created_at', '>', $this->getStartDate($timeRange))
            ->whereIn('event_name', ['click', 'mouse_move', 'scroll'])
            ->get(['properties']);

        return $this->processAttentionData($interactions);
    }

    private function processClickData($clickData)
    {
        $heatmap = [];
        $gridSize = 50; // 50x50 grid

        foreach ($clickData as $click) {
            $props = json_decode($click->properties, true);
            if (isset($props['x']) && isset($props['y'])) {
                $gridX = floor($props['x'] / $gridSize);
                $gridY = floor($props['y'] / $gridSize);
                
                $key = "{$gridX}_{$gridY}";
                if (!isset($heatmap[$key])) {
                    $heatmap[$key] = 0;
                }
                $heatmap[$key]++;
            }
        }

        return $this->normalizeHeatmapData($heatmap);
    }

    private function processMovementData($movementData)
    {
        $heatmap = [];
        $gridSize = 50;

        foreach ($movementData as $movement) {
            $props = json_decode($movement->properties, true);
            if (isset($props['x']) && isset($props['y'])) {
                $gridX = floor($props['x'] / $gridSize);
                $gridY = floor($props['y'] / $gridSize);
                
                $key = "{$gridX}_{$gridY}";
                if (!isset($heatmap[$key])) {
                    $heatmap[$key] = 0;
                }
                $heatmap[$key] += 0.1; // Weight movements less than clicks
            }
        }

        return $this->normalizeHeatmapData($heatmap);
    }

    private function processScrollData($scrollData)
    {
        $scrollDepth = [];
        $totalScrolls = 0;

        foreach ($scrollData as $scroll) {
            $props = json_decode($scroll->properties, true);
            if (isset($props['scroll_depth'])) {
                $depth = floor($props['scroll_depth'] / 10) * 10; // Group by 10%
                if (!isset($scrollDepth[$depth])) {
                    $scrollDepth[$depth] = 0;
                }
                $scrollDepth[$depth]++;
                $totalScrolls++;
            }
        }

        // Convert to heatmap format
        $heatmap = [];
        foreach ($scrollDepth as $depth => $count) {
            $percentage = $totalScrolls > 0 ? ($count / $totalScrolls) * 100 : 0;
            $heatmap["scroll_{$depth}"] = $percentage;
        }

        return $heatmap;
    }

    private function processAttentionData($attentionData)
    {
        $heatmap = [];
        $gridSize = 50;

        foreach ($attentionData as $attention) {
            $props = json_decode($attention->properties, true);
            if (isset($props['x']) && isset($props['y'])) {
                $gridX = floor($props['x'] / $gridSize);
                $gridY = floor($props['y'] / $gridSize);
                
                $key = "{$gridX}_{$gridY}";
                $weight = match($attention->event_name) {
                    'click' => 3,
                    'mouse_move' => 1,
                    'scroll' => 2,
                    default => 1
                };
                
                if (!isset($heatmap[$key])) {
                    $heatmap[$key] = 0;
                }
                $heatmap[$key] += $weight;
            }
        }

        return $this->normalizeHeatmapData($heatmap);
    }

    private function normalizeHeatmapData($heatmap)
    {
        if (empty($heatmap)) return [];

        $maxValue = max($heatmap);
        $normalized = [];

        foreach ($heatmap as $key => $value) {
            $normalized[$key] = ($value / $maxValue) * 100;
        }

        return $normalized;
    }

    private function generateRealtimeHeatmap($pageUrl, $type, $timeRange)
    {
        return $this->generateHeatmapData([
            'page_url' => $pageUrl,
            'heatmap_type' => $type,
            'time_range' => $timeRange
        ]);
    }

    private function getHistoricalHeatmap($pageUrl, $type, $timeRange, $date)
    {
        $startDate = $date ? Carbon::parse($date) : $this->getStartDate($timeRange);
        
        $events = AnalyticEvent::where('page_url', $pageUrl)
            ->where('created_at', '>', $startDate)
            ->where('created_at', '<', $startDate->copy()->addDays($this->getDaysFromRange($timeRange)))
            ->get(['properties']);

        return match($type) {
            'click' => $this->processClickData($events),
            'movement' => $this->processMovementData($events),
            'scroll' => $this->processScrollData($events),
            'attention' => $this->processAttentionData($events),
            default => []
        };
    }

    private function compareHeatmapData($heatmap1, $heatmap2)
    {
        $comparison = [];
        $allKeys = array_unique(array_merge(array_keys($heatmap1), array_keys($heatmap2)));

        foreach ($allKeys as $key) {
            $value1 = $heatmap1[$key] ?? 0;
            $value2 = $heatmap2[$key] ?? 0;
            
            $change = $value2 - $value1;
            $percentChange = $value1 > 0 ? ($change / $value1) * 100 : 0;
            
            $comparison[$key] = [
                'period1' => $value1,
                'period2' => $value2,
                'change' => $change,
                'percent_change' => $percentChange
            ];
        }

        return $comparison;
    }

    private function getTotalInteractions($pageUrl, $timeRange)
    {
        return AnalyticEvent::where('page_url', $pageUrl)
            ->where('created_at', '>', $this->getStartDate($timeRange))
            ->whereIn('event_name', ['click', 'mouse_move', 'scroll'])
            ->count();
    }

    private function identifyHotspots($pageUrl, $timeRange)
    {
        $heatmap = $this->generateClickHeatmap($pageUrl, $timeRange);
        arsort($heatmap);
        
        return array_slice($heatmap, 0, 10, true);
    }

    private function identifyColdZones($pageUrl, $timeRange)
    {
        $heatmap = $this->generateClickHeatmap($pageUrl, $timeRange);
        asort($heatmap);
        
        return array_slice($heatmap, 0, 10, true);
    }

    private function calculateInteractionDensity($pageUrl, $timeRange)
    {
        $interactions = $this->getTotalInteractions($pageUrl, $timeRange);
        $pageViews = AnalyticEvent::where('event_name', 'page_view')
            ->where('page_url', $pageUrl)
            ->where('created_at', '>', $this->getStartDate($timeRange))
            ->count();

        return $pageViews > 0 ? $interactions / $pageViews : 0;
    }

    private function analyzeUserPaths($pageUrl, $timeRange)
    {
        // Simplified path analysis
        return [
            'common_paths' => ['header -> content -> footer', 'sidebar -> content -> cta'],
            'entry_points' => ['header', 'sidebar', 'direct_link'],
            'exit_points' => ['footer', 'close_button', 'external_link']
        ];
    }

    private function exportHeatmapToCsv($heatmapData)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="heatmap.csv"'
        ];

        $callback = function() use ($heatmapData) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Position', 'Intensity']);
            
            foreach ($heatmapData as $position => $intensity) {
                fputcsv($file, [$position, $intensity]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStartDate($timeRange)
    {
        return match($timeRange) {
            '1d' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            default => now()->subDays(30)
        };
    }

    private function getDaysFromRange($timeRange)
    {
        return match($timeRange) {
            '1d' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 30
        };
    }

    private function getTotalClicks()
    {
        return AnalyticEvent::where('event_name', 'click')
            ->where('created_at', '>', now()->subDays(30))
            ->count();
    }

    private function getActivePages()
    {
        return AnalyticEvent::where('created_at', '>', now()->subDays(30))
            ->distinct('page_url')
            ->count();
    }

    private function getTotalHotspots()
    {
        // Simplified hotspot calculation
        return AnalyticEvent::where('event_name', 'click')
            ->where('created_at', '>', now()->subDays(30))
            ->selectRaw('page_url, COUNT(*) as click_count')
            ->having('click_count', '>', 10)
            ->count();
    }

    private function getAvgInteractionPerPage()
    {
        $totalInteractions = AnalyticEvent::whereIn('event_name', ['click', 'mouse_move', 'scroll'])
            ->where('created_at', '>', now()->subDays(30))
            ->count();
        
        $activePages = $this->getActivePages();
        
        return $activePages > 0 ? $totalInteractions / $activePages : 0;
    }
}
