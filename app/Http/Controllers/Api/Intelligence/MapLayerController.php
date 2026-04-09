<?php

namespace App\Http\Controllers\Api\Intelligence;

use App\Http\Controllers\Controller;
use App\Services\Intelligence\MapLayerService;
use Illuminate\Http\Request;

/**
 * API: Smart Map Intelligence Layers.
 */
class MapLayerController extends Controller
{
    public function priceHeatmap(MapLayerService $service)
    {
        return response()->json([
            'layer' => 'price_heatmap',
            'data' => $service->priceHeatmap(),
        ]);
    }

    public function demandDensity(MapLayerService $service)
    {
        return response()->json([
            'layer' => 'demand_density',
            'data' => $service->demandDensity(),
        ]);
    }

    public function growthZones(MapLayerService $service)
    {
        return response()->json([
            'layer' => 'growth_zones',
            'data' => $service->growthZones(),
        ]);
    }

    public function schoolProximity(MapLayerService $service)
    {
        return response()->json([
            'layer' => 'school_proximity',
            'data' => $service->schoolProximity(),
        ]);
    }

    public function transportAccess(MapLayerService $service)
    {
        return response()->json([
            'layer' => 'transport_access',
            'data' => $service->transportAccess(),
        ]);
    }

    public function crimeIndex(MapLayerService $service)
    {
        return response()->json([
            'layer' => 'crime_index',
            'data' => $service->crimeIndex(),
        ]);
    }
}
