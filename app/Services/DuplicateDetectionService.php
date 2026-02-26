<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyDuplicate;
use App\Models\PropertyMedia;
use App\Models\FraudDetectionLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DuplicateDetectionService
 *
 * Detects duplicate property listings using a weighted scoring system:
 *  - Image similarity (dHash):  40%
 *  - Location similarity:       30%
 *  - Title similarity:          15%
 *  - Price similarity:          15%
 */
class DuplicateDetectionService
{
    private array $weights;
    private int $similarityThreshold;
    private int $maxDistanceMeters;
    private float $priceTolerance;
    private float $minTitleSimilarity;
    private int $searchWindowDays;
    private ImageHashService $hashService;

    public function __construct(ImageHashService $hashService)
    {
        $this->hashService = $hashService;
        $config = config('fraud_detection.duplicate_detection');

        $this->weights = $config['weights'] ?? [
            'image_similarity' => 0.40,
            'location_similarity' => 0.30,
            'title_similarity' => 0.15,
            'price_similarity' => 0.15,
        ];
        $this->similarityThreshold = $config['similarity_threshold'] ?? 85;
        $this->maxDistanceMeters = $config['max_distance_meters'] ?? 20;
        $this->priceTolerance = ($config['price_tolerance_pct'] ?? 10) / 100;
        $this->minTitleSimilarity = $config['min_title_similarity'] ?? 0.70;
        $this->searchWindowDays = $config['search_window_days'] ?? 90;
    }

    /**
     * Run full duplicate detection for a property.
     * Returns an array of potential duplicates with similarity scores.
     *
     * @param Property $property
     * @return array{
     *   has_duplicates: bool,
     *   duplicates: array,
     *   highest_score: int,
     *   recommendation: string
     * }
     */
    public function detectDuplicates(Property $property): array
    {
        $candidates = $this->getCandidateProperties($property);
        $duplicates = [];

        foreach ($candidates as $candidate) {
            $scoreData = $this->computeSimilarityScore($property, $candidate);

            if ($scoreData['total_score'] >= $this->similarityThreshold) {
                $duplicates[] = array_merge($scoreData, [
                    'candidate_id' => $candidate->id,
                    'candidate_title' => $candidate->title,
                ]);

                // Persist to property_duplicates table
                $this->persistDuplicate($property, $candidate, $scoreData);
            }
        }

        // Sort by highest similarity first
        usort($duplicates, fn($a, $b) => $b['total_score'] <=> $a['total_score']);

        $highestScore = !empty($duplicates) ? $duplicates[0]['total_score'] : 0;

        $result = [
            'has_duplicates' => !empty($duplicates),
            'duplicates' => $duplicates,
            'highest_score' => $highestScore,
            'count' => count($duplicates),
            'recommendation' => $this->getRecommendation($highestScore),
        ];

        // Update property flags if duplicates found
        if (!empty($duplicates)) {
            $property->update([
                'is_duplicate' => true,
                'fraud_status' => $highestScore >= 90 ? 'blocked' : 'suspicious',
                'fraud_score' => min(100, $property->fraud_score + 30),
            ]);
        }

        // Log the detection event
        $this->logDetection($property, $result);

        return $result;
    }

