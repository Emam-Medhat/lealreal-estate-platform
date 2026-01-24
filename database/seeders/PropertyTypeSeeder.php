<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertyTypes = [
            [
                'name' => 'Apartment',
                'slug' => 'apartment',
                'description' => 'Residential apartment units',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Villa',
                'slug' => 'villa',
                'description' => 'Luxury villa properties',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'House',
                'slug' => 'house',
                'description' => 'Single family houses',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Land',
                'slug' => 'land',
                'description' => 'Residential and commercial land',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Commercial',
                'slug' => 'commercial',
                'description' => 'Commercial properties and offices',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Studio',
                'slug' => 'studio',
                'description' => 'Studio apartments',
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Penthouse',
                'slug' => 'penthouse',
                'description' => 'Luxury penthouse units',
                'is_active' => true,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Townhouse',
                'slug' => 'townhouse',
                'description' => 'Townhouse properties',
                'is_active' => true,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('property_types')->insert($propertyTypes);
    }
}
