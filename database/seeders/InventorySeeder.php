<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\InventoryCategory;

class InventorySeeder extends Seeder
{
    public function run()
    {
        // Create categories first
        $categories = [
            ['name' => 'أجهزة كمبيوتر', 'slug' => 'computers', 'description' => 'لابتوبات، كمبيوترات مكتبية، ملحقات'],
            ['name' => 'طابعات', 'slug' => 'printers', 'description' => 'طابعات حبر، ليزر، ماسح ضوئي'],
            ['name' => 'أثاث مكتبي', 'slug' => 'furniture', 'description' => 'كراسي، مكاتب، خزائن'],
            ['name' => 'مستلزمات مكتبية', 'slug' => 'supplies', 'description' => 'ورق، أقلام، مجلدات'],
        ];

        foreach ($categories as $category) {
            InventoryCategory::firstOrCreate($category);
        }

        // Create inventory items
        $items = [
            [
                'item_code' => 'DL-5420-001',
                'name' => 'لابتوب Dell Latitude 5420',
                'sku' => 'DL-5420-001',
                'description' => 'لابتوب Dell بمعالج Intel Core i5، ذاكرة 8GB، قرص 256GB SSD',
                'category' => 'equipment',
                'quantity' => 5,
                'unit_cost' => 3500.00,
                'selling_price' => 4000.00,
                'reorder_point' => 2,
                'status' => 'active',
                'unit' => 'piece',
                'brand' => 'Dell',
                'model' => 'Latitude 5420'
            ],
            [
                'item_code' => 'HP-840-001',
                'name' => 'لابتوب HP EliteBook 840 G6',
                'sku' => 'HP-840-001',
                'description' => 'لابتوب HP بمعالج Intel Core i7، ذاكرة 16GB، قرص 512GB SSD',
                'category' => 'equipment',
                'quantity' => 3,
                'unit_cost' => 4500.00,
                'selling_price' => 5200.00,
                'reorder_point' => 1,
                'status' => 'active',
                'unit' => 'piece',
                'brand' => 'HP',
                'model' => 'EliteBook 840 G6'
            ],
            [
                'item_code' => 'HP-M404-001',
                'name' => 'طابعة HP LaserJet Pro M404n',
                'sku' => 'HP-M404-001',
                'description' => 'طابعة ليزر أحادية اللون، سرعة 40 صفحة في الدقيقة',
                'category' => 'equipment',
                'quantity' => 8,
                'unit_cost' => 1200.00,
                'selling_price' => 1500.00,
                'reorder_point' => 3,
                'status' => 'active',
                'unit' => 'piece',
                'brand' => 'HP',
                'model' => 'LaserJet Pro M404n'
            ],
            [
                'item_code' => 'CN-G301-001',
                'name' => 'طابعة Canon PIXMA G3010',
                'sku' => 'CN-G301-001',
                'description' => 'طابعة حبر متعددة الوظائف، طبع، نسخ، مسح ضوئي',
                'category' => 'equipment',
                'quantity' => 12,
                'unit_cost' => 450.00,
                'selling_price' => 600.00,
                'reorder_point' => 5,
                'status' => 'active',
                'unit' => 'piece',
                'brand' => 'Canon',
                'model' => 'PIXMA G3010'
            ],
            [
                'item_code' => 'CH-ERG-001',
                'name' => 'كرسي مكتبي ergonomic',
                'sku' => 'CH-ERG-001',
                'description' => 'كرسي مكتبي مريح مع دعم للظهر، قابل للتعديل',
                'category' => 'supplies',
                'quantity' => 15,
                'unit_cost' => 800.00,
                'selling_price' => 1000.00,
                'reorder_point' => 5,
                'status' => 'active',
                'unit' => 'piece',
                'brand' => 'Generic',
                'model' => 'Ergonomic Chair'
            ],
            [
                'item_code' => 'DS-160-001',
                'name' => 'مكتب عمل 160x80 سم',
                'sku' => 'DS-160-001',
                'description' => 'مكتب عمل خشبي، أبعاد 160x80 سم، مع أدراج',
                'category' => 'supplies',
                'quantity' => 6,
                'unit_cost' => 1500.00,
                'selling_price' => 2000.00,
                'reorder_point' => 2,
                'status' => 'active',
                'unit' => 'piece',
                'brand' => 'Generic',
                'model' => 'Office Desk 160x80'
            ],
            [
                'item_code' => 'PA-A4-001',
                'name' => 'رز A4 80 جرام',
                'sku' => 'PA-A4-001',
                'description' => 'رز ورق A4، 80 جرام، 500 ورقة',
                'category' => 'supplies',
                'quantity' => 50,
                'unit_cost' => 25.00,
                'selling_price' => 35.00,
                'reorder_point' => 20,
                'status' => 'active',
                'unit' => 'ream',
                'brand' => 'Generic',
                'model' => 'A4 Paper 80g'
            ],
            [
                'item_code' => 'PN-BLK-001',
                'name' => 'قلم حبر أسود',
                'sku' => 'PN-BLK-001',
                'description' => 'قلم حبر جاف، لون أسود، عبوة 12 قطعة',
                'category' => 'supplies',
                'quantity' => 100,
                'unit_cost' => 15.00,
                'selling_price' => 20.00,
                'reorder_point' => 50,
                'status' => 'active',
                'unit' => 'pack',
                'brand' => 'Generic',
                'model' => 'Pen Black 12pc'
            ],
        ];

        foreach ($items as $item) {
            $item['created_by'] = 1; // Assuming user ID 1 exists
            $item['updated_by'] = 1;
            Inventory::firstOrCreate(['sku' => $item['sku']], $item);
        }

        $this->command->info('Inventory items seeded successfully!');
    }
}
