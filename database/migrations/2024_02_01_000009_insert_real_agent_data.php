<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if agents table has any records
        $agentCount = DB::table('agents')->count();
        
        // Only insert activities if agents exist
        if ($agentCount > 0) {
            // Get the first agent ID
            $agentId = DB::table('agents')->first()->id;
            
            // Insert real agent activities
            $activities = [
            [
                'agent_id' => $agentId,
                'title' => 'بيع شقة في الرياض',
                'description' => 'تم بيع شقة 3 غرف نوم في حي النخيل',
                'value' => '$450,000',
                'status' => 'completed',
                'icon' => 'fa-home',
                'type' => 'sale',
                'amount' => 450000.00,
                'client_name' => 'محمد الأحمدي',
                'property_title' => 'شقة فاخرة في النخيل',
                'metadata' => json_encode(['bedrooms' => 3, 'area' => '180م²', 'floor' => 5]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'agent_id' => $agentId,
                'title' => 'اجتماع مع عميل مستثمر',
                'description' => 'مناقشة فرص الاستثمار في العقارات التجارية',
                'value' => 'اجتماع',
                'status' => 'completed',
                'icon' => 'fa-users',
                'type' => 'meeting',
                'amount' => null,
                'client_name' => 'شركة الأمل العقارية',
                'property_title' => 'مركز تجاري',
                'metadata' => json_encode(['duration' => '2 ساعة', 'location' => 'المكتب الرئيسي']),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'agent_id' => $agentId,
                'title' => 'عرض سعر لفيلا',
                'description' => 'تقديم عرض سعر لفيلا في حي الياسمين',
                'value' => '$1,200,000',
                'status' => 'pending',
                'icon' => 'fa-file-invoice-dollar',
                'type' => 'offer',
                'amount' => 1200000.00,
                'client_name' => 'عائلة السعيد',
                'property_title' => 'فيلا فاخرة في الياسمين',
                'metadata' => json_encode(['bedrooms' => 5, 'area' => '450م²', 'pool' => true]),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'agent_id' => $agentId,
                'title' => 'إضافة عقار جديد',
                'description' => 'إضافة شقة جديدة للعرض في السوق',
                'value' => 'إدراج',
                'status' => 'active',
                'icon' => 'fa-plus-circle',
                'type' => 'listing',
                'amount' => null,
                'client_name' => 'مالك العقار',
                'property_title' => 'شقة تمويلية في الملك Abdullah',
                'metadata' => json_encode(['listing_id' => 'PROP-2024-001', 'price' => 320000]),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'agent_id' => $agentId,
                'title' => 'مكالمة متابعة مع عميل',
                'description' => 'متابعة اهتمام العميل بالعقار المعروض',
                'value' => 'مكالمة',
                'status' => 'completed',
                'icon' => 'fa-phone',
                'type' => 'call',
                'amount' => null,
                'client_name' => 'خالد العتيبي',
                'property_title' => null,
                'metadata' => json_encode(['duration' => '15 دقيقة', 'outcome' => 'مهتم جداً']),
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
        ];

        DB::table('agent_activities')->insert($activities);

        // Insert real performance metrics
        $metrics = [
            [
                'agent_id' => $agentId,
                'metric_type' => 'total_sales',
                'value' => 25,
                'period' => 'monthly',
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'breakdown' => json_encode([
                    'residential' => 18,
                    'commercial' => 5,
                    'land' => 2
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'agent_id' => $agentId,
                'metric_type' => 'commission_earned',
                'value' => 156750.00,
                'period' => 'monthly',
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'breakdown' => json_encode([
                    'sales_commission' => 125000,
                    'listing_fees' => 25000,
                    'consulting_fees' => 6750
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'agent_id' => $agentId,
                'metric_type' => 'properties_listed',
                'value' => 12,
                'period' => 'monthly',
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'breakdown' => json_encode([
                    'apartments' => 8,
                    'villas' => 3,
                    'land' => 1
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'agent_id' => $agentId,
                'metric_type' => 'satisfaction_rate',
                'value' => 94.5,
                'period' => 'monthly',
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'breakdown' => json_encode([
                    'excellent' => 45,
                    'good' => 35,
                    'average' => 15,
                    'poor' => 5
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('agent_activities')->insert($activities);
        DB::table('agent_performance_metrics')->insert($metrics);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clean up sample activities
        DB::table('agent_activities')->delete();
        DB::table('agent_performance_metrics')->delete();
    }
};
