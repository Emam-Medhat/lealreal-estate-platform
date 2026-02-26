<?php

namespace App\Services\Intelligence;

use App\Models\Property;
use Illuminate\Support\Facades\Log;

/**
 * Risk & Trust scoring service.
 */
class RiskTrustService
{
    /**
     * Compute trust index (0–100) and trust level from property signals.
     *
     * @param Property $property
     * @return array{trust_index:float,trust_level:string}
     */
    public function compute(Property $property): array
    {
        $fraud = (int) ($property->fraud_score ?? 0);
        $base = max(0, min(100, 100 - $fraud));

        $imagesBonus = $this->imagesBonus($property);
        $profileBonus = $this->profileBonus($property);

        $trustIndex = max(0, min(100, $base + $imagesBonus + $profileBonus));
        $level = $this->levelFromIndex($trustIndex);

        return [
            'trust_index' => round($trustIndex, 2),
            'trust_level' => $level,
        ];
    }

    /**
     * Persist trust fields.
     */
    public function persist(Property $property, array $trust): void
    {
        $property->fill([
            'trust_index' => $trust['trust_index'],
            'trust_level' => $trust['trust_level'],
        ])->save();

        Log::channel('intelligence')->info('Risk/Trust updated', [
            'property_id' => $property->id,
            'trust_index' => $trust['trust_index'],
            'trust_level' => $trust['trust_level'],
        ]);
    }

    private function imagesBonus(Property $property): float
    {
        try {
            if (method_exists($property, 'media')) {
                $count = (int) $property->media()->count();
                return min(10, $count * 1.0);
            }
        } catch (\Throwable $e) {
        }
        return 0.0;
    }

    private function profileBonus(Property $property): float
    {
        $bonus = 0.0;
        if (!empty($property->agent_id) || !empty($property->company_id)) {
            $bonus += 5.0;
        }
        if (!empty($property->owner_id) || !empty($property->user_id)) {
            $bonus += 2.5;
        }
        return min(10.0, $bonus);
    }

    private function levelFromIndex(float $idx): string
    {
        if ($idx >= 80) {
            return 'high';
        }
        if ($idx >= 50) {
            return 'medium';
        }
        if ($idx >= 20) {
            return 'low';
        }
        return 'critical';
    }
}
