<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        return [
            // Basic Information
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'property_type_id' => 'required|exists:property_types,id',
            'listing_type' => 'required|in:sale,rent,lease',
            'status' => 'required|in:draft,active,inactive,sold,rented',
            'featured' => 'boolean',
            'premium' => 'boolean',

            // Property Details
            'bedrooms' => 'nullable|integer|min:0|max:50',
            'bathrooms' => 'nullable|integer|min:0|max:50',
            'floors' => 'nullable|integer|min:0|max:100',
            'parking_spaces' => 'nullable|integer|min:0|max:50',
            'year_built' => 'nullable|integer|min:1900|max:' . date('Y'),
            'area' => 'required|numeric|min:1|max:100000',
            'area_unit' => 'required|in:sq_m,sq_ft',
            'land_area' => 'nullable|numeric|min:1|max:1000000',
            'land_area_unit' => 'nullable|in:sq_m,sq_ft,acre,hectare',
            'specifications' => 'nullable|array',
            'materials' => 'nullable|array',
            'interior_features' => 'nullable|array',
            'exterior_features' => 'nullable|array',

            // Location Information
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'neighborhood' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'coordinates' => 'nullable|array',
            'nearby_landmarks' => 'nullable|array',
            'transportation' => 'nullable|array',

            // Price Information
            'price' => 'required|numeric|min:0|max:999999999.99',
            'currency' => 'required|string|in:SAR,USD,EUR,GBP,AED',
            'is_negotiable' => 'boolean',
            'includes_vat' => 'boolean',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'service_charges' => 'nullable|numeric|min:0|max:999999.99',
            'maintenance_fees' => 'nullable|numeric|min:0|max:999999.99',
            'payment_frequency' => 'nullable|in:monthly,quarterly,annually',
            'payment_terms' => 'nullable|array',

            // Media Files
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif|max:10240',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'videos' => 'nullable|array',
            'videos.*' => 'file|mimes:mp4,avi,mov,wmv|max:51200',

            // Amenities and Features
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:property_amenities,id',
            'features' => 'nullable|array',
            'features.*' => 'exists:property_features,id',

            // Virtual Tours and Floor Plans
            'virtual_tour_url' => 'nullable|url',
            'floor_plans' => 'nullable|array',
            'floor_plans.*' => 'image|mimes:jpeg,jpg,png|max:10240',

            // Additional Information
            'nearby_places' => 'nullable|array',
            'schools' => 'nullable|array',
            'hospitals' => 'nullable|array',
            'shopping_centers' => 'nullable|array',
            'restaurants' => 'nullable|array',
            'public_transport' => 'nullable|array',

            // Legal and Compliance
            'ownership_type' => 'nullable|in:freehold,leasehold,shared_ownership',
            'deed_number' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'zoning' => 'nullable|string|max:100',
            'building_permit' => 'nullable|string|max:100',
            'occupancy_permit' => 'nullable|string|max:100',

            // Energy and Sustainability
            'energy_rating' => 'nullable|string|max:50',
            'solar_panels' => 'boolean',
            'water_heating' => 'nullable|in:solar,electric,gas,heat_pump',
            'insulation' => 'nullable|in:poor,fair,good,excellent',
            'double_glazing' => 'boolean',
            'air_conditioning' => 'nullable|in:none,window,central,split,heat_pump',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Property title is required.',
            'title.max' => 'Property title may not be greater than 255 characters.',
            'description.required' => 'Property description is required.',
            'description.min' => 'Property description must be at least 50 characters.',
            'property_type_id.required' => 'Property type is required.',
            'property_type_id.exists' => 'Selected property type is invalid.',
            'listing_type.required' => 'Listing type is required.',
            'listing_type.in' => 'Listing type must be sale, rent, or lease.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 0.',
            'area.required' => 'Property area is required.',
            'area.numeric' => 'Area must be a number.',
            'area.min' => 'Area must be greater than 0.',
            'address.required' => 'Address is required.',
            'city.required' => 'City is required.',
            'country.required' => 'Country is required.',
            'currency.required' => 'Currency is required.',
            'currency.in' => 'Currency must be SAR, USD, EUR, GBP, or AED.',
            'images.*.image' => 'Uploaded files must be images.',
            'images.*.mimes' => 'Images must be jpeg, jpg, png, or gif files.',
            'images.*.max' => 'Image files may not be larger than 10MB.',
            'documents.*.file' => 'Uploaded files must be documents.',
            'documents.*.mimes' => 'Documents must be PDF, DOC, DOCX, XLS, XLSX, PPT, or PPTX files.',
            'documents.*.max' => 'Document files may not be larger than 10MB.',
            'amenities.*.exists' => 'One or more selected amenities are invalid.',
            'features.*.exists' => 'One or more selected features are invalid.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'year_built.min' => 'Year built must be 1900 or later.',
            'year_built.max' => 'Year built cannot be in the future.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Convert comma-separated strings to arrays
        if ($this->has('specifications') && is_string($this->specifications)) {
            $this->merge([
                'specifications' => array_map('trim', explode(',', $this->specifications))
            ]);
        }

        if ($this->has('materials') && is_string($this->materials)) {
            $this->merge([
                'materials' => array_map('trim', explode(',', $this->materials))
            ]);
        }

        if ($this->has('interior_features') && is_string($this->interior_features)) {
            $this->merge([
                'interior_features' => array_map('trim', explode(',', $this->interior_features))
            ]);
        }

        if ($this->has('exterior_features') && is_string($this->exterior_features)) {
            $this->merge([
                'exterior_features' => array_map('trim', explode(',', $this->exterior_features))
            ]);
        }

        if ($this->has('nearby_places') && is_string($this->nearby_places)) {
            $this->merge([
                'nearby_places' => array_map('trim', explode(',', $this->nearby_places))
            ]);
        }

        if ($this->has('schools') && is_string($this->schools)) {
            $this->merge([
                'schools' => array_map('trim', explode(',', $this->schools))
            ]);
        }

        if ($this->has('hospitals') && is_string($this->hospitals)) {
            $this->merge([
                'hospitals' => array_map('trim', explode(',', $this->hospitals))
            ]);
        }

        if ($this->has('shopping_centers') && is_string($this->shopping_centers)) {
            $this->merge([
                'shopping_centers' => array_map('trim', explode(',', $this->shopping_centers))
            ]);
        }

        if ($this->has('restaurants') && is_string($this->restaurants)) {
            $this->merge([
                'restaurants' => array_map('trim', explode(',', $this->restaurants))
            ]);
        }

        if ($this->has('public_transport') && is_string($this->public_transport)) {
            $this->merge([
                'public_transport' => array_map('trim', explode(',', $this->public_transport))
            ]);
        }

        // Set default values
        $this->merge([
            'featured' => $this->boolean('featured', false),
            'premium' => $this->boolean('premium', false),
            'is_negotiable' => $this->boolean('is_negotiable', false),
            'includes_vat' => $this->boolean('includes_vat', false),
            'solar_panels' => $this->boolean('solar_panels', false),
            'double_glazing' => $this->boolean('double_glazing', false),
        ]);
    }
}
