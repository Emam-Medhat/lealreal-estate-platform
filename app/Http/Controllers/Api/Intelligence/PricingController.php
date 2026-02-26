<?php

namespace App\Http\Controllers\Api\Intelligence;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\Intelligence\PricingService;
use Illuminate\Http\Request;

/**
 * API: Predictive Pricing
 */
class PricingController extends Controller
{
    public function predictForProperty(Request $request, int $id, PricingService $service)
    {
        $property = Property::findOrFail($id);
        $pricing = $service->predict($property);
        return response()->json($pricing);
    }
}

