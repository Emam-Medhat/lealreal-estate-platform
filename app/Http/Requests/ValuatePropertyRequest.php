<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValuatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        return [
            // Property Identification
            'property_id' => 'nullable|exists:properties,id',
            
            // Property Basic Information
            'title' => 'required_without:property_id|string|max:255',
            'property_type_id' => 'required_without:property_id|exists:property_types,id',
            'listing_type' => 'required_without:property_id|in:sale,rent,lease',
            
            // Location Information
            'address' => 'required_without:property_id|string|max:500',
            'city' => 'required_without:property_id|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required_without:property_id|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'neighborhood' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            
            // Property Details
            'bedrooms' => 'nullable|integer|min:0|max:50',
            'bathrooms' => 'nullable|integer|min:0|max:50',
            'floors' => 'nullable|integer|min:0|max:100',
            'parking_spaces' => 'nullable|integer|min:0|max:50',
            'year_built' => 'nullable|integer|min:1900|max:' . date('Y'),
            'area' => 'required_without:property_id|numeric|min:1|max:100000',
            'area_unit' => 'required_without:property_id|in:sq_m,sq_ft',
            'land_area' => 'nullable|numeric|min:1|max:1000000',
            'land_area_unit' => 'nullable|in:sq_m,sq_ft,acre,hectare',
            
            // Property Condition
            'condition' => 'required|in:excellent,good,fair,poor,needs_renovation',
            'condition_score' => 'nullable|numeric|min:1|max:10',
            'renovation_needed' => 'nullable|boolean',
            'renovation_cost_estimate' => 'nullable|numeric|min:0|max:1000000',
            'last_renovation_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            
            // Features and Amenities
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:property_amenities,id',
            'features' => 'nullable|array',
            'features.*' => 'exists:property_features,id',
            'custom_features' => 'nullable|array',
            'custom_features.*' => 'string|max:255',
            
            // Building Information
            'building_type' => 'nullable|in:detached,semi_detached,terraced,apartment,condo,townhouse,villa,mansion,commercial,industrial',
            'construction_material' => 'nullable|string|max:100',
            'foundation_type' => 'nullable|string|max:100',
            'roof_type' => 'nullable|string|max:100',
            'exterior_condition' => 'nullable|in:excellent,good,fair,poor',
            'interior_condition' => 'nullable|in:excellent,good,fair,poor',
            
            // Utilities and Systems
            'heating_system' => 'nullable|in:none,electric,gas,oil,heat_pump,central,solar',
            'cooling_system' => 'nullable|in:none,electric,central,window,split,heat_pump',
            'plumbing_condition' => 'nullable|in:excellent,good,fair,poor,outdated',
            'electrical_system' => 'nullable|in:modern,updated,adequate,outdated,needs_upgrade',
            'water_supply' => 'nullable|in:municipal,well,tank,rainwater',
            'sewage_system' => 'nullable|in:municipal,septic_tank,other',
            
            // Energy and Sustainability
            'energy_rating' => 'nullable|string|max:50',
            'energy_efficiency_score' => 'nullable|numeric|min:1|max:100',
            'solar_panels' => 'nullable|boolean',
            'solar_panel_capacity' => 'nullable|numeric|min:0|max:100', // kW
            'insulation_quality' => 'nullable|in:poor,fair,good,excellent',
            'double_glazing' => 'nullable|boolean',
            'triple_glazing' => 'nullable|boolean',
            'smart_home_features' => 'nullable|boolean',
            
            // Security and Safety
            'security_system' => 'nullable|in:none,basic,advanced,premium',
            'fire_safety' => 'nullable|in:none,basic,standard,advanced',
            'security_doors' => 'nullable|boolean',
            'security_windows' => 'nullable|boolean',
            'cctv' => 'nullable|boolean',
            'alarm_system' => 'nullable|boolean',
            
            // Outdoor Features
            'garden' => 'nullable|boolean',
            'garden_area' => 'nullable|numeric|min:0|max:10000',
            'pool' => 'nullable|boolean',
            'pool_type' => 'nullable|in:none,above_ground,in_ground,indoor,outdoor,infinity',
            'pool_size' => 'nullable|string|max:100',
            'terrace' => 'nullable|boolean',
            'balcony' => 'nullable|boolean',
            'balcony_count' => 'nullable|integer|min:0|max:20',
            'outdoor_kitchen' => 'nullable|boolean',
            'bbq_area' => 'nullable|boolean',
            
            // Parking
            'parking_type' => 'nullable|in:none,street,garage,carport,driveway,underground',
            'garage_spaces' => 'nullable|integer|min:0|max:20',
            'carport_spaces' => 'nullable|integer|min:0|max:20',
            'driveway_spaces' => 'nullable|integer|min:0|max:20',
            'secured_parking' => 'nullable|boolean',
            
            // Views and Location Advantages
            'view_quality' => 'nullable|in:none,poor,fair,good,excellent,premium',
            'view_type' => 'nullable|string|max:100',
            'sea_view' => 'nullable|boolean',
            'mountain_view' => 'nullable|boolean',
            'city_view' => 'nullable|boolean',
            'park_view' => 'nullable|boolean',
            'waterfront' => 'nullable|boolean',
            'waterfront_type' => 'nullable|in:beach,lake,river,canal',
            
            // Neighborhood Information
            'neighborhood_quality' => 'nullable|in:poor,fair,good,excellent,premium',
            'school_district_quality' => 'nullable|in:poor,fair,good,excellent',
            'proximity_schools' => 'nullable|numeric|min:0|max:50', // km
            'proximity_shopping' => 'nullable|numeric|min:0|max:50', // km
            'proximity_transport' => 'nullable|numeric|min:0|max:50', // km
            'proximity_healthcare' => 'nullable|numeric|min:0|max:50', // km
            'noise_level' => 'nullable|in:quiet,moderate,noisy,very_noisy',
            'crime_rate' => 'nullable|in:very_low,low,moderate,high,very_high',
            
            // Market Information
            'current_market_trend' => 'nullable|in:declining,stable,increasing,rapidly_increasing',
            'local_demand' => 'nullable|in:very_low,low,moderate,high,very_high',
            'days_on_market_avg' => 'nullable|integer|min:0|max:1000',
            'price_per_sqm_area' => 'nullable|numeric|min:0|max:100000',
            
            // Legal and Ownership
            'ownership_type' => 'nullable|in:freehold,leasehold,shared_ownership,rental',
            'lease_years_remaining' => 'nullable|integer|min:0|max:999',
            'ground_rent' => 'nullable|numeric|min:0|max:10000',
            'service_charges' => 'nullable|numeric|min:0|max:10000',
            'council_tax_band' => 'nullable|string|max:10',
            
            // Comparable Properties
            'comparable_properties' => 'nullable|array',
            'comparable_properties.*.address' => 'required|string|max:500',
            'comparable_properties.*.price' => 'required|numeric|min:0',
            'comparable_properties.*.area' => 'required|numeric|min:1',
            'comparable_properties.*.bedrooms' => 'nullable|integer|min:0',
            'comparable_properties.*.condition' => 'nullable|in:excellent,good,fair,poor',
            'comparable_properties.*.distance' => 'nullable|numeric|min:0|max:10', // km
            
            // Valuation Preferences
            'valuation_method' => 'nullable|in:automated,manual,hybrid',
            'valuation_purpose' => 'required|in:sale,purchase,refinance,insurance,investment,probate,divorce',
            'valuation_date' => 'required|date|before_or_equal:today',
            'currency' => 'required|string|in:SAR,USD,EUR,GBP,AED',
            'include_confidence_score' => 'nullable|boolean',
            'include_market_analysis' => 'nullable|boolean',
            'include_recommendations' => 'nullable|boolean',
            
            // Additional Information
            'notes' => 'nullable|string|max:2000',
            'special_features' => 'nullable|array',
            'special_features.*' => 'string|max:255',
            'defects' => 'nullable|array',
            'defects.*' => 'string|max:255',
            'recent_improvements' => 'nullable|array',
            'recent_improvements.*' => 'string|max:255',
            'improvement_cost' => 'nullable|numeric|min:0|max:500000',
            'improvement_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        ];
    }

    public function messages(): array
    {
        return [
            'property_id.exists' => 'Selected property is invalid.',
            'title.required_without' => 'Title is required when not valuing an existing property.',
            'property_type_id.required_without' => 'Property type is required when not valuing an existing property.',
            'property_type_id.exists' => 'Selected property type is invalid.',
            'listing_type.required_without' => 'Listing type is required when not valuing an existing property.',
            'listing_type.in' => 'Listing type must be sale, rent, or lease.',
            'address.required_without' => 'Address is required when not valuing an existing property.',
            'city.required_without' => 'City is required when not valuing an existing property.',
            'country.required_without' => 'Country is required when not valuing an existing property.',
            'area.required_without' => 'Property area is required when not valuing an existing property.',
            'area.numeric' => 'Area must be a number.',
            'area.min' => 'Area must be greater than 0.',
            'area.max' => 'Area may not be greater than 100,000.',
            'area_unit.required_without' => 'Area unit is required when not valuing an existing property.',
            'area_unit.in' => 'Area unit must be sq_m or sq_ft.',
            'condition.required' => 'Property condition is required.',
            'condition.in' => 'Condition must be excellent, good, fair, poor, or needs_renovation.',
            'condition_score.numeric' => 'Condition score must be a number.',
            'condition_score.min' => 'Condition score must be at least 1.',
            'condition_score.max' => 'Condition score may not be greater than 10.',
            'renovation_cost_estimate.numeric' => 'Renovation cost estimate must be a number.',
            'renovation_cost_estimate.min' => 'Renovation cost estimate must be at least 0.',
            'renovation_cost_estimate.max' => 'Renovation cost estimate may not be greater than 1,000,000.',
            'last_renovation_year.integer' => 'Last renovation year must be an integer.',
            'last_renovation_year.min' => 'Last renovation year must be 1900 or later.',
            'last_renovation_year.max' => 'Last renovation year cannot be in the future.',
            'amenities.*.exists' => 'One or more selected amenities are invalid.',
            'features.*.exists' => 'One or more selected features are invalid.',
            'custom_features.*.string' => 'Custom features must be strings.',
            'custom_features.*.max' => 'Custom feature descriptions may not be greater than 255 characters.',
            'energy_efficiency_score.numeric' => 'Energy efficiency score must be a number.',
            'energy_efficiency_score.min' => 'Energy efficiency score must be at least 1.',
            'energy_efficiency_score.max' => 'Energy efficiency score may not be greater than 100.',
            'solar_panel_capacity.numeric' => 'Solar panel capacity must be a number.',
            'solar_panel_capacity.min' => 'Solar panel capacity must be at least 0.',
            'solar_panel_capacity.max' => 'Solar panel capacity may not be greater than 100 kW.',
            'garden_area.numeric' => 'Garden area must be a number.',
            'garden_area.min' => 'Garden area must be at least 0.',
            'garden_area.max' => 'Garden area may not be greater than 10,000.',
            'balcony_count.integer' => 'Balcony count must be an integer.',
            'balcony_count.min' => 'Balcony count must be at least 0.',
            'balcony_count.max' => 'Balcony count may not be greater than 20.',
            'garage_spaces.integer' => 'Garage spaces must be an integer.',
            'garage_spaces.min' => 'Garage spaces must be at least 0.',
            'garage_spaces.max' => 'Garage spaces may not be greater than 20.',
            'carport_spaces.integer' => 'Carport spaces must be an integer.',
            'carport_spaces.min' => 'Carport spaces must be at least 0.',
            'carport_spaces.max' => 'Carport spaces may not be greater than 20.',
            'driveway_spaces.integer' => 'Driveway spaces must be an integer.',
            'driveway_spaces.min' => 'Driveway spaces must be at least 0.',
            'driveway_spaces.max' => 'Driveway spaces may not be greater than 20.',
            'proximity_schools.numeric' => 'Proximity to schools must be a number.',
            'proximity_schools.min' => 'Proximity to schools must be at least 0.',
            'proximity_schools.max' => 'Proximity to schools may not be greater than 50 km.',
            'proximity_shopping.numeric' => 'Proximity to shopping must be a number.',
            'proximity_shopping.min' => 'Proximity to shopping must be at least 0.',
            'proximity_shopping.max' => 'Proximity to shopping may not be greater than 50 km.',
            'proximity_transport.numeric' => 'Proximity to transport must be a number.',
            'proximity_transport.min' => 'Proximity to transport must be at least 0.',
            'proximity_transport.max' => 'Proximity to transport may not be greater than 50 km.',
            'proximity_healthcare.numeric' => 'Proximity to healthcare must be a number.',
            'proximity_healthcare.min' => 'Proximity to healthcare must be at least 0.',
            'proximity_healthcare.max' => 'Proximity to healthcare may not be greater than 50 km.',
            'days_on_market_avg.integer' => 'Average days on market must be an integer.',
            'days_on_market_avg.min' => 'Average days on market must be at least 0.',
            'days_on_market_avg.max' => 'Average days on market may not be greater than 1000.',
            'price_per_sqm_area.numeric' => 'Price per square meter must be a number.',
            'price_per_sqm_area.min' => 'Price per square meter must be at least 0.',
            'price_per_sqm_area.max' => 'Price per square meter may not be greater than 100,000.',
            'lease_years_remaining.integer' => 'Lease years remaining must be an integer.',
            'lease_years_remaining.min' => 'Lease years remaining must be at least 0.',
            'lease_years_remaining.max' => 'Lease years remaining may not be greater than 999.',
            'ground_rent.numeric' => 'Ground rent must be a number.',
            'ground_rent.min' => 'Ground rent must be at least 0.',
            'ground_rent.max' => 'Ground rent may not be greater than 10,000.',
            'service_charges.numeric' => 'Service charges must be a number.',
            'service_charges.min' => 'Service charges must be at least 0.',
            'service_charges.max' => 'Service charges may not be greater than 10,000.',
            'council_tax_band.max' => 'Council tax band may not be greater than 10 characters.',
            'comparable_properties.*.address.required' => 'Address is required for comparable properties.',
            'comparable_properties.*.price.required' => 'Price is required for comparable properties.',
            'comparable_properties.*.price.numeric' => 'Price must be a number for comparable properties.',
            'comparable_properties.*.price.min' => 'Price must be at least 0 for comparable properties.',
            'comparable_properties.*.area.required' => 'Area is required for comparable properties.',
            'comparable_properties.*.area.numeric' => 'Area must be a number for comparable properties.',
            'comparable_properties.*.area.min' => 'Area must be greater than 0 for comparable properties.',
            'comparable_properties.*.bedrooms.integer' => 'Bedrooms must be an integer for comparable properties.',
            'comparable_properties.*.bedrooms.min' => 'Bedrooms must be at least 0 for comparable properties.',
            'comparable_properties.*.condition.in' => 'Condition must be excellent, good, fair, or poor for comparable properties.',
            'comparable_properties.*.distance.numeric' => 'Distance must be a number for comparable properties.',
            'comparable_properties.*.distance.min' => 'Distance must be at least 0 for comparable properties.',
            'comparable_properties.*.distance.max' => 'Distance may not be greater than 10 km for comparable properties.',
            'valuation_purpose.required' => 'Valuation purpose is required.',
            'valuation_purpose.in' => 'Valuation purpose must be sale, purchase, refinance, insurance, investment, probate, or divorce.',
            'valuation_date.required' => 'Valuation date is required.',
            'valuation_date.date' => 'Valuation date must be a valid date.',
            'valuation_date.before_or_equal' => 'Valuation date cannot be in the future.',
            'currency.required' => 'Currency is required.',
            'currency.in' => 'Currency must be SAR, USD, EUR, GBP, or AED.',
            'notes.max' => 'Notes may not be greater than 2000 characters.',
            'special_features.*.string' => 'Special features must be strings.',
            'special_features.*.max' => 'Special feature descriptions may not be greater than 255 characters.',
            'defects.*.string' => 'Defects must be strings.',
            'defects.*.max' => 'Defect descriptions may not be greater than 255 characters.',
            'recent_improvements.*.string' => 'Recent improvements must be strings.',
            'recent_improvements.*.max' => 'Recent improvement descriptions may not be greater than 255 characters.',
            'improvement_cost.numeric' => 'Improvement cost must be a number.',
            'improvement_cost.min' => 'Improvement cost must be at least 0.',
            'improvement_cost.max' => 'Improvement cost may not be greater than 500,000.',
            'improvement_year.integer' => 'Improvement year must be an integer.',
            'improvement_year.min' => 'Improvement year must be 1900 or later.',
            'improvement_year.max' => 'Improvement year cannot be in the future.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'include_confidence_score' => $this->boolean('include_confidence_score', true),
            'include_market_analysis' => $this->boolean('include_market_analysis', true),
            'include_recommendations' => $this->boolean('include_recommendations', true),
            'renovation_needed' => $this->boolean('renovation_needed', false),
            'solar_panels' => $this->boolean('solar_panels', false),
            'double_glazing' => $this->boolean('double_glazing', false),
            'triple_glazing' => $this->boolean('triple_glazing', false),
            'smart_home_features' => $this->boolean('smart_home_features', false),
            'security_doors' => $this->boolean('security_doors', false),
            'security_windows' => $this->boolean('security_windows', false),
            'cctv' => $this->boolean('cctv', false),
            'alarm_system' => $this->boolean('alarm_system', false),
            'garden' => $this->boolean('garden', false),
            'pool' => $this->boolean('pool', false),
            'terrace' => $this->boolean('terrace', false),
            'balcony' => $this->boolean('balcony', false),
            'outdoor_kitchen' => $this->boolean('outdoor_kitchen', false),
            'bbq_area' => $this->boolean('bbq_area', false),
            'secured_parking' => $this->boolean('secured_parking', false),
            'sea_view' => $this->boolean('sea_view', false),
            'mountain_view' => $this->boolean('mountain_view', false),
            'city_view' => $this->boolean('city_view', false),
            'park_view' => $this->boolean('park_view', false),
            'waterfront' => $this->boolean('waterfront', false),
        ]);

        // Set default valuation date to today if not provided
        if (!$this->has('valuation_date')) {
            $this->merge(['valuation_date' => now()->toDateString()]);
        }

        // Clean and format array fields
        $arrayFields = ['amenities', 'features', 'custom_features', 'special_features', 'defects', 'recent_improvements'];
        foreach ($arrayFields as $field) {
            if ($this->has($field) && is_array($this->$field)) {
                $this->merge([$field => array_map('trim', array_filter($this->$field))]);
            }
        }

        // Validate comparable properties structure
        if ($this->has('comparable_properties') && is_array($this->comparable_properties)) {
            $validatedComparables = [];
            foreach ($this->comparable_properties as $index => $comparable) {
                if (is_array($comparable)) {
                    $validatedComparables[] = $comparable;
                }
            }
            $this->merge(['comparable_properties' => $validatedComparables]);
        }
    }
}
