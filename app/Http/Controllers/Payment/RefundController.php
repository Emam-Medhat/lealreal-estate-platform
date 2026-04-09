<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\RequestRefundRequest;
use App\Models\Refund;
use App\Models\Payment;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $refunds = Refund::with(['user', 'payment', 'processor'])
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
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.refunds.index', compact('refunds'));
    }

    public function create()
    {
        return view('payments.refunds.create');
    }

    public function store(RequestRefundRequest $request)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($request->payment_id);

            // Check if payment can be refunded
            if ($payment->status !== 'completed') {
                return back()->with('error', 'Only completed payments can be refunded.');
            }

            // Check refund amount doesn't exceed payment amount
            $totalRefunded = $payment->refunds()->where('status', 'completed')->sum('amount');
            $availableRefund = $payment->amount - $totalRefunded;

            if ($request->amount > $availableRefund) {
                return back()->with('error', 'Refund amount exceeds available refund amount.');
            }

            $refund = Refund::create([
                'user_id' => $payment->user_id,
                'payment_id' => $payment->id,
                'reference' => $this->generateRefundReference(),
                'type' => $request->type,
                'amount' => $request->amount,
                'currency' => $payment->currency,
                'reason' => $request->reason,
                'refund_method' => $request->refund_method,
                'processor_id' => Auth::id(),
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Process refund through payment gateway
            $refundResult = $this->processRefund($payment, $refund, $request);

            if ($refundResult['success']) {
                $refund->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $refundResult['transaction_id'],
                    'gateway_response' => $refundResult['response'],
                    'completed_at' => now(),
                ]);

                // Update payment status if fully refunded
                if (($totalRefunded + $request->amount) >= $payment->amount) {
                    $payment->update(['status' => 'refunded']);
                } else {
                    $payment->update(['status' => 'partially_refunded']);
                }

                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'processed_refund',
                    'details' => "Processed refund {$refund->reference} for payment {$payment->reference}",
                    'ip_address' => $request->ip(),
                ]);

                DB::commit();

                return redirect()->route('payments.refunds.show', $refund)
                    ->with('success', 'Refund processed successfully.');
            } else {
                $refund->update([
                    'status' => 'failed',
                    'gateway_response' => $refundResult['response'],
                    'failed_at' => now(),
                ]);

                DB::rollBack();

                return back()->with('error', 'Refund failed: ' . $refundResult['message']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing refund: ' . $e->getMessage());
        }
    }

    public function show(Refund $refund)
    {
        $refund->load(['user', 'payment', 'processor']);
        return view('payments.refunds.show', compact('refund'));
    }

    public function updateStatus(Request $request, Refund $refund): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,failed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $refund->update([
                'status' => $request->status,
                'notes' => $request->notes,
                'updated_by' => Auth::id(),
            ]);

            // Update completion timestamp
            if ($request->status === 'completed') {
                $refund->update(['completed_at' => now()]);
            }

            // Update payment status if needed
            if ($request->status === 'completed') {
                $this->updatePaymentRefundStatus($refund->payment);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_refund_status',
                'details' => "Updated refund {$refund->reference} status to {$request->status}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'status' => $request->status,
                'message' => 'Refund status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating refund: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, Refund $refund): JsonResponse
    {
        if ($refund->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending refunds can be approved'
            ], 400);
        }

        try {
            $refund->update([
                'status' => 'processing',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'approved_refund',
                'details' => "Approved refund {$refund->reference}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund approved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving refund: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, Refund $refund): JsonResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($refund->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending refunds can be rejected'
            ], 400);
        }

        try {
            $refund->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $request->rejection_reason,
                'rejected_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'rejected_refund',
                'details' => "Rejected refund {$refund->reference}: {$request->rejection_reason}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund rejected successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting refund: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request, Refund $refund): JsonResponse
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        if (!in_array($refund->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or processing refunds can be cancelled'
            ], 400);
        }

        try {
            $refund->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'cancelled_refund',
                'details' => "Cancelled refund {$refund->reference}: {$request->cancellation_reason}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling refund: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRefundStats(): JsonResponse
    {
        $stats = [
            'total_refunds' => Refund::count(),
            'pending_refunds' => Refund::where('status', 'pending')->count(),
            'processing_refunds' => Refund::where('status', 'processing')->count(),
            'completed_refunds' => Refund::where('status', 'completed')->count(),
            'failed_refunds' => Refund::where('status', 'failed')->count(),
            'total_refunded_amount' => Refund::where('status', 'completed')->sum('amount'),
            'by_type' => Refund::groupBy('type')
                ->selectRaw('type, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'by_status' => Refund::groupBy('status')
                ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'by_refund_method' => Refund::groupBy('refund_method')
                ->selectRaw('refund_method, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'daily_stats' => Refund::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
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

    public function exportRefunds(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:pending,processing,completed,failed,rejected,cancelled',
            'type' => 'nullable|in:full,partial,dispute',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Refund::with(['user', 'payment', 'processor']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $refunds = $query->get();

        $filename = "refunds_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $refunds,
            'filename' => $filename,
            'message' => 'Refunds exported successfully'
        ]);
    }

    private function generateRefundReference()
    {
        return 'REF-' . strtoupper(uniqid()) . '-' . time();
    }

    private function processRefund($payment, $refund, $request)
    {
        // Mock refund processing - in real implementation, integrate with payment gateway
        return [
            'success' => true,
            'transaction_id' => 'refund_' . uniqid(),
            'response' => ['status' => 'success', 'message' => 'Refund processed'],
            'message' => 'Refund processed successfully'
        ];
    }

    private function updatePaymentRefundStatus($payment)
    {
        $totalRefunded = $payment->refunds()->where('status', 'completed')->sum('amount');
        
        if ($totalRefunded >= $payment->amount) {
            $payment->update(['status' => 'refunded']);
        } elseif ($totalRefunded > 0) {
            $payment->update(['status' => 'partially_refunded']);
        }
    }
}
