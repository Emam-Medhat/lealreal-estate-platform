<?php

namespace App\Services\Intelligence;

use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Behavior-based recommendation engine (hybrid).
 */
class RecommendationService
{
    /**
     * Build a personalized feed for a user.
     *
     * @param User $user
     * @return array<int,array{property_id:int,match:float}>
     */
    public function feed(User $user): array
    {
        $weights = config('intelligence.recommendations.weights');
        $decayDays = (int) config('intelligence.recommendations.decay_days', 30);

        // Collect behavior signals (replace with your analytics tables if available)
        $events = DB::table('properties')
            ->select([
                'properties.id',
                'properties.price',
                'properties.city',
                'properties.property_type',
                'properties.bedrooms',
                'properties.area',
                DB::raw('properties.views_count as clicks'),
                DB::raw('properties.favorites_count as favorites'),
            ])
            ->where('properties.status', 'active')
            ->get();

        $pref = $this->inferPreferences($user);

        $result = [];
        foreach ($events as $e) {
            $contentScore = $this->contentMatch($e, $pref);
            $behaviorScore = $this->behaviorScore(
                (int) $e->clicks,
                (int) $e->favorites
            );
            $match = $contentScore * $weights['search_match']
                + $behaviorScore * ($weights['clicks'] + $weights['favorites']);
            $result[] = [
                'property_id' => (int) $e->id,
                'match' => round(min(100.0, $match * 100), 2),
            ];
        }

        usort($result, fn ($a, $b) => $b['match'] <=> $a['match']);
        return array_slice($result, 0, 50);
    }

    /**
     * Infer basic preferences from user profile or last searches.
     */
    private function inferPreferences(User $user): array
    {
        // Placeholder: use user's last known city/budget if available
        return [
            'city' => null,
            'min_price' => null,
            'max_price' => null,
            'property_type' => null,
            'bedrooms' => null,
        ];
    }

    /**
     * Simple content-based similarity.
     *
     * @param object $prop
     * @param array $pref
     */
    private function contentMatch(object $prop, array $pref): float
    {
        $score = 0;
        $total = 0;

        if ($pref['city']) {
            $total += 1;
            $score += strtolower($prop->city) === strtolower($pref['city']) ? 1 : 0;
        }
        if ($pref['property_type']) {
            $total += 1;
            $score += $prop->property_type === $pref['property_type'] ? 1 : 0;
        }
        if ($pref['bedrooms']) {
            $total += 1;
            $score += ((int) $prop->bedrooms >= (int) $pref['bedrooms']) ? 1 : 0;
        }
        if ($pref['min_price'] || $pref['max_price']) {
            $total += 1;
            $ok = true;
            if ($pref['min_price'] && $prop->price < $pref['min_price']) $ok = false;
            if ($pref['max_price'] && $prop->price > $pref['max_price']) $ok = false;
            $score += $ok ? 1 : 0;
        }
        return $total > 0 ? $score / $total : 0.5;
    }

    /**
     * Behavior score from clicks and favorites.
     */
    private function behaviorScore(int $clicks, int $favorites): float
    {
        $clicksNorm = min($clicks / 100.0, 1.0);
        $favNorm = min($favorites / 50.0, 1.0);
        return 0.6 * $clicksNorm + 0.4 * $favNorm;
    }
}
