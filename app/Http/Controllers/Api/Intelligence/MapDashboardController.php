<?php

namespace App\Http\Controllers\Api\Intelligence;

use App\Http\Controllers\Controller;
use App\Services\Intelligence\MapLayerService;

class MapDashboardController extends Controller
{
    public function overview(MapLayerService $service)
    {
        return response()->json([
            'price_heatmap' => $service->priceHeatmap(),
            'demand_density' => $service->demandDensity(),
            'growth_zones' => $service->growthZones(),
            'schools' => $service->schoolProximity(),
            'transport' => $service->transportAccess(),
            'crime' => $service->crimeIndex(),
        ]);
    }
}
