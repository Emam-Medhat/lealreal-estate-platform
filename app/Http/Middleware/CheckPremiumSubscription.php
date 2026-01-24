<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class CheckPremiumSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (! $user || ! $user->is_premium) {
            return $request->expectsJson()
                ? abort(403, 'Premium subscription required.')
                : Redirect::route('subscription.plans')->with('error', 'This feature requires a premium subscription.');
        }

        return $next($request);
    }
}
