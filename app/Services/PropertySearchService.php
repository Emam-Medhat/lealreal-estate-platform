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
        // Haversine formula
        return Property::active()
            ->select('properties.*')
            ->selectRaw(
                '( 6371 * acos( cos( radians(?) ) *
                  cos( radians( latitude ) ) *
                  cos( radians( longitude ) - radians(?) ) +
                  sin( radians(?) ) *
                  sin( radians( latitude ) ) )
                ) AS distance',
                [$lat, $lng, $lat]
            )
            ->having('distance', '<', $radius)
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
