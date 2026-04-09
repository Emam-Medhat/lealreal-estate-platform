<?php

namespace Database\Seeders;

use App\Models\PropertyAmenity;
use App\Models\PropertyFeature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PropertyAmenityFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PropertyAmenity::truncate();
        PropertyFeature::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $amenities = [
            ['name' => 'Swimming Pool', 'icon' => 'fas fa-swimmer', 'category' => 'outdoor'],
            ['name' => 'Gym', 'icon' => 'fas fa-dumbbell', 'category' => 'other'],
            ['name' => 'Garden', 'icon' => 'fas fa-leaf', 'category' => 'outdoor'],
            ['name' => 'Parking', 'icon' => 'fas fa-car', 'category' => 'general'],
            ['name' => 'Security', 'icon' => 'fas fa-shield-alt', 'category' => 'security'],
            ['name' => 'Air Conditioning', 'icon' => 'fas fa-snowflake', 'category' => 'utilities'],
            ['name' => 'Heating', 'icon' => 'fas fa-thermometer-half', 'category' => 'utilities'],
            ['name' => 'Balcony', 'icon' => 'fas fa-door-open', 'category' => 'outdoor'],
        ];

        foreach ($amenities as $index => $amenity) {
            PropertyAmenity::create([
                'name' => $amenity['name'],
                'slug' => Str::slug($amenity['name']),
                'icon' => $amenity['icon'],
                'category' => $amenity['category'],
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }

        $features = [
            ['name' => 'Sea View', 'icon' => 'fas fa-water', 'category' => 'location'],
            ['name' => 'City View', 'icon' => 'fas fa-city', 'category' => 'location'],
            ['name' => 'Smart Home', 'icon' => 'fas fa-home', 'category' => 'technology'],
            ['name' => 'Fiber Optic Internet', 'icon' => 'fas fa-network-wired', 'category' => 'technology'],
            ['name' => 'Private Entrance', 'icon' => 'fas fa-door-closed', 'category' => 'general'],
            ['name' => 'Maid Room', 'icon' => 'fas fa-user-friends', 'category' => 'interior'],
            ['name' => 'Storage Room', 'icon' => 'fas fa-box-open', 'category' => 'interior'],
        ];

        foreach ($features as $index => $feature) {
            PropertyFeature::create([
                'name' => $feature['name'],
                'slug' => Str::slug($feature['name']),
                'icon' => $feature['icon'],
                'category' => $feature['category'],
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
