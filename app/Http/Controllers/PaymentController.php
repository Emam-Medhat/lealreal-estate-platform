<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\ProcessPaymentRequest;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['user', 'paymentMethod', 'invoice'])
            ->when($request->search, function ($query, $search) {
                $query->where('reference_id', 'like', "%{$search}%")
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
            ->latest()
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'paymentMethod', 'invoice', 'transaction']);
        return view('payments.show', compact('payment'));
    }

    public function create()
    {
        $users = User::where('status', 'active')->get(['id', 'name', 'email']);
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $invoices = Invoice::where('status', 'pending')->get(['id', 'invoice_number', 'amount', 'user_id']);
        
        return view('payments.create', compact('users', 'paymentMethods', 'invoices'));
    }

    public function store(ProcessPaymentRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $payment = Payment::create([
                'user_id' => $request->user_id,
                'payment_method_id' => $request->payment_method_id,
                'invoice_id' => $request->invoice_id,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'USD',
                'reference_id' => 'PAY_' . strtoupper(uniqid()),
                'description' => $request->description,
                'status' => 'pending',
                'gateway' => $request->gateway,
                'gateway_transaction_id' => null,
                'gateway_response' => null,
                'fees' => $request->fees ?? 0,
                'net_amount' => $request->amount - ($request->fees ?? 0),
                'due_date' => $request->due_date,
                'paid_at' => null,
                'created_by' => Auth::id(),
            ]);

            // Process payment based on gateway
            $result = $this->processPaymentGateway($payment, $request);

            if ($result['success']) {
                $payment->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $result['transaction_id'],
                    'gateway_response' => $result['response'],
                    'paid_at' => now(),
                ]);

                // Update invoice status if applicable
                if ($payment->invoice) {
                    $this->updateInvoiceStatus($payment->invoice);
                }

                // Create transaction record
                Transaction::create([
                    'user_id' => $payment->user_id,
                    'type' => 'payment',
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'reference_id' => $payment->reference_id,
                    'description' => $payment->description,
                    'status' => 'completed',
                    'payment_id' => $payment->id,
                    'created_by' => Auth::id(),
                ]);
            } else {
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => $result['response'],
                ]);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'processed_payment',
                'details' => "Processed payment {$payment->reference_id} for {$payment->amount} {$payment->currency}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            if ($result['success']) {
                return redirect()->route('payments.show', $payment)
                    ->with('success', 'Payment processed successfully.');
            } else {
                return redirect()->route('payments.show', $payment)
                    ->with('error', 'Payment failed: ' . $result['message']);
            }

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process payment: ' . $e->getMessage());
        }
    }

    public function refund(Payment $payment, Request $request)
    {
        $this->authorize('refund', $payment);
        
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'reason' => 'required|string|max:500',
        ]);

        if ($payment->status !== 'completed') {
            return redirect()->back()
                ->with('error', 'Only completed payments can be refunded.');
        }

        DB::beginTransaction();
        
        try {
            $refundAmount = $request->amount;
            
            // Process refund through gateway
            $result = $this->processRefund($payment, $refundAmount);

            if ($result['success']) {
                // Create refund record
                $payment->refunds()->create([
                    'amount' => $refundAmount,
                    'reason' => $request->reason,
                    'status' => 'completed',
                    'gateway_refund_id' => $result['refund_id'],
                    'gateway_response' => $result['response'],
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);

                // Update payment status if fully refunded
                $totalRefunded = $payment->refunds()->sum('amount');
                if ($totalRefunded >= $payment->amount) {
                    $payment->update(['status' => 'refunded']);
                }

                // Create transaction record
                Transaction::create([
                    'user_id' => $payment->user_id,
                    'type' => 'refund',
                    'amount' => $refundAmount,
                    'currency' => $payment->currency,
                    'reference_id' => 'REF_' . strtoupper(uniqid()),
                    'description' => "Refund for payment {$payment->reference_id}: {$request->reason}",
                    'status' => 'completed',
                    'payment_id' => $payment->id,
                    'created_by' => Auth::id(),
                ]);

                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'processed_refund',
                    'details' => "Processed refund of {$refundAmount} {$payment->currency} for payment {$payment->reference_id}",
                    'ip_address' => $request->ip(),
                ]);

                DB::commit();

                return redirect()->route('payments.show', $payment)
                    ->with('success', 'Refund processed successfully.');
            } else {
                DB::rollback();
                
                return redirect()->back()
                    ->with('error', 'Refund failed: ' . $result['message']);
            }

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }

    public function processPaymentGateway(Payment $payment, Request $request): array
    {
        // This would integrate with actual payment gateways
        // For now, simulating payment processing
        
        $gateway = $request->gateway ?? 'stripe';
        
        switch ($gateway) {
            case 'stripe':
                return $this->processStripePayment($payment, $request);
            case 'paypal':
                return $this->processPayPalPayment($payment, $request);
            case 'bank_transfer':
                return $this->processBankTransferPayment($payment, $request);
            default:
                return ['success' => false, 'message' => 'Unsupported payment gateway'];
        }
    }

    private function processStripePayment(Payment $payment, Request $request): array
    {
        // Simulate Stripe payment processing
        // In real implementation, you would use Stripe SDK
        
        return [
            'success' => true,
            'transaction_id' => 'ch_' . Str::random(20),
            'response' => 'Payment successful',
        ];
    }

    private function processPayPalPayment(Payment $payment, Request $request): array
    {
        // Simulate PayPal payment processing
        // In real implementation, you would use PayPal SDK
        
        return [
            'success' => true,
            'transaction_id' => 'PAYPAL_' . Str::random(15),
            'response' => 'Payment completed',
        ];
    }

    private function processBankTransferPayment(Payment $payment, Request $request): array
    {
        // Bank transfers are usually manual processes
        return [
            'success' => true,
            'transaction_id' => 'BANK_' . Str::random(10),
            'response' => 'Bank transfer initiated',
        ];
    }

    private function processRefund(Payment $payment, float $amount): array
    {
        // Simulate refund processing
        // In real implementation, you would call the gateway's refund API
        
        return [
            'success' => true,
            'refund_id' => 'REF_' . Str::random(15),
            'response' => 'Refund processed successfully',
        ];
    }

    private function updateInvoiceStatus(Invoice $invoice)
    {
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        
        if ($totalPaid >= $invoice->amount) {
            $invoice->update(['status' => 'paid', 'paid_at' => now()]);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partially_paid']);
        }
    }

    public function getPaymentStats(Request $request): JsonResponse
    {
        $query = Payment::query();

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total_payments' => $query->count(),
            'completed_payments' => $query->where('status', 'completed')->count(),
            'failed_payments' => $query->where('status', 'failed')->count(),
            'refunded_payments' => $query->where('status', 'refunded')->count(),
            'total_amount' => $query->where('status', 'completed')->sum('amount'),
            'total_fees' => $query->where('status', 'completed')->sum('fees'),
            'net_amount' => $query->where('status', 'completed')->sum('net_amount'),
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
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Payment::with(['user', 'paymentMethod']);

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
}
