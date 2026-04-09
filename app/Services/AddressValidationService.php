<?php

namespace App\Services;

use App\Models\Property;
use App\Models\FraudDetectionLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * AddressValidationService
 *
 * Validates property addresses using Google Maps Geocoding API.
 * Detects:
 *  - Invalid / non-existent addresses
 *  - Country mismatches
 *  - Low-accuracy geocode results
 *  - City/state mismatches between submitted data and geocoded data
 */
class AddressValidationService
{
    private string $apiKey;
    private array $allowedCountries;
    private string $minimumAccuracy;
    private array $scores;

    private const ACCURACY_ORDER = [
        'street_address' => 10,
        'route' => 9,
        'sublocality_level_1' => 8,
        'sublocality' => 7,
        'locality' => 6,
        'administrative_area_level_2' => 5,
        'administrative_area_level_1' => 4,
        'country' => 3,
        'administrative_area' => 4,
    ];

    private const GEOCODE_API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function __construct()
    {
        $config = config('fraud_detection.address_validation');

        $this->apiKey = $config['google_maps_api_key'] ?? '';
        $this->allowedCountries = array_map('trim', explode(',', $config['allowed_countries'] ?? ''));
        $this->minimumAccuracy = $config['minimum_accuracy'] ?? 'locality';
        $this->scores = $config['scores'] ?? [];
    }

    /**
     * Validate a property's address and return a fraud score + geocode data.
     *
     * @param Property $property
     * @return array{
     *   fraud_score: int,
     *   is_valid: bool,
     *   flags: array,
     *   geocode_data: array|null
     * }
     */
    public function validateProperty(Property $property): array
    {
        $result = [
            'fraud_score' => 0,
            'is_valid' => true,
            'flags' => [],
            'geocode_data' => null,
        ];

        // If no API key, skip geocoding but still do basic checks
        if (empty($this->apiKey)) {
            $result['flags'][] = 'no_api_key';
            $result['fraud_score'] = 5;
            Log::warning('AddressValidationService: No Google Maps API key configured.');
            return $result;
        }

        $address = $this->buildAddressString($property);
        if (!$address) {
            $result['is_valid'] = false;
            $result['fraud_score'] = $this->scores['invalid_geocode'] ?? 30;
            $result['flags'][] = 'empty_address';
            return $result;
        }

        // Cache geocode results to avoid repeated API calls
        $cacheKey = 'geocode_' . md5($address);
        $geocodeResult = Cache::remember($cacheKey, 3600, function () use ($address) {
            return $this->callGeocodingAPI($address);
        });

        if (is_null($geocodeResult)) {
            // API error
            $result['is_valid'] = false;
            $result['fraud_score'] = $this->scores['invalid_geocode'] ?? 30;
            $result['flags'][] = 'api_error';
            return $result;
        }

        if (empty($geocodeResult['results'])) {
            // No results = invalid address
            $result['is_valid'] = false;
            $result['fraud_score'] = $this->scores['invalid_geocode'] ?? 30;
            $result['flags'][] = 'no_geocode_results';
            return $result;
        }

        $best = $geocodeResult['results'][0];
        $result['geocode_data'] = $best;

        // 1. Accuracy check
        $accuracyScore = $this->checkAccuracy($best, $result);
        $result['fraud_score'] += $accuracyScore;

        // 2. Country check
        $countryScore = $this->checkCountry($best, $property, $result);
        $result['fraud_score'] += $countryScore;

        // 3. City/State mismatch check
        $mismatchScore = $this->checkCityStateMismatch($best, $property, $result);
        $result['fraud_score'] += $mismatchScore;

        // 4. Coordinate validity
        $coordScore = $this->checkCoordinates($best, $property, $result);
        $result['fraud_score'] += $coordScore;

        $result['fraud_score'] = min(100, $result['fraud_score']);
        $result['is_valid'] = empty($result['flags']) || $result['fraud_score'] < 25;

        // Update property with geocode data
        $property->update([
            'geocode_verified' => $result['is_valid'],
            'geocode_data' => $best,
            'address_fraud_score' => $result['fraud_score'],
        ]);

        // Auto-fill coordinates if missing
        if (!$property->latitude && !$property->longitude && isset($best['geometry']['location'])) {
            $property->update([
                'latitude' => $best['geometry']['location']['lat'],
                'longitude' => $best['geometry']['location']['lng'],
            ]);
        }

        // Log detection
        $this->logDetection($property, $result);

        return $result;
    }

    // -------------------------------------------------------------------------
    // Private checks
    // -------------------------------------------------------------------------

    private function checkAccuracy(array $geocodeResult, array &$result): int
    {
        $types = $geocodeResult['types'] ?? [];
        $minAccOrder = self::ACCURACY_ORDER[$this->minimumAccuracy] ?? 6;

        // Find the best accuracy type in the result
        $resultAccOrder = 0;
        foreach ($types as $type) {
            $order = self::ACCURACY_ORDER[$type] ?? 0;
            $resultAccOrder = max($resultAccOrder, $order);
        }

        if ($resultAccOrder < $minAccOrder) {
            $result['flags'][] = 'low_accuracy_geocode';
            return $this->scores['low_accuracy'] ?? 20;
        }

        // Check for street-level accuracy bonus
        if ($resultAccOrder < (self::ACCURACY_ORDER['route'] ?? 9)) {
            $result['flags'][] = 'missing_street_level';
            return $this->scores['missing_street'] ?? 10;
        }

        return 0;
    }

