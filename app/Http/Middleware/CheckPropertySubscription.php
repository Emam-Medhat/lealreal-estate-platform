<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Property;

class CheckPropertySubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Logic: Check if user has active subscription to post/manage properties
        // Assuming user has 'subscription_status' or similar
        // Or if they have reached their limit of listings

        $limit = $this->getListingLimit($user);
        $currentCount = Property::where('agent_id', $user->id)->count();

        if ($currentCount >= $limit) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Listing limit reached. Please upgrade your subscription.'], 403);
            }
            return redirect()->route('subscription.plans')->with('error', 'Listing limit reached.');
        }

        return $next($request);
    }

    private function getListingLimit($user)
    {
        // Simple logic based on user type or subscription
        // This could be fetched from a SubscriptionPlan model
        if ($user->is_company)
            return 100;
        if ($user->is_developer)
            return 500;
        if ($user->subscription_status === 'premium')
            return 50;
        return 5; // Default free limit
    }
}
