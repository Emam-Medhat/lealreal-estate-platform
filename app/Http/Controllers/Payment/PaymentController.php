<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\ProcessPaymentRequest;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['user', 'paymentMethod', 'transaction', 'invoice'])
            ->when($request->search, function ($query, $search) {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->payment_method, function ($query, $method) {
                $query->where('payment_method_id', $method);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        return view('payments.create');
    }

    public function process(ProcessPaymentRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create payment record
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'invoice_id' => $request->invoice_id,
                'payment_method_id' => $request->payment_method_id,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'USD',
                'reference' => $this->generateReference(),
                'description' => $request->description,
                'status' => 'pending',
                'metadata' => $request->metadata ?? [],
                'created_by' => Auth::id(),
            ]);

            // Process payment through gateway
            $gatewayResult = $this->processPaymentGateway($payment, $request);

            if ($gatewayResult['success']) {
                $payment->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $gatewayResult['transaction_id'],
                    'gateway_response' => $gatewayResult['response'],
                    'completed_at' => now(),
                ]);

                // Create transaction record
                Transaction::create([
                    'user_id' => Auth::id(),
                    'payment_id' => $payment->id,
                    'type' => 'payment',
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'status' => 'completed',
                    'reference' => $payment->reference,
                    'description' => $payment->description,
                    'metadata' => $gatewayResult['response'],
                ]);

                // Update invoice status if applicable
                if ($payment->invoice) {
                    $this->updateInvoiceStatus($payment->invoice);
                }

                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'processed_payment',
                    'details' => "Processed payment of {$payment->amount} {$payment->currency}",
                    'ip_address' => $request->ip(),
                ]);

                DB::commit();

                return redirect()->route('payments.show', $payment)
                    ->with('success', 'Payment processed successfully.');
            } else {
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => $gatewayResult['response'],
                    'failed_at' => now(),
                ]);

                DB::rollBack();

                return back()->with('error', 'Payment failed: ' . $gatewayResult['message']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Payment processing error: ' . $e->getMessage());
        }
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'paymentMethod', 'transaction', 'invoice']);
        return view('payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, Payment $payment): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,failed,cancelled,refunded',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_payment_status',
            'details' => "Updated payment {$payment->reference} status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Payment status updated successfully'
        ]);
    }

    public function refund(Request $request, Payment $payment): JsonResponse
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'reason' => 'required|string|max:500',
            'refund_method' => 'required|string|max:100',
        ]);

        if ($payment->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed payments can be refunded'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create refund record
            $refund = $payment->refunds()->create([
                'user_id' => Auth::id(),
                'amount' => $request->refund_amount,
                'reason' => $request->reason,
                'refund_method' => $request->refund_method,
                'status' => 'pending',
                'reference' => $this->generateReference('REF'),
                'created_by' => Auth::id(),
            ]);

            // Process refund through gateway
            $refundResult = $this->processRefundGateway($payment, $refund, $request);

            if ($refundResult['success']) {
                $refund->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $refundResult['transaction_id'],
                    'gateway_response' => $refundResult['response'],
                    'completed_at' => now(),
                ]);

                // Update payment status
                if ($request->refund_amount >= $payment->amount) {
                    $payment->update(['status' => 'refunded']);
                } else {
                    $payment->update(['status' => 'partially_refunded']);
                }

                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'processed_refund',
                    'details' => "Processed refund of {$request->refund_amount} for payment {$payment->reference}",
                    'ip_address' => $request->ip(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'refund' => $refund,
                    'message' => 'Refund processed successfully'
                ]);
            } else {
                $refund->update([
                    'status' => 'failed',
                    'gateway_response' => $refundResult['response'],
                    'failed_at' => now(),
                ]);

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Refund failed: ' . $refundResult['message']
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Refund processing error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPaymentStats(): JsonResponse
    {
        $stats = [
            'total_payments' => Payment::count(),
            'completed_payments' => Payment::where('status', 'completed')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_amount' => Payment::sum('amount'),
            'completed_amount' => Payment::where('status', 'completed')->sum('amount'),
            'by_currency' => Payment::groupBy('currency')
                ->selectRaw('currency, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'by_payment_method' => Payment::with('paymentMethod')
                ->groupBy('payment_method_id')
                ->selectRaw('payment_method_id, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'by_status' => Payment::groupBy('status')
                ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'daily_stats' => Payment::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportPayments(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:pending,processing,completed,failed,cancelled,refunded',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Payment::with(['user', 'paymentMethod', 'transaction']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->get();

        $filename = "payments_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $payments,
            'filename' => $filename,
            'message' => 'Payments exported successfully'
        ]);
    }

    private function generateReference($prefix = 'PAY')
    {
        return $prefix . '-' . strtoupper(uniqid()) . '-' . time();
    }

    private function processPaymentGateway($payment, $request)
    {
        // This would integrate with actual payment gateway (Stripe, PayPal, etc.)
        // For now, return mock success response
        return [
            'success' => true,
            'transaction_id' => 'txn_' . uniqid(),
            'response' => ['status' => 'success', 'message' => 'Payment processed'],
            'message' => 'Payment processed successfully'
        ];
    }

    private function processRefundGateway($payment, $refund, $request)
    {
        // This would integrate with actual payment gateway for refunds
        // For now, return mock success response
        return [
            'success' => true,
            'transaction_id' => 'ref_' . uniqid(),
            'response' => ['status' => 'success', 'message' => 'Refund processed'],
            'message' => 'Refund processed successfully'
        ];
    }

    private function updateInvoiceStatus($invoice)
    {
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        
        if ($totalPaid >= $invoice->total_amount) {
            $invoice->update(['status' => 'paid', 'paid_at' => now()]);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partially_paid']);
        }
    }
}
