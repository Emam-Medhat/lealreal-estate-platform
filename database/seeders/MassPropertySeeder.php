<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\PropertyLocation;
use App\Models\PropertyPrice;
use App\Models\PropertyDetail;
use App\Models\PropertyMedia;
use App\Models\Agent;
use App\Models\PropertyType;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class MassPropertySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $imageUrls = [
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1598228723793-52759bba239c?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1583608205776-bfd35f0d9f83?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600607687940-47a0f9259d4b?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1416331108676-a22ccb276e35?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1513584684374-8bdb74838a0f?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1523217582562-09d0def993a4?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80'
        ];

        $agents = Agent::all();
        $propertyTypes = PropertyType::all();

        if ($agents->isEmpty()) {
            $this->command->error("No agents found. Please seed agents first.");
            return;
        }

        if ($propertyTypes->isEmpty()) {
            $this->command->error("No property types found. Please seed property types first.");
            return;
        }

        echo "Generating 100 properties...\n";

        for ($i = 0; $i < 100; $i++) {
            $title = $faker->company . " " . $faker->randomElement(['Villa', 'Apartment', 'Tower', 'Residence', 'Lodge']);
            $price = $faker->numberBetween(500000, 10000000);
            $agent = $agents->random();
            $type = $propertyTypes->random();
            $mediaUrl = $faker->randomElement($imageUrls);

            // 1. Create Property
            $property = Property::create([
                'agent_id' => $agent->id,
                'property_code' => 'MASS-' . strtoupper(Str::random(6)),
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'description' => $faker->paragraph(4),
                'property_type' => $type->id,
                'listing_type' => $faker->randomElement(['sale', 'rent']),
                'price' => $price,
                'currency' => 'SAR',
                'area' => $faker->numberBetween(100, 1000),
                'area_unit' => 'sq_m',
                'bedrooms' => $faker->numberBetween(1, 8),
                'bathrooms' => $faker->numberBetween(1, 6),
                'floors' => $faker->numberBetween(1, 3),
                'year_built' => $faker->numberBetween(2000, 2025),
                'status' => 'active',
                'featured' => $faker->boolean(20),
                'premium' => $faker->boolean(10),
                'address' => $faker->address,
                'city' => $faker->city,
                'state' => $faker->state,
                'country' => 'Saudi Arabia',
                'postal_code' => $faker->postcode,
                'latitude' => $faker->latitude(24.0, 25.0),
                'longitude' => $faker->longitude(46.0, 47.0),
                'views_count' => $faker->numberBetween(10, 5000),
                'favorites_count' => $faker->numberBetween(0, 100),
                'inquiries_count' => $faker->numberBetween(0, 50),
                'facade_image' => $mediaUrl, // Full URL
            ]);

            // 2. Create Property Location
            PropertyLocation::create([
                'property_id' => $property->id,
                'address' => $property->address,
                'city' => $property->city,
                'state' => $property->state,
                'country' => $property->country,
                'postal_code' => $property->postal_code,
                'latitude' => $property->latitude,
                'longitude' => $property->longitude,
                'neighborhood' => $faker->streetName,
                'district' => $faker->citySuffix,
            ]);

            // 3. Create Property Price
            PropertyPrice::create([
                'property_id' => $property->id,
                'price' => $property->price,
                'currency' => $property->currency,
                'price_type' => $property->listing_type,
                'price_per_sqm' => round($property->price / $property->area, 2),
                'is_negotiable' => $faker->boolean(30),
                'includes_vat' => true,
                'vat_rate' => 15.00,
                'effective_date' => now()->toDateString(),
                'is_active' => true,
            ]);

            // 4. Create Property Details
            PropertyDetail::create([
                'property_id' => $property->id,
                'bedrooms' => $property->bedrooms,
                'bathrooms' => $property->bathrooms,
                'floors' => $property->floors,
                'parking_spaces' => $faker->numberBetween(1, 4),
                'year_built' => $property->year_built,
                'area' => $property->area,
                'area_unit' => $property->area_unit,
                'land_area' => $property->area + 50,
                'land_area_unit' => 'sq_m',
                'interior_features' => $faker->sentence(10),
                'exterior_features' => $faker->sentence(10),
            ]);

            // 5. Create Property Media
            PropertyMedia::create([
                'property_id' => $property->id,
                'media_type' => 'image',
                'file_name' => basename($mediaUrl),
                'file_path' => $mediaUrl, // Full URL
                'file_size' => 1024 * 1024,
                'file_type' => 'image/jpeg',
                'is_primary' => true,
                'is_featured' => $property->featured,
                'uploaded_by' => 1,
                'sort_order' => 0,
            ]);

            if ($i % 10 == 0) echo "Created $i properties...\n";
        }
        
        echo "Successfully seeded 100 properties with real image URLs!\n";
    }
}
