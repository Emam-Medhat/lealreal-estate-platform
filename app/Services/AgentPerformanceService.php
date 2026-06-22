<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Appointment;
use App\Models\Lead;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AgentPerformanceService
{
    /**
     * Get performance metrics for agent
     */
    public function getMetrics(int $agentId, string $period = 'month'): array
    {
        $agent = Agent::findOrFail($agentId);

        // Get date range
        $dateRange = $this->getDateRange($period);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Consolidate property/sales metrics
        $salesMetrics = Property::where('agent_id', $agentId)
            ->whereBetween('sold_date', [$start, $end])
            ->where('status', 'sold')
            ->selectRaw('COUNT(*) as total_count, SUM(price) as total_revenue')
            ->first();

        // Consolidate lead metrics
        $leadMetrics = Lead::where('agent_id', $agentId)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("COUNT(*) as total_count, COUNT(CASE WHEN lead_status = 'converted' THEN 1 END) as converted_count")
            ->first();

        // Consolidate appointment metrics
        $appointmentMetrics = Appointment::where('agent_id', $agentId)
            ->whereBetween('start_datetime', [$start, $end])
            ->selectRaw("COUNT(*) as total_count, COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count")
            ->first();

        // Commission metrics
        $totalCommissions = DB::table('agent_commissions')
            ->where('agent_id', $agentId)
            ->whereBetween('paid_date', [$start, $end])
            ->where('status', 'paid')
            ->sum('amount');

        $totalSales = $salesMetrics->total_count ?? 0;
        $totalRevenue = $salesMetrics->total_revenue ?? 0;
        $totalLeads = $leadMetrics->total_count ?? 0;
        $convertedLeads = $leadMetrics->converted_count ?? 0;
        $totalAppointments = $appointmentMetrics->total_count ?? 0;
        $completedAppointments = $appointmentMetrics->completed_count ?? 0;

        $conversionRate = $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;
        $averageSalePrice = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        $metrics = [
            'agent_id' => $agentId,
            'period' => $period,
            'date_range' => $dateRange,
            'sales' => [
                'total_count' => $totalSales,
                'total_revenue' => $totalRevenue,
                'average_price' => $averageSalePrice,
                'conversion_rate' => $conversionRate,
            ],
            'leads' => [
                'total_count' => $totalLeads,
                'converted_count' => $convertedLeads,
                'conversion_rate' => $conversionRate,
            ],
            'appointments' => [
                'total_count' => $totalAppointments,
                'completed_count' => $completedAppointments,
                'completion_rate' => $totalAppointments > 0 ? ($completedAppointments / $totalAppointments) * 100 : 0,
            ],
            'commissions' => [
                'total_amount' => $totalCommissions,
                'average_amount' => $totalSales > 0 ? $totalCommissions / $totalSales : 0,
            ],
        ];

        $metrics['performance_score'] = $this->calculatePerformanceScore($metrics);
        $metrics['trend'] = $this->calculateTrend($agentId, $period);

        return $metrics;
    }

    /**
     * Get metrics for a specific range without trend/score recursion
     */
    private function getMetricsForRange(int $agentId, array $dateRange): array
    {
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $salesMetrics = Property::where('agent_id', $agentId)
            ->whereBetween('sold_date', [$start, $end])
            ->where('status', 'sold')
            ->selectRaw('COUNT(*) as total_count, SUM(price) as total_revenue')
            ->first();

        $leadMetrics = Lead::where('agent_id', $agentId)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("COUNT(*) as total_count, COUNT(CASE WHEN lead_status = 'converted' THEN 1 END) as converted_count")
            ->first();

        $appointmentMetrics = Appointment::where('agent_id', $agentId)
            ->whereBetween('start_datetime', [$start, $end])
            ->selectRaw("COUNT(*) as total_count, COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count")
            ->first();

        $totalCommissions = DB::table('agent_commissions')
            ->where('agent_id', $agentId)
            ->whereBetween('paid_date', [$start, $end])
            ->where('status', 'paid')
            ->sum('amount');

        $totalSales = $salesMetrics->total_count ?? 0;
        $totalRevenue = $salesMetrics->total_revenue ?? 0;
        $totalLeads = $leadMetrics->total_count ?? 0;
        $convertedLeads = $leadMetrics->converted_count ?? 0;
        $totalAppointments = $appointmentMetrics->total_count ?? 0;

        $metrics = [
            'sales' => ['total_count' => $totalSales, 'total_revenue' => $totalRevenue, 'average_price' => $totalSales > 0 ? $totalRevenue / $totalSales : 0, 'conversion_rate' => $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0],
            'leads' => ['total_count' => $totalLeads, 'converted_count' => $convertedLeads, 'conversion_rate' => $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0],
            'appointments' => ['total_count' => $totalAppointments, 'completed_count' => $appointmentMetrics->completed_count ?? 0, 'completion_rate' => $totalAppointments > 0 ? (($appointmentMetrics->completed_count ?? 0) / $totalAppointments) * 100 : 0],
            'commissions' => ['total_amount' => $totalCommissions, 'average_amount' => $totalSales > 0 ? $totalCommissions / $totalSales : 0],
        ];

        return $metrics;
    }

    /**
     * Get ranking for agent
     */
    public function getRanking(int $agentId, string $period = 'month'): array
    {
        $agent = Agent::findOrFail($agentId);
        $dateRange = $this->getDateRange($period);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $companyAgents = $agent->company ? $agent->company->agents()->get(['id', 'name']) : collect([$agent]);
        $agentIds = $companyAgents->pluck('id')->toArray();

        // Batch fetch metrics for all agents in the company to avoid N+1
        $salesMetrics = Property::whereIn('agent_id', $agentIds)
            ->whereBetween('sold_date', [$start, $end])
            ->where('status', 'sold')
            ->selectRaw('agent_id, COUNT(*) as total_count, SUM(price) as total_revenue')
            ->groupBy('agent_id')
            ->get()
            ->keyBy('agent_id');

        $leadMetrics = Lead::whereIn('agent_id', $agentIds)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("agent_id, COUNT(*) as total_count, COUNT(CASE WHEN lead_status = 'converted' THEN 1 END) as converted_count")
            ->groupBy('agent_id')
            ->get()
            ->keyBy('agent_id');

        $appointmentMetrics = Appointment::whereIn('agent_id', $agentIds)
            ->whereBetween('start_datetime', [$start, $end])
            ->selectRaw("agent_id, COUNT(*) as total_count, COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count")
            ->groupBy('agent_id')
            ->get()
            ->keyBy('agent_id');

        $commissionMetrics = DB::table('agent_commissions')
            ->whereIn('agent_id', $agentIds)
            ->whereBetween('paid_date', [$start, $end])
            ->where('status', 'paid')
            ->selectRaw('agent_id, SUM(amount) as total_amount')
            ->groupBy('agent_id')
            ->get()
            ->keyBy('agent_id');

        $agentScores = [];
        foreach ($companyAgents as $companyAgent) {
            $companyAgentId = $companyAgent->id;
            $s = $salesMetrics->get($companyAgentId);
            $l = $leadMetrics->get($companyAgentId);
            $a = $appointmentMetrics->get($companyAgentId);
            $c = $commissionMetrics->get($companyAgentId);

            $totalSales = $s->total_count ?? 0;
            $totalRevenue = $s->total_revenue ?? 0;
            $totalLeads = $l->total_count ?? 0;
            $convertedLeads = $l->converted_count ?? 0;
            $totalApps = $a->total_count ?? 0;
            $completedApps = $a->completed_count ?? 0;
            $totalComm = $c->total_amount ?? 0;

            $convRate = $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;
            $avgPrice = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

            $tempMetrics = [
                'sales' => ['total_count' => $totalSales, 'total_revenue' => $totalRevenue, 'average_price' => $avgPrice, 'conversion_rate' => $convRate],
                'leads' => ['total_count' => $totalLeads, 'converted_count' => $convertedLeads, 'conversion_rate' => $convRate],
                'appointments' => ['total_count' => $totalApps, 'completed_count' => $completedApps, 'completion_rate' => $totalApps > 0 ? ($completedApps / $totalApps) * 100 : 0],
                'commissions' => ['total_amount' => $totalComm, 'average_amount' => $totalSales > 0 ? $totalComm / $totalSales : 0],
            ];

            $agentScores[] = [
                'agent_id' => $companyAgentId,
                'agent_name' => $companyAgent->name,
                'performance_score' => $this->calculatePerformanceScore($tempMetrics),
                'total_sales' => $totalSales,
                'total_revenue' => $totalRevenue,
                'conversion_rate' => $convRate,
            ];
        }

        usort($agentScores, function ($a, $b) {
            return $b['performance_score'] - $a['performance_score'];
        });

        $currentRank = 0;
        foreach ($agentScores as $index => $agentScore) {
            if ($agentScore['agent_id'] === $agentId) {
                $currentRank = $index + 1;
                break;
            }
        }

        return [
            'agent_id' => $agentId,
            'period' => $period,
            'date_range' => $dateRange,
            'current_rank' => $currentRank,
            'total_agents' => count($agentScores),
            'top_performers' => array_slice($agentScores, 0, 5),
            'percentile' => $this->calculatePercentile($currentRank, count($agentScores)),
            'rank_trend' => $this->calculateRankTrend($agentId, $period),
        ];
    }

    /**
     * Get monthly performance for agent
     */
    public function getMonthlyPerformance(int $agentId, string $month = null, int $year = null): array
    {
        $agent = Agent::findOrFail($agentId);
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth()->toDateString();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth()->toDateString();

        $monthlyPerformance = $agent->performances()
            ->whereBetween('period_start', [$startDate, $endDate])
            ->first();

        return [
            'agent_id' => $agentId,
            'month' => $month,
            'year' => $year,
            'date_range' => ['start' => $startDate, 'end' => $endDate],
            'performance' => $monthlyPerformance,
            'daily_breakdown' => $this->getDailyBreakdown($agentId, $year, $month),
            'weekly_breakdown' => $this->getWeeklyBreakdown($agentId, $year, $month),
            'goals_progress' => $this->getGoalsProgress($agentId, $month, $year),
        ];
    }

    /**
     * Calculate performance score
     */
    private function calculatePerformanceScore(array $metrics): int
    {
        $score = $this->calculateSalesScore($metrics['sales']) +
                 $this->calculateLeadScore($metrics['leads']) +
                 $this->calculateAppointmentScore($metrics['appointments']) +
                 $this->calculateCommissionScore($metrics['commissions']);

        return min($score, 100);
    }

    private function calculateSalesScore(array $salesMetrics): int
    {
        $score = 0;
        if ($salesMetrics['total_count'] >= 10) {
            $score += 15;
        } elseif ($salesMetrics['total_count'] >= 5) {
            $score += 10;
        } elseif ($salesMetrics['total_count'] >= 1) {
            $score += 5;
        }

        if ($salesMetrics['total_revenue'] >= 100000) {
            $score += 15;
        } elseif ($salesMetrics['total_revenue'] >= 50000) {
            $score += 10;
        } elseif ($salesMetrics['total_revenue'] >= 10000) {
            $score += 5;
        }

        if ($salesMetrics['conversion_rate'] >= 20) {
            $score += 10;
        } elseif ($salesMetrics['conversion_rate'] >= 15) {
            $score += 7;
        } elseif ($salesMetrics['conversion_rate'] >= 10) {
            $score += 5;
        }

        if ($salesMetrics['average_price'] >= 500000) {
            $score += 10;
        } elseif ($salesMetrics['average_price'] >= 250000) {
            $score += 7;
        } elseif ($salesMetrics['average_price'] >= 100000) {
            $score += 5;
        }

        return $score;
    }

    private function calculateLeadScore(array $leadMetrics): int
    {
        $score = 0;
        if ($leadMetrics['total_count'] >= 50) {
            $score += 10;
        } elseif ($leadMetrics['total_count'] >= 25) {
            $score += 7;
        } elseif ($leadMetrics['total_count'] >= 10) {
            $score += 5;
        }

        if ($leadMetrics['conversion_rate'] >= 25) {
            $score += 20;
        } elseif ($leadMetrics['conversion_rate'] >= 20) {
            $score += 15;
        } elseif ($leadMetrics['conversion_rate'] >= 15) {
            $score += 10;
        } elseif ($leadMetrics['conversion_rate'] >= 10) {
            $score += 5;
        }

        return $score;
    }

    private function calculateAppointmentScore(array $appointmentMetrics): int
    {
        $score = 0;
        if ($appointmentMetrics['total_count'] >= 20) {
            $score += 5;
        } elseif ($appointmentMetrics['total_count'] >= 10) {
            $score += 3;
        } elseif ($appointmentMetrics['total_count'] >= 5) {
            $score += 1;
        }

        if ($appointmentMetrics['completion_rate'] >= 90) {
            $score += 15;
        } elseif ($appointmentMetrics['completion_rate'] >= 80) {
            $score += 10;
        } elseif ($appointmentMetrics['completion_rate'] >= 70) {
            $score += 5;
        }

        return $score;
    }

    private function calculateCommissionScore(array $commissionMetrics): int
    {
        $score = 0;
        if ($commissionMetrics['total_amount'] >= 10000) {
            $score += 10;
        } elseif ($commissionMetrics['total_amount'] >= 5000) {
            $score += 7;
        } elseif ($commissionMetrics['total_amount'] >= 1000) {
            $score += 5;
        }

        if ($commissionMetrics['average_amount'] >= 1000) {
            $score += 10;
        } elseif ($commissionMetrics['average_amount'] >= 500) {
            $score += 7;
        } elseif ($commissionMetrics['average_amount'] >= 100) {
            $score += 5;
        }

        return $score;
    }

    private function getDailyBreakdown(int $agentId, int $year, string $month): array
    {
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth();
        $dailyData = [];
        for ($day = 1; $day <= $endDate->daysInMonth; $day++) {
            $dailyData[] = [
                'date' => $startDate->copy()->addDays($day - 1)->toDateString(),
                'sales_count' => 0, 'leads_count' => 0, 'appointments_count' => 0, 'commissions_amount' => 0,
            ];
        }

        return $dailyData;
    }

    private function getWeeklyBreakdown(int $agentId, int $year, string $month): array
    {
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth();
        $weeklyData = [];
        $currentWeek = 1;
        while ($startDate->lte($endDate)) {
            $weekEnd = $startDate->copy()->addDays(6);
            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate;
            }
            $weeklyData[] = [
                'week' => $currentWeek,
                'start_date' => $startDate->toDateString(),
                'end_date' => $weekEnd->toDateString(),
                'sales_count' => 0, 'leads_count' => 0, 'appointments_count' => 0, 'commissions_amount' => 0,
            ];
            $startDate = $weekEnd->copy()->addDay();
            $currentWeek++;
        }

        return $weeklyData;
    }

    private function getGoalsProgress(int $agentId, string $month, int $year): array
    {
        $agent = Agent::findOrFail($agentId);
        if (! method_exists($agent, 'goals')) {
            return ['sales_goal' => 0, 'leads_goal' => 0, 'appointments_goal' => 0, 'commissions_goal' => 0, 'progress' => []];
        }
        $monthlyGoals = $agent->goals()->where('period', 'monthly')->where('year', $year)->where('month', $month)->first();
        if (! $monthlyGoals) {
            return ['sales_goal' => 0, 'leads_goal' => 0, 'appointments_goal' => 0, 'commissions_goal' => 0, 'progress' => []];
        }
        $actualPerformance = $this->getMonthlyPerformance($agentId, $month, $year);

        return [
            'sales_goal' => $monthlyGoals->sales_goal ?? 0,
            'leads_goal' => $monthlyGoals->leads_goal ?? 0,
            'appointments_goal' => $monthlyGoals->appointments_goal ?? 0,
            'commissions_goal' => $monthlyGoals->commissions_goal ?? 0,
            'progress' => [
                'sales' => [
                    'goal' => $monthlyGoals->sales_goal ?? 0,
                    'actual' => $actualPerformance['sales']['total_count'] ?? 0,
                    'percentage' => $monthlyGoals->sales_goal > 0 ? (($actualPerformance['sales']['total_count'] ?? 0) / $monthlyGoals->sales_goal) * 100 : 0,
                ],
                'leads' => [
                    'goal' => $monthlyGoals->leads_goal ?? 0,
                    'actual' => $actualPerformance['leads']['total_count'] ?? 0,
                    'percentage' => $monthlyGoals->leads_goal > 0 ? (($actualPerformance['leads']['total_count'] ?? 0) / $monthlyGoals->leads_goal) * 100 : 0,
                ],
                'appointments' => [
                    'goal' => $monthlyGoals->appointments_goal ?? 0,
                    'actual' => $actualPerformance['appointments']['total_count'] ?? 0,
                    'percentage' => $monthlyGoals->appointments_goal > 0 ? (($actualPerformance['appointments']['total_count'] ?? 0) / $monthlyGoals->appointments_goal) * 100 : 0,
                ],
                'commissions' => [
                    'goal' => $monthlyGoals->commissions_goal ?? 0,
                    'actual' => $actualPerformance['commissions']['total_amount'] ?? 0,
                    'percentage' => $monthlyGoals->commissions_goal > 0 ? (($actualPerformance['commissions']['total_amount'] ?? 0) / $monthlyGoals->commissions_goal) * 100 : 0,
                ],
            ],
        ];
    }

    /**
     * Calculate trend by comparing with previous period
     */
    private function calculateTrend(int $agentId, string $period): string
    {
        $dateRange = $this->getDateRange($period);
        $currentMetrics = $this->getMetricsForRange($agentId, $dateRange);
        $currentScore = $this->calculatePerformanceScore($currentMetrics);

        // Calculate previous period dates
        $currentStart = Carbon::parse($dateRange['start']);
        $currentEnd = Carbon::parse($dateRange['end']);
        $diff = $currentStart->diffInDays($currentEnd) + 1;

        $previousRange = [
            'start' => $currentStart->copy()->subDays($diff)->toDateString(),
            'end' => $currentStart->copy()->subDay()->toDateString(),
        ];

        $previousMetrics = $this->getMetricsForRange($agentId, $previousRange);
        $previousScore = $this->calculatePerformanceScore($previousMetrics);

        if ($currentScore > $previousScore) {
            return 'improving';
        } elseif ($currentScore < $previousScore) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Calculate rank trend (simplified for performance)
     */
    private function calculateRankTrend(int $agentId, string $period): string
    {
        // For performance, we'll return 'stable' as calculating historical rank is expensive
        // and might not be supported by the simplified batch fetching.
        return 'stable';
    }

    private function calculatePercentile(int $rank, int $total): float { return $total === 0 ? 0 : (($total - $rank) / $total) * 100; }

    private function getDateRange(string $period): array
    {
        return match ($period) {
            'week' => ['start' => now()->subWeek()->startOfWeek()->toDateString(), 'end' => now()->subWeek()->endOfWeek()->toDateString()],
            'month' => ['start' => now()->subMonth()->startOfMonth()->toDateString(), 'end' => now()->subMonth()->endOfMonth()->toDateString()],
            'quarter' => ['start' => now()->subQuarter()->startOfMonth()->toDateString(), 'end' => now()->subQuarter()->endOfMonth()->toDateString()],
            'year' => ['start' => now()->subYear()->startOfYear()->toDateString(), 'end' => now()->subYear()->endOfYear()->toDateString()],
            default => ['start' => now()->subMonth()->startOfMonth()->toDateString(), 'end' => now()->subMonth()->endOfMonth()->toDateString()],
        };
    }
}