    /**
     * Calculate the weighted similarity score between two properties.
     */
    public function computeSimilarityScore(Property $property, Property $candidate): array
    {
        // 1. Image Similarity (40%)
        $imageScore = $this->computeImageSimilarity($property, $candidate);

        // 2. Location Similarity (30%)
        $locationScore = $this->computeLocationSimilarity($property, $candidate);

        // 3. Title Similarity (15%)
        $titleScore = $this->computeTitleSimilarity($property->title, $candidate->title);

        // 4. Price Similarity (15%)
        $priceScore = $this->computePriceSimilarity($property->price, $candidate->price);

        $totalScore = (int) round(
            ($imageScore * $this->weights['image_similarity']) +
            ($locationScore * $this->weights['location_similarity']) +
            ($titleScore * $this->weights['title_similarity']) +
            ($priceScore * $this->weights['price_similarity'])
        );

        return [
            'total_score' => min(100, $totalScore),
            'image_similarity' => $imageScore,
            'location_similarity' => $locationScore,
            'title_similarity' => $titleScore,
            'price_similarity' => $priceScore,
            'breakdown' => [
                'image_contribution' => (int) round($imageScore * $this->weights['image_similarity']),
                'location_contribution' => (int) round($locationScore * $this->weights['location_similarity']),
                'title_contribution' => (int) round($titleScore * $this->weights['title_similarity']),
                'price_contribution' => (int) round($priceScore * $this->weights['price_similarity']),
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Individual similarity computations
    // -------------------------------------------------------------------------

    /**
     * Image similarity: Compare image hashes of both properties (0-100).
     */
    private function computeImageSimilarity(Property $property, Property $candidate): int
    {
        $hashes1 = PropertyMedia::where('property_id', $property->id)
            ->where('media_type', 'image')
            ->whereNotNull('image_hash')
            ->pluck('image_hash');

        $hashes2 = PropertyMedia::where('property_id', $candidate->id)
            ->where('media_type', 'image')
            ->whereNotNull('image_hash')
            ->pluck('image_hash');

        if ($hashes1->isEmpty() || $hashes2->isEmpty()) {
            return 0;
        }

        // Find the highest similarity between any pair of images
        $maxSimilarity = 0;
        foreach ($hashes1 as $hash1) {
            foreach ($hashes2 as $hash2) {
                $similarity = $this->hashService->calculateSimilarity($hash1, $hash2);
                $maxSimilarity = max($maxSimilarity, $similarity);
            }
        }

        return (int) round($maxSimilarity);
    }

    /**
     * Location similarity: Distance-based score (0-100).
     * 100 = within maxDistanceMeters, scales down linearly up to 5km.
     */
    private function computeLocationSimilarity(Property $property, Property $candidate): int
    {
        $lat1 = $property->latitude;
        $lng1 = $property->longitude;
        $lat2 = $candidate->latitude;
        $lng2 = $candidate->longitude;

        if (!$lat1 || !$lng1 || !$lat2 || !$lng2) {
            // If no coords, check address match
            return $this->computeAddressSimilarity($property, $candidate);
        }

        $distanceMeters = $this->haversineDistance((float) $lat1, (float) $lng1, (float) $lat2, (float) $lng2);

        if ($distanceMeters <= $this->maxDistanceMeters) {
            return 100;
        }

        // Scale: 100 at 20m, 0 at 5000m+
        $maxFalloff = 5000;
        $score = max(0, 100 - ($distanceMeters / $maxFalloff * 100));
        return (int) round($score);
    }

    /**
     * Fallback address-based similarity when coordinates are missing.
     */
    private function computeAddressSimilarity(Property $property, Property $candidate): int
    {
        $score = 0;

        if ($property->city && $property->city === $candidate->city) {
            $score += 30;
        }
        if ($property->state && $property->state === $candidate->state) {
            $score += 20;
        }
        if ($property->country && $property->country === $candidate->country) {
            $score += 10;
        }
        if ($property->address && $candidate->address) {
            $score += (int) round($this->levenshteinSimilarity($property->address, $candidate->address) * 40);
        }

        return min(100, $score);
    }

    /**
     * Title similarity using Levenshtein distance normalized to 0-100.
     */
    private function computeTitleSimilarity(?string $title1, ?string $title2): int
    {
        if (!$title1 || !$title2) {
            return 0;
        }

        $similarity = $this->levenshteinSimilarity(
            mb_strtolower(trim($title1)),
            mb_strtolower(trim($title2))
        );

        return (int) round($similarity * 100);
    }

    /**
     * Price similarity: 100 if within tolerance %, scales to 0 at 2x tolerance.
     */
    private function computePriceSimilarity(float|null $price1, float|null $price2): int
    {
        if (!$price1 || !$price2 || $price1 <= 0 || $price2 <= 0) {
            return 0;
        }

        $maxPrice = max($price1, $price2);
        $minPrice = min($price1, $price2);
        $diffRatio = ($maxPrice - $minPrice) / $maxPrice;

        if ($diffRatio <= $this->priceTolerance) {
            return 100;
        }

        // Scale: 100 at tolerance, 0 at 2x tolerance
        $score = max(0, 100 - (($diffRatio - $this->priceTolerance) / $this->priceTolerance * 100));
        return (int) round($score);
    }

    // -------------------------------------------------------------------------
    // Helper methods
    // -------------------------------------------------------------------------

    /**
     * Get candidate properties to compare against.
     * Optimization: Filter by city, property_type, and bedroom count first.
     */
    private function getCandidateProperties(Property $property): Collection
    {
        return Property::where('id', '!=', $property->id)
            ->where('property_type', $property->property_type)
            ->whereIn('status', ['active', 'pending', 'under_review'])
            ->where('created_at', '>=', now()->subDays($this->searchWindowDays))
            ->when($property->city, fn($q) => $q->where('city', $property->city))
            ->when($property->bedrooms, fn($q) => $q->where('bedrooms', $property->bedrooms))
            ->select(['id', 'title', 'price', 'latitude', 'longitude', 'address', 'city', 'state', 'country', 'bedrooms'])
            ->limit(200) // Limit for performance
            ->get();
    }

    /**
     * Persist a duplicate detection result to the database.
     */
    private function persistDuplicate(Property $property, Property $candidate, array $scoreData): void
    {
        try {
            PropertyDuplicate::updateOrCreate(
                [
                    'property_id' => min($property->id, $candidate->id),
                    'duplicate_property_id' => max($property->id, $candidate->id),
                ],
                [
                    'similarity_score' => $scoreData['total_score'],
                    'image_similarity' => $scoreData['image_similarity'],
                    'location_similarity' => $scoreData['location_similarity'],
                    'title_similarity' => $scoreData['title_similarity'],
                    'price_similarity' => $scoreData['price_similarity'],
                    'matching_details' => $scoreData,
                    'status' => 'pending',
                ]
            );
        } catch (\Throwable $e) {
            Log::error('DuplicateDetectionService: Failed to persist duplicate', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Haversine formula to compute distance between two lat/lng points in meters.
     */
    public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(sqrt($a));
    }

    /**
     * Calculate Levenshtein similarity ratio (0.0 to 1.0).
     */
    public function levenshteinSimilarity(string $s1, string $s2): float
    {
        if ($s1 === $s2) {
            return 1.0;
        }

        $maxLen = max(mb_strlen($s1), mb_strlen($s2));
        if ($maxLen === 0) {
            return 1.0;
        }

        $distance = levenshtein($s1, $s2);
        return 1 - ($distance / $maxLen);
    }

    /**
     * Get action recommendation based on similarity score.
     */
    private function getRecommendation(int $score): string
    {
        if ($score >= 95) {
            return 'block_duplicate';
        }
        if ($score >= $this->similarityThreshold) {
            return 'flag_for_review';
        }
        return 'allow';
    }

    private function logDetection(Property $property, array $result): void
    {
        try {
            if ($result['has_duplicates']) {
                FraudDetectionLog::create([
                    'entity_type' => 'property',
                    'entity_id' => $property->id,
                    'detection_type' => FraudDetectionLog::TYPE_DUPLICATE_DETECTION,
                    'fraud_score' => $result['highest_score'],
                    'details' => [
                        'count' => $result['count'],
                        'highest_score' => $result['highest_score'],
                        'recommendation' => $result['recommendation'],
                        'duplicate_ids' => array_column($result['duplicates'], 'candidate_id'),
                    ],
                    'action_taken' => $result['recommendation'],
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('DuplicateDetectionService: Failed to write log', ['error' => $e->getMessage()]);
        }
    }
}
