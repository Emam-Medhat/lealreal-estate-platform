<?php

namespace App\Services\Payment\Providers;

use App\Models\Payment;
use App\Services\Payment\Interfaces\GatewayInterface;
use Illuminate\Http\Request;

class StripeProvider extends AbstractProvider implements GatewayInterface
{
    public function __construct()
    {
        // Stripe initialization (keys from config)
    }

    public function createPayment(Payment $payment): array
    {
        // Placeholder for Stripe Checkout Session creation
        return [
            'redirect_url' => route('payments.callback', ['success' => 'true']),
            'gateway_reference' => 'stripe_ref_' . uniqid(),
        ];
    }

    public function verifyWebhook(Request $request): bool
    {
        return true;
    }

    public function handleWebhook(Request $request): array
    {
        return [
            'status' => 'completed',
            'gateway_transaction_id' => $request->id,
            'metadata' => [],
        ];
    }

    public function getStatus(string $transactionId): string
    {
        return 'completed';
    }
}
