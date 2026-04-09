<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionUpgrade;
use App\Http\Requests\UpgradeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionUpgradeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $upgrades = SubscriptionUpgrade::where('user_id', $user->id)
            ->with(['oldPlan', 'newPlan', 'subscription'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('subscriptions.upgrades.index', compact('upgrades'));
    }

    public function create(Subscription $subscription)
    {
        $this->authorize('upgrade', $subscription);

        $currentPlan = $subscription->plan;
        $availablePlans = SubscriptionPlan::where('is_active', true)
            ->where('id', '!=', $currentPlan->id)
            ->where('price', '>', $currentPlan->price)
            ->with(['features', 'tier'])
            ->orderBy('price', 'asc')
            ->get();

        return view('subscriptions.upgrades.create', compact('subscription', 'currentPlan', 'availablePlans'));
    }

    public function store(UpgradeRequest $request, Subscription $subscription)
    {
        $this->authorize('upgrade', $subscription);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $currentPlan = $subscription->plan;
            $newPlan = SubscriptionPlan::findOrFail($request->plan_id);

            // Validate upgrade eligibility
            if (!$this->canUpgrade($subscription, $newPlan)) {
                return redirect()->back()
                    ->with('error', 'You cannot upgrade to this plan.');
            }

            // Calculate proration amount
            $prorationAmount = $this->calculateProration($subscription, $newPlan);

            // Create upgrade record
            $upgrade = SubscriptionUpgrade::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'old_plan_id' => $currentPlan->id,
                'new_plan_id' => $newPlan->id,
                'old_price' => $currentPlan->price,
                'new_price' => $newPlan->price,
                'proration_amount' => $prorationAmount,
                'upgrade_type' => $this->getUpgradeType($currentPlan, $newPlan),
                'status' => 'pending',
                'effective_date' => now(),
                'reason' => $request->reason ?? 'User requested upgrade'
            ]);

            // Update subscription
            $subscription->update([
                'plan_id' => $newPlan->id,
                'amount' => $newPlan->price,
                'currency' => $newPlan->currency,
                'upgraded_at' => now()
            ]);

            // Create invoice for proration (if relation exists)
            if (method_exists($upgrade, 'invoices')) {
                $upgrade->invoices()->create([
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'amount' => $prorationAmount,
                    'currency' => $newPlan->currency,
                    'billing_date' => now(),
                    'due_date' => now()->addDays(7),
                    'status' => 'pending',
                    'description' => "Subscription upgrade proration from {$currentPlan->name} to {$newPlan->name}"
                ]);
            }

            DB::commit();

            return redirect()->route('subscriptions.upgrades.show', $upgrade)
                ->with('success', 'Subscription upgrade initiated successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to process upgrade: ' . $e->getMessage());
        }
    }

    public function show(SubscriptionUpgrade $upgrade)
    {
        $this->authorize('view', $upgrade);

        $upgrade->load(['oldPlan', 'newPlan', 'subscription']);

        return view('subscriptions.upgrades.show', compact('upgrade'));
    }

    public function confirmUpgrade(Request $request, SubscriptionUpgrade $upgrade)
    {
        $this->authorize('update', $upgrade);

        try {
            DB::beginTransaction();

            $subscription = $upgrade->subscription;
            $newPlan = $upgrade->newPlan;

            // Process payment for proration
            $paymentResult = $this->processProrationPayment($request, $upgrade);

            if ($paymentResult['success']) {
                // Complete the upgrade
                $subscription->update([
                    'plan_id' => $newPlan->id,
                    'amount' => $newPlan->price,
                    'currency' => $newPlan->currency,
                    'upgraded_at' => now()
                ]);

                $upgrade->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);

                // Update invoice status
                $upgrade->invoices()->where('status', 'pending')->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);

                DB::commit();

                return redirect()->route('subscriptions.show', $subscription)
                    ->with('success', 'Subscription upgraded successfully!');
            } else {
                return redirect()->back()
                    ->with('error', $paymentResult['message']);
            }

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to complete upgrade: ' . $e->getMessage());
        }
    }

    public function cancelUpgrade(Request $request, SubscriptionUpgrade $upgrade)
    {
        $this->authorize('update', $upgrade);

        if ($upgrade->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Cannot cancel completed upgrade.');
        }

        try {
            DB::beginTransaction();

            $subscription = $upgrade->subscription;
            $oldPlan = $upgrade->oldPlan;

            // Revert subscription to old plan
            $subscription->update([
                'plan_id' => $oldPlan->id,
                'amount' => $oldPlan->price,
                'currency' => $oldPlan->currency
            ]);

            // Cancel pending invoices
            $upgrade->invoices()->where('status', 'pending')->update([
                'status' => 'cancelled'
            ]);

            $upgrade->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason ?? 'User requested cancellation'
            ]);

            DB::commit();

            return redirect()->route('subscriptions.show', $subscription)
                ->with('success', 'Upgrade cancelled successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to cancel upgrade: ' . $e->getMessage());
        }
    }

    public function getUpgradeOptions(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $currentPlan = $subscription->plan;
        
        $availablePlans = SubscriptionPlan::where('is_active', true)
            ->where('id', '!=', $currentPlan->id)
            ->where('price', '>', $currentPlan->price)
            ->with(['features'])
            ->orderBy('price', 'asc')
            ->get()
            ->map(function ($plan) use ($subscription, $currentPlan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'currency' => $plan->currency,
                    'proration_amount' => $this->calculateProration($subscription, $plan),
                    'upgrade_type' => $this->getUpgradeType($currentPlan, $plan),
                    'features' => $plan->features->pluck('name'),
                    'can_upgrade' => $this->canUpgrade($subscription, $plan)
                ];
            });

        return response()->json($availablePlans);
    }

    private function canUpgrade(Subscription $subscription, SubscriptionPlan $newPlan)
    {
        // Check if user can upgrade to the new plan
        if ($subscription->status !== 'active') {
            return false;
        }

        if ($newPlan->price <= $subscription->plan->price) {
            return false;
        }

        // Add any additional business rules here
        return true;
    }

    private function calculateProration(Subscription $subscription, SubscriptionPlan $newPlan)
    {
        $currentPlan = $subscription->plan;
        $remainingDays = $subscription->ends_at->diffInDays(now());
        $totalDays = $subscription->created_at->diffInDays($subscription->ends_at);

        if ($remainingDays <= 0 || $totalDays <= 0) {
            return $newPlan->price;
        }

        $dailyRateCurrent = $currentPlan->price / $totalDays;
        $dailyRateNew = $newPlan->price / $totalDays;
        $remainingValue = $dailyRateCurrent * $remainingDays;
        $newValue = $dailyRateNew * $remainingDays;

        return max(0, $newValue - $remainingValue);
    }

    private function getUpgradeType(SubscriptionPlan $currentPlan, SubscriptionPlan $newPlan)
    {
        if ($newPlan->tier_id > $currentPlan->tier_id) {
            return 'tier_upgrade';
        } elseif ($newPlan->price > $currentPlan->price) {
            return 'feature_upgrade';
        } else {
            return 'custom_upgrade';
        }
    }

    private function processProrationPayment(Request $request, SubscriptionUpgrade $upgrade)
    {
        $paymentMethod = $request->payment_method ?? 'stripe';

        switch ($paymentMethod) {
            case 'stripe':
                return $this->processStripePayment($request, $upgrade);
            case 'paypal':
                return $this->processPayPalPayment($request, $upgrade);
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    private function processStripePayment(Request $request, SubscriptionUpgrade $upgrade)
    {
        // Implement Stripe payment processing for proration
        return ['success' => true, 'message' => 'Stripe payment processed'];
    }

    private function processPayPalPayment(Request $request, SubscriptionUpgrade $upgrade)
    {
        // Implement PayPal payment processing for proration
        return ['success' => true, 'message' => 'PayPal payment processed'];
    }

    public function getUpgradeHistory()
    {
        $user = Auth::user();
        
        $history = SubscriptionUpgrade::where('user_id', $user->id)
            ->with(['oldPlan', 'newPlan'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($upgrade) {
                return [
                    'id' => $upgrade->id,
                    'old_plan' => $upgrade->oldPlan->name,
                    'new_plan' => $upgrade->newPlan->name,
                    'upgrade_type' => $upgrade->upgrade_type,
                    'proration_amount' => $upgrade->proration_amount,
                    'status' => $upgrade->status,
                    'created_at' => $upgrade->created_at,
                    'completed_at' => $upgrade->completed_at
                ];
            });

        return response()->json($history);
    }
}
