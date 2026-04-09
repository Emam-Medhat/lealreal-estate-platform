<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocationApiController extends Controller
{
    public function getCountries(): JsonResponse
    {
        $countries = Country::orderBy('name')->get(['id', 'name', 'name_ar', 'code']);
        return response()->json($countries);
    }

    public function getGovernorates($countryId): JsonResponse
    {
        $governorates = Governorate::where('country_id', $countryId)->orderBy('name')->get(['id', 'name', 'name_ar']);
        return response()->json($governorates);
    }

    public function getCities($governorateId): JsonResponse
    {
        $cities = City::where('governorate_id', $governorateId)->orderBy('name')->get(['id', 'name', 'name_ar']);
        return response()->json($cities);
    }

    public function getDistricts($cityId): JsonResponse
    {
        $districts = District::where('city_id', $cityId)->orderBy('name')->get(['id', 'name', 'name_ar']);
        return response()->json($districts);
    }
}
