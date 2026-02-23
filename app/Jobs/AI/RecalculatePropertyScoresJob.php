<?php

namespace App\Jobs\AI;

use App\Services\AI\RecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Recalculate and pre-warm recommendation caches for all active users
 * who have shown activity in the past 30 days.
 *
 * Dispatched by the scheduler: app/Console/Kernel.php → hourly
 */
class RecalculatePropertyScoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 600; // 10 min

    public function handle(RecommendationService $service): void
    {
        $activeUsers = DB::table('property_views')
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct()
            ->pluck('user_id')
            ->filter(); // remove nulls (anonymous)

        Log::info('RecalculatePropertyScoresJob started', ['user_count' => $activeUsers->count()]);

        foreach ($activeUsers as $userId) {
            try {
                // Flush stale user-profile cache
                Cache::forget("user_profile_{$userId}");

                // Recompute and cache scores
                $scores = $service->computeScores((int) $userId);

                // The computeScores method caches internally, but we also
                // store the pre-warmed result under the recommendation key
                Cache::put(
                    "recs_user_{$userId}",
                    $scores,
                    config('ai.recommendations.cache_ttl', 3600)
                );

            } catch (\Throwable $e) {
                Log::warning("RecalcScores failed for user {$userId}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('RecalculatePropertyScoresJob completed');
    }
}
