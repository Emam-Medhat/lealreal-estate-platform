<?php

namespace App\Services\Intelligence;

use App\Models\Property;
use Illuminate\Support\Collection;

/**
 * Compute map intelligence layers from property data.
 */
class MapLayerService
{
    /**
     * Price heatmap grid.
     *
     * @return array<int,array{lat:float,lng:float,count:int,avg_price:float}>
     */
    public function priceHeatmap(): array
    {
        $grid = $this->gridAggregate(function ($group) {
            return [
                'count' => $group->count(),
                'avg_price' => round($group->avg('price'), 2),
            ];
        });
        return $grid;
    }

    /**
     * Demand density using views/favorites/inquiries.
     */
    public function demandDensity(): array
    {
        $grid = $this->gridAggregate(function ($group) {
            $score = $group->sum(function ($p) {
                return (int) $p->views_count + (int) $p->inquiries_count + (int) $p->favorites_count * 2;
            });
            return [
                'count' => $group->count(),
                'demand_score' => $score,
            ];
        });
        return $grid;
    }

    /**
     * Investment growth zones based on forecasted appreciation or price deltas.
     */
    public function growthZones(): array
    {
        $grid = $this->gridAggregate(function ($group) {
            $rates = $group->map(function ($p) {
                $forecast = is_array($p->appreciation_forecast ?? null) ? $p->appreciation_forecast : null;
                if ($forecast && isset($forecast['12']) && $p->price > 0) {
                    return max(0.0, ($forecast['12'] - $p->price) / $p->price);
                }
                if (!is_null($p->original_price) && $p->original_price > 0) {
                    return max(0.0, ($p->price - $p->original_price) / $p->original_price);
                }
                return 0.0;
            });
            return [
                'count' => $group->count(),
                'growth_rate' => round($rates->avg() * 100, 2),
            ];
        });
        return $grid;
    }

    /**
     * School proximity layer (proxy using presence of schools field).
     */
    public function schoolProximity(): array
    {
        $grid = $this->gridAggregate(function ($group) {
            $withSchools = $group->filter(fn ($p) => !empty($p->schools))->count();
            return [
                'count' => $group->count(),
                'schools_coverage' => $withSchools,
            ];
        });
        return $grid;
    }

    /**
     * Transport access layer (proxy using presence of transportation field).
     */
    public function transportAccess(): array
    {
        $grid = $this->gridAggregate(function ($group) {
            $withTransport = $group->filter(fn ($p) => !empty($p->transportation))->count();
            return [
                'count' => $group->count(),
                'transport_coverage' => $withTransport,
            ];
        });
        return $grid;
    }

    /**
     * Crime index placeholder (requires external data ingestion).
     */
    public function crimeIndex(): array
    {
        return [];
    }

    /**
     * Generic grid aggregator over properties by lat/lng step.
     *
     * @param callable(Collection):array $reducer
     * @return array<int,array>
     */
    private function gridAggregate(callable $reducer): array
    {
        $latStep = (float) config('intelligence.maps.grid.lat_step', 0.05);
        $lngStep = (float) config('intelligence.maps.grid.lng_step', 0.05);

        /** @var Collection $props */
        $props = Property::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get([
                'id',
                'price',
                'original_price',
                'appreciation_forecast',
                'latitude',
                'longitude',
                'views_count',
                'inquiries_count',
                'favorites_count',
                'schools',
                'transportation',
            ]);

        $buckets = [];
        foreach ($props as $p) {
            $latKey = floor($p->latitude / $latStep) * $latStep;
            $lngKey = floor($p->longitude / $lngStep) * $lngStep;
            $key = $latKey . ':' . $lngKey;
            $buckets[$key] = $buckets[$key] ?? ['lat' => (float) $latKey, 'lng' => (float) $lngKey, '_items' => collect()];
            $buckets[$key]['_items']->push($p);
        }

        $out = [];
        foreach ($buckets as $key => $bucket) {
            $calc = $reducer($bucket['_items']);
            unset($bucket['_items']);
            $out[] = array_merge($bucket, $calc);
        }
        return $out;
    }
}
