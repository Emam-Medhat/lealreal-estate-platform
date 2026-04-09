<?php

namespace App\Services;

use App\Models\Property;
use App\Models\FraudDetectionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PriceAnomalyService
 *
 * Uses statistical analysis (mean, standard deviation, Z-score) to detect
 * abnormal pricing in real estate listings.
 *
 * Detection method:
 *  - Calculate baseline price per sqm from comparable properties
 *  - Compute Z-score for the target property
 *  - Flag if deviation exceeds configured thresholds
 */
class PriceAnomalyService
{
    private float $upperDeviationPct;
    private float $lowerDeviationPct;
    private float $zScoreThreshold;
    private int $minSampleSize;
    private array $scores;
    private array $contextWeights;

    public function __construct()
    {
        $config = config('fraud_detection.price_anomaly');

        $this->upperDeviationPct = $config['upper_deviation_pct'] ?? 40;
        $this->lowerDeviationPct = $config['lower_deviation_pct'] ?? 40;
        $this->zScoreThreshold = $config['z_score_threshold'] ?? 2.5;
        $this->minSampleSize = $config['min_sample_size'] ?? 5;
        $this->scores = $config['scores'] ?? [];
        $this->contextWeights = $config['context_weights'] ?? [
            'neighborhood' => 0.50,
            'city' => 0.30,
            'property_type' => 0.20,
        ];
    }

    /**
     * Analyze the price of a property and return an anomaly score (0-100).
     *
     * @param Property $property
     * @return array{
     *   anomaly_score: int,
     *   is_anomalous: bool,
     *   details: array,
     *   action: string
     * }
     */
    public function analyzeProperty(Property $property): array
    {
        $result = [
            'anomaly_score' => 0,
            'is_anomalous' => false,
            'details' => [],
            'action' => 'none',
        ];

        // Need a price and area to calculate price per sqm
        if (!$property->price || !$property->area || $property->area <= 0) {
            $result['details'][] = [
                'type' => 'insufficient_data',
                'message' => 'Missing price or area data for anomaly detection',
            ];
            return $result;
        }

        $pricePerSqm = (float) $property->price / (float) $property->area;

        // Get statistics for different contexts
        $stats = $this->gatherStatistics($property);

        if (empty($stats)) {
            $result['details'][] = [
                'type' => 'insufficient_sample',
                'message' => "Not enough comparable properties (min: {$this->minSampleSize}) to detect anomalies",
            ];
            return $result;
        }

        // Calculate weighted anomaly score
        $weightedAnomalyScore = 0;
        $weights = 0;

        foreach ($stats as $contextName => $stat) {
            if ($stat['count'] < $this->minSampleSize) {
                continue;
            }

            $contextScore = $this->scoreContext($pricePerSqm, $stat, $contextName);
            $weight = $this->contextWeights[$contextName] ?? 0.20;
            $weightedAnomalyScore += $contextScore * $weight;
            $weights += $weight;

            $result['details'][$contextName] = array_merge($stat, [
                'property_price_per_sqm' => $pricePerSqm,
                'anomaly_score' => $contextScore,
            ]);
        }

        if ($weights > 0) {
            $finalScore = (int) round($weightedAnomalyScore / $weights);
        } else {
            $finalScore = 0;
        }

        $result['anomaly_score'] = min(100, $finalScore);
        $result['is_anomalous'] = $finalScore >= $this->scores['high_deviation'] ?? 30;
        $result['price_per_sqm'] = round($pricePerSqm, 2);

        // Determine action
        if ($finalScore >= ($this->scores['extreme_deviation'] ?? 50)) {
            $result['action'] = 'block';
        } elseif ($finalScore >= ($this->scores['high_deviation'] ?? 30)) {
            $result['action'] = 'flag';
        }

        // Persist to property
        $property->update(['anomaly_score' => $result['anomaly_score']]);

        // Log detection
        $this->logDetection($property, $result);

        return $result;
    }

    /**
     * Calculate anomaly score for a single context stat block.
     */
    private function scoreContext(float $pricePerSqm, array $stat, string $contextName): int
    {
        $score = 0;
        $mean = $stat['mean'];
        $std = $stat['std_dev'];

        if ($mean <= 0) {
            return 0;
        }

        // Deviation percentage
        $deviationPct = (($pricePerSqm - $mean) / $mean) * 100;
        $absDeviation = abs($deviationPct);

        // Z-score
        $zScore = ($std > 0) ? abs($pricePerSqm - $mean) / $std : 0;

        // Score based on percentage deviation
        if ($absDeviation > $this->upperDeviationPct) {
            if ($absDeviation > $this->upperDeviationPct * 2) {
                $score = max($score, $this->scores['extreme_deviation'] ?? 50);
            } else {
                $score = max($score, $this->scores['high_deviation'] ?? 30);
            }
        }

        // Score boost from Z-score
        if ($zScore > $this->zScoreThreshold) {
            $score = max($score, $this->scores['zscore_high'] ?? 40);
            // Scale up for extreme z-scores
            if ($zScore > $this->zScoreThreshold * 2) {
                $score = min(100, (int) ($score * 1.5));
            }
        }

        return $score;
    }

