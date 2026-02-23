<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;

class LeadSeeder extends Seeder
{
    public function run()
    {
        // Get or create a user for leads
        $user = User::first() ?: User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $leads = [
            [
                'first_name' => 'أحمد',
                'last_name' => 'محمد',
                'email' => 'ahmed@example.com',
                'phone' => '+966501234567',
                'company' => 'شركة الأحلام العقارية',
                'budget_max' => 500000,
                'lead_source' => 'website',
                'lead_status' => 'new',
                'priority' => 3, // high
                'created_by' => $user->id,
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'first_name' => 'فاطمة',
                'last_name' => 'العلي',
                'email' => 'fatima@example.com',
                'phone' => '+966502345678',
                'company' => 'مؤسسة المستقبل',
                'budget_max' => 750000,
                'lead_source' => 'referral',
                'lead_status' => 'contacted',
                'priority' => 2, // medium
                'created_by' => $user->id,
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'first_name' => 'محمد',
                'last_name' => 'السالم',
                'email' => 'mohammed@example.com',
                'phone' => '+966503456789',
                'company' => 'شركة النجاح',
                'budget_max' => 1200000,
                'lead_source' => 'social_media',
                'lead_status' => 'qualified',
                'priority' => 3, // high
                'created_by' => $user->id,
                'created_at' => Carbon::now()->subDays(7),
            ],
            [
                'first_name' => 'مريم',
                'last_name' => 'حسن',
                'email' => 'mariam@example.com',
                'phone' => '+966504567890',
                'company' => 'مجموعة الأمل',
                'budget_max' => 300000,
                'lead_source' => 'email',
                'lead_status' => 'new',
                'priority' => 1, // low
                'created_by' => $user->id,
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'first_name' => 'عبدالله',
                'last_name' => 'الخالد',
                'email' => 'abdullah@example.com',
                'phone' => '+966505678901',
                'company' => 'شركة التميز',
                'budget_max' => 900000,
                'lead_source' => 'phone',
                'lead_status' => 'contacted',
                'priority' => 2, // medium
                'created_by' => $user->id,
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'first_name' => 'نورة',
                'last_name' => 'العبدالكريم',
                'email' => 'noura@example.com',
                'phone' => '+966506789012',
                'company' => 'مؤسسة الإبداع',
                'budget_max' => 600000,
                'lead_source' => 'website',
                'lead_status' => 'qualified',
                'priority' => 2, // medium
                'created_by' => $user->id,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'first_name' => 'خالد',
                'last_name' => 'الرشيد',
                'email' => 'khaled@example.com',
                'phone' => '+966507890123',
                'company' => null,
                'budget_max' => 400000,
                'lead_source' => 'referral',
                'lead_status' => 'new',
                'priority' => 1, // low
                'created_by' => $user->id,
                'created_at' => Carbon::now()->subDays(4),
            ],
            [
                'first_name' => 'سارة',
                'last_name' => 'الأحمدي',
                'email' => 'sara@example.com',
                'phone' => '+966508901234',
                'company' => 'شركة الرؤية',
                'budget_max' => 1500000,
                'lead_source' => 'social_media',
                'lead_status' => 'qualified',
                'priority' => 3, // high
                'created_by' => $user->id,
                'created_at' => Carbon::now()->subDays(6),
            ],
        ];

        foreach ($leads as $leadData) {
            Lead::create($leadData);
        }

        $this->command->info('Sample leads created successfully!');
    }
}
