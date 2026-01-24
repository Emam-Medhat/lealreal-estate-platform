<?php

namespace App\Services;

use App\Models\Property;

class PropertyValuationService
{
    /**
     * Calculate property value based on algorithm.
     *
     * @param mixed $propertyId
     * @return float
     */
    public function calculateValue($propertyId): float
    {
        $property = Property::findOrFail($propertyId);

        // Placeholder simple algorithm
        // Base value per sq unit * area + ...
        $baseRate = 1000; // default currency per area unit

        $value = $property->area * $baseRate;

        // Adjust for bedrooms
        $value += $property->bedrooms * 5000;

        // Adjust for amenities?

        return $value;
    }

    /**
     * Estimate price for data without property record.
     *
     * @param array $data
     * @return float
     */
    public function estimatePrice(array $data): float
    {
        $area = $data['area'] ?? 0;
        $bedrooms = $data['bedrooms'] ?? 0;

        $value = $area * 1000;
        $value += $bedrooms * 5000;

        return $value;
    }

    /**
     * Get price history for a property.
     *
     * @param mixed $propertyId
     * @return array
     */
    public function getPriceHistory($propertyId): array
    {
        // return PropertyPriceHistory::where('property_id', $propertyId)->get();
        return [];
    }
}