    /**
     * Gather statistical data (mean, stddev) for comparable properties.
     * Checks 3 context levels: neighborhood, city, property_type.
     */
    private function gatherStatistics(Property $property): array
    {
        $stats = [];

        // Context 1: Same neighborhood
        if ($property->city && $property->state) {
            $neighborhoodStats = $this->getStatsByQuery(
                $property->id,
                $property->property_type,
                function ($q) use ($property) {
                    return $q->where('city', $property->city)
                        ->where('state', $property->state);
                }
            );
            if ($neighborhoodStats) {
                $stats['neighborhood'] = $neighborhoodStats;
            }
        }

        // Context 2: Same city
        if ($property->city) {
            $cityStats = $this->getStatsByQuery(
                $property->id,
                $property->property_type,
                fn($q) => $q->where('city', $property->city)
            );
            if ($cityStats) {
                $stats['city'] = $cityStats;
            }
        }

        // Context 3: Same property type (countrywide)
        if ($property->property_type) {
            $typeStats = $this->getStatsByQuery(
                $property->id,
                $property->property_type,
                fn($q) => $q
            );
            if ($typeStats) {
                $stats['property_type'] = $typeStats;
            }
        }

        return $stats;
    }

    /**
     * Query DB for price-per-sqm statistics excluding the current property.
     */
    private function getStatsByQuery(int $excludeId, string $propertyType, callable $queryModifier): ?array
    {
        $baseQuery = DB::table('properties')
            ->where('id', '!=', $excludeId)
            ->where('property_type', $propertyType)
            ->where('status', 'active')
            ->whereNotNull('price')
            ->whereNotNull('area')
            ->where('area', '>', 0)
            ->where('price', '>', 0)
            ->where('fraud_status', '!=', 'blocked'); // Exclude known fraud

        $query = $queryModifier($baseQuery);

        $rows = $query->select(
            DB::raw('(price / area) as price_per_sqm')
        )->get();

        if ($rows->isEmpty() || $rows->count() < $this->minSampleSize) {
            return null;
        }

        $values = $rows->pluck('price_per_sqm')->map(fn($v) => (float) $v)->values();

        return [
            'count' => $values->count(),
            'mean' => $this->mean($values->toArray()),
            'std_dev' => $this->standardDeviation($values->toArray()),
            'min' => $values->min(),
            'max' => $values->max(),
            'median' => $this->median($values->toArray()),
        ];
    }

    // -------------------------------------------------------------------------
    // Statistical helpers
    // -------------------------------------------------------------------------

    public function mean(array $values): float
    {
        if (empty($values))
            return 0.0;
        return array_sum($values) / count($values);
    }

    public function standardDeviation(array $values): float
    {
        $n = count($values);
        if ($n < 2)
            return 0.0;

        $mean = $this->mean($values);
        $variance = array_sum(array_map(fn($v) => pow($v - $mean, 2), $values)) / ($n - 1);
        return sqrt($variance);
    }

    public function zScore(float $value, float $mean, float $stdDev): float
    {
        if ($stdDev <= 0)
            return 0.0;
        return abs($value - $mean) / $stdDev;
    }

    public function median(array $values): float
    {
        if (empty($values))
            return 0.0;
        sort($values);
        $count = count($values);
        $mid = (int) floor($count / 2);
        return $count % 2 === 0
            ? ($values[$mid - 1] + $values[$mid]) / 2
            : $values[$mid];
    }

    // -------------------------------------------------------------------------
    // Logging
    // -------------------------------------------------------------------------

    private function logDetection(Property $property, array $result): void
    {
        try {
            FraudDetectionLog::create([
                'entity_type' => 'property',
                'entity_id' => $property->id,
                'detection_type' => FraudDetectionLog::TYPE_PRICE_ANOMALY,
                'fraud_score' => $result['anomaly_score'],
                'details' => $result,
                'action_taken' => $result['action'],
            ]);
        } catch (\Throwable $e) {
            Log::error('PriceAnomalyService: Failed to write log', ['error' => $e->getMessage()]);
        }
    }
}
