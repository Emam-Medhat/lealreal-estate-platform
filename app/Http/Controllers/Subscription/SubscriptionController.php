<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionInvoice;
use App\Http\Requests\SubscribeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $subscriptions = Subscription::where('user_id', $user->id)
            ->with(['plan', 'invoices', 'usage'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->with(['features', 'tier'])
            ->orderBy('price', 'asc')
            ->get();

        return view('subscriptions.create', compact('plans'));
    }

    public function store(SubscribeRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $plan = SubscriptionPlan::findOrFail($request->plan_id);

            // Check if user already has active subscription
            $activeSubscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($activeSubscription) {
                return redirect()->back()
                    ->with('error', 'You already have an active subscription. Please upgrade or cancel first.');
            }

            // Create subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'pending',
                'starts_at' => now(),
                'ends_at' => now()->addDays($plan->billing_cycle),
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'billing_cycle' => $plan->billing_cycle,
                'auto_renew' => $request->auto_renew ?? true,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending'
            ]);

            // Create initial invoice
            SubscriptionInvoice::create([
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'billing_date' => now(),
                'due_date' => now()->addDays(7),
                'status' => 'pending',
                'description' => "Initial subscription to {$plan->name}"
            ]);

            DB::commit();

            // Process payment (redirect to payment gateway)
            return redirect()->route('subscriptions.payment', $subscription->id)
                ->with('success', 'Subscription created successfully. Please complete payment.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    public function show(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $subscription->load(['plan.features', 'invoices', 'usage', 'cancellation']);

        return view('subscriptions.show', compact('subscription'));
    }

    public function edit(Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $subscription->load(['plan', 'usage']);

        return view('subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $validated = $request->validate([
            'auto_renew' => 'boolean',
            'payment_method' => 'string|max:255'
        ]);

        $subscription->update($validated);

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Subscription updated successfully.');
    }

    public function destroy(Subscription $subscription)
    {
        $this->authorize('delete', $subscription);

        try {
            DB::beginTransaction();

            // Cancel subscription
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'auto_renew' => false
            ]);

            // Create cancellation record
            $subscription->cancellation()->create([
                'user_id' => $subscription->user_id,
                'reason' => request('reason', 'User requested cancellation'),
                'effective_date' => $subscription->ends_at,
                'refund_eligible' => $subscription->isRefundEligible(),
                'refund_amount' => $subscription->calculateRefundAmount()
            ]);

            DB::commit();

            return redirect()->route('subscriptions.index')
                ->with('success', 'Subscription cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    public function payment(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $subscription->load(['plan', 'invoices' => function($query) {
            $query->where('status', 'pending')->first();
        }]);

        return view('subscriptions.payment', compact('subscription'));
    }

    public function processPayment(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        try {
            // Process payment based on payment method
            $paymentResult = $this->processPaymentMethod($request, $subscription);

            if ($paymentResult['success']) {
                $subscription->update([
                    'status' => 'active',
                    'payment_status' => 'paid',
                    'activated_at' => now()
                ]);

                // Update invoice
                $subscription->invoices()->where('status', 'pending')->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payment_method' => $request->payment_method
                ]);

                return redirect()->route('subscriptions.show', $subscription)
                    ->with('success', 'Payment processed successfully!');
            } else {
                return redirect()->back()
                    ->with('error', $paymentResult['message']);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    public function renew(Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        try {
            DB::beginTransaction();

            $plan = $subscription->plan;
            $newEndDate = now()->addDays($plan->billing_cycle);

            // Create renewal record
            $subscription->renewals()->create([
                'user_id' => $subscription->user_id,
                'plan_id' => $plan->id,
                'old_ends_at' => $subscription->ends_at,
                'new_ends_at' => $newEndDate,
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'status' => 'pending'
            ]);

            // Update subscription
            $subscription->update([
                'ends_at' => $newEndDate,
                'status' => 'active'
            ]);

            // Create renewal invoice
            SubscriptionInvoice::create([
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'billing_date' => now(),
                'due_date' => now()->addDays(7),
                'status' => 'pending',
                'description' => "Renewal of {$plan->name}"
            ]);

            DB::commit();

            return redirect()->route('subscriptions.payment', $subscription)
                ->with('success', 'Subscription renewal initiated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to renew subscription: ' . $e->getMessage());
        }
    }

    public function usage(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $usage = $subscription->usage()
            ->with(['feature'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $usageStats = $subscription->getUsageStats();

        return view('subscriptions.usage', compact('subscription', 'usage', 'usageStats'));
    }

    public function invoices(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $invoices = $subscription->invoices()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('subscriptions.invoices', compact('subscription', 'invoices'));
    }

    private function processPaymentMethod(Request $request, Subscription $subscription)
    {
        $paymentMethod = $request->payment_method;

        switch ($paymentMethod) {
            case 'stripe':
                return $this->processStripePayment($request, $subscription);
            case 'paypal':
                return $this->processPayPalPayment($request, $subscription);
            case 'bank_transfer':
                return $this->processBankTransfer($request, $subscription);
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    private function processStripePayment(Request $request, Subscription $subscription)
    {
        // Implement Stripe payment processing
        return ['success' => true, 'message' => 'Stripe payment processed'];
    }

    private function processPayPalPayment(Request $request, Subscription $subscription)
    {
        // Implement PayPal payment processing
        return ['success' => true, 'message' => 'PayPal payment processed'];
    }

    private function processBankTransfer(Request $request, Subscription $subscription)
    {
        // Implement bank transfer processing
        return ['success' => true, 'message' => 'Bank transfer initiated'];
    }

    public function getSubscriptionStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_subscriptions' => Subscription::where('user_id', $user->id)->count(),
            'active_subscriptions' => Subscription::where('user_id', $user->id)->where('status', 'active')->count(),
            'total_spent' => SubscriptionInvoice::where('user_id', $user->id)->where('status', 'paid')->sum('amount'),
            'current_month_spending' => SubscriptionInvoice::where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereMonth('billing_date', now()->month)
                ->sum('amount'),
            'upcoming_renewals' => Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('auto_renew', true)
                ->where('ends_at', '<=', now()->addDays(7))
                ->count()
        ];

        return response()->json($stats);
    }
}
