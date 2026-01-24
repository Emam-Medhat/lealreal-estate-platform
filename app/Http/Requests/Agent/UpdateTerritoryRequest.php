<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTerritoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:exclusive,shared,open',
            'status' => 'required|in:active,inactive,suspended',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_codes' => 'nullable|array',
            'postal_codes.*' => 'string|max:20',
            'neighborhoods' => 'nullable|array',
            'neighborhoods.*' => 'string|max:255',
            'north_lat' => 'required|numeric|between:-90,90',
            'south_lat' => 'required|numeric|between:-90,90|lt:north_lat',
            'east_lng' => 'required|numeric|between:-180,180',
            'west_lng' => 'required|numeric|between:-180,180|gt:east_lng',
            'coordinates' => 'nullable|array',
            'coordinates.*.lat' => 'required|numeric|between:-90,90',
            'coordinates.*.lng' => 'required|numeric|between:-180,180',
            'population_density' => 'nullable|string|max:100',
            'average_income' => 'nullable|numeric|min:0',
            'property_types' => 'nullable|array',
            'property_types.*' => 'string|max:100',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gt:min_price',
            'competition_level' => 'nullable|in:low,medium,high',
            'market_potential' => 'nullable|in:low,medium,high',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Territory name is required.',
            'type.required' => 'Territory type is required.',
            'status.required' => 'Territory status is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'country.required' => 'Country is required.',
            'north_lat.required' => 'North boundary latitude is required.',
            'south_lat.required' => 'South boundary latitude is required.',
            'east_lng.required' => 'East boundary longitude is required.',
            'west_lng.required' => 'West boundary longitude is required.',
            'south_lat.lt' => 'South latitude must be less than north latitude.',
            'west_lng.gt' => 'West longitude must be greater than east longitude.',
            'max_price.gt' => 'Maximum price must be greater than minimum price.',
            'coordinates.*.lat.required' => 'Latitude is required for each coordinate.',
            'coordinates.*.lng.required' => 'Longitude is required for each coordinate.',
        ];
    }
}
