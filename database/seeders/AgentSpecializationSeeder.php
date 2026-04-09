<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgentSpecialization;

class AgentSpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            ['name' => 'السكني', 'description' => 'متخصص في العقارات السكنية', 'icon' => 'fas fa-home'],
            ['name' => 'تجاري', 'description' => 'متخصص في العقارات التجارية', 'icon' => 'fas fa-store'],
            ['name' => 'فلل', 'description' => 'متخصص في العقارات الفاخرة', 'icon' => 'fas fa-gem'],
            ['name' => 'أراضي', 'description' => 'متخصص في الأراضي والمقاسم', 'icon' => 'fas fa-mountain'],
            ['name' => 'استثماري', 'description' => 'متخصص في العقارات الاستثمارية', 'icon' => 'fas fa-chart-line'],
            ['name' => 'إداري', 'description' => 'متخصص في العقارات الصناعية', 'icon' => 'fas fa-industry'],
            ['name' => 'مزارع', 'description' => 'متخصص في المزارع والمزارع السكنية', 'icon' => 'fas fa-seedling'],
            ['name' => 'شاليهات', 'description' => 'متخصص في الشاليهات والقصور', 'icon' => 'fas fa-umbrella-beach'],
            ['name' => 'عمارات سكنية', 'description' => 'متخصص في العمارات السكنية', 'icon' => 'fas fa-building'],
            ['name' => 'فيلا', 'description' => 'متخصص في الفلل والقصور', 'icon' => 'fas fa-home'],
            ['name' => 'مكاتب', 'description' => 'متخصص في المكاتب والمكاتب الإدارية', 'icon' => 'fas fa-briefcase'],
            ['name' => 'محلات تجارية', 'description' => 'متخصص في المحلات التجارية', 'icon' => 'fas fa-shopping-cart'],
            ['name' => 'مستودعات', 'description' => 'متخصص في المستودعات والمخازن', 'icon' => 'fas fa-warehouse'],
        ];

        foreach ($specializations as $specialization) {
            AgentSpecialization::create(array_merge($specialization, ['is_active' => true]));
        }
    }
}
