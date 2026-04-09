<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * AI Rate Limiter Middleware
 *
 * Applies per-user (authenticated) or per-IP (guest) rate limits
 * configured in config/ai.php → rate_limits.
 *
 * Also checks monthly AI budget and blocks requests when exceeded.
 */
class AiRateLimitMiddleware
{
    public function handle(Request $request, Closure $next, string $feature = 'search'): Response
    {
        $key = $this->buildKey($request, $feature);
        $limit = $this->getLimit($feature);
        $window = $this->getWindow($feature); // seconds

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'message' => "Too many AI requests. Retry in {$seconds} seconds.",
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($key, $window);

        // Budget guard
        if ($this->monthlyBudgetExceeded()) {
            return response()->json([
                'success' => false,
                'message' => 'AI service temporarily unavailable.',
            ], 503);
        }

        return $next($request);
    }

    private function buildKey(Request $request, string $feature): string
    {
        $id = $request->user()?->id ?? $request->ip();
        return "ai_rate_{$feature}_{$id}";
    }

    private function getLimit(string $feature): int
    {
        return match ($feature) {
            'search' => config('ai.rate_limits.search_per_minute', 20),
            'chat' => config('ai.rate_limits.chat_per_minute', 10),
            'image_analysis' => config('ai.rate_limits.image_analysis_per_hour', 100),
            'price_prediction' => config('ai.rate_limits.price_prediction_per_hour', 200),
            default => 30,
        };
    }

    private function getWindow(string $feature): int
    {
        return match ($feature) {
            'image_analysis', 'price_prediction' => 3600,
            default => 60,
        };
    }

    private function monthlyBudgetExceeded(): bool
    {
        $budget = config('ai.cost_tracking.monthly_budget_usd', 500);
        $threshold = config('ai.cost_tracking.alert_threshold', 0.80);

        $spent = DB::table('ai_usage_logs')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('cost_usd');

        return $spent >= ($budget * $threshold);
    }
}
