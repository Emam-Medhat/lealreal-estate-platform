<?php

namespace App\Http\Controllers;

use App\Models\Funnel;
use App\Models\Conversion;
use App\Models\AnalyticEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FunnelAnalysisController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        $funnels = Funnel::with(['steps'])->latest()->paginate(20);
        
        return view('analytics.funnel.index', compact('funnels'));
    }

    public function createFunnel(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'steps' => 'required|array',
            'steps.*.name' => 'required|string',
            'steps.*.event_name' => 'required|string',
            'steps.*.order' => 'required|integer'
        ]);

        $funnel = Funnel::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_at' => now()
        ]);

        foreach ($request->steps as $stepData) {
            $funnel->steps()->create($stepData);
        }

        return response()->json([
            'status' => 'success',
            'funnel' => $funnel->load('steps')
        ]);
    }

    public function analyzeFunnel(Request $request)
    {
        $funnelId = $request->funnel_id;
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $funnel = Funnel::with('steps')->findOrFail($funnelId);
        $analysis = $this->performFunnelAnalysis($funnel, $startDate);

        return response()->json($analysis);
    }

    public function conversionFunnel(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $steps = ['page_view', 'property_detail', 'contact_form', 'appointment', 'conversion'];
        $funnelData = $this->buildConversionFunnel($steps, $startDate);

        return response()->json($funnelData);
    }

    public function userJourneyFunnel(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $journeyData = $this->analyzeUserJourney($startDate);

        return response()->json($journeyData);
    }

    public function funnelComparison(Request $request)
    {
        $period1 = $request->period1 ?? '30d';
        $period2 = $request->period2 ?? '30d';
        $funnelId = $request->funnel_id;

        $funnel = Funnel::with('steps')->findOrFail($funnelId);
        
        $analysis1 = $this->performFunnelAnalysis($funnel, $this->getStartDate($period1));
        $analysis2 = $this->performFunnelAnalysis($funnel, $this->getStartDate($period2));

        $comparison = $this->compareFunnels($analysis1, $analysis2);

        return response()->json($comparison);
    }

    public function funnelOptimization(Request $request)
    {
        $funnelId = $request->funnel_id;
        $period = $request->period ?? '30d';

        $funnel = Funnel::with('steps')->findOrFail($funnelId);
        $optimization = $this->generateOptimizationSuggestions($funnel, $period);

        return response()->json($optimization);
    }

    public function realTimeFunnel(Request $request)
    {
        $funnelId = $request->funnel_id;
        $timeWindow = $request->time_window ?? 60; // minutes

        $funnel = Funnel::with('steps')->findOrFail($funnelId);
        $realTimeData = $this->getRealTimeFunnelData($funnel, $timeWindow);

        return response()->json($realTimeData);
    }

    public function exportFunnelReport(Request $request)
    {
        $format = $request->format ?? 'json';
        $funnelId = $request->funnel_id;
        $period = $request->period ?? '30d';

        $funnel = Funnel::with('steps')->findOrFail($funnelId);
        $report = $this->generateFunnelReport($funnel, $period);

        if ($format === 'csv') {
            return $this->exportFunnelToCsv($report);
        }

        return response()->json($report);
    }

    private function performFunnelAnalysis($funnel, $startDate)
    {
        $steps = $funnel->steps->sortBy('order');
        $analysis = [];

        $previousStepUsers = null;
        $totalUsers = 0;

        foreach ($steps as $step) {
            $stepUsers = $this->getStepUsers($step->event_name, $startDate);
            $totalUsers = max($totalUsers, $stepUsers);

            $conversionRate = $previousStepUsers !== null && $previousStepUsers > 0 
                ? ($stepUsers / $previousStepUsers) * 100 
                : 100;

            $overallConversionRate = $totalUsers > 0 ? ($stepUsers / $totalUsers) * 100 : 0;

            $analysis[] = [
                'step_name' => $step->name,
                'event_name' => $step->event_name,
                'users' => $stepUsers,
                'conversion_rate' => $conversionRate,
                'overall_conversion_rate' => $overallConversionRate,
                'drop_off_rate' => 100 - $conversionRate,
                'drop_off_users' => $previousStepUsers ? $previousStepUsers - $stepUsers : 0
            ];

            $previousStepUsers = $stepUsers;
        }

        return [
            'funnel_name' => $funnel->name,
            'period' => $startDate->toDateString() . ' to ' . now()->toDateString(),
            'steps' => $analysis,
            'total_conversion_rate' => end($analysis)['overall_conversion_rate'] ?? 0,
            'biggest_drop_off' => $this->findBiggestDropOff($analysis),
            'recommendations' => $this->generateStepRecommendations($analysis)
        ];
    }

    private function buildConversionFunnel($steps, $startDate)
    {
        $funnelData = [];
        $previousCount = null;

        foreach ($steps as $index => $step) {
            $count = $this->getStepUsers($step, $startDate);
            
            $conversionRate = $previousCount !== null && $previousCount > 0 
                ? ($count / $previousCount) * 100 
                : 100;

            $funnelData[] = [
                'step' => $step,
                'count' => $count,
                'conversion_rate' => $conversionRate,
                'drop_off_rate' => 100 - $conversionRate
            ];

            $previousCount = $count;
        }

        return $funnelData;
    }

    private function analyzeUserJourney($startDate)
    {
        $journeys = AnalyticEvent::where('created_at', '>', $startDate)
            ->selectRaw('user_session_id, GROUP_CONCAT(event_name ORDER BY created_at) as journey')
            ->groupBy('user_session_id')
            ->get();

        $commonPaths = [];
        $pathCounts = [];

        foreach ($journeys as $journey) {
            $path = $journey->journey;
            if (!isset($pathCounts[$path])) {
                $pathCounts[$path] = 0;
            }
            $pathCounts[$path]++;
        }

        arsort($pathCounts);
        $topPaths = array_slice($pathCounts, 0, 10, true);

        foreach ($topPaths as $path => $count) {
            $commonPaths[] = [
                'path' => $path,
                'count' => $count,
                'percentage' => ($count / $journeys->count()) * 100
            ];
        }

        return $commonPaths;
    }

    private function compareFunnels($analysis1, $analysis2)
    {
        $comparison = [];

        foreach ($analysis1['steps'] as $index => $step1) {
            $step2 = $analysis2['steps'][$index] ?? null;
            
            if ($step2) {
                $conversionChange = $step2['conversion_rate'] - $step1['conversion_rate'];
                $usersChange = $step2['users'] - $step1['users'];

                $comparison[] = [
                    'step_name' => $step1['step_name'],
                    'period1_conversion' => $step1['conversion_rate'],
                    'period2_conversion' => $step2['conversion_rate'],
                    'conversion_change' => $conversionChange,
                    'period1_users' => $step1['users'],
                    'period2_users' => $step2['users'],
                    'users_change' => $usersChange
                ];
            }
        }

        return [
            'steps_comparison' => $comparison,
            'total_conversion_change' => $analysis2['total_conversion_rate'] - $analysis1['total_conversion_rate'],
            'improvement_areas' => $this->identifyImprovementAreas($comparison)
        ];
    }

    private function generateOptimizationSuggestions($funnel, $period)
    {
        $analysis = $this->performFunnelAnalysis($funnel, $this->getStartDate($period));
        
        $suggestions = [];
        
        foreach ($analysis['steps'] as $step) {
            if ($step['drop_off_rate'] > 50) {
                $suggestions[] = [
                    'step' => $step['step_name'],
                    'issue' => 'High drop-off rate',
                    'suggestion' => 'Consider improving user experience or reducing friction',
                    'priority' => 'high'
                ];
            } elseif ($step['drop_off_rate'] > 30) {
                $suggestions[] = [
                    'step' => $step['step_name'],
                    'issue' => 'Moderate drop-off rate',
                    'suggestion' => 'Review step performance and user feedback',
                    'priority' => 'medium'
                ];
            }
        }

        return $suggestions;
    }

    private function getRealTimeFunnelData($funnel, $timeWindow)
    {
        $startTime = now()->subMinutes($timeWindow);
        $steps = $funnel->steps->sortBy('order');
        $realTimeData = [];

        foreach ($steps as $step) {
            $count = AnalyticEvent::where('event_name', $step->event_name)
                ->where('created_at', '>', $startTime)
                ->distinct('user_session_id')
                ->count();

            $realTimeData[] = [
                'step_name' => $step->name,
                'real_time_count' => $count,
                'rate_per_minute' => $count / $timeWindow
            ];
        }

        return $realTimeData;
    }

    private function generateFunnelReport($funnel, $period)
    {
        $analysis = $this->performFunnelAnalysis($funnel, $this->getStartDate($period));
        
        return [
            'funnel_info' => [
                'name' => $funnel->name,
                'description' => $funnel->description,
                'created_at' => $funnel->created_at
            ],
            'analysis' => $analysis,
            'export_date' => now()->toDateString()
        ];
    }

    private function getStepUsers($eventName, $startDate)
    {
        return AnalyticEvent::where('event_name', $eventName)
            ->where('created_at', '>', $startDate)
            ->distinct('user_session_id')
            ->count();
    }

    private function findBiggestDropOff($steps)
    {
        $biggestDrop = null;
        $maxDropRate = 0;

        foreach ($steps as $step) {
            if ($step['drop_off_rate'] > $maxDropRate) {
                $maxDropRate = $step['drop_off_rate'];
                $biggestDrop = $step['step_name'];
            }
        }

        return $biggestDrop;
    }

    private function generateStepRecommendations($steps)
    {
        $recommendations = [];

        foreach ($steps as $step) {
            if ($step['drop_off_rate'] > 40) {
                $recommendations[] = "Focus on improving {$step['step_name']} - high drop-off rate of {$step['drop_off_rate']}%";
            }
        }

        return $recommendations;
    }

    private function identifyImprovementAreas($comparison)
    {
        $areas = [];

        foreach ($comparison as $step) {
            if ($step['conversion_change'] < -10) {
                $areas[] = [
                    'step' => $step['step_name'],
                    'issue' => 'Conversion rate decreased',
                    'change' => $step['conversion_change']
                ];
            }
        }

        return $areas;
    }

    private function exportFunnelToCsv($report)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="funnel_report.csv"'
        ];

        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Step', 'Users', 'Conversion Rate', 'Drop-off Rate']);
            
            foreach ($report['analysis']['steps'] as $step) {
                fputcsv($file, [
                    $step['step_name'],
                    $step['users'],
                    $step['conversion_rate'],
                    $step['drop_off_rate']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
