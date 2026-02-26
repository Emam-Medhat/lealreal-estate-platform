<?php

namespace App\Services\Intelligence;

use App\Models\Property;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Predictive pricing and forecasts.
 */
class PricingService
{
    /**
     * Predict price using a simple regression-like heuristic over features.
     *
     * @param Property $property
     * @param array $comparables Optional comparables and stats
     * @return array{predicted_price:float,confidence:float,overpriced:bool,forecast:array}
     */
    public function predict(Property $property, array $comparables = []): array
    {
        $mlCfg = config('intelligence.pricing.ml');
        if (!empty($mlCfg['enabled']) && !empty($mlCfg['url'])) {
            try {
                $resp = Http::timeout((int) ($mlCfg['timeout'] ?? 3))
                    ->post($mlCfg['url'], [
                        'id' => $property->id,
                        'price' => (float) $property->price,
                        'area' => (float) $property->area,
                        'bedrooms' => (int) $property->bedrooms,
                        'bathrooms' => (int) $property->bathrooms,
                        'property_type' => $property->property_type,
                        'city' => $property->city,
                        'latitude' => $property->latitude,
                        'longitude' => $property->longitude,
                    ]);
                if ($resp->ok()) {
                    $p = $resp->json();
                    if (isset($p['predicted_price'], $p['confidence'])) {
                        $overpriced = $property->price > $p['predicted_price'] * (1 + config('intelligence.pricing.overpriced_percent') / 100);
                        return [
                            'predicted_price' => round((float) $p['predicted_price'], 2),
                            'confidence' => round((float) $p['confidence'], 2),
                            'overpriced' => $overpriced,
                            'forecast' => $p['forecast'] ?? $this->forecast((float) $p['predicted_price']),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::channel('intelligence')->warning('ML pricing failed, falling back', ['error' => $e->getMessage()]);
            }
        }
        $base = $comparables['mean'] ?? (float) $property->price;
        $adj = 1.0;

        // Feature adjustments (very simplified baseline)
        $typeFactor = match ($property->property_type) {
            'villa' => 1.2,
            'commercial' => 1.3,
            'land' => 0.8,
            default => 1.0,
        };
        $adj *= $typeFactor;

        $sizeFactor = $property->area > 0 ? (1 + min(($property->area - 120) / 1200, 0.3)) : 1.0;
        $adj *= $sizeFactor;

        $roomsFactor = max(1.0, 1 + (int) $property->bedrooms * 0.03);
        $adj *= $roomsFactor;

        $predicted = $base * $adj;
        $confidence = 0.6;
        if (!empty($comparables['count']) && $comparables['count'] >= 20) {
            $confidence = 0.8;
        }

        $overpriced = $property->price > $predicted * (1 + config('intelligence.pricing.overpriced_percent') / 100);

        $forecast = $this->forecast($predicted);

        return [
            'predicted_price' => round($predicted, 2),
            'confidence' => round($confidence * 100, 2),
            'overpriced' => $overpriced,
            'forecast' => $forecast,
        ];
    }

    /**
     * Persist pricing intelligence.
     */
    public function persist(Property $property, array $pricing): void
    {
        $property->fill([
            'predicted_price' => $pricing['predicted_price'],
            'confidence_score' => $pricing['confidence'],
            'appreciation_forecast' => $pricing['forecast'],
            'overpriced_flag' => $pricing['overpriced'],
        ])->save();

        Log::channel('intelligence')->info('Pricing intelligence updated', [
            'property_id' => $property->id,
            'predicted_price' => $pricing['predicted_price'],
            'overpriced' => $pricing['overpriced'],
        ]);
    }

    /**
     * Simple compounding forecast for 6 and 12 months.
     *
     * @param float $price
     * @return array{6:float,12:float,rate:float}
     */
    private function forecast(float $price): array
    {
        $annualRate = 0.06; // 6% baseline
        $monthly = $annualRate / 12;
        $m6 = $price * pow(1 + $monthly, 6);
        $m12 = $price * pow(1 + $monthly, 12);
        return [
            '6' => round($m6, 2),
            '12' => round($m12, 2),
            'rate' => round($annualRate * 100, 2),
        ];
    }
}
