<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Property;
use App\Models\PaymentMethod;
use App\Services\Payment\Interfaces\GatewayInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Initialize a payment for a property.
     */
    public function initiatePayment(Property $property, array $data): Payment
    {
        return DB::transaction(function () use ($property, $data) {
            // 1. Check if property is already locked/sold
            if ($property->status !== 'active') {
                throw new \Exception('Property is no longer available.');
            }

            // 2. Lock property temporarily (Simplified logic)
            $property->update(['status' => 'pending_payment']);

            // 3. Create Payment Record
            $payment = Payment::create([
                'user_id' => auth()->id(),
                'property_id' => $property->id,
                'payment_method_id' => $data['payment_method_id'],
                'transaction_id' => 'TRX-' . strtoupper(Str::random(12)),
                'idempotency_key' => $data['idempotency_key'] ?? Str::uuid(),
                'amount' => $property->price,
                'currency' => $property->currency ?? 'SAR',
                'status' => 'pending',
                'type' => $property->listing_type === 'sale' ? 'payment' : 'deposit',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'expires_at' => now()->addMinutes(15), // Lock for 15 mins
            ]);

            return $payment;
        });
    }

    /**
     * Resolve the appropriate gateway provider.
     */
    public function getProvider(string $gateway): GatewayInterface
    {
        return match ($gateway) {
            'paymob' => app(\App\Services\Payment\Providers\PaymobProvider::class),
            'stripe' => app(\App\Services\Payment\Providers\StripeProvider::class),
            'bank' => app(\App\Services\Payment\Providers\BankProvider::class),
            default => throw new \Exception("Unsupported gateway: {$gateway}"),
        };
    }

    /**
     * Finalize payment after successful verification.
     */
    public function finalizePayment(Payment $payment, array $metadata = []): void
    {
        DB::transaction(function () use ($payment, $metadata) {
            $payment->update([
                'status' => 'completed',
                'processed_at' => now(),
                'metadata' => array_merge($payment->metadata ?? [], $metadata),
            ]);

            // Update Property Status
            $propertyStatus = $payment->type === 'payment' ? 'sold' : 'rented';
            $payment->property->update([
                'status' => $propertyStatus,
                'sold_date' => now(),
            ]);

            // Trigger Notifications/Emails here
            \Illuminate\Support\Facades\Mail::to($payment->user->email)
                ->send(new \App\Mail\PaymentInvoiceMail($payment));

            Log::info("Payment {$payment->transaction_id} finalized successfully and invoice sent.");
        });
    }
}
