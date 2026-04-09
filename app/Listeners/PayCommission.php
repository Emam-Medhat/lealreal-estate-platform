<?php

namespace App\Listeners;

use App\Events\CommissionEarned;
use App\Models\Agent;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class PayCommission
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
            // Create payment record
            $payment = $agent->payments()->create([
                'type' => 'commission',
                'amount' => $commission->amount,
                'commission_id' => $commission->id,
                'payment_date' => now(),
                'payment_method' => 'wallet',
                'status' => 'paid',
                'paid_by' => $earnedBy->id
            ]);

            // Update commission status
            $commission->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_id' => $payment->id
            ]);

            // Send payment confirmation email
            Mail::to($agent->email)->send(new \App\Mail\CommissionPaymentMail($commission, $payment));

            // Create notification for agent
            $agent->notifications()->create([
                'title' => 'دفعة عمولة',
                'message' => "تم دفع عمولة بقيمة {$commission->amount} لحسابك.",
                'type' => 'commission_paid',
                'data' => [
                    'commission_id' => $commission->id,
                    'commission_amount' => $commission->amount,
                    'sale_id' => $commission->sale->id,
                    'property_title' => $commission->sale->property->title,
                    'client_name' => $commission->sale->client->name,
                    'payment_id' => $payment->id,
                    'paid_at' => now()
                ]
            ]);

            Log::info('Commission paid to agent', [
                'agent_id' => $agent->id,
                'commission_id' => $commission->id,
                'amount' => $commission->amount,
                'payment_id' => $payment->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to pay commission', [
                'agent_id' => $agent->id,
                'commission_id' => $commission->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