    private function checkCountry(array $geocodeResult, Property $property, array &$result): int
    {
        if (empty($this->allowedCountries)) {
            return 0;
        }

        $geocodedCountry = $this->extractComponent($geocodeResult, 'country', 'short_name') ?? '';

        if (!empty($geocodedCountry) && !in_array(strtoupper($geocodedCountry), $this->allowedCountries)) {
            $result['flags'][] = 'country_not_allowed';
            $result['flags'][] = "geocoded_country:{$geocodedCountry}";
            return $this->scores['country_mismatch'] ?? 25;
        }

        // Cross-reference with property's stated country
        if ($property->country && $geocodedCountry) {
            $propCountryCode = strtoupper(substr($property->country, 0, 2));
            if ($propCountryCode !== strtoupper($geocodedCountry)) {
                $result['flags'][] = 'country_data_mismatch';
                return ($this->scores['country_mismatch'] ?? 25) / 2;
            }
        }

        return 0;
    }

    private function checkCityStateMismatch(array $geocodeResult, Property $property, array &$result): int
    {
        $score = 0;

        $geocodedCity = $this->extractComponent($geocodeResult, 'locality', 'long_name')
            ?? $this->extractComponent($geocodeResult, 'administrative_area_level_2', 'long_name')
            ?? '';

        $geocodedState = $this->extractComponent($geocodeResult, 'administrative_area_level_1', 'long_name') ?? '';

        // City mismatch
        if ($property->city && $geocodedCity) {
            $citySimilarity = $this->stringSimilarityRatio(
                mb_strtolower($property->city),
                mb_strtolower($geocodedCity)
            );

            if ($citySimilarity < 0.7) {
                $result['flags'][] = "city_mismatch:submitted={$property->city},geocoded={$geocodedCity}";
                $score += $this->scores['country_mismatch'] ?? 20; // Reuse country score for city mismatch
            }
        }

        return $score;
    }

    private function checkCoordinates(array $geocodeResult, Property $property, array &$result): int
    {
        $lat = $geocodeResult['geometry']['location']['lat'] ?? null;
        $lng = $geocodeResult['geometry']['location']['lng'] ?? null;

        if (is_null($lat) || is_null($lng)) {
            $result['flags'][] = 'missing_coordinates';
            return $this->scores['coordinate_invalid'] ?? 15;
        }

        // Validate coordinate ranges
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            $result['flags'][] = 'invalid_coordinate_range';
            return $this->scores['coordinate_invalid'] ?? 15;
        }

        // Check if property has coordinates that match geocoded ones
        if ($property->latitude && $property->longitude) {
            $distance = $this->simpleDistance(
                (float) $property->latitude,
                (float) $property->longitude,
                (float) $lat,
                (float) $lng
            );

            // More than 50km off = potential fraud
            if ($distance > 50000) {
                $result['flags'][] = "coordinate_far_from_address:{$distance}m";
                return $this->scores['coordinate_invalid'] ?? 15;
            }
        }

        return 0;
    }

    // -------------------------------------------------------------------------
    // API call
    // -------------------------------------------------------------------------

    private function callGeocodingAPI(string $address): ?array
    {
        try {
            $response = Http::timeout(10)->get(self::GEOCODE_API_URL, [
                'address' => $address,
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('AddressValidationService: API returned non-200', [
                'status' => $response->status(),
            ]);
        } catch (\Throwable $e) {
            Log::error('AddressValidationService: API call failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function buildAddressString(Property $property): string
    {
        $parts = array_filter([
            $property->address,
            $property->city,
            $property->state,
            $property->country,
        ]);
        return implode(', ', $parts);
    }

    private function extractComponent(array $geocodeResult, string $type, string $nameType = 'long_name'): ?string
    {
        foreach ($geocodeResult['address_components'] ?? [] as $component) {
            if (in_array($type, $component['types'] ?? [])) {
                return $component[$nameType] ?? null;
            }
        }
        return null;
    }

    private function stringSimilarityRatio(string $s1, string $s2): float
    {
        similar_text($s1, $s2, $percent);
        return $percent / 100;
    }

    private function simpleDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(sqrt($a));
    }

    private function logDetection(Property $property, array $result): void
    {
        try {
            FraudDetectionLog::create([
                'entity_type' => 'property',
                'entity_id' => $property->id,
                'detection_type' => FraudDetectionLog::TYPE_ADDRESS_VALIDATION,
                'fraud_score' => $result['fraud_score'],
                'details' => [
                    'flags' => $result['flags'],
                    'is_valid' => $result['is_valid'],
                ],
                'action_taken' => $result['fraud_score'] > 40 ? FraudDetectionLog::ACTION_FLAGGED : FraudDetectionLog::ACTION_NONE,
            ]);
        } catch (\Throwable $e) {
            Log::error('AddressValidationService: Failed to write log', ['error' => $e->getMessage()]);
        }
    }
}
