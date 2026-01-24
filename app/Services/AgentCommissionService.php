<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Commission;
use App\Models\AgentPerformance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AgentCommissionService
{
    /**
     * Calculate commission for agent
     */
    public function calculateCommission(int $agentId, float $saleAmount, string $type = 'sale'): array
    {
        $agent = Agent::findOrFail($agentId);
        $commissionStructure = $agent->commissionStructure;

        $baseAmount = 0;
        $rate = 0;

        switch ($type) {
            case 'sale':
                $baseAmount = $saleAmount;
                $rate = $commissionStructure->sale_rate;
                break;
            case 'rental':
                $baseAmount = $saleAmount;
                $rate = $commissionStructure->rental_rate;
                break;
            case 'referral':
                $baseAmount = $commissionStructure->referral_amount ?? 0;
                $rate = $commissionStructure->referral_rate ?? 0;
                break;
            case 'bonus':
                $baseAmount = $commissionStructure->bonus_amount ?? 0;
                $rate = 100; // Bonus is full amount
                break;
            default:
                $baseAmount = $saleAmount;
                $rate = $commissionStructure->base_rate ?? 2.5;
                break;
        }

        $commissionAmount = $baseAmount * ($rate / 100);

        return [
            'agent_id' => $agentId,
            'type' => $type,
            'base_amount' => $baseAmount,
            'rate' => $rate,
            'commission_amount' => $commissionAmount,
            'calculated_at' => now()
        ];
    }

    /**
     * Pay commission to agent
     */
    public function payCommission(int $agentId, int $commissionId, string $paymentMethod = 'wallet'): bool
    {
        DB::beginTransaction();
        try {
            $agent = Agent::findOrFail($agentId);
            $commission = Commission::findOrFail($commissionId);

            // Create payment record
            $payment = $agent->payments()->create([
                'type' => 'commission',
                'amount' => $commission->amount,
                'commission_id' => $commission->id,
                'payment_method' => $paymentMethod,
                'payment_date' => now(),
                'status' => 'paid',
                'paid_by' => auth()->id()
            ]);

            // Update commission status
            $commission->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_id' => $payment->id
            ]);

            // Update agent's total earned commissions
            $agent->increment('total_earned_commissions', $commission->amount);

            // Update agent's wallet balance
            if ($paymentMethod === 'wallet') {
                $agent->wallet()->increment('balance', $commission->amount);
            }

            Log::info('Commission paid to agent', [
                'agent_id' => $agentId,
                'commission_id' => $commissionId,
                'amount' => $commission->amount,
                'payment_method' => $paymentMethod
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to pay commission to agent', [
                'agent_id' => $agentId,
                'commission_id' => $commissionId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Get commission history for agent
     */
    public function getCommissionHistory(int $agentId, array $filters = []): array
    {
        $query = Commission::where('agent_id', $agentId)
            ->with(['sale', 'client', 'payment'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $commissions = $query->paginate($filters['per_page'] ?? 20);

        return [
            'commissions' => $commissions,
            'total_amount' => $commissions->sum('amount'),
            'total_count' => $commissions->count(),
            'filters' => $filters
        ];
    }

    /**
     * Get commission summary for agent
     */
    public function getCommissionSummary(int $agentId, string $period = 'month'): array
    {
        $agent = Agent::findOrFail($agentId);
        
        // Get date range
        $dateRange = $this->getDateRange($period);
        
        // Get commissions for the period
        $commissions = Commission::where('agent_id', $agentId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', 'paid')
            ->get(['type', 'amount', 'created_at']);

        // Calculate summary
        $totalAmount = $commissions->sum('amount');
        $totalCount = $commissions->count();
        $averageAmount = $totalCount > 0 ? $totalAmount / $totalCount : 0;

        // Group by type
        $byType = $commissions->groupBy('type')->map(function ($group) {
            return [
                'type' => $group->first()->type,
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
                'average_amount' => $group->count() > 0 ? $group->sum('amount') / $group->count() : 0
            ];
        });

        return [
            'agent_id' => $agentId,
            'period' => $period,
            'date_range' => $dateRange,
            'total_amount' => $totalAmount,
            'total_count' => $totalCount,
            'average_amount' => $averageAmount,
            'by_type' => $byType,
            'monthly_average' => $this->calculateMonthlyAverage($agentId),
            'yearly_projection' => $this->calculateYearlyProjection($agentId)
        ];
    }

    /**
     * Get monthly average commission
     */
    private function calculateMonthlyAverage(int $agentId): float
    {
        $commissions = Commission::where('agent_id', $agentId)
            ->where('status', 'paid')
            ->whereBetween('created_at', [
                now()->subMonths(6)->startOfMonth()->toDateString(),
                now()->subMonth()->endOfMonth()->toDateString()
            ])
            ->get(['amount']);

        return $commissions->avg('amount') ?? 0;
    }

    /**
     * Calculate yearly projection
     */
    private function calculateYearlyProjection(int $agentId): array
    {
        $monthlyAverage = $this->calculateMonthlyAverage($agentId);
        $currentYearToDate = Commission::where('agent_id', $agentId)
            ->where('status', 'paid')
            ->whereBetween('created_at', [
                now()->startOfYear()->toDateString(),
                now()->endOfDay()->toDateString()
            ])
            ->sum('amount');

        $projectedYearEnd = $currentYearToDate + ($monthlyAverage * 12);

        return [
            'current_year_to_date' => $currentYearToDate,
            'monthly_average' => $monthlyAverage,
            'projected_year_end' => $projectedYearEnd,
            'projection_confidence' => 'medium'
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
}
