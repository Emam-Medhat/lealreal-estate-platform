<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Subscription;
use Carbon\Carbon;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $plan
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $plan = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = Auth::user();

        // Admin can bypass subscription check
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        $subscription = $user->activeSubscription;

        if (!$subscription) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب الاشتراك في باقة لاستخدام هذه الميزة',
                    'code' => 402,
                    'requires_subscription' => true
                ], 402);
            }

            $route = Route::has('subscription.plans') ? 'subscription.plans' : 'home';
            return redirect()->route($route)
                ->with('error', 'يجب الاشتراك في باقة لاستخدام هذه الميزة');
        }

        // Check if subscription is active
        if (!$subscription->isActive()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'اشتراكك غير نشط. يرجى تجديد الاشتراك',
                    'code' => 402,
                    'subscription_expired' => true
                ], 402);
            }

            return redirect()->route('subscription.renew')
                ->with('error', 'اشتراكك غير نشط. يرجى تجديد الاشتراك');
        }

        // Check if specific plan is required
        if ($plan && !$subscription->hasPlan($plan)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الميزة تتطلب باقة ' . $this->getPlanName($plan),
                    'code' => 402,
                    'required_plan' => $plan,
                    'current_plan' => $subscription->plan
                ], 402);
            }

            return redirect()->route('subscription.upgrade')
                ->with('error', 'هذه الميزة تتطلب باقة ' . $this->getPlanName($plan));
        }

        // Check usage limits
        $limitCheck = $this->checkUsageLimits($subscription, $request);
        if ($limitCheck !== true) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $limitCheck,
                    'code' => 429,
                    'limit_reached' => true
                ], 429);
            }

            return redirect()->back()
                ->with('error', $limitCheck);
        }

        // Add subscription info to request
        $request->merge([
            'subscription' => $subscription,
            'plan' => $subscription->plan
        ]);

        return $next($request);
    }

    /**
     * Check usage limits for the subscription
     *
     * @param  \App\Models\Subscription  $subscription
     * @param  \Illuminate\Http\Request  $request
     * @return bool|string
     */
    private function checkUsageLimits($subscription, $request)
    {
        $limits = $subscription->getLimits();
        $usage = $subscription->getCurrentUsage();

        // Check properties limit
        if (isset($limits['properties']) && $usage['properties'] >= $limits['properties']) {
            if ($request->routeIs('properties.*') && $request->isMethod('POST')) {
                return 'لقد وصلت إلى الحد الأقصى للعقارات في باقتك. قم بترقية باقتك لإضافة المزيد';
            }
        }

        // Check API calls limit
        if (isset($limits['api_calls']) && $usage['api_calls'] >= $limits['api_calls']) {
            if ($request->is('api/*')) {
                return 'لقد وصلت إلى الحد الأقصى لطلبات API في باقتك. قم بترقية باقتك أو انتظر حتى الفترة التالية';
            }
        }

        // Check storage limit
        if (isset($limits['storage']) && $usage['storage'] >= $limits['storage']) {
            if ($request->routeIs('documents.*') && $request->hasFile('file')) {
                return 'لقد وصلت إلى الحد الأقصى لمساحة التخزين في باقتك. قم بترقية باقتك للحصول على مساحة أكبر';
            }
        }

        return true;
    }

    /**
     * Get plan name in Arabic
     *
     * @param  string  $plan
     * @return string
     */
    private function getPlanName($plan)
    {
        $plans = [
            'basic' => 'أساسية',
            'professional' => 'احترافية',
            'enterprise' => 'مؤسسات',
            'premium' => 'مميزة'
        ];

        return $plans[$plan] ?? $plan;
    }
}
