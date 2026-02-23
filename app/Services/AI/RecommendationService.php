<?php

namespace App\Services\AI;

use App\Models\Property;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Property Recommendation Engine
 *
 * Hybrid scoring model:
 *   40%  – Price similarity
 *   30%  – Location similarity
 *   15%  – Bedroom match
 *   15%  – Behavioral preference score
 *
 * Scores are pre-computed by RecalculatePropertyScoresJob and stored in Redis.
 * The public recommend() method reads from cache; on cache-miss it computes live.
 */
class RecommendationService
{
    // Scoring weights
    private const W_PRICE = 0.40;
    private const W_LOCATION = 0.30;
    private const W_BEDS = 0.15;
    private const W_BEHAVIOR = 0.15;

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Return top-N recommended property IDs with scores and explanations
     * for the given user.
     *
     * @return array{items: array, source: string}
     */
    public function recommend(int $userId, int $topN = 5): array
    {
        $cacheKey = "recs_user_{$userId}";

        $cached = Cache::get($cacheKey);
        if ($cached) {
            return ['items' => array_slice($cached, 0, $topN), 'source' => 'cache'];
        }

        $scores = $this->computeScores($userId);
        Cache::put($cacheKey, $scores, config('ai.recommendations.cache_ttl', 3600));

        return ['items' => array_slice($scores, 0, $topN), 'source' => 'live'];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Core scoring
    // ─────────────────────────────────────────────────────────────────────────

    public function computeScores(int $userId): array
    {
        $profile = $this->buildUserProfile($userId);

        if (empty($profile)) {
            return [];
        }

        // Fetch candidate properties (exclude already viewed/saved)
        $excluded = array_merge(
            $profile['viewed_ids'] ?? [],
            $profile['saved_ids'] ?? []
        );

        $candidates = DB::table('properties as p')
            ->join('property_prices   as pp', 'p.id', '=', 'pp.property_id')
            ->join('property_details  as pd', 'p.id', '=', 'pd.property_id')
            ->join('property_locations as pl', 'p.id', '=', 'pl.property_id')
            ->select(
                'p.id',
                'p.title',
                'p.listing_type',
                'pp.price',
                'pd.bedrooms',
                'pd.bathrooms',
                'pl.city',
                'pl.district'
            )
            ->where('p.status', 'active')
            ->whereNotIn('p.id', $excluded)
            ->limit(500)   // work on top 500 candidates
            ->get();

        $scored = [];

        foreach ($candidates as $prop) {
            $score = $this->score($prop, $profile);
            $scored[] = [
                'property_id' => $prop->id,
                'title' => $prop->title,
                'score' => $score['total'],
                'breakdown' => $score['breakdown'],
                'reason' => $this->generateReason($score['breakdown'], $prop, $profile),
            ];
        }

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

        return $scored;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Per-property score (0–100)
    // ─────────────────────────────────────────────────────────────────────────

    private function score(\stdClass $prop, array $profile): array
    {
        $priceScore = $this->priceSimilarity($prop->price, $profile);
        $locationScore = $this->locationSimilarity($prop, $profile);
        $bedroomScore = $this->bedroomMatch($prop->bedrooms, $profile);
        $behaviorScore = $this->behaviorScore($prop, $profile);

        $total = round(
            ($priceScore * self::W_PRICE)
            + ($locationScore * self::W_LOCATION)
            + ($bedroomScore * self::W_BEDS)
            + ($behaviorScore * self::W_BEHAVIOR),
            1
        );

        return [
            'total' => min(100, max(0, $total)),
            'breakdown' => [
                'price' => $priceScore,
                'location' => $locationScore,
                'bedrooms' => $bedroomScore,
                'behavior' => $behaviorScore,
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Individual scorers (each returns 0–100)
    // ─────────────────────────────────────────────────────────────────────────

    private function priceSimilarity(float $price, array $profile): float
    {
        if (!isset($profile['avg_price']) || $profile['avg_price'] <= 0) {
            return 50.0;
        }

        $diff = abs($price - $profile['avg_price']);
        $ratio = $diff / $profile['avg_price'];

        // Penalise by deviation: 0% diff → 100, 50% diff → 0
        return max(0, 100 - ($ratio * 200));
    }

    private function locationSimilarity(\stdClass $prop, array $profile): float
    {
        $preferredCities = $profile['preferred_cities'] ?? [];
        $preferredDistricts = $profile['preferred_districts'] ?? [];

        if (in_array($prop->city, $preferredCities, true))
            return 100.0;
        if (in_array($prop->district, $preferredDistricts, true))
            return 60.0;

        return 10.0;
    }

    private function bedroomMatch(int $bedrooms, array $profile): float
    {
        $preferred = $profile['preferred_bedrooms'] ?? null;
        if ($preferred === null)
            return 50.0;

        $diff = abs($bedrooms - $preferred);
        return max(0, 100 - ($diff * 25));
    }

    private function behaviorScore(\stdClass $prop, array $profile): float
    {
        $score = 0.0;
        $count = 0;

        // Boost if the property type appears in search history
        $searchedTypes = $profile['searched_property_types'] ?? [];
        if (in_array($prop->listing_type, $searchedTypes, true)) {
            $score += 80;
            $count++;
        }

        // Boost if user contacted agents for same city
        $contactedCities = $profile['contacted_cities'] ?? [];
        if (in_array($prop->city, $contactedCities, true)) {
            $score += 100;
            $count++;
        }

        return $count > 0 ? $score / $count : 30.0;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // User profile builder
    // ─────────────────────────────────────────────────────────────────────────

    private function buildUserProfile(int $userId): array
    {
        return Cache::remember("user_profile_{$userId}", 1800, function () use ($userId) {
            // Viewed properties
            $viewed = DB::table('property_views')
                ->where('user_id', $userId)
                ->orderByDesc('created_at')
                ->limit(50)
                ->pluck('property_id');

            // Saved properties
            $saved = DB::table('saved_properties')
                ->where('user_id', $userId)
                ->pluck('property_id');

            // Price signal from viewed/saved properties
            $avgPrice = DB::table('property_prices')
                ->whereIn('property_id', $viewed->merge($saved)->unique())
                ->avg('price');

            // Location from viewed
            $cities = DB::table('property_locations')
                ->whereIn('property_id', $viewed)
                ->pluck('city')
                ->countBy()
                ->sortDesc()
                ->keys()
                ->take(3)
                ->all();

            $districts = DB::table('property_locations')
                ->whereIn('property_id', $viewed)
                ->pluck('district')
                ->countBy()
                ->sortDesc()
                ->keys()
                ->take(3)
                ->all();

            // Bedroom preference from viewed
            $beds = DB::table('property_details')
                ->whereIn('property_id', $viewed)
                ->pluck('bedrooms')
                ->filter()
                ->avg();

            // Search history
            $searchTypes = DB::table('ai_search_logs')
                ->where('user_id', $userId)
                ->whereNotNull('listing_type')
                ->pluck('listing_type')
                ->countBy()
                ->sortDesc()
                ->keys()
                ->take(3)
                ->all();

            // Contact requests
            $contactCities = DB::table('property_inquiries as pi')
                ->join('property_locations as pl', 'pl.property_id', '=', 'pi.property_id')
                ->where('pi.user_id', $userId)
                ->pluck('pl.city')
                ->countBy()
                ->sortDesc()
                ->keys()
                ->take(3)
                ->all();

            return [
                'user_id' => $userId,
                'viewed_ids' => $viewed->all(),
                'saved_ids' => $saved->all(),
                'avg_price' => $avgPrice ?? 0,
                'preferred_cities' => $cities,
                'preferred_districts' => $districts,
                'preferred_bedrooms' => $beds ? (int) round($beds) : null,
                'searched_property_types' => $searchTypes,
                'contacted_cities' => $contactCities,
            ];
        });
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function generateReason(array $breakdown, \stdClass $prop, array $profile): string
    {
        $reasons = [];

        if ($breakdown['location'] >= 80) {
            $reasons[] = "Located in your preferred area ({$prop->city})";
        }

        if ($breakdown['price'] >= 70) {
            $reasons[] = "Price matches your typical budget";
        }

        if ($breakdown['bedrooms'] >= 75) {
            $reasons[] = "Bedroom count matches your preference";
        }

        if ($breakdown['behavior'] >= 70) {
            $reasons[] = "Similar to properties you've shown interest in";
        }

        return implode('; ', $reasons) ?: 'Recommended based on platform activity';
    }
}
