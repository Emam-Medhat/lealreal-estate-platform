<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'العقود', 'description' => 'المستندات التعاقدية', 'color' => '#3b82f6'],
            ['name' => 'المستندات المالية', 'description' => 'الفواتير والإيصالات والكشوفات البنكية', 'color' => '#10b981'],
            ['name' => 'الهوية والجوازات', 'description' => 'بطاقات الهوية والجوازات والتصاريح', 'color' => '#8b5cf6'],
            ['name' => 'العقارات', 'description' => 'مستندات الملكية والعقارات', 'color' => '#ec4899'],
            ['name' => 'التأمين', 'description' => 'وثائق التأمين', 'color' => '#f59e0b'],
            ['name' => 'الضرائب', 'description' => 'الإقرارات الضريبية والمستندات المالية', 'color' => '#ef4444'],
        ];

        foreach ($categories as $category) {
            DocumentCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
