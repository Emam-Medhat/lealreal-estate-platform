<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show checkout page.
     */
    public function show(Property $property)
    {
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('order')->get();

        return view('payments.checkout', compact('property', 'paymentMethods'));
    }

    /**
     * Process checkout and redirect to gateway.
     */
    public function process(Request $request, Property $property)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        try {
            // 1. Initiate Payment Record
            $payment = $this->paymentService->initiatePayment($property, [
                'payment_method_id' => $request->payment_method_id,
            ]);

            // 2. Get Gateway Provider
            $method = PaymentMethod::find($request->payment_method_id);
            $provider = $this->paymentService->getProvider($method->gateway);

            // 3. Request Gateway Session
            $gatewayResponse = $provider->createPayment($payment);

            // 4. Update Payment with Gateway Reference
            $payment->update([
                'gateway_transaction_id' => $gatewayResponse['gateway_reference'],
            ]);

            return redirect($gatewayResponse['redirect_url']);

        } catch (\Exception $e) {
            Log::error("Checkout failed: " . $e->getMessage());
            return back()->with('error', 'Unable to process payment at this time: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment return (Synchronous callback).
     */
    public function callback(Request $request)
    {
        // Usually, we just show a "Processing" or "Success/Fail" page
        // and rely on webhooks for the actual business logic.
        $status = $request->query('success') === 'true' ? 'success' : 'error';

        return view('payments.status', compact('status'));
    }
}
