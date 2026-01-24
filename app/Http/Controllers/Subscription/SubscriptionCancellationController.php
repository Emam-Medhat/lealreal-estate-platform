<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionCancellation;
use App\Http\Requests\CancelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionCancellationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $cancellations = SubscriptionCancellation::where('user_id', $user->id)
            ->with(['subscription.plan', 'subscription.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('subscriptions.cancellations.index', compact('cancellations'));
    }

    public function create(Subscription $subscription)
    {
        $this->authorize('cancel', $subscription);

        $subscription->load(['plan', 'invoices' => function($query) {
            $query->where('status', 'paid')->get();
        }]);

        // Calculate refund eligibility
        $refundInfo = $this->calculateRefundInfo($subscription);

        return view('subscriptions.cancellations.create', compact('subscription', 'refundInfo'));
    }

    public function store(CancelRequest $request, Subscription $subscription)
    {
        $this->authorize('cancel', $subscription);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $effectiveDate = $this->getEffectiveDate($subscription, $request->effective_date, $request->custom_date);
            
            // Calculate refund amount
            $refundAmount = $this->calculateRefundAmount($subscription, $effectiveDate);
            $refundEligible = $refundAmount > 0;

            // Create cancellation record
            $cancellation = SubscriptionCancellation::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'reason' => $request->reason,
                'cancellation_type' => $request->cancellation_type ?? 'immediate',
                'effective_date' => $effectiveDate,
                'refund_eligible' => $refundEligible,
                'refund_amount' => $refundAmount,
                'refund_method' => $request->refund_method ?? 'original_payment',
                'feedback' => $request->feedback,
                'would_recommend' => $request->would_recommend,
                'alternative_solution' => $request->alternative_solution,
                'status' => 'pending'
            ]);

            // Update subscription
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'auto_renew' => false,
                'ends_at' => $effectiveDate
            ]);

            // Process refund if eligible
            if ($refundEligible && $request->process_refund) {
                $refundResult = $this->performRefundProcessing($subscription, $refundAmount, $request->refund_method);
                
                if ($refundResult['success']) {
                    $cancellation->update([
                        'refund_status' => 'processed',
                        'refund_processed_at' => now(),
                        'refund_transaction_id' => $refundResult['transaction_id']
                    ]);
                }
            }

            // Send cancellation notifications
            $this->sendCancellationNotifications($subscription, $cancellation);

            DB::commit();

            return redirect()->route('subscriptions.cancellations.show', $cancellation)
                ->with('success', 'Subscription cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to cancel subscription: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(SubscriptionCancellation $cancellation)
    {
        $this->authorize('view', $cancellation);

        $cancellation->load(['subscription.plan', 'subscription.user', 'subscription.invoices']);

        return view('subscriptions.cancellations.show', compact('cancellation'));
    }

    public function processRefund(Request $request, SubscriptionCancellation $cancellation)
    {
        $this->authorize('update', $cancellation);

        if (!$cancellation->refund_eligible) {
            return redirect()->back()
                ->with('error', 'This cancellation is not eligible for refund.');
        }

        try {
            $refundResult = $this->performRefundProcessing(
                $cancellation->subscription,
                $cancellation->refund_amount,
                $request->refund_method ?? 'original_payment'
            );

            if ($refundResult['success']) {
                $cancellation->update([
                    'refund_status' => 'processed',
                    'refund_processed_at' => now(),
                    'refund_transaction_id' => $refundResult['transaction_id']
                ]);

                return redirect()->back()
                    ->with('success', 'Refund processed successfully.');
            } else {
                return redirect()->back()
                    ->with('error', $refundResult['message']);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }

    public function reactivate(Request $request, SubscriptionCancellation $cancellation)
    {
        $this->authorize('reactivate', $cancellation);

        if ($cancellation->effective_date < now()) {
            return redirect()->back()
                ->with('error', 'Cannot reactivate expired subscription.');
        }

        try {
            DB::beginTransaction();

            $subscription = $cancellation->subscription;

            // Reactivate subscription
            $subscription->update([
                'status' => 'active',
                'cancelled_at' => null,
                'auto_renew' => true,
                'ends_at' => $subscription->plan->billing_cycle > 0 
                    ? now()->addDays($subscription->plan->billing_cycle)
                    : now()->addMonth()
            ]);

            // Update cancellation status
            $cancellation->update([
                'status' => 'reactivated',
                'reactivated_at' => now()
            ]);

            DB::commit();

            return redirect()->route('subscriptions.show', $subscription)
                ->with('success', 'Subscription reactivated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to reactivate subscription: ' . $e->getMessage());
        }
    }

    private function calculateRefundInfo(Subscription $subscription)
    {
        $paidInvoices = $subscription->invoices()->where('status', 'paid')->get();
        $totalPaid = $paidInvoices->sum('amount');
        
        $daysUsed = $subscription->created_at->diffInDays(now());
        $totalDays = $subscription->created_at->diffInDays($subscription->ends_at);
        $remainingDays = max(0, $totalDays - $daysUsed);
        
        $dailyRate = $totalDays > 0 ? $totalPaid / $totalDays : 0;
        $refundAmount = $dailyRate * $remainingDays;
        
        // Apply refund policy (e.g., 80% of remaining value)
        $refundAmount = $refundAmount * 0.8;
        
        return [
            'total_paid' => $totalPaid,
            'days_used' => $daysUsed,
            'total_days' => $totalDays,
            'remaining_days' => $remainingDays,
            'daily_rate' => $dailyRate,
            'refund_amount' => max(0, $refundAmount),
            'refund_eligible' => $refundAmount > 0,
            'policy_days' => 30 // Days within which full refund is available
        ];
    }

    private function getEffectiveDate(Subscription $subscription, $effectiveDate, $customDate = null)
    {
        switch ($effectiveDate) {
            case 'immediate':
                return now();
            case 'end_of_period':
                return $subscription->ends_at;
            case 'custom':
                return Carbon::parse($customDate);
            default:
                return $subscription->ends_at;
        }
    }

    private function calculateRefundAmount(Subscription $subscription, Carbon $effectiveDate)
    {
        $paidInvoices = $subscription->invoices()->where('status', 'paid')->get();
        $totalPaid = $paidInvoices->sum('amount');
        
        $daysUsed = $subscription->created_at->diffInDays($effectiveDate);
        $totalDays = $subscription->created_at->diffInDays($subscription->ends_at);
        $remainingDays = max(0, $totalDays - $daysUsed);
        
        $dailyRate = $totalDays > 0 ? $totalPaid / $totalDays : 0;
        $refundAmount = $dailyRate * $remainingDays;
        
        // Apply refund policy (80% of remaining value)
        return max(0, $refundAmount * 0.8);
    }

    private function performRefundProcessing(Subscription $subscription, float $amount, string $method)
    {
        // Implement refund processing based on payment method
        switch ($method) {
            case 'stripe':
                return $this->processStripeRefund($subscription, $amount);
            case 'paypal':
                return $this->processPayPalRefund($subscription, $amount);
            case 'bank_transfer':
                return $this->processBankTransferRefund($subscription, $amount);
            default:
                return ['success' => false, 'message' => 'Invalid refund method'];
        }
    }

    private function processStripeRefund(Subscription $subscription, float $amount)
    {
        // Implement Stripe refund logic
        return ['success' => true, 'transaction_id' => 'stripe_refund_' . uniqid()];
    }

    private function processPayPalRefund(Subscription $subscription, float $amount)
    {
        // Implement PayPal refund logic
        return ['success' => true, 'transaction_id' => 'paypal_refund_' . uniqid()];
    }

    private function processBankTransferRefund(Subscription $subscription, float $amount)
    {
        // Implement bank transfer refund logic
        return ['success' => true, 'transaction_id' => 'bank_refund_' . uniqid()];
    }

    private function sendCancellationNotifications(Subscription $subscription, SubscriptionCancellation $cancellation)
    {
        // Send email notifications to user and admin
        // Implementation depends on your notification system
    }

    public function getCancellationStats()
    {
        $stats = [
            'total_cancellations' => SubscriptionCancellation::count(),
            'this_month' => SubscriptionCancellation::whereMonth('created_at', now()->month)->count(),
            'refund_processed' => SubscriptionCancellation::where('refund_status', 'processed')->count(),
            'reactivated' => SubscriptionCancellation::where('status', 'reactivated')->count(),
            'by_reason' => SubscriptionCancellation::groupBy('reason')
                ->selectRaw('reason, count(*) as count')
                ->orderBy('count', 'desc')
                ->get()
        ];

        return response()->json($stats);
    }
}
