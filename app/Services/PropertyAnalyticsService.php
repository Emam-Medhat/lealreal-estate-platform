<?php

namespace App\Services;

use App\Models\Property;
// use App\Models\PropertyAnalytic;
use Illuminate\Support\Facades\Cache;

class PropertyAnalyticsService
{
    /**
     * Track a view for a property.
     *
     * @param mixed $propertyId
     * @return void
     */
    public function trackView($propertyId)
    {
        // Increment redis counter or DB
        $property = Property::find($propertyId);
        if ($property) {
            $property->incrementViews();

            // Log detailed analytic
            // PropertyAnalytic::create([...]);
        }
    }

    /**
     * Get unique views count.
     *
     * @param mixed $propertyId
     * @return int
     */
    public function getViewsCount($propertyId): int
    {
        return Property::find($propertyId)?->views_count ?? 0;
    }

    /**
     * Get popular properties.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPopularProperties(int $limit = 5)
    {
        return Property::active()->orderBy('views_count', 'desc')->limit($limit)->get();
    }

    /**
     * Get performance metrics.
     *
     * @param mixed $propertyId
     * @return array
     */
    public function getPerformanceMetrics($propertyId): array
    {
        $property = Property::find($propertyId);
        return [
            'views' => $property->views_count,
            'favorites' => $property->favorites_count,
            'inquiries' => $property->inquiries_count,
        ];
    }
}
