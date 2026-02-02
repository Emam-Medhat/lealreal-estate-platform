<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeadSource;

class LeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            ['name' => 'الموقع الإلكتروني', 'description' => 'عملاء جاءوا من الموقع الرسمي', 'weight' => 1],
            ['name' => 'وسائل التواصل الاجتماعي', 'description' => 'عملاء جاءوا من منصات التواصل الاجتماعي', 'weight' => 2],
            ['name' => 'إحالة مباشرة', 'description' => 'عملاء جاءوا من الإحالات المباشرة', 'weight' => 3],
            ['name' => 'مكالمة هاتفية', 'description' => 'عملاء جاءوا من المكالمات الهاتفية', 'weight' => 4],
            ['name' => 'بريد إلكتروني', 'description' => 'عملاء جاءوا من رسائل البريد الإلكتروني', 'weight' => 5],
            ['name' => 'معرض', 'description' => 'عملاء جاءوا من المعارض', 'weight' => 6],
            ['name' => 'إعلانات', 'description' => 'عملاء جاءوا من الإعلانات المدفوعة', 'weight' => 7],
            ['name' => 'توصية', 'description' => 'عملاء جاءوا من توصيات العملاء', 'weight' => 8],
            ['name' => 'أخرى', 'description' => 'مصادر أخرى', 'weight' => 9],
        ];

        foreach ($sources as $source) {
            LeadSource::firstOrCreate(
                ['name' => $source['name']],
                [
                    'description' => $source['description'],
                    'weight' => $source['weight'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
