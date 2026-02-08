<?php

namespace App\Http\Controllers\Api;

use App\Services\PaymentService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Payment;
use App\Models\Invoice;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected InvoiceService $invoiceService;

    public function __construct(PaymentService $paymentService, InvoiceService $invoiceService)
    {
        $this->paymentService = $paymentService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display a listing of payments.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $payments = $this->paymentService->generatePaymentReport($filters);
            
            return response()->json([
                'success' => true,
                'data' => $payments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'payment_gateway' => 'nullable|string',
                'description' => 'nullable|string',
                'notes' => 'nullable|string',
                'client_id' => 'nullable|exists:clients,id',
                'agent_id' => 'nullable|exists:agents,id',
                'company_id' => 'nullable|exists:companies,id',
                'property_id' => 'nullable|exists:properties,id',
            ]);

            $payment = $this->paymentService->createPayment($data);
            
            return response()->json([
                'success' => true,
                'data' => $payment->load(['client', 'property', 'company', 'invoice']),
                'message' => 'Payment created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment): JsonResponse
    {
        try {
            $payment->load([
                'client',
                'property', 
                'company',
                'agent',
                'invoice',
                'documents'
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, Payment $payment): JsonResponse
    {
        try {
            $data = $request->validate([
                'amount' => 'sometimes|numeric|min:0',
                'payment_method' => 'sometimes|string',
                'payment_gateway' => 'sometimes|string',
                'description' => 'sometimes|string',
                'notes' => 'sometimes|string',
                'client_id' => 'sometimes|exists:clients,id',
                'agent_id' => 'sometimes|exists:agents,id',
                'company_id' => 'sometimes|exists:companies,id',
                'property_id' => 'sometimes|exists:properties,id',
            ]);

            $updatedPayment = $this->paymentService->updatePayment($payment, $data);
            
            return response()->json([
                'success' => true,
                'data' => $updatedPayment->load(['client', 'property', 'company', 'invoice']),
                'message' => 'Payment updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified payment.
     */
    public function destroy(Payment $payment): JsonResponse
    {
        try {
            $this->paymentService->deletePayment($payment);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve a payment.
     */
    public function approve(Request $request, Payment $payment): JsonResponse
    {
        try {
            $data = $request->validate([
                'notes' => 'nullable|string',
            ]);

            $this->paymentService->approvePayment($payment, auth()->user(), $data['notes'] ?? '');
            
            return response()->json([
                'success' => true,
                'message' => 'Payment approved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject a payment.
     */
    public function reject(Request $request, Payment $payment): JsonResponse
    {
        try {
            $data = $request->validate([
                'reason' => 'required|string',
            ]);

            $this->paymentService->rejectPayment($payment, auth()->user(), $data['reason']);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment rejected successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process payment completion.
     */
    public function complete(Request $request, Payment $payment): JsonResponse
    {
        try {
            $data = $request->validate([
                'gateway_data' => 'nullable|array',
            ]);

            $this->paymentService->processPaymentCompletion($payment, $data['gateway_data'] ?? []);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment completed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process payment failure.
     */
    public function fail(Request $request, Payment $payment): JsonResponse
    {
        try {
            $data = $request->validate([
                'reason' => 'required|string',
                'code' => 'nullable|string',
            ]);

            $this->paymentService->processPaymentFailure($payment, $data['reason'], $data['code'] ?? '');
            
            return response()->json([
                'success' => true,
                'message' => 'Payment failure processed',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process a refund.
     */
    public function refund(Request $request, Payment $payment): JsonResponse
    {
        try {
            $data = $request->validate([
                'amount' => 'required|numeric|min:0',
                'reason' => 'required|string',
            ]);

            $this->paymentService->processRefund($payment, $data['amount'], $data['reason']);
            
            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify a payment.
     */
    public function verify(Payment $payment): JsonResponse
    {
        try {
            $this->paymentService->verifyPayment($payment, auth()->user());
            
            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Flag payment as suspicious.
     */
    public function flagSuspicious(Request $request, Payment $payment): JsonResponse
    {
        try {
            $data = $request->validate([
                'reason' => 'required|string',
            ]);

            $this->paymentService->flagAsSuspicious($payment, $data['reason']);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment flagged as suspicious',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get payment statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $statistics = $this->paymentService->getPaymentStatistics($filters);
            
            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get high-risk payments.
     */
    public function highRisk(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $payments = $this->paymentService->getHighRiskPayments($filters);
            
            return response()->json([
                'success' => true,
                'data' => $payments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get pending payments.
     */
    public function pending(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $payments = $this->paymentService->getPendingPayments($filters);
            
            return response()->json([
                'success' => true,
                'data' => $payments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create recurring payments.
     */
    public function createRecurring(): JsonResponse
    {
        try {
            $payments = $this->paymentService->createRecurringPayments();
            
            return response()->json([
                'success' => true,
                'data' => $payments,
                'message' => 'Recurring payments created successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process pending payments.
     */
    public function processPending(): JsonResponse
    {
        try {
            $payments = $this->paymentService->processPendingPayments();
            
            return response()->json([
                'success' => true,
                'data' => $payments,
                'message' => 'Pending payments processed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get client payment history.
     */
    public function clientHistory(Request $request, $clientId): JsonResponse
    {
        try {
            $client = \App\Models\Client::findOrFail($clientId);
            $filters = $request->all();
            $payments = $this->paymentService->getClientPaymentHistory($client, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $payments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get property payment history.
     */
    public function propertyHistory(Request $request, $propertyId): JsonResponse
    {
        try {
            $property = \App\Models\Property::findOrFail($propertyId);
            $filters = $request->all();
            $payments = $this->paymentService->getPropertyPaymentHistory($property, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $payments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
