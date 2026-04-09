<?php

namespace App\Listeners;

use App\Events\CommissionEarned;
use App\Models\Agent;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CalculateCommission
{
    /**
     * Handle the event.
     */
    public function handle(CommissionEarned $event): void
    {
        $commission = $event->commission;
        $agent = $event->agent;
        $earnedBy = $event->earnedBy;

        try {
            // Calculate commission amount
            $commissionAmount = $this->calculateCommissionAmount($commission);

            // Update commission record
            $commission->update([
                'amount' => $commissionAmount,
                'calculated_at' => now(),
                'calculated_by' => $earnedBy->id
            ]);

            // Update agent's total earned commissions
            $agent->increment('total_earned_commissions', $commissionAmount);

            // Create notification for agent
            $agent->notifications()->create([
                'title' => 'عمولة جديدة',
                'message' => "تم احتساب عمولة بقيمة {$commissionAmount} من عملية {$commission->sale->property->title}",
                'type' => 'commission_earned',
                'data' => [
                    'commission_id' => $commission->id,
                    'commission_amount' => $commissionAmount,
                    'sale_id' => $commission->sale->id,
                    'property_title' => $commission->sale->property->title,
                    'client_name' => $commission->sale->client->name,
                    'calculated_at' => now()
                ]
            ]);

            Log::info('Commission calculated and credited', [
                'agent_id' => $agent->id,
                'commission_id' => $commission->id,
                'commission_amount' => $commissionAmount,
                'earned_by_id' => $earnedBy->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to calculate commission', [
                'agent_id' => $agent->id,
                'commission_id' => $commission->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate commission amount based on commission type
     */
    private function calculateCommissionAmount(Commission $commission): float
    {
        $baseAmount = 0;

        switch ($commission->type) {
            case 'sale':
                $baseAmount = $commission->sale->price * ($commission->commission_rate / 100);
                break;
            case 'rental':
                $baseAmount = $commission->rental->monthly_rent * ($commission->commission_rate / 100);
                break;
            case 'referral':
                $baseAmount = $commission->referral_amount;
                break;
            case 'bonus':
                $baseAmount = $commission->bonus_amount;
                break;
            default:
                $baseAmount = 0;
                break;
        }

        return $baseAmount;
    }
}
