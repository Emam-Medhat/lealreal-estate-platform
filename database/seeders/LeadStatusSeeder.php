<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeadStatus;
use Illuminate\Support\Facades\DB;

class LeadStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'جديد', 'description' => 'عميل جديد لم يتم التواصل معه بعد', 'color' => '#6B7280', 'order' => 1],
            ['name' => 'اتصال أولي', 'description' => 'تم التواصل مع العميل لأول مرة', 'color' => '#3B82F6', 'order' => 2],
            ['name' => 'متابعة', 'description' => 'جاري متابعة العميل', 'color' => '#F59E0B', 'order' => 3],
            ['name' => 'مؤهل', 'description' => 'عميل مؤهل للشراء', 'color' => '#10B981', 'order' => 4],
            ['name' => 'مفاوضات', 'description' => 'جاري المفاوضات مع العميل', 'color' => '#8B5CF6', 'order' => 5],
            ['name' => 'مغلق', 'description' => 'تم إغلاق الصفقة بنجاح', 'color' => '#059669', 'order' => 6],
            ['name' => 'ملغي', 'description' => 'تم إلغاء الصفقة', 'color' => '#EF4444', 'order' => 7],
            ['name' => 'بارد', 'description' => 'عميل غير مهتم في الوقت الحالي', 'color' => '#64748B', 'order' => 8],
        ];

        foreach ($statuses as $status) {
            LeadStatus::firstOrCreate(
                ['name' => $status['name']],
                [
                    'description' => $status['description'],
                    'color' => $status['color'],
                    'order' => $status['order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
