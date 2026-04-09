<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(): View
    {
        $subscriptions = Subscription::where('user_id', Auth::id())
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('subscription.index', compact('subscriptions'));
    }

    public function current(): View
    {
        $subscription = Auth::user()->subscription;
        
        if (!$subscription) {
            return Redirect::route('subscription.plans')
                ->with('info', 'You don\'t have an active subscription. Choose a plan to get started.');
        }

        return view('subscription.current', compact('subscription'));
    }

    public function history(): View
    {
        $subscriptions = Subscription::where('user_id', Auth::id())
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('subscription.history', compact('subscriptions'));
    }

    public function invoices(): View
    {
        $invoices = Auth::user()->invoices()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('subscription.invoices', compact('invoices'));
    }

    public function subscribe(SubscriptionPlan $plan)
    {
        $user = Auth::user();
        
        if ($user->subscription && $user->subscription->isActive()) {
            return Redirect::back()
                ->with('error', 'You already have an active subscription.');
        }

        // Create subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->duration_in_days),
            'amount' => $plan->price,
        ]);

        return Redirect::route('subscription.payment.process', $subscription)
            ->with('success', 'Subscription created! Please complete the payment.');
    }

    public function upgrade(Request $request)
    {
        $user = Auth::user();
        $currentSubscription = $user->subscription;
        
        if (!$currentSubscription || !$currentSubscription->isActive()) {
            return Redirect::route('subscription.plans')
                ->with('error', 'You need an active subscription to upgrade.');
        }

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        
        if ($plan->price <= $currentSubscription->plan->price) {
            return Redirect::back()
                ->with('error', 'Please select a higher-priced plan to upgrade.');
        }

        // Process upgrade logic
        $currentSubscription->update([
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'status' => 'pending_upgrade',
        ]);

        return Redirect::route('subscription.payment.process', $currentSubscription)
            ->with('success', 'Upgrade initiated! Please complete the payment.');
    }

    public function downgrade(Request $request)
    {
        $user = Auth::user();
        $currentSubscription = $user->subscription;
        
        if (!$currentSubscription || !$currentSubscription->isActive()) {
            return Redirect::route('subscription.plans')
                ->with('error', 'You need an active subscription to downgrade.');
        }

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        
        if ($plan->price >= $currentSubscription->plan->price) {
            return Redirect::back()
                ->with('error', 'Please select a lower-priced plan to downgrade.');
        }

        // Schedule downgrade for next billing cycle
        $currentSubscription->update([
            'pending_plan_id' => $plan->id,
            'will_downgrade_at' => $currentSubscription->ends_at,
        ]);

        return Redirect::back()
            ->with('success', 'Your subscription will be downgraded to ' . $plan->name . ' at the next billing cycle.');
    }

    public function cancel()
    {
        $user = Auth::user();
        $subscription = $user->subscription;
        
        if (!$subscription) {
            return Redirect::back()
                ->with('error', 'No active subscription found.');
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'ends_at' => $subscription->ends_at, // Keep access until period ends
        ]);

        return Redirect::back()
            ->with('success', 'Subscription cancelled. You will continue to have access until ' . $subscription->ends_at->format('M d, Y'));
    }

    public function resume()
    {
        $user = Auth::user();
        $subscription = $user->subscription;
        
        if (!$subscription || $subscription->status !== 'cancelled') {
            return Redirect::back()
                ->with('error', 'No cancelled subscription found to resume.');
        }

        $subscription->update([
            'status' => 'active',
            'cancelled_at' => null,
        ]);

        return Redirect::back()
            ->with('success', 'Subscription resumed successfully!');
    }

    public function adminDashboard(): View
    {
        $stats = [
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'monthly_revenue' => Subscription::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'yearly_revenue' => Subscription::where('status', 'active')
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        return view('subscription.admin.dashboard', compact('stats'));
    }

    public function users(): View
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('subscription.admin.users', compact('subscriptions'));
    }

    public function revenue(): View
    {
        $revenue = Subscription::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->where('status', 'active')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('subscription.admin.revenue', compact('revenue'));
    }

    public function analytics(): View
    {
        $planStats = SubscriptionPlan::withCount(['subscriptions' => function($query) {
                $query->where('status', 'active');
            }])
            ->get();

        return view('subscription.admin.analytics', compact('planStats'));
    }

    public function adminSubscribe(User $user, SubscriptionPlan $plan)
    {
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->duration_in_days),
            'amount' => $plan->price,
            'admin_created' => true,
        ]);

        return Redirect::back()
            ->with('success', 'Subscription created for ' . $user->name);
    }

    public function adminCancel(User $user)
    {
        $subscription = $user->subscription;
        
        if (!$subscription) {
            return Redirect::back()
                ->with('error', 'No subscription found for this user.');
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return Redirect::back()
            ->with('success', 'Subscription cancelled for ' . $user->name);
    }

    public function extendSubscription(Request $request, User $user)
    {
        $subscription = $user->subscription;
        
        if (!$subscription) {
            return Redirect::back()
                ->with('error', 'No subscription found for this user.');
        }

        $days = $request->validate(['days' => 'required|integer|min:1|max:365'])['days'];
        
        $subscription->update([
            'ends_at' => $subscription->ends_at->addDays($days),
        ]);

        return Redirect::back()
            ->with('success', 'Subscription extended by ' . $days . ' days for ' . $user->name);
    }

    public function getStats()
    {
        $user = Auth::user();
        $subscription = $user->subscription;
        
        return response()->json([
            'has_subscription' => $subscription ? true : false,
            'is_active' => $subscription && $subscription->isActive(),
            'plan_name' => $subscription ? $subscription->plan->name : null,
            'ends_at' => $subscription ? $subscription->ends_at->format('Y-m-d') : null,
            'days_left' => $subscription ? max(0, $subscription->ends_at->diffInDays(now())) : 0,
        ]);
    }

    public function getUsage()
    {
        $user = Auth::user();
        $subscription = $user->subscription;
        
        if (!$subscription) {
            return response()->json(['error' => 'No subscription found'], 404);
        }

        return response()->json([
            'properties_listed' => $user->properties()->count(),
            'featured_properties' => $user->properties()->where('is_featured', true)->count(),
            'api_calls' => $user->apiCalls()->whereMonth('created_at', now()->month)->count(),
            'storage_used' => $user->mediaFiles()->sum('size'),
            'plan_limits' => [
                'max_properties' => $subscription->plan->max_properties,
                'max_featured' => $subscription->plan->max_featured_properties,
                'api_calls_limit' => $subscription->plan->api_calls_limit,
                'storage_limit' => $subscription->plan->storage_limit,
            ],
        ]);
    }
}
