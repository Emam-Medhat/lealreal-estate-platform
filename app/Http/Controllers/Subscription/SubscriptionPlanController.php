<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionFeature;
use App\Models\SubscriptionTier;
use App\Models\Subscription;
use App\Models\SubscriptionUpgrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::with(['features', 'tier', 'subscriptions'])
            ->orderBy('price', 'asc')
            ->paginate(15);

        return view('subscriptions.plans.index', compact('plans'));
    }

    public function publicIndex()
    {
        try {
            $plans = SubscriptionPlan::with(['tier', 'features'])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('price', 'asc')
                ->get();

            return view('subscriptions.plans.public', compact('plans'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'Please login to subscribe to a plan.');
        }

        try {
            $user = auth()->user();
            
            // Check if user already has an active subscription
            $activeSubscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($activeSubscription) {
                return back()->with('error', 'You already have an active subscription. Please cancel it first or upgrade your plan.');
            }

            // Create new subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'pending',
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'billing_cycle' => $plan->billing_cycle,
                'auto_renew' => true,
                'payment_method' => 'stripe', // Default payment method
                'payment_status' => 'pending',
            ]);

            // Redirect to payment page
            return redirect()->route('subscriptions.payment', $subscription)
                ->with('success', 'Subscription created! Please complete the payment.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    public function upgrade(Request $request, SubscriptionPlan $plan)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'Please login to upgrade your plan.');
        }

        try {
            $user = auth()->user();
            $currentSubscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (!$currentSubscription) {
                return redirect()->route('subscriptions.plans.public')
                    ->with('error', 'You need an active subscription to upgrade.');
            }

            if ($plan->price <= $currentSubscription->plan->price) {
                return back()->with('error', 'Please select a higher-priced plan to upgrade.');
            }

            // Create upgrade record
            $upgrade = SubscriptionUpgrade::create([
                'subscription_id' => $currentSubscription->id,
                'from_plan_id' => $currentSubscription->plan_id,
                'to_plan_id' => $plan->id,
                'status' => 'pending',
                'price_difference' => $plan->price - $currentSubscription->plan->price,
            ]);

            return redirect()->route('subscriptions.payment.upgrade', $upgrade)
                ->with('success', 'Upgrade initiated! Please complete the payment.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process upgrade: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $tiers = SubscriptionTier::where('is_active', true)->get();
        $features = SubscriptionFeature::where('is_active', true)->get();

        return view('subscriptions.plans.create', compact('tiers', 'features'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'tier_id' => 'required|exists:subscription_tiers,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|integer|min:1',
            'billing_cycle_unit' => 'required|in:day,month,year',
            'trial_days' => 'nullable|integer|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'max_users' => 'nullable|integer|min:1',
            'storage_limit' => 'nullable|integer|min:0',
            'bandwidth_limit' => 'nullable|integer|min:0',
            'api_calls_limit' => 'nullable|integer|min:0',
            'features' => 'array',
            'features.*' => 'exists:subscription_features,id',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            $plan = SubscriptionPlan::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'tier_id' => $validated['tier_id'],
                'price' => $validated['price'],
                'currency' => $validated['currency'],
                'billing_cycle' => $validated['billing_cycle'],
                'billing_cycle_unit' => $validated['billing_cycle_unit'],
                'trial_days' => $validated['trial_days'] ?? 0,
                'setup_fee' => $validated['setup_fee'] ?? 0,
                'max_users' => $validated['max_users'] ?? 1,
                'storage_limit' => $validated['storage_limit'] ?? 0,
                'bandwidth_limit' => $validated['bandwidth_limit'] ?? 0,
                'api_calls_limit' => $validated['api_calls_limit'] ?? 0,
                'is_popular' => $validated['is_popular'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => $validated['sort_order'] ?? 0
            ]);

            // Attach features
            if (!empty($validated['features'])) {
                $plan->features()->attach($validated['features']);
            }

            DB::commit();

            return redirect()->route('subscriptions.plans.show', $plan)
                ->with('success', 'Subscription plan created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create subscription plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(SubscriptionPlan $plan)
    {
        $plan->load(['features', 'tier', 'subscriptions' => function($query) {
            $query->where('status', 'active')->count();
        }]);

        $stats = [
            'active_subscriptions' => $plan->subscriptions()->where('status', 'active')->count(),
            'total_subscriptions' => $plan->subscriptions()->count(),
            'total_revenue' => $plan->invoices()->where('status', 'paid')->sum('amount'),
            'monthly_revenue' => $plan->invoices()
                ->where('status', 'paid')
                ->whereMonth('billing_date', now()->month)
                ->sum('amount')
        ];

        return view('subscriptions.plans.show', compact('plan', 'stats'));
    }

    public function edit(SubscriptionPlan $plan)
    {
        $plan->load('features');
        $tiers = SubscriptionTier::where('is_active', true)->get();
        $features = SubscriptionFeature::where('is_active', true)->get();

        return view('subscriptions.plans.edit', compact('plan', 'tiers', 'features'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'tier_id' => 'required|exists:subscription_tiers,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|integer|min:1',
            'billing_cycle_unit' => 'required|in:day,month,year',
            'trial_days' => 'nullable|integer|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'max_users' => 'nullable|integer|min:1',
            'storage_limit' => 'nullable|integer|min:0',
            'bandwidth_limit' => 'nullable|integer|min:0',
            'api_calls_limit' => 'nullable|integer|min:0',
            'features' => 'array',
            'features.*' => 'exists:subscription_features,id',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            $plan->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'tier_id' => $validated['tier_id'],
                'price' => $validated['price'],
                'currency' => $validated['currency'],
                'billing_cycle' => $validated['billing_cycle'],
                'billing_cycle_unit' => $validated['billing_cycle_unit'],
                'trial_days' => $validated['trial_days'] ?? 0,
                'setup_fee' => $validated['setup_fee'] ?? 0,
                'max_users' => $validated['max_users'] ?? 1,
                'storage_limit' => $validated['storage_limit'] ?? 0,
                'bandwidth_limit' => $validated['bandwidth_limit'] ?? 0,
                'api_calls_limit' => $validated['api_calls_limit'] ?? 0,
                'is_popular' => $validated['is_popular'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => $validated['sort_order'] ?? 0
            ]);

            // Sync features
            if (!empty($validated['features'])) {
                $plan->features()->sync($validated['features']);
            } else {
                $plan->features()->detach();
            }

            DB::commit();

            return redirect()->route('subscriptions.plans.show', $plan)
                ->with('success', 'Subscription plan updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update subscription plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(SubscriptionPlan $plan)
    {
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete plan with active subscriptions.');
        }

        try {
            DB::beginTransaction();

            $plan->features()->detach();
            $plan->delete();

            DB::commit();

            return redirect()->route('subscriptions.plans.index')
                ->with('success', 'Subscription plan deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete subscription plan: ' . $e->getMessage());
        }
    }

    public function duplicate(SubscriptionPlan $plan)
    {
        try {
            DB::beginTransaction();

            $newPlan = $plan->replicate();
            $newPlan->name = $plan->name . ' (Copy)';
            $newPlan->slug = Str::slug($newPlan->name);
            $newPlan->is_active = false;
            $newPlan->save();

            // Copy features
            $newPlan->features()->attach($plan->features->pluck('id'));

            DB::commit();

            return redirect()->route('subscriptions.plans.edit', $newPlan)
                ->with('success', 'Subscription plan duplicated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to duplicate subscription plan: ' . $e->getMessage());
        }
    }

    public function toggleStatus(SubscriptionPlan $plan)
    {
        $plan->update([
            'is_active' => !$plan->is_active
        ]);

        $status = $plan->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Subscription plan {$status} successfully.");
    }

    public function getPlanStats()
    {
        $plans = SubscriptionPlan::with(['subscriptions', 'invoices'])->get();

        $stats = $plans->map(function ($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'active_subscriptions' => $plan->subscriptions()->where('status', 'active')->count(),
                'total_subscriptions' => $plan->subscriptions()->count(),
                'total_revenue' => $plan->invoices()->where('status', 'paid')->sum('amount'),
                'monthly_revenue' => $plan->invoices()
                    ->where('status', 'paid')
                    ->whereMonth('billing_date', now()->month)
                    ->sum('amount')
            ];
        });

        return response()->json($stats);
    }

    public function comparePlans()
    {
        $plans = SubscriptionPlan::with(['features', 'tier'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price', 'asc')
            ->get();

        return view('subscriptions.plans.compare', compact('plans'));
    }
}
