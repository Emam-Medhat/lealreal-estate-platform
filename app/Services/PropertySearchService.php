<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PropertySearchService
{
    /**
     * Search properties based on criteria.
     *
     * @param array $criteria
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(array $criteria)
    {
        $query = Property::active();

        if (isset($criteria['keyword'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('title', 'like', '%' . $criteria['keyword'] . '%')
                    ->orWhere('description', 'like', '%' . $criteria['keyword'] . '%')
                    ->orWhere('city', 'like', '%' . $criteria['keyword'] . '%');
            });
        }

        if (isset($criteria['type'])) {
            $query->where('property_type', $criteria['type']);
        }

        if (isset($criteria['status'])) {
            $query->where('listing_type', $criteria['status']); // listing_type: sale/rent
        }

        if (isset($criteria['min_price'])) {
            $query->where('price', '>=', $criteria['min_price']);
        }

        if (isset($criteria['max_price'])) {
            $query->where('price', '<=', $criteria['max_price']);
        }

        if (isset($criteria['bedrooms'])) {
            $query->where('bedrooms', '>=', $criteria['bedrooms']);
        }

        if (isset($criteria['bathrooms'])) {
            $query->where('bathrooms', '>=', $criteria['bathrooms']);
        }

        // Add more filters as needed

        if (isset($criteria['sort_by'])) {
            switch ($criteria['sort_by']) {
                case 'price_low_high':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high_low':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
        }

        return $query->paginate($criteria['per_page'] ?? 15);
    }

    /**
     * Advanced property search with detailed filters.
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function advancedSearch(array $filters)
    {
        // Can wrap basic search or extend it
        return $this->search($filters);
    }

    /**
     * Search properties nearby a location.
     *
     * @param float $lat
     * @param float $lng
     * @param int $radius in km
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function nearbySearch(float $lat, float $lng, int $radius = 10)
    {
        // Optimization: Add bounding box filter to leverage database indexes
        // 1 degree of latitude is approximately 111.045 km
        $latDelta = $radius / 111.045;
        $lngDelta = $radius / (111.045 * abs(cos(deg2rad($lat))));

        $minLat = $lat - $latDelta;
        $maxLat = $lat + $latDelta;
        $minLng = $lng - $lngDelta;
        $maxLng = $lng + $lngDelta;

        // Haversine formula with bounding box pre-filter
        $query = Property::active()
            ->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng])
            ->select('properties.*')
            ->selectRaw(
                '( 6371 * acos( cos( radians(?) ) *
                  cos( radians( latitude ) ) *
                  cos( radians( longitude ) - radians(?) ) +
                  sin( radians(?) ) *
                  sin( radians( latitude ) ) )
                ) AS distance',
                [$lat, $lng, $lat]
            );

        // SQLite doesn't always support HAVING on non-aggregate columns in all configurations
        // or might have issues with trigonometric functions if not loaded.
        // However, we verified trigonometric functions are available in this environment.
        if (DB::connection()->getDriverName() === 'sqlite') {
            return $query->get()
                ->filter(function ($property) use ($radius) {
                    return $property->distance < $radius;
                })
                ->sortBy('distance')
                ->take(20)
                ->values();
        }

        return $query->having('distance', '<', $radius)
            ->orderBy('distance')
            ->limit(20)
            ->get();
    }

    /**
     * Find similar properties.
     *
     * @param mixed $propertyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function similarProperties($propertyId)
    {
        $property = Property::findOrFail($propertyId);

        return Property::active()
            ->where('id', '!=', $propertyId)
            ->where('property_type', $property->property_type)
            ->where('listing_type', $property->listing_type)
            ->whereBetween('price', [$property->price * 0.8, $property->price * 1.2])
            ->where('city', $property->city)
            ->limit(5)
            ->get();
    }
}
