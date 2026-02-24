<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Entry point for all payment webhooks.
     */
    public function handle(Request $request, string $gateway)
    {
        Log::info("Webhook received from {$gateway}", $request->all());

        try {
            $provider = $this->paymentService->getProvider($gateway);

            // 1. Verify Signature
            if (!$provider->verifyWebhook($request)) {
                Log::warning("Invalid webhook signature from {$gateway}");
                return response()->json(['message' => 'Invalid signature'], 400);
            }

            // 2. Normalize Gateway Response
            $data = $provider->handleWebhook($request);

            // 3. Find associated payment
            $payment = Payment::where('gateway_transaction_id', $data['gateway_transaction_id'])->first();

            if (!$payment) {
                Log::error("Payment not found for gateway ref: " . $data['gateway_transaction_id']);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            // 4. Update Payment Status & Finalize if Success
            if ($data['status'] === 'completed' && $payment->status !== 'completed') {
                $this->paymentService->finalizePayment($payment, $data['metadata']);
            } elseif ($data['status'] === 'failed') {
                $payment->update(['status' => 'failed', 'metadata' => $data['metadata']]);
                // Optional: Unlock property here if needed
                $payment->property->update(['status' => 'active']);
            }

            return response()->json(['message' => 'Processed']);

        } catch (\Exception $e) {
            Log::error("Webhook error: " . $e->getMessage());
            return response()->json(['message' => 'Error processing'], 500);
        }
    }
}
