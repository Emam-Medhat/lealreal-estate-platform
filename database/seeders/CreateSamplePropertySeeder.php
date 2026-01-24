<?php

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\PropertyLocation;
use App\Models\PropertyPrice;
use App\Models\PropertyDetail;

class CreateSamplePropertySeeder extends Seeder
{
    public function run()
    {
        echo "Creating sample property...\n";

        // Create Property
        $property = Property::create([
            'agent_id' => 1,
            'property_code' => 'PROP-001',
            'title' => 'Luxury Villa in Riyadh',
            'description' => 'Beautiful luxury villa with modern amenities and spacious living areas',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'price' => 2500000.00,
            'currency' => 'SAR',
            'area' => 500.00,
            'area_unit' => 'sq_m',
            'bedrooms' => 5,
            'bathrooms' => 4,
            'floors' => 2,
            'year_built' => 2020,
            'status' => 'active',
            'featured' => true,
            'premium' => true,
            'address' => 'King Abdullah Road',
            'city' => 'Riyadh',
            'state' => 'Riyadh Province',
            'country' => 'Saudi Arabia',
            'postal_code' => '12345',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'views_count' => 0,
            'favorites_count' => 0,
            'inquiries_count' => 0,
        ]);

        echo "Property created with ID: {$property->id}\n";

        // Create Property Location
        PropertyLocation::create([
            'property_id' => $property->id,
            'address' => 'King Abdullah Road',
            'city' => 'Riyadh',
            'state' => 'Riyadh Province',
            'country' => 'Saudi Arabia',
            'postal_code' => '12345',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'neighborhood' => 'Al-Malaz',
            'district' => 'Central',
        ]);

        echo "Location created\n";

        // Create Property Price
        PropertyPrice::create([
            'property_id' => $property->id,
            'price' => 2500000.00,
            'currency' => 'SAR',
            'price_type' => 'sale',
            'price_per_sqm' => 5000.00,
            'is_negotiable' => false,
            'includes_vat' => true,
            'vat_rate' => 15.00,
            'service_charges' => 5000.00,
            'maintenance_fees' => 2000.00,
            'effective_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        echo "Price created\n";

        // Create Property Details
        PropertyDetail::create([
            'property_id' => $property->id,
            'bedrooms' => 5,
            'bathrooms' => 4,
            'floors' => 2,
            'parking_spaces' => 3,
            'year_built' => 2020,
            'area' => 500.00,
            'area_unit' => 'sq_m',
            'land_area' => 600.00,
            'land_area_unit' => 'sq_m',
            'specifications' => json_encode([
                'construction' => 'Concrete',
                'roof_type' => 'Flat',
                'foundation' => 'Reinforced Concrete'
            ]),
            'materials' => json_encode([
                'walls' => 'Brick',
                'floors' => 'Marble',
                'windows' => 'Aluminum'
            ]),
            'interior_features' => 'Marble floors, central AC, modern kitchen, built-in wardrobes',
            'exterior_features' => 'Garden, swimming pool, garage, security system',
        ]);

        echo "Details created\n";
        echo "Sample property created successfully!\n";
    }
}
