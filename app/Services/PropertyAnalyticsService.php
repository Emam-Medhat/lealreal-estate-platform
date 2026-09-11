<?php

namespace App\Services;

use App\Models\Property;

// use App\Models\PropertyAnalytic;

class PropertyAnalyticsService
{
    /**
     * Track a view for a property.
     * Direct atomic update to prevent redundant SELECT queries and full model hydration.
     *
     * @param  mixed  $propertyId
     * @return void
     */
    public function trackView($propertyId)
    {
        Property::where('id', $propertyId)->increment('views_count');
    }

    /**
     * Get unique views count.
     * Select only the required column value instead of fetching full model instance.
     *
     * @param  mixed  $propertyId
     */
    public function getViewsCount($propertyId): int
    {
        return (int) (Property::where('id', $propertyId)->value('views_count') ?? 0);
    }

    /**
     * Get popular properties.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPopularProperties(int $limit = 5)
    {
        return Property::active()->orderBy('views_count', 'desc')->limit($limit)->get();
    }

    /**
     * Get performance metrics.
     * Fetch only required metric columns to reduce memory overhead and avoid loading unneeded model attributes.
     *
     * @param  mixed  $propertyId
     */
    public function getPerformanceMetrics($propertyId): array
    {
        $property = Property::where('id', $propertyId)
            ->select(['views_count', 'favorites_count', 'inquiries_count'])
            ->first();

        return [
            'views' => $property?->views_count ?? 0,
            'favorites' => $property?->favorites_count ?? 0,
            'inquiries' => $property?->inquiries_count ?? 0,
        ];
    }
}
