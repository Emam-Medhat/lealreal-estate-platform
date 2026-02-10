<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryCategory;
use App\Models\InventorySupplier;

class InventorySeeder extends Seeder
{
    public function run()
    {
        // Seed Categories
        $categories = [
            ['name' => 'Plumbing', 'slug' => 'plumbing', 'icon' => 'fa-faucet', 'color' => 'blue'],
            ['name' => 'Electrical', 'slug' => 'electrical', 'icon' => 'fa-bolt', 'color' => 'yellow'],
            ['name' => 'HVAC', 'slug' => 'hvac', 'icon' => 'fa-fan', 'color' => 'cyan'],
            ['name' => 'Structural', 'slug' => 'structural', 'icon' => 'fa-building', 'color' => 'gray'],
            ['name' => 'Tools', 'slug' => 'tools', 'icon' => 'fa-tools', 'color' => 'red'],
            ['name' => 'Materials', 'slug' => 'materials', 'icon' => 'fa-cubes', 'color' => 'green'],
            ['name' => 'Safety', 'slug' => 'safety', 'icon' => 'fa-hard-hat', 'color' => 'orange'],
        ];

        foreach ($categories as $cat) {
            InventoryCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // Seed Suppliers
        $suppliers = [
            ['name' => 'General Supply Co.', 'email' => 'contact@generalsupply.com', 'phone' => '123-456-7890'],
            ['name' => 'Best Tools Ltd.', 'email' => 'sales@besttools.com', 'phone' => '098-765-4321'],
            ['name' => 'ElectroWorld', 'email' => 'info@electroworld.com', 'phone' => '555-123-4567'],
            ['name' => 'Pipe Masters', 'email' => 'orders@pipemasters.com', 'phone' => '555-987-6543'],
        ];

        foreach ($suppliers as $sup) {
            InventorySupplier::updateOrCreate(['email' => $sup['email']], $sup);
        }
    }
}
