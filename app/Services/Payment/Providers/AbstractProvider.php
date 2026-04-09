<?php

namespace App\Services\Payment\Providers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Services\Payment\Interfaces\GatewayInterface;

abstract class AbstractProvider implements GatewayInterface
{
    protected function logTransaction(Payment $payment, string $status, array $payload = [], ?string $errorCode = null, ?string $errorMessage = null): Transaction
    {
        return Transaction::create([
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'type' => $payment->type,
            'status' => $status,
            'gateway_reference' => $payload['id'] ?? $payload['transaction_id'] ?? null,
            'payload' => $payload,
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
        ]);
    }
}
