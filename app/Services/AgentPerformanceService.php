<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\AgentPerformance;
use App\Models\Lead;
use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
        
        // Get performance data for the period
        $performanceMetrics = $agent->performanceMetrics()
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get();

        // Calculate metrics
        $totalSales = $agent->sales()
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $totalRevenue = $agent->sales()
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('sale_price');

        $totalLeads = $agent->leads()
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $convertedLeads = $agent->leads()
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'converted')
            ->count();

        $totalAppointments = $agent->appointments()
            ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
            ->count();

        $completedAppointments = $agent->appointments()
            ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'completed')
            ->count();

        $totalCommissions = $agent->commissions()
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'paid')
            ->sum('amount');

        $conversionRate = $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;

        $averageSalePrice = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        return [
            'agent_id' => $agentId,
            'period' => $period,
            'date_range' => $dateRange,
            'sales' => [
                'total_count' => $totalSales,
                'total_revenue' => $totalRevenue,
                'average_price' => $averageSalePrice,
                'conversion_rate' => $conversionRate
            ],
            'leads' => [
                'total_count' => $totalLeads,
                'converted_count' => $convertedLeads,
                'conversion_rate' => $conversionRate
            ],
            'appointments' => [
                'total_count' => $totalAppointments,
                'completed_count' => $completedAppointments,
                'completion_rate' => $totalAppointments > 0 ? ($completedAppointments / $totalAppointments) * 100 : 0
            ],
            'commissions' => [
                'total_amount' => $totalCommissions,
                'average_amount' => $totalCommissions > 0 ? $totalCommissions / $totalSales : 0
            ],
            'performance_score' => $this->calculatePerformanceScore($agentId, $dateRange),
            'trend' => $this->calculateTrend($agentId, $dateRange)
        ];
    }

    /**
     * Get ranking for agent
     */
    public function getRanking(int $agentId, string $period = 'month'): array
    {
        $agent = Agent::findOrFail($agentId);
        
        // Get date range
        $dateRange = $this->getDateRange($period);
        
        // Get all agents in the same company
        $companyAgents = $agent->company ? $agent->company->agents()->get() : collect([$agent]);
        
        // Calculate performance scores for all agents
        $agentScores = [];
        
        foreach ($companyAgents as $companyAgent) {
            $agentMetrics = $this->getMetrics($companyAgent->id, $period);
            $agentScores[] = [
                'agent_id' => $companyAgent->id,
                'agent_name' => $companyAgent->name,
                'performance_score' => $agentMetrics['performance_score'],
                'total_sales' => $agentMetrics['sales']['total_count'],
                'total_revenue' => $agentMetrics['sales']['total_revenue'],
                'conversion_rate' => $agentMetrics['leads']['conversion_rate']
            ];
        }
        
        // Sort by performance score
        usort($agentScores, function ($a, $b) {
            return $b['performance_score'] - $a['performance_score'];
        });
        
        // Find current agent's rank
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
            'rank_trend' => $this->calculateRankTrend($agentId, $dateRange)
        ];
    }

    /**
     * Get monthly performance for agent
     */
    public function getMonthlyPerformance(int $agentId, string $month = null, int $year = null): array
    {
        $agent = Agent::findOrFail($agentId);
        
        // Set default month and year
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        // Get date range for the month
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth()->toDateString();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth()->toDateString();
        
        // Get performance data for the month
        $monthlyPerformance = $agent->performanceMetrics()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();

        // Calculate monthly breakdown
        $dailyBreakdown = $this->getDailyBreakdown($agentId, $year, $month);
        $weeklyBreakdown = $this->getWeeklyBreakdown($agentId, $year, $month);

        return [
            'agent_id' => $agentId,
            'month' => $month,
            'year' => $year,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'performance' => $monthlyPerformance,
            'daily_breakdown' => $dailyBreakdown,
            'weekly_breakdown' => $weeklyBreakdown,
            'goals_progress' => $this->getGoalsProgress($agentId, $month, $year)
        ];
    }

    /**
     * Calculate performance score
     */
    private function calculatePerformanceScore(int $agentId, array $dateRange): int
    {
        $agent = Agent::findOrFail($agentId);
        
        // Get metrics for the period
        $metrics = $this->getMetrics($agentId, 'month');
        
        // Calculate score based on multiple factors
        $score = 0;
        
        // Sales performance (40%)
        $salesScore = $this->calculateSalesScore($metrics['sales']);
        $score += $salesScore;
        
        // Lead performance (30%)
        $leadScore = $this->calculateLeadScore($metrics['leads']);
        $score += $leadScore;
        
        // Appointment performance (20%)
        $appointmentScore = $this->calculateAppointmentScore($metrics['appointments']);
        $score += $appointmentScore;
        
        // Commission performance (10%)
        $commissionScore = $this->calculateCommissionScore($metrics['commissions']);
        $score += $commissionScore;
        
        return min($score, 100);
    }

    /**
     * Calculate sales score
     */
    private function calculateSalesScore(array $salesMetrics): int
    {
        $score = 0;
        
        // Total sales (15%)
        if ($salesMetrics['total_count'] >= 10) {
            $score += 15;
        } elseif ($salesMetrics['total_count'] >= 5) {
            $score += 10;
        } elseif ($salesMetrics['total_count'] >= 1) {
            $score += 5;
        }
        
        // Total revenue (15%)
        if ($salesMetrics['total_revenue'] >= 100000) {
            $score += 15;
        } elseif ($salesMetrics['total_revenue'] >= 50000) {
            $score += 10;
        } elseif ($salesMetrics['total_revenue'] >= 10000) {
            $score += 5;
        }
        
        // Conversion rate (10%)
        if ($salesMetrics['conversion_rate'] >= 20) {
            $score += 10;
        } elseif ($salesMetrics['conversion_rate'] >= 15) {
            $score += 7;
        } elseif ($salesMetrics['conversion_rate'] >= 10) {
            $score += 5;
        }
        
        // Average sale price (10%)
        if ($salesMetrics['average_price'] >= 500000) {
            $score += 10;
        } elseif ($salesMetrics['average_price'] >= 250000) {
            $score += 7;
        } elseif ($salesMetrics['average_price'] >= 100000) {
            $score += 5;
        }
        
        return $score;
    }

    /**
     * Calculate lead score
     */
    private function calculateLeadScore(array $leadMetrics): int
    {
        $score = 0;
        
        // Total leads (10%)
        if ($leadMetrics['total_count'] >= 50) {
            $score += 10;
        } elseif ($leadMetrics['total_count'] >= 25) {
            $score += 7;
        } elseif ($leadMetrics['total_count'] >= 10) {
            $score += 5;
        }
        
        // Conversion rate (20%)
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

    /**
     * Calculate appointment score
     */
    private function calculateAppointmentScore(array $appointmentMetrics): int
    {
        $score = 0;
        
        // Total appointments (5%)
        if ($appointmentMetrics['total_count'] >= 20) {
            $score += 5;
        } elseif ($appointmentMetrics['total_count'] >= 10) {
            $score += 3;
        } elseif ($appointmentMetrics['total_count'] >= 5) {
            $score += 1;
        }
        
        // Completion rate (15%)
        if ($appointmentMetrics['completion_rate'] >= 90) {
            $score += 15;
        } elseif ($appointmentMetrics['completion_rate'] >= 80) {
            $score += 10;
        } elseif ($appointmentMetrics['completion_rate'] >= 70) {
            $score += 5;
        }
        
        return $score;
    }

    /**
     * Calculate commission score
     */
    private function calculateCommissionScore(array $commissionMetrics): int
    {
        $score = 0;
        
        // Total commission amount (10%)
        if ($commissionMetrics['total_amount'] >= 10000) {
            $score += 10;
        } elseif ($commissionMetrics['total_amount'] >= 5000) {
            $score += 7;
        } elseif ($commissionMetrics['total_amount'] >= 1000) {
            $score += 5;
        }
        
        // Average commission (10%)
        if ($commissionMetrics['average_amount'] >= 1000) {
            $score += 10;
        } elseif ($commissionMetrics['average_amount'] >= 500) {
            $score += 7;
        } elseif ($commissionMetrics['average_amount'] >= 100) {
            $score += 5;
        }
        
        return $score;
    }

    /**
     * Get daily breakdown
     */
    private function getDailyBreakdown(int $agentId, int $year, string $month): array
    {
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth();
        
        $dailyData = [];
        
        for ($day = 1; $day <= $endDate->daysInMonth; $day++) {
            $currentDay = $startDate->copy()->addDays($day - 1);
            
            $dailyData[] = [
                'date' => $currentDay->toDateString(),
                'sales_count' => 0,
                'leads_count' => 0,
                'appointments_count' => 0,
                'commissions_amount' => 0
            ];
        }
        
        return $dailyData;
    }

    /**
     * Get weekly breakdown
     */
    private function getWeeklyBreakdown(int $agentId, int $year, string $month): array
    {
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth();
        
        $weeklyData = [];
        $currentWeek = 1;
        
        while ($startDate->lte($endDate)) {
            $weekStart = $startDate->copy();
            $weekEnd = $startDate->copy()->addDays(6);
            
            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate;
            }
            
            $weeklyData[] = [
                'week' => $currentWeek,
                'start_date' => $weekStart->toDateString(),
                'end_date' => $weekEnd->toDateString(),
                'sales_count' => 0,
                'leads_count' => 0,
                'appointments_count' => 0,
                'commissions_amount' => 0
            ];
            
            $startDate = $weekEnd->copy()->addDay();
            $currentWeek++;
        }
        
        return $weeklyData;
    }

    /**
     * Get goals progress
     */
    private function getGoalsProgress(int $agentId, string $month, int $year): array
    {
        $agent = Agent::findOrFail($agentId);
        
        // Get monthly goals
        $monthlyGoals = $agent->goals()
            ->where('period', 'monthly')
            ->where('year', $year)
            ->where('month', $month)
            ->first();
        
        if (!$monthlyGoals) {
            return [
                'sales_goal' => 0,
                'leads_goal' => 0,
                'appointments_goal' => 0,
                'commissions_goal' => 0,
                'progress' => []
            ];
        }
        
        // Get actual performance for the month
        $actualPerformance = $this->getMonthlyPerformance($agentId, $month, $year);
        
        return [
            'sales_goal' => $monthlyGoals->sales_goal ?? 0,
            'leads_goal' => $monthlyGoals->leads_goal ?? 0,
            'appointments_goal' => $monthlyGoals->appointments_goal ?? 0,
            'commissions_goal' => $monthlyGoals->commissions_goal ?? 0,
            'progress' => [
                'sales' => [
                    'goal' => $monthlyGoals->sales_goal ?? 0,
                    'actual' => $actualPerformance['sales']['total_count'],
                    'percentage' => $monthlyGoals->sales_goal > 0 ? ($actualPerformance['sales']['total_count'] / $monthlyGoals->sales_goal) * 100 : 0
                ],
                'leads' => [
                    'goal' => $monthlyGoals->leads_goal ?? 0,
                    'actual' => $actualPerformance['leads']['total_count'],
                    'percentage' => $monthlyGoals->leads_goal > 0 ? ($actualPerformance['leads']['total_count'] / $monthlyGoals->leads_goal) * 100 : 0
                ],
                'appointments' => [
                    'goal' => $monthlyGoals->appointments_goal ?? 0,
                    'actual' => $actualPerformance['appointments']['total_count'],
                    'percentage' => $monthlyGoals->appointments_goal > 0 ? ($actualPerformance['appointments']['total_count'] / $monthlyGoals->appointments_goal) * 100 : 0
                ],
                'commissions' => [
                    'goal' => $monthlyGoals->commissions_goal ?? 0,
                    'actual' => $actualPerformance['commissions']['total_amount'],
                    'percentage' => $monthlyGoals->commissions_goal > 0 ? ($actualPerformance['commissions']['total_amount'] / $monthlyGoals->commissions_goal) * 100 : 0
                ]
            ]
        ];
    }

    /**
     * Calculate trend
     */
    private function calculateTrend(int $agentId, array $dateRange): string
    {
        // Get performance for current and previous period
        $currentMetrics = $this->getMetrics($agentId, 'month');
        $previousMetrics = $this->getPreviousPeriodMetrics($agentId, $dateRange);
        
        $currentScore = $currentMetrics['performance_score'];
        $previousScore = $previousMetrics['performance_score'];
        
        if ($currentScore > $previousScore) {
            return 'improving';
        } elseif ($currentScore < $previousScore) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Get previous period metrics
     */
    private function getPreviousPeriodMetrics(int $agentId, array $dateRange): array
    {
        // Calculate previous period dates
        $currentStart = new \DateTime($dateRange['start']);
        $currentEnd = new \DateTime($dateRange['end']);
        $duration = $currentStart->diff($currentEnd);
        
        $previousStart = $currentStart->sub($duration);
        $previousEnd = $currentEnd->sub($duration);
        
        return $this->getMetrics($agentId, 'month');
    }

    /**
     * Calculate percentile
     */
    private function calculatePercentile(int $rank, int $total): float
    {
        if ($total === 0) {
            return 0;
        }
        
        return (($total - $rank) / $total) * 100;
    }

    /**
     * Get date range for period
     */
    private function getDateRange(string $period): array
    {
        switch ($period) {
            case 'week':
                return [
                    'start' => now()->subWeek()->startOfWeek()->toDateString(),
                    'end' => now()->subWeek()->endOfWeek()->toDateString()
                ];
            case 'month':
                return [
                    'start' => now()->subMonth()->startOfMonth()->toDateString(),
                    'end' => now()->subMonth()->endOfMonth()->toDateString()
                ];
            case 'quarter':
                return [
                    'start' => now()->subQuarter()->startOfMonth()->toDateString(),
                    'end' => now()->subQuarter()->endOfMonth()->toDateString()
                ];
            case 'year':
                return [
                    'start' => now()->subYear()->startOfYear()->toDateString(),
                    'end' => now()->subYear()->endOfYear()->toDateString()
                ];
            default:
                return [
                    'start' => now()->subMonth()->startOfMonth()->toDateString(),
                    'end' => now()->subMonth()->endOfMonth()->toDateString()
                ];
        }
    }
}
