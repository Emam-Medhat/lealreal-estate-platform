<?php

namespace App\Jobs;

use App\Models\Agent;
use App\Models\Commission;
use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CalculateAgentCommissions implements ShouldQueue
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

            // Get commissions for the period
            $commissions = $agent->commissions()
                ->whereBetween('created_at', $this->getPeriodStart($this->period), $this->getPeriodEnd($this->period)])
                ->with(['sale', 'client'])
                ->get();

            // Calculate commission metrics
            $totalCommissions = $commissions->count();
            $totalAmount = $commissions->sum('amount');
            $averageCommission = $totalCommissions > 0 ? $totalAmount / $totalCommissions : 0;
            $totalSales = $commissions->where('type', 'sale')->count();
            $totalRentals = $commissions->where('type', 'rental')->count();
            $totalReferrals = $commissions->where('type', 'referral')->count();
            $totalBonuses = $commissions->where('type', 'bonus')->count();

            // Update agent's commission metrics
            $agent->update([
                'total_commissions' => $totalCommissions,
                'total_commission_amount' => $totalAmount,
                'average_commission' => $averageCommission,
                'total_sales' => $totalSales,
                'total_rentals' => $totalRentals,
                'total_referrals' => $totalReferrals,
                'total_bonuses' => $totalBonuses,
                'commission_ranking' => $this->calculateCommissionRanking($agent, $totalAmount)
            ]);

            // Create commission summary notification
            $agent->notifications()->create([
                'title' => 'ملخص العمولات الشهرية',
                'message' => "ملخص العمولات لشهر {$this->period}: {$totalCommissions} عميلة بقيمة إجمالي {$totalAmount}",
                'type' => 'commission_summary',
                'data' => [
                    'period' => $this->period,
                    'total_commissions' => $totalCommissions,
                    'total_amount' => $totalAmount,
                    'average_commission' => $averageCommission,
                    'total_sales' => $totalSales,
                    'total_rentals' => $totalRentals,
                    'total_referrals' => $totalReferrals,
                    'total_bonuses' => $totalBonuses,
                    'commission_ranking' => $this->calculateCommissionRanking($agent, $totalAmount)
                ]
            ]);

            Log::info('Agent commissions calculated', [
                'agent_id' => $this->agentId,
                'period' => $this->period,
                'total_commissions' => $totalCommissions,
                'total_amount' => $totalAmount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to calculate agent commissions', [
                'agent_id' => $this->agentId,
                'period' => $this->period,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Get period start date
     */
    private function getPeriodStart(string $period): string
    {
        switch ($period) {
            case 'week':
                return now()->subWeek()->startOfWeek()->toDateString();
            case 'month':
                return now()->subMonth()->startOfMonth()->toDateString();
            case 'quarter':
                return now()->subQuarter()->startOfMonth()->toDateString();
            case 'year':
                return now()->subYear()->startOfYear()->toDateString();
            default:
                return now()->subMonth()->startOfMonth()->toDateString();
        }
    }

    /**
     * Get period end date
     */
    private function getPeriodEnd(string $period): string
    {
        switch ($period) {
            case 'week':
                return now()->subWeek()->endOfWeek()->toDateString();
            case 'month':
                return now()->subMonth()->endOfMonth()->toDateString();
            case 'quarter':
                return now()->subQuarter()->endOfMonth()->toDateString();
            case 'year':
                return now()->subYear()->endOfYear()->toDateString();
            default:
                return now()->subMonth()->endOfMonth()->toDateString();
        }
    }

    /**
     * Calculate commission ranking
     */
    private function calculateCommissionRanking(Agent $agent, float $totalAmount): int
    {
        // This would compare with other agents in the company
        // Placeholder implementation
        
        $companyAgents = $agent->company ? $agent->company->agents()->count() : 0;
        
        if ($companyAgents > 0) {
            $averageAmount = $totalAmount / $companyAgents;
            return $this->getRankingScore($averageAmount);
        }
        
        return $this->getRankingScore($totalAmount);
    }

    /**
     * Get ranking score based on amount
     */
    private function getRankingScore(float $amount): int
    {
        if ($amount >= 10000) {
            return 5; // Top performer
        } elseif ($amount >= 5000) {
            return 4; // Excellent performer
        } elseif ($amount >= 2500) {
            return 3; // Very good performer
        } elseif ($amount >= 1000) {
            return 2; // Good performer
        } elseif ($amount >= 500) {
            return 1; // Average performer
        } else {
            return 0; // Needs improvement
        }
    }
    }
}
