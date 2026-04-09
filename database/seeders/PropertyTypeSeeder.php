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
                'name' => 'شقة',
                'slug' => 'apartment',
                'description' => 'وحدة سكنية في مبنى سكني',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'فيلا',
                'slug' => 'villa',
                'description' => 'منزل منفصل مع حديقة خاصة',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'منزل',
                'slug' => 'house',
                'description' => 'منزل عائلي منفصل',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'أرض',
                'slug' => 'land',
                'description' => 'قطعة أرض فارغة للبناء',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'مكتب',
                'slug' => 'office',
                'description' => 'مساحة تجارية للعمل',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'محل تجاري',
                'slug' => 'shop',
                'description' => 'مساحة تجارية للبيع بالتجزئة',
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'استوديو',
                'slug' => 'studio',
                'description' => 'وحدة سكنية صغيرة بغرفة واحدة',
                'is_active' => true,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'بنتهاوس',
                'slug' => 'penthouse',
                'description' => 'شقة فاخرة في الطابق العلوي',
                'is_active' => true,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'دوبليكس',
                'slug' => 'duplex',
                'description' => 'وحدة سكنية بمستويين',
                'is_active' => true,
                'sort_order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'توين هاوس',
                'slug' => 'townhouse',
                'description' => 'منزل متصل بمنازل أخرى',
                'is_active' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('property_types')->insert($propertyTypes);
    }
}
