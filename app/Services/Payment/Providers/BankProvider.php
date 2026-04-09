<?php

namespace App\Services\Payment\Providers;

use App\Models\Payment;
use App\Services\Payment\Interfaces\GatewayInterface;
use Illuminate\Http\Request;

class BankProvider extends AbstractProvider implements GatewayInterface
{
    public function createPayment(Payment $payment): array
    {
        // For bank transfers, we just redirect to a page with bank details
        // or the success callback with a 'pending' status.
        return [
            'redirect_url' => route('payments.callback', ['success' => 'true', 'method' => 'bank']),
            'gateway_reference' => 'bank_ref_' . strtoupper(uniqid()),
        ];
    }

    public function verifyWebhook(Request $request): bool
    {
        return true;
    }

    public function handleWebhook(Request $request): array
    {
        return [
            'status' => 'pending',
            'gateway_transaction_id' => $request->id,
            'metadata' => $request->all(),
        ];
    }

    public function getStatus(string $transactionId): string
    {
        return 'pending';
    }
}
