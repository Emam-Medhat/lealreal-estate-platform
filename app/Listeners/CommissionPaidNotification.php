<?php

namespace App\Listeners;

use App\Events\CommissionEarned;
use App\Models\Agent;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CommissionPaidNotification
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
            // Create notification for agent
            $agent->notifications()->create([
                'title' => 'تم دفع العمولة',
                'message' => "تم دفع عمولة بقيمة {$commission->amount} بنجاح. سيتم إضافتها إلى رصيدك.",
                'type' => 'commission_paid',
                'data' => [
                    'commission_id' => $commission->id,
                    'commission_amount' => $commission->amount,
                    'sale_id' => $commission->sale->id,
                    'property_title' => $commission->sale->property->title,
                    'client_name' => $commission->sale->client->name,
                    'payment_date' => now(),
                    'wallet_balance' => $agent->wallet ? $agent->wallet->balance : 0
                ]
            ]);

            // Send email notification
            Mail::to($agent->email)->send(new \App\Mail\CommissionPaidNotificationMail($commission, $agent));

            Log::info('Commission paid notification sent', [
                'agent_id' => $agent->id,
                'commission_id' => $commission->id,
                'amount' => $commission->amount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send commission paid notification', [
                'agent_id' => $agent->id,
                'commission_id' => $commission->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
