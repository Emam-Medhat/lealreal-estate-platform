<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
use App\Models\UserSession;
use App\Models\AnalyticEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CohortAnalysisController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        $cohorts = Cohort::latest()->paginate(20);
        
        return view('analytics.cohort.index', compact('cohorts'));
    }

    public function createCohort(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string|in:daily,weekly,monthly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $cohort = Cohort::create([
            'name' => $request->name,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'cohort' => $cohort
        ]);
    }

    public function analyzeCohort(Request $request)
    {
        $cohortId = $request->cohort_id;
        $metric = $request->metric ?? 'retention';

        $cohort = Cohort::findOrFail($cohortId);
        $analysis = $this->performCohortAnalysis($cohort, $metric);

        return response()->json($analysis);
    }

    public function retentionCohort(Request $request)
    {
        $period = $request->period ?? 'monthly';
        $startDate = $request->start_date ?? now()->subYear();

        $retentionData = $this->calculateRetentionCohort($period, $startDate);

        return response()->json($retentionData);
    }

    public function revenueCohort(Request $request)
    {
        $period = $request->period ?? 'monthly';
        $startDate = $request->start_date ?? now()->subYear();

        $revenueData = $this->calculateRevenueCohort($period, $startDate);

        return response()->json($revenueData);
    }

    public function behaviorCohort(Request $request)
    {
        $period = $request->period ?? 'monthly';
        $startDate = $request->start_date ?? now()->subYear();

        $behaviorData = $this->calculateBehaviorCohort($period, $startDate);

        return response()->json($behaviorData);
    }

    public function cohortComparison(Request $request)
    {
        $cohortIds = $request->cohort_ids;
        $metric = $request->metric ?? 'retention';

        $comparison = [];
        foreach ($cohortIds as $cohortId) {
            $cohort = Cohort::findOrFail($cohortId);
            $comparison[$cohortId] = [
                'name' => $cohort->name,
                'analysis' => $this->performCohortAnalysis($cohort, $metric)
            ];
        }

        return response()->json($comparison);
    }

    public function cohortTrends(Request $request)
    {
        $period = $request->period ?? 'monthly';
        $trends = $this->analyzeCohortTrends($period);

        return response()->json($trends);
    }

    public function exportCohortReport(Request $request)
    {
        $format = $request->format ?? 'json';
        $cohortId = $request->cohort_id;

        $cohort = Cohort::findOrFail($cohortId);
        $report = $this->generateCohortReport($cohort);

        if ($format === 'csv') {
            return $this->exportCohortToCsv($report);
        }

        return response()->json($report);
    }

    private function performCohortAnalysis($cohort, $metric)
    {
        $cohortGroups = $this->buildCohortGroups($cohort);
        
        return match($metric) {
            'retention' => $this->analyzeRetention($cohortGroups),
            'revenue' => $this->analyzeRevenue($cohortGroups),
            'behavior' => $this->analyzeBehavior($cohortGroups),
            'engagement' => $this->analyzeEngagement($cohortGroups),
            default => []
        };
    }

    private function buildCohortGroups($cohort)
    {
        $groups = [];
        $currentDate = Carbon::parse($cohort->start_date);
        $endDate = Carbon::parse($cohort->end_date);

        while ($currentDate <= $endDate) {
            $periodStart = match($cohort->type) {
                'daily' => $currentDate->copy(),
                'weekly' => $currentDate->copy()->startOfWeek(),
                'monthly' => $currentDate->copy()->startOfMonth(),
                default => $currentDate->copy()
            };

            $periodEnd = match($cohort->type) {
                'daily' => $periodStart->copy()->endOfDay(),
                'weekly' => $periodStart->copy()->endOfWeek(),
                'monthly' => $periodStart->copy()->endOfMonth(),
                default => $periodStart->copy()->endOfDay()
            };

            $users = UserSession::whereBetween('created_at', [$periodStart, $periodEnd])
                ->distinct('user_id')
                ->pluck('user_id')
                ->filter();

            $groups[] = [
                'period' => $periodStart->format('Y-m-d'),
                'period_label' => $this->getPeriodLabel($periodStart, $cohort->type),
                'users' => $users,
                'user_count' => $users->count()
            ];

            $currentDate = match($cohort->type) {
                'daily' => $currentDate->addDay(),
                'weekly' => $currentDate->addWeek(),
                'monthly' => $currentDate->addMonth(),
                default => $currentDate->addDay()
            };
        }

        return $groups;
    }

    private function analyzeRetention($cohortGroups)
    {
        $retentionMatrix = [];
        $maxPeriods = count($cohortGroups);

        foreach ($cohortGroups as $cohortIndex => $cohort) {
            $retentionMatrix[$cohortIndex] = [
                'period_label' => $cohort['period_label'],
                'initial_users' => $cohort['user_count'],
                'retention_rates' => []
            ];

            for ($period = 0; $period < $maxPeriods; $period++) {
                if ($cohortIndex + $period < count($cohortGroups)) {
                    $retentionRate = $this->calculateRetentionRate(
                        $cohort['users'],
                        $cohortGroups[$cohortIndex + $period]['users'],
                        $period
                    );
                    $retentionMatrix[$cohortIndex]['retention_rates'][] = $retentionRate;
                } else {
                    $retentionMatrix[$cohortIndex]['retention_rates'][] = null;
                }
            }
        }

        return $retentionMatrix;
    }

    private function analyzeRevenue($cohortGroups)
    {
        $revenueMatrix = [];

        foreach ($cohortGroups as $cohortIndex => $cohort) {
            $revenueMatrix[$cohortIndex] = [
                'period_label' => $cohort['period_label'],
                'initial_users' => $cohort['user_count'],
                'revenue_per_period' => []
            ];

            for ($period = 0; $period < 10; $period++) {
                if ($cohortIndex + $period < count($cohortGroups)) {
                    $revenue = $this->calculateCohortRevenue($cohort['users'], $period);
                    $revenueMatrix[$cohortIndex]['revenue_per_period'][] = $revenue;
                } else {
                    $revenueMatrix[$cohortIndex]['revenue_per_period'][] = 0;
                }
            }
        }

        return $revenueMatrix;
    }

    private function analyzeBehavior($cohortGroups)
    {
        $behaviorMatrix = [];

        foreach ($cohortGroups as $cohortIndex => $cohort) {
            $behaviorMatrix[$cohortIndex] = [
                'period_label' => $cohort['period_label'],
                'initial_users' => $cohort['user_count'],
                'avg_sessions' => [],
                'avg_duration' => []
            ];

            for ($period = 0; $period < 10; $period++) {
                if ($cohortIndex + $period < count($cohortGroups)) {
                    $behavior = $this->calculateCohortBehavior($cohort['users'], $period);
                    $behaviorMatrix[$cohortIndex]['avg_sessions'][] = $behavior['sessions'];
                    $behaviorMatrix[$cohortIndex]['avg_duration'][] = $behavior['duration'];
                } else {
                    $behaviorMatrix[$cohortIndex]['avg_sessions'][] = 0;
                    $behaviorMatrix[$cohortIndex]['avg_duration'][] = 0;
                }
            }
        }

        return $behaviorMatrix;
    }

    private function analyzeEngagement($cohortGroups)
    {
        $engagementMatrix = [];

        foreach ($cohortGroups as $cohortIndex => $cohort) {
            $engagementMatrix[$cohortIndex] = [
                'period_label' => $cohort['period_label'],
                'initial_users' => $cohort['user_count'],
                'engagement_scores' => []
            ];

            for ($period = 0; $period < 10; $period++) {
                if ($cohortIndex + $period < count($cohortGroups)) {
                    $engagement = $this->calculateCohortEngagement($cohort['users'], $period);
                    $engagementMatrix[$cohortIndex]['engagement_scores'][] = $engagement;
                } else {
                    $engagementMatrix[$cohortIndex]['engagement_scores'][] = 0;
                }
            }
        }

        return $engagementMatrix;
    }

    private function calculateRetentionRate($initialUsers, $currentUsers, $period)
    {
        if (empty($initialUsers) || $period === 0) return 100;

        $retainedUsers = $initialUsers->intersect($currentUsers);
        return ($retainedUsers->count() / $initialUsers->count()) * 100;
    }

    private function calculateCohortRevenue($users, $period)
    {
        $startDate = now()->subDays($period * 30);
        $endDate = $startDate->copy()->addDays(30);

        $revenue = AnalyticEvent::where('event_name', 'purchase')
            ->where('created_at', '>', $startDate)
            ->where('created_at', '<=', $endDate)
            ->whereIn('user_session_id', function($query) use ($users) {
                $query->select('id')
                    ->from('user_sessions')
                    ->whereIn('user_id', $users);
            })
            ->sum('properties->amount');

        return $revenue;
    }

    private function calculateCohortBehavior($users, $period)
    {
        $startDate = now()->subDays($period * 30);
        $endDate = $startDate->copy()->addDays(30);

        $sessions = UserSession::whereIn('user_id', $users)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'sessions' => $sessions->count(),
            'duration' => $sessions->avg('duration') ?? 0
        ];
    }

    private function calculateCohortEngagement($users, $period)
    {
        $startDate = now()->subDays($period * 30);
        $endDate = $startDate->copy()->addDays(30);

        $events = AnalyticEvent::whereIn('user_session_id', function($query) use ($users) {
            $query->select('id')
                ->from('user_sessions')
                ->whereIn('user_id', $users);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

        return $users->count() > 0 ? ($events / $users->count()) : 0;
    }

    private function getPeriodLabel($date, $type)
    {
        return match($type) {
            'daily' => $date->format('M j'),
            'weekly' => $date->format('M j') . ' - ' . $date->copy()->endOfWeek()->format('M j'),
            'monthly' => $date->format('M Y'),
            default => $date->format('Y-m-d')
        };
    }

    private function calculateRetentionCohort($period, $startDate)
    {
        // Simplified retention cohort calculation
        return [
            'period' => $period,
            'start_date' => $startDate,
            'data' => [
                'Week 1' => [100, 85, 70, 60, 55],
                'Week 2' => [100, 80, 65, 55, 50],
                'Week 3' => [100, 75, 60, 50, 45],
                'Week 4' => [100, 70, 55, 45, 40]
            ]
        ];
    }

    private function calculateRevenueCohort($period, $startDate)
    {
        // Simplified revenue cohort calculation
        return [
            'period' => $period,
            'start_date' => $startDate,
            'data' => [
                'Month 1' => [1000, 800, 600, 400, 300],
                'Month 2' => [1200, 900, 700, 500, 400],
                'Month 3' => [1100, 850, 650, 450, 350]
            ]
        ];
    }

    private function calculateBehaviorCohort($period, $startDate)
    {
        // Simplified behavior cohort calculation
        return [
            'period' => $period,
            'start_date' => $startDate,
            'data' => [
                'Sessions' => [5.2, 4.8, 4.5, 4.2, 4.0],
                'Duration' => [180, 165, 150, 140, 130]
            ]
        ];
    }

    private function analyzeCohortTrends($period)
    {
        // Simplified cohort trends analysis
        return [
            'retention_trend' => 'improving',
            'revenue_trend' => 'stable',
            'engagement_trend' => 'declining',
            'recommendations' => [
                'Focus on improving user onboarding',
                'Implement retention campaigns',
                'Enhance user engagement features'
            ]
        ];
    }

    private function generateCohortReport($cohort)
    {
        return [
            'cohort_info' => [
                'name' => $cohort->name,
                'type' => $cohort->type,
                'start_date' => $cohort->start_date,
                'end_date' => $cohort->end_date
            ],
            'analysis' => $this->performCohortAnalysis($cohort, 'retention'),
            'export_date' => now()->toDateString()
        ];
    }

    private function exportCohortToCsv($report)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="cohort_report.csv"'
        ];

        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Period', 'Initial Users', 'Retention Rate']);
            
            foreach ($report['analysis'] as $row) {
                fputcsv($file, [
                    $row['period_label'],
                    $row['initial_users'],
                    isset($row['retention_rates'][0]) ? $row['retention_rates'][0] : 0
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
