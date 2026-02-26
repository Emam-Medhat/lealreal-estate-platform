<?php

namespace App\Services\Intelligence;

use App\Models\Property;
use Illuminate\Support\Facades\Log;

/**
 * Fraud detection engine for properties.
 */
class FraudDetectionService
{
    /**
     * Evaluate fraud signals and compute fraud score.
     *
     * @param Property $property
     * @param array $context Optional context (comparables, images, pricing stats)
     * @return array{fraud_score:int,fraud_flags:array,risk_level:string,restrict:bool,manual_review:bool}
     */
    public function evaluate(Property $property, array $context = []): array
    {
        $weights = config('intelligence.fraud.weights');
        $thresholdRestrict = (int) config('intelligence.fraud.thresholds.restrict');
        $thresholdReview = (int) config('intelligence.fraud.thresholds.review');

        $flags = [];
        $score = 0;

        // 1) Duplicate images (average hash)
        $dupScore = $this->duplicateImagesScore($property, $context['image_hashes'] ?? []);
        if ($dupScore > 0) {
            $flags[] = ['type' => 'duplicate_images', 'score' => $dupScore];
            $score += min(100, $dupScore * ($weights['duplicate_images'] / 100));
        }

        // 2) Unrealistic pricing (z-score)
        $z = $this->pricingZScore($property, $context['pricing_stats'] ?? null);
        if (!is_null($z) && abs($z) >= config('intelligence.fraud.pricing.zscore_threshold')) {
            $flags[] = ['type' => 'unrealistic_pricing', 'z' => $z];
            $score += $weights['unrealistic_pricing'];
        }

        // 3) Suspicious accounts (ip velocity / devices)
        if (!empty($context['account_risk']) && $context['account_risk'] === true) {
            $flags[] = ['type' => 'suspicious_account'];
            $score += $weights['suspicious_account'];
        }

        // 4) Duplicate listing (fuzzy + geo distance)
        if (!empty($context['duplicate_listing']) && $context['duplicate_listing'] === true) {
            $flags[] = ['type' => 'duplicate_listing'];
            $score += $weights['duplicate_listing'];
        }

        $score = (int) min(100, max(0, round($score)));
        $riskLevel = $score >= 80 ? 'high' : ($score >= 50 ? 'medium' : 'low');
        $restrict = $score >= $thresholdRestrict;
        $manual = !$restrict && $score >= $thresholdReview;

        return [
            'fraud_score' => $score,
            'fraud_flags' => $flags,
            'risk_level' => $riskLevel,
            'restrict' => $restrict,
            'manual_review' => $manual,
        ];
    }

    /**
     * Persist the fraud evaluation on the property.
     */
    public function persist(Property $property, array $result): void
    {
        $property->fill([
            'fraud_score' => $result['fraud_score'],
            'fraud_flags' => $result['fraud_flags'],
            'fraud_risk_level' => $result['risk_level'],
            'requires_manual_review' => $result['manual_review'],
            'restricted_at' => $result['restrict'] ? now() : null,
        ])->save();

        Log::channel('intelligence')->warning('Fraud evaluation completed', [
            'property_id' => $property->id,
            'fraud_score' => $result['fraud_score'],
            'risk' => $result['risk_level'],
            'restrict' => $result['restrict'],
        ]);
    }

    /**
     * Compute a score for duplicate images using average hashing and Hamming distance.
     *
     * @param Property $property
     * @param array $existingHashes Array of existing image hashes to compare against.
     */
    private function duplicateImagesScore(Property $property, array $existingHashes): int
    {
        if (empty($existingHashes) || empty($property->id)) {
            return 0;
        }
        // Placeholder: assume caller provides 'hashes' for this property's images
        $hashes = $existingHashes['current'] ?? [];
        $others = $existingHashes['others'] ?? [];
        $matches = 0;
        foreach ($hashes as $h) {
            foreach ($others as $oh) {
                $sim = $this->hammingSimilarity($h, $oh);
                if ($sim >= config('intelligence.fraud.image_hash.similarity_threshold')) {
                    $matches++;
                    break;
                }
            }
        }
        if ($matches === 0) {
            return 0;
        }
        return min(100, $matches * 20);
    }

    /**
     * Z-score for pricing based on mean and std from context.
     */
    private function pricingZScore(Property $property, ?array $stats): ?float
    {
        if (!$stats || empty($stats['mean']) || empty($stats['std'])) {
            return null;
        }
        if ($stats['std'] == 0) {
            return null;
        }
        return ($property->price - $stats['mean']) / $stats['std'];
    }

    /**
     * Hamming similarity for binary strings or hex strings of equal length.
     */
    private function hammingSimilarity(string $a, string $b): float
    {
        if (strlen($a) !== strlen($b)) {
            return 0.0;
        }
        $len = strlen($a);
        $same = 0;
        for ($i = 0; $i < $len; $i++) {
            if ($a[$i] === $b[$i]) {
                $same++;
            }
        }
        return $same / max($len, 1);
    }
}

