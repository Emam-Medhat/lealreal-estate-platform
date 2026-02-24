<?php

namespace App\Services\Payment\Interfaces;

use App\Models\Payment;
use Illuminate\Http\Request;

interface GatewayInterface
{
    /**
     * Create a secure payment session/request.
     */
    public function createPayment(Payment $payment): array;

    /**
     * Verify the webhook signature and validity.
     */
    public function verifyWebhook(Request $request): bool;

    /**
     * Process the webhook payload and return normalized status.
     */
    public function handleWebhook(Request $request): array;

    /**
     * Check transaction status directly from gateway.
     */
    public function getStatus(string $transactionId): string;
}
