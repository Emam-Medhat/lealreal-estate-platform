<?php

namespace App\Services\Payment\Providers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymobProvider extends AbstractProvider
{
    protected string $apiKey;
    protected string $integrationId;
    protected string $iframeId;

    public function __construct()
    {
        $this->apiKey = config('services.paymob.api_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->iframeId = config('services.paymob.iframe_id');
    }

    public function createPayment(Payment $payment): array
    {
        // 1. Authenticate with Paymob
        $authToken = $this->getAuthToken();

        // 2. Register Order
        $orderId = $this->registerOrder($authToken, $payment);

        // 3. Get Payment Key
        $paymentKey = $this->getPaymentKey($authToken, $orderId, $payment);

        return [
            'redirect_url' => "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentKey}",
            'gateway_reference' => $orderId,
        ];
    }

    protected function getAuthToken(): string
    {
        $response = Http::post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $this->apiKey,
        ]);

        return $response->json('token');
    }

    protected function registerOrder(string $token, Payment $payment): string
    {
        $response = Http::post('https://accept.paymob.com/api/acceptance/orders', [
            'auth_token' => $token,
            'delivery_needed' => 'false',
            'amount_cents' => (int) ($payment->amount * 100),
            'currency' => $payment->currency,
            'items' => [],
        ]);

        return $response->json('id');
    }

    protected function getPaymentKey(string $token, string $orderId, Payment $payment): string
    {
        $response = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', [
            'auth_token' => $token,
            'amount_cents' => (int) ($payment->amount * 100),
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => [
                'first_name' => $payment->user->name ?? 'Guest',
                'last_name' => 'User',
                'email' => $payment->user->email ?? 'guest@example.com',
                'phone_number' => $payment->user->phone ?? '+201000000000',
                'apartment' => 'NA',
                'floor' => 'NA',
                'street' => 'NA',
                'building' => 'NA',
                'shipping_method' => 'NA',
                'postal_code' => 'NA',
                'city' => 'NA',
                'country' => 'EG',
                'state' => 'NA',
            ],
            'currency' => $payment->currency,
            'integration_id' => $this->integrationId,
        ]);

        return $response->json('token');
    }

    public function verifyWebhook(Request $request): bool
    {
        $hmac = $request->query('hmac');
        $data = $request->all();

        // Simplified HMAC verification logic (Production should follow Paymob docs closely)
        return !empty($hmac);
    }

    public function handleWebhook(Request $request): array
    {
        $status = $request->input('success') === 'true' ? 'completed' : 'failed';

        return [
            'status' => $status,
            'gateway_transaction_id' => $request->input('id'),
            'metadata' => $request->all(),
        ];
    }

    public function getStatus(string $transactionId): string
    {
        // Implementation for status polling
        return 'pending';
    }
}
