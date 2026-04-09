<?php

namespace App\Console\Commands\Payments;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnlockExpiredPayments extends Command
{
    protected $signature = 'payments:unlock-expired';
    protected $description = 'Unlock properties associated with expired payment sessions';

    public function handle()
    {
        $expiredPayments = Payment::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->with('property')
            ->get();

        if ($expiredPayments->isEmpty()) {
            $this->info('No expired payments found.');
            return;
        }

        foreach ($expiredPayments as $payment) {
            DB::transaction(function () use ($payment) {
                // 1. Update Payment Status
                $payment->update(['status' => 'cancelled']);

                // 2. Unlock Property
                if ($payment->property && $payment->property->status === 'pending_payment') {
                    $payment->property->update(['status' => 'active']);
                    Log::info("Unlocked property {$payment->property->id} due to expired payment {$payment->transaction_id}");
                }
            });
        }

        $this->info("Handled " . $expiredPayments->count() . " expired payments.");
    }
}
