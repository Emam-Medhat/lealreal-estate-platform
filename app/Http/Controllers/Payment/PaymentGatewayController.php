<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\Payment;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PaymentGatewayController extends Controller
{
    public function index(Request $request)
    {
        $gateways = PaymentGateway::when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.gateways.index', compact('gateways'));
    }

    public function create()
    {
        return view('payments.gateways.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:credit_card,bank_transfer,crypto,digital_wallet',
            'provider' => 'required|string|max:100',
            'api_key' => 'required|string|max:500',
            'api_secret' => 'required|string|max:500',
            'webhook_url' => 'nullable|url|max:500',
            'supported_currencies' => 'required|array|min:1',
            'supported_currencies.*' => 'required|string|size:3',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'fees' => 'required|array',
            'fees.fixed' => 'required|numeric|min:0',
            'fees.percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'test_mode' => 'boolean',
            'config' => 'nullable|array',
        ]);

        try {
            $gateway = PaymentGateway::create([
                'name' => $request->name,
                'type' => $request->type,
                'provider' => $request->provider,
                'api_key' => encrypt($request->api_key),
                'api_secret' => encrypt($request->api_secret),
                'webhook_url' => $request->webhook_url,
                'supported_currencies' => $request->supported_currencies,
                'min_amount' => $request->min_amount,
                'max_amount' => $request->max_amount,
                'fees' => $request->fees,
                'is_active' => $request->is_active ?? true,
                'test_mode' => $request->test_mode ?? false,
                'config' => $request->config ?? [],
                'created_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_payment_gateway',
                'details' => "Created payment gateway: {$gateway->name}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('payments.gateways.index')
                ->with('success', 'Payment gateway created successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating payment gateway: ' . $e->getMessage());
        }
    }

    public function show(PaymentGateway $gateway)
    {
        return view('payments.gateways.show', compact('gateway'));
    }

    public function edit(PaymentGateway $gateway)
    {
        return view('payments.gateways.edit', compact('gateway'));
    }

    public function update(Request $request, PaymentGateway $gateway)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:credit_card,bank_transfer,crypto,digital_wallet',
            'provider' => 'required|string|max:100',
            'api_key' => 'nullable|string|max:500',
            'api_secret' => 'nullable|string|max:500',
            'webhook_url' => 'nullable|url|max:500',
            'supported_currencies' => 'required|array|min:1',
            'supported_currencies.*' => 'required|string|size:3',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'fees' => 'required|array',
            'fees.fixed' => 'required|numeric|min:0',
            'fees.percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'test_mode' => 'boolean',
            'config' => 'nullable|array',
        ]);

        try {
            $updateData = [
                'name' => $request->name,
                'type' => $request->type,
                'provider' => $request->provider,
                'webhook_url' => $request->webhook_url,
                'supported_currencies' => $request->supported_currencies,
                'min_amount' => $request->min_amount,
                'max_amount' => $request->max_amount,
                'fees' => $request->fees,
                'is_active' => $request->is_active,
                'test_mode' => $request->test_mode,
                'config' => $request->config ?? [],
                'updated_by' => Auth::id(),
            ];

            if ($request->filled('api_key')) {
                $updateData['api_key'] = encrypt($request->api_key);
            }

            if ($request->filled('api_secret')) {
                $updateData['api_secret'] = encrypt($request->api_secret);
            }

            $gateway->update($updateData);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_payment_gateway',
                'details' => "Updated payment gateway: {$gateway->name}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('payments.gateways.index')
                ->with('success', 'Payment gateway updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating payment gateway: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, PaymentGateway $gateway)
    {
        try {
            $gateway->update([
                'is_active' => false,
                'deleted_at' => now(),
                'deleted_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted_payment_gateway',
                'details' => "Deleted payment gateway: {$gateway->name}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment gateway deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting payment gateway: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testConnection(Request $request, PaymentGateway $gateway): JsonResponse
    {
        try {
            // Test connection based on gateway type
            $result = $this->testGatewayConnection($gateway);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'tested_payment_gateway',
                'details' => "Tested connection for gateway: {$gateway->name} - Result: " . ($result['success'] ? 'Success' : 'Failed'),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'response_time' => $result['response_time'] ?? null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processWebhook(Request $request, $gateway)
    {
        try {
            $paymentGateway = PaymentGateway::where('provider', $gateway)->firstOrFail();
            
            // Verify webhook signature
            if (!$this->verifyWebhookSignature($request, $paymentGateway)) {
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Process webhook based on event type
            $event = $request->get('event');
            $payload = $request->get('data');

            switch ($event) {
                case 'payment.completed':
                    $this->handlePaymentCompleted($payload, $paymentGateway);
                    break;
                case 'payment.failed':
                    $this->handlePaymentFailed($payload, $paymentGateway);
                    break;
                case 'payment.refunded':
                    $this->handlePaymentRefunded($payload, $paymentGateway);
                    break;
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            \Log::error('Webhook processing error: ' . $e->getMessage());
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    public function getActiveGateways(): JsonResponse
    {
        $gateways = PaymentGateway::where('is_active', true)
            ->get()
            ->map(function ($gateway) {
                return [
                    'id' => $gateway->id,
                    'name' => $gateway->name,
                    'type' => $gateway->type,
                    'provider' => $gateway->provider,
                    'supported_currencies' => $gateway->supported_currencies,
                    'min_amount' => $gateway->min_amount,
                    'max_amount' => $gateway->max_amount,
                    'fees' => $gateway->fees,
                ];
            });

        return response()->json([
            'success' => true,
            'gateways' => $gateways
        ]);
    }

    public function getGatewayStats(): JsonResponse
    {
        $stats = [
            'total_gateways' => PaymentGateway::count(),
            'active_gateways' => PaymentGateway::where('is_active', true)->count(),
            'by_type' => PaymentGateway::groupBy('type')
                ->selectRaw('type, COUNT(*) as count')
                ->get(),
            'by_provider' => PaymentGateway::groupBy('provider')
                ->selectRaw('provider, COUNT(*) as count')
                ->get(),
            'test_mode_gateways' => PaymentGateway::where('test_mode', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    private function testGatewayConnection($gateway)
    {
        $startTime = microtime(true);

        try {
            // Mock connection test - in real implementation, this would test actual API
            $response = Http::timeout(10)->get('https://api.example.com/health');
            
            $responseTime = (microtime(true) - $startTime) * 1000;

            return [
                'success' => $response->successful(),
                'message' => $response->successful() ? 'Connection successful' : 'Connection failed',
                'response_time' => round($responseTime, 2),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
                'response_time' => null,
            ];
        }
    }

    private function verifyWebhookSignature($request, $gateway)
    {
        // Mock signature verification - in real implementation, verify with actual gateway
        return true;
    }

    private function handlePaymentCompleted($payload, $gateway)
    {
        $payment = Payment::where('reference', $payload['reference'])->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'completed',
                'gateway_transaction_id' => $payload['transaction_id'],
                'gateway_response' => $payload,
                'completed_at' => now(),
            ]);
        }
    }

    private function handlePaymentFailed($payload, $gateway)
    {
        $payment = Payment::where('reference', $payload['reference'])->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => $payload,
                'failed_at' => now(),
            ]);
        }
    }

    private function handlePaymentRefunded($payload, $gateway)
    {
        $payment = Payment::where('reference', $payload['reference'])->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'refunded',
                'gateway_response' => $payload,
            ]);
        }
    }
}
