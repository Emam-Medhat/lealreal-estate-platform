<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionRenewal;
use App\Models\SubscriptionInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionRenewalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $renewals = SubscriptionRenewal::where('user_id', $user->id)
            ->with(['subscription.plan', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('subscriptions.renewals.index', compact('renewals'));
    }

    public function create(Subscription $subscription)
    {
        $this->authorize('renew', $subscription);

        $subscription->load(['plan', 'invoices']);
        
        // Calculate renewal details
        $renewalDetails = $this->calculateRenewalDetails($subscription);

        return view('subscriptions.renewals.create', compact('subscription', 'renewalDetails'));
    }

    public function store(Request $request, Subscription $subscription)
    {
        $this->authorize('renew', $subscription);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $plan = $subscription->plan;
            
            // Calculate new end date
            $newEndDate = $subscription->ends_at->copy()->addDays($plan->billing_cycle);

            // Create renewal record
            $renewal = SubscriptionRenewal::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'old_ends_at' => $subscription->ends_at,
                'new_ends_at' => $newEndDate,
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'discount_amount' => $request->discount_amount ?? 0,
                'final_amount' => $plan->price - ($request->discount_amount ?? 0),
                'renewal_type' => $request->renewal_type ?? 'manual',
                'auto_renewal' => $request->auto_renewal ?? false,
                'status' => 'pending',
                'notes' => $request->notes
            ]);

            // Update subscription
            $subscription->update([
                'ends_at' => $newEndDate,
                'auto_renew' => $request->auto_renewal ?? $subscription->auto_renew,
                'last_renewed_at' => now()
            ]);

            // Create renewal invoice
            SubscriptionInvoice::create([
                'subscription_id' => $subscription->id,
                'renewal_id' => $renewal->id,
                'user_id' => $user->id,
                'amount' => $renewal->final_amount,
                'currency' => $plan->currency,
                'billing_date' => now(),
                'due_date' => now()->addDays(7),
                'status' => 'pending',
                'description' => "Renewal of {$plan->name} for {$plan->billing_cycle} days"
            ]);

            DB::commit();

            return redirect()->route('subscriptions.renewals.show', $renewal)
                ->with('success', 'Subscription renewal initiated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to initiate renewal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(SubscriptionRenewal $renewal)
    {
        $this->authorize('view', $renewal);

        $renewal->load(['subscription.plan', 'plan', 'invoices']);

        return view('subscriptions.renewals.show', compact('renewal'));
    }

    public function processRenewal(Request $request, SubscriptionRenewal $renewal)
    {
        $this->authorize('update', $renewal);

        try {
            DB::beginTransaction();

            $subscription = $renewal->subscription;

            // Process payment
            $paymentResult = $this->processRenewalPayment($request, $renewal);

            if ($paymentResult['success']) {
                // Complete the renewal
                $subscription->update([
                    'status' => 'active',
                    'ends_at' => $renewal->new_ends_at,
                    'last_renewed_at' => now()
                ]);

                $renewal->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'payment_method' => $request->payment_method
                ]);

                // Update invoice status
                $renewal->invoices()->where('status', 'pending')->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payment_method' => $request->payment_method
                ]);

                DB::commit();

                return redirect()->route('subscriptions.show', $subscription)
                    ->with('success', 'Subscription renewed successfully!');
            } else {
                return redirect()->back()
                    ->with('error', $paymentResult['message']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to complete renewal: ' . $e->getMessage());
        }
    }

    public function cancelRenewal(Request $request, SubscriptionRenewal $renewal)
    {
        $this->authorize('update', $renewal);

        if ($renewal->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Cannot cancel completed renewal.');
        }

        try {
            DB::beginTransaction();

            $subscription = $renewal->subscription;

            // Revert subscription end date
            $subscription->update([
                'ends_at' => $renewal->old_ends_at
            ]);

            // Cancel pending invoices
            $renewal->invoices()->where('status', 'pending')->update([
                'status' => 'cancelled'
            ]);

            $renewal->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason ?? 'User requested cancellation'
            ]);

            DB::commit();

            return redirect()->route('subscriptions.show', $subscription)
                ->with('success', 'Renewal cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to cancel renewal: ' . $e->getMessage());
        }
    }

    public function enableAutoRenewal(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $subscription->update([
            'auto_renew' => true
        ]);

        return redirect()->back()
            ->with('success', 'Auto-renewal enabled successfully.');
    }

    public function disableAutoRenewal(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $subscription->update([
            'auto_renew' => false
        ]);

        return redirect()->back()
            ->with('success', 'Auto-renewal disabled successfully.');
    }

    public function getUpcomingRenewals()
    {
        $user = Auth::user();
        
        $upcomingRenewals = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->where('ends_at', '<=', now()->addDays(30))
            ->with(['plan'])
            ->orderBy('ends_at', 'asc')
            ->get()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'plan_name' => $subscription->plan->name,
                    'ends_at' => $subscription->ends_at,
                    'days_until_renewal' => $subscription->ends_at->diffInDays(now()),
                    'renewal_amount' => $subscription->plan->price,
                    'currency' => $subscription->plan->currency
                ];
            });

        return response()->json($upcomingRenewals);
    }

    public function processAutoRenewals()
    {
        // This would typically be called by a scheduled job
        $subscriptions = Subscription::where('status', 'active')
            ->where('auto_renew', true)
            ->where('ends_at', '<=', now())
            ->with(['plan', 'user'])
            ->get();

        $processed = 0;
        $failed = 0;

        foreach ($subscriptions as $subscription) {
            try {
                DB::beginTransaction();

                $newEndDate = $subscription->ends_at->copy()->addDays($subscription->plan->billing_cycle);

                // Create renewal record
                $renewal = SubscriptionRenewal::create([
                    'user_id' => $subscription->user_id,
                    'subscription_id' => $subscription->id,
                    'plan_id' => $subscription->plan->id,
                    'old_ends_at' => $subscription->ends_at,
                    'new_ends_at' => $newEndDate,
                    'amount' => $subscription->plan->price,
                    'currency' => $subscription->plan->currency,
                    'renewal_type' => 'auto',
                    'auto_renewal' => true,
                    'status' => 'pending'
                ]);

                // Update subscription
                $subscription->update([
                    'ends_at' => $newEndDate,
                    'last_renewed_at' => now()
                ]);

                // Create invoice
                SubscriptionInvoice::create([
                    'subscription_id' => $subscription->id,
                    'renewal_id' => $renewal->id,
                    'user_id' => $subscription->user_id,
                    'amount' => $subscription->plan->price,
                    'currency' => $subscription->plan->currency,
                    'billing_date' => now(),
                    'due_date' => now()->addDays(7),
                    'status' => 'pending',
                    'description' => "Auto-renewal of {$subscription->plan->name}"
                ]);

                // Process payment (this would integrate with your payment system)
                $paymentResult = $this->processAutoRenewalPayment($subscription);

                if ($paymentResult['success']) {
                    $renewal->update([
                        'status' => 'completed',
                        'completed_at' => now()
                    ]);

                    $subscription->update(['status' => 'active']);
                    $processed++;
                } else {
                    $failed++;
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $failed++;
            }
        }

        return response()->json([
            'processed' => $processed,
            'failed' => $failed,
            'total' => $subscriptions->count()
        ]);
    }

    private function calculateRenewalDetails(Subscription $subscription)
    {
        $plan = $subscription->plan;
        $newEndDate = $subscription->ends_at->copy()->addDays($plan->billing_cycle);
        
        return [
            'current_end_date' => $subscription->ends_at,
            'new_end_date' => $newEndDate,
            'renewal_amount' => $plan->price,
            'currency' => $plan->currency,
            'billing_cycle' => $plan->billing_cycle,
            'days_added' => $plan->billing_cycle,
            'eligible_for_discount' => $this->isEligibleForDiscount($subscription),
            'discount_amount' => $this->calculateDiscount($subscription, $plan)
        ];
    }

    private function isEligibleForDiscount(Subscription $subscription)
    {
        // Check if user is eligible for renewal discount
        $monthsActive = $subscription->created_at->diffInMonths(now());
        return $monthsActive >= 12; // 1 year active for discount
    }

    private function calculateDiscount(Subscription $subscription, $plan)
    {
        if ($this->isEligibleForDiscount($subscription)) {
            return $plan->price * 0.1; // 10% discount
        }
        return 0;
    }

    private function processRenewalPayment(Request $request, SubscriptionRenewal $renewal)
    {
        $paymentMethod = $request->payment_method ?? 'stripe';

        switch ($paymentMethod) {
            case 'stripe':
                return $this->processStripePayment($request, $renewal);
            case 'paypal':
                return $this->processPayPalPayment($request, $renewal);
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    private function processAutoRenewalPayment(Subscription $subscription)
    {
        // Process payment using user's default payment method
        // This would integrate with your payment system
        return ['success' => true, 'transaction_id' => 'auto_renewal_' . uniqid()];
    }

    private function processStripePayment(Request $request, SubscriptionRenewal $renewal)
    {
        // Implement Stripe payment processing
        return ['success' => true, 'transaction_id' => 'stripe_renewal_' . uniqid()];
    }

    private function processPayPalPayment(Request $request, SubscriptionRenewal $renewal)
    {
        // Implement PayPal payment processing
        return ['success' => true, 'transaction_id' => 'paypal_renewal_' . uniqid()];
    }

    public function getRenewalStats()
    {
        $stats = [
            'total_renewals' => SubscriptionRenewal::count(),
            'this_month' => SubscriptionRenewal::whereMonth('created_at', now()->month)->count(),
            'completed' => SubscriptionRenewal::where('status', 'completed')->count(),
            'pending' => SubscriptionRenewal::where('status', 'pending')->count(),
            'auto_renewals' => SubscriptionRenewal::where('renewal_type', 'auto')->count(),
            'manual_renewals' => SubscriptionRenewal::where('renewal_type', 'manual')->count(),
            'revenue' => SubscriptionRenewal::where('status', 'completed')->sum('final_amount')
        ];

        return response()->json($stats);
    }
}
