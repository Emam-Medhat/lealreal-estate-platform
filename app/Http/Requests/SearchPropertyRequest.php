<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchPropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Basic Search
            'q' => 'nullable|string|min:2|max:255',
            'property_type' => 'nullable|string|exists:property_types,slug',
            'listing_type' => 'nullable|in:sale,rent,lease',
            'status' => 'nullable|in:active,inactive,draft,sold,rented',

            // Price Range
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:SAR,USD,EUR,GBP,AED',
            'negotiable' => 'nullable|boolean',
            'includes_vat' => 'nullable|boolean',

            // Area Range
            'min_area' => 'nullable|numeric|min:1',
            'max_area' => 'nullable|numeric|min:1',
            'area_unit' => 'nullable|in:sq_m,sq_ft',

            // Property Details
            'bedrooms' => 'nullable|integer|min:0|max:50',
            'bathrooms' => 'nullable|integer|min:0|max:50',
            'floors' => 'nullable|integer|min:0|max:100',
            'parking_spaces' => 'nullable|integer|min:0|max:50',
            'year_built_min' => 'nullable|integer|min:1900|max:' . date('Y'),
            'year_built_max' => 'nullable|integer|min:1900|max:' . date('Y'),

            // Location
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.5|max:100', // in kilometers

            // Amenities and Features
            'amenities' => 'nullable|string',
            'features' => 'nullable|string',

            // Special Flags
            'featured' => 'nullable|boolean',
            'premium' => 'nullable|boolean',
            'new_construction' => 'nullable|boolean',
            'furnished' => 'nullable|boolean',
            'pet_friendly' => 'nullable|boolean',

            // Date Posted
            'posted_within' => 'nullable|integer|min:1|max:365', // days

            // Sorting
            'sort' => 'nullable|in:created_at,updated_at,price_low,price_high,area,bedrooms,bathrooms,views,relevance',
            'order' => 'nullable|in:asc,desc',

            // Pagination
            'per_page' => 'nullable|integer|min:6|max:100',
            'page' => 'nullable|integer|min:1',

            // Advanced Filters
            'ownership_type' => 'nullable|in:freehold,leasehold,shared_ownership',
            'energy_rating' => 'nullable|string|max:50',
            'solar_panels' => 'nullable|boolean',
            'air_conditioning' => 'nullable|in:none,window,central,split,heat_pump',
            'parking_type' => 'nullable|in:none,street,garage,carport,driveway',
            'heating_type' => 'nullable|in:none,electric,gas,oil,heat_pump,central',
            'cooling_type' => 'nullable|in:none,electric,gas,central,window,split',
            'foundation_type' => 'nullable|string|max:100',
            'roof_type' => 'nullable|string|max:100',
            'exterior_material' => 'nullable|string|max:100',
            'interior_material' => 'nullable|string|max:100',

            // Map Bounds (for map search)
            'north' => 'nullable|numeric|between:-90,90',
            'south' => 'nullable|numeric|between:-90,90',
            'east' => 'nullable|numeric|between:-180,180',
            'west' => 'nullable|numeric|between:-180,180',

            // Property Code
            'property_code' => 'nullable|string|max:50',

            // Agent/Agency
            'agent_id' => 'nullable|exists:users,id',
            'agency_id' => 'nullable|exists:agencies,id',
        ];
    }

    public function messages(): array
    {
        return [
            'q.min' => 'Search query must be at least 2 characters.',
            'q.max' => 'Search query may not be greater than 255 characters.',
            'property_type.exists' => 'Selected property type is invalid.',
            'listing_type.in' => 'Listing type must be sale, rent, or lease.',
            'min_price.numeric' => 'Minimum price must be a number.',
            'min_price.min' => 'Minimum price must be at least 0.',
            'max_price.numeric' => 'Maximum price must be a number.',
            'max_price.min' => 'Maximum price must be at least 0.',
            'min_area.numeric' => 'Minimum area must be a number.',
            'min_area.min' => 'Minimum area must be greater than 0.',
            'max_area.numeric' => 'Maximum area must be a number.',
            'max_area.min' => 'Maximum area must be greater than 0.',
            'bedrooms.integer' => 'Bedrooms must be an integer.',
            'bedrooms.min' => 'Bedrooms must be at least 0.',
            'bathrooms.integer' => 'Bathrooms must be an integer.',
            'bathrooms.min' => 'Bathrooms must be at least 0.',
            'year_built_min.min' => 'Minimum year built must be 1900 or later.',
            'year_built_min.max' => 'Minimum year built cannot be in the future.',
            'year_built_max.min' => 'Maximum year built must be 1900 or later.',
            'year_built_max.max' => 'Maximum year built cannot be in the future.',
            'city.max' => 'City name may not be greater than 100 characters.',
            'state.max' => 'State name may not be greater than 100 characters.',
            'country.max' => 'Country name may not be greater than 100 characters.',
            'neighborhood.max' => 'Neighborhood name may not be greater than 100 characters.',
            'lat.between' => 'Latitude must be between -90 and 90.',
            'lng.between' => 'Longitude must be between -180 and 180.',
            'radius.numeric' => 'Radius must be a number.',
            'radius.min' => 'Radius must be at least 0.5 km.',
            'radius.max' => 'Radius may not be greater than 100 km.',
            'posted_within.integer' => 'Posted within must be an integer.',
            'posted_within.min' => 'Posted within must be at least 1 day.',
            'posted_within.max' => 'Posted within may not be greater than 365 days.',
            'sort.in' => 'Sort option is invalid.',
            'order.in' => 'Order must be asc or desc.',
            'per_page.integer' => 'Per page must be an integer.',
            'per_page.min' => 'Per page must be at least 6.',
            'per_page.max' => 'Per page may not be greater than 100.',
            'page.integer' => 'Page must be an integer.',
            'page.min' => 'Page must be at least 1.',
            'agent_id.exists' => 'Selected agent is invalid.',
            'agency_id.exists' => 'Selected agency is invalid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Convert comma-separated amenity and feature strings to arrays
        if ($this->has('amenities') && is_string($this->amenities)) {
            $this->merge([
                'amenities' => array_map('trim', explode(',', $this->amenities))
            ]);
        }

        if ($this->has('features') && is_string($this->features)) {
            $this->merge([
                'features' => array_map('trim', explode(',', $this->features))
            ]);
        }

        // Set default values
        $this->merge([
            'sort' => $this->get('sort', 'created_at'),
            'order' => $this->get('order', 'desc'),
            'per_page' => $this->get('per_page', 12),
            'page' => $this->get('page', 1),
        ]);

        // Convert boolean strings to actual booleans
        $booleanFields = ['featured', 'premium', 'new_construction', 'furnished', 'pet_friendly', 'negotiable', 'includes_vat', 'solar_panels'];
        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([$field => $this->boolean($field)]);
            }
        }

        // Validate date ranges
        if ($this->has('year_built_min') && $this->has('year_built_max')) {
            if ($this->year_built_min > $this->year_built_max) {
                $this->merge([
                    'year_built_max' => $this->year_built_min,
                    'year_built_min' => $this->year_built_max
                ]);
            }
        }

        // Validate price ranges
        if ($this->has('min_price') && $this->has('max_price')) {
            if ($this->min_price > $this->max_price) {
                $this->merge([
                    'max_price' => $this->min_price,
                    'min_price' => $this->max_price
                ]);
            }
        }

        // Validate area ranges
        if ($this->has('min_area') && $this->has('max_area')) {
            if ($this->min_area > $this->max_area) {
                $this->merge([
                    'max_area' => $this->min_area,
                    'min_area' => $this->max_area
                ]);
            }
        }
    }
}
