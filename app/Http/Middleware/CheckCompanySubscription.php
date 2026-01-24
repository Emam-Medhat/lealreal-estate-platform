<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCompanySubscription
{
    public function handle(Request $request, Closure $next, string $plan = null)
    {
        $user = $request->user();
        if (!$user)
            return $next($request);

        $company = $request->route('company');
        if (!($company instanceof \App\Models\Company)) {
            $company = $request->route('company_id') ? \App\Models\Company::find($request->route('company_id')) : $user->company;
        }

        if (!$company) {
            return $next($request);
        }

        if (!$company->hasActiveSubscription()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Subscription expired'], 403);
            }
            return redirect()->route('subscription.index')->with('error', 'Subscription expired.');
        }

        if ($plan && $company->subscription_plan !== $plan) {
            // Basic tier check logic (simplified)
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Plan upgrade required'], 403);
            }
            abort(403, 'Plan upgrade required.');
        }

        return $next($request);
    }
}
