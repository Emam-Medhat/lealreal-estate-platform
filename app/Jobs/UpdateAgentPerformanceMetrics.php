<?php

namespace App\Jobs;

use App\Models\Agent;
use App\Models\Commission;
use App\Models\AgentPerformance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateAgentPerformanceMetrics implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 600;

    protected $agentId;
    protected $period;

    /**
     * Create a new job instance.
     */
    public function __construct(int $agentId, string $period = 'month')
    {
        $this->agentId = $agentId;
        $this->period = $period;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $agent = Agent::findOrFail($this->agentId);
            
            // Calculate performance metrics for the period
            $performanceMetrics = $this->calculatePerformanceMetrics($agent, $this->period);
            
            // Update or create performance record
            $performanceRecord = $agent->performanceMetrics()
                ->where('period', $this->period)
                ->where('year', now()->year)
                ->where('month', now()->month)
                ->first();

            if ($performanceRecord) {
                $performanceRecord->update($performanceMetrics);
            } else {
                $agent->performanceMetrics()->create($performanceMetrics);
            }

            // Update agent's monthly performance score
            $agent->update([
                'monthly_performance_score' => $performanceMetrics['performance_score'],
                'last_performance_update' => now()
            ]);

            // Clear agent cache
            Cache::tags(['agent', 'agent.' . $this->agentId])->flush();

            Log::info('Agent performance metrics updated', [
                'agent_id' => $this->agentId,
                'period' => $this->period,
                'performance_score' => $performanceMetrics['performance_score']
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update agent performance metrics', [
                'agent_id' => $this->agentId,
                'period' => $this->period,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Calculate performance metrics
     */
    private function calculatePerformanceMetrics(Agent $agent, string $period): array
    {
        // Get date range
        $dateRange = $this->getDateRange($period);
        
        // Get data for the period
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

        // Calculate performance score
        $performanceScore = $this->calculatePerformanceScore($agent, $dateRange);

        return [
            'agent_id' => $agent->id,
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
            'performance_score' => $performanceScore,
            'calculated_at' => now()
        ];
    }

    /**
     * Calculate performance score
     */
    private function calculatePerformanceScore(Agent $agent, array $dateRange): int
    {
        // Get metrics for the period
        $metrics = $this->getMetrics($agent, 'month');
        
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
     * Get metrics for period
     */
    private function getMetrics(Agent $agent, string $period): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'sales' => [
                'total_count' => $agent->sales()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->count(),
                'total_revenue' => $agent->sales()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->sum('sale_price'),
                'average_price' => $agent->sales()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->avg('sale_price')
            ],
            'leads' => [
                'total_count' => $agent->leads()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->count(),
                'converted_count' => $agent->leads()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->where('status', 'converted')
                    ->count()
            ],
            'appointments' => [
                'total_count' => $agent->appointments()
                    ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
                    ->count(),
                'completed_count' => $agent->appointments()
                    ->whereBetween('start_time', [$dateRange['start'], $dateRange['end']])
                    ->where('status', 'completed')
                    ->count()
            ],
            'commissions' => [
                'total_amount' => $agent->commissions()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->where('status', 'paid')
                    ->sum('amount'),
                'average_amount' => $agent->commissions()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->where('status', 'paid')
                    ->avg('amount')
            ]
        ];
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

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Agent performance metrics update job failed', [
            'agent_id' => $this->agentId,
            'period' => $this->period,
            'error' => $exception->getMessage()
        ]);
    }
}
