<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        $tables = [
            'performance_reports', 'reports', 'warranties', 
            'service_providers', 'agents', 'properties', 'users'
        ];
        
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed Users
        $this->seedUsers();
        
        // Seed Agents
        $this->seedAgents();
        
        // Seed Service Providers
        $this->seedServiceProviders();
        
        // Seed Properties
        $this->seedProperties();
        
        // Seed Warranties
        $this->seedWarranties();
        
        // Seed Reports
        $this->seedReports();
        
        // Seed Performance Reports
        $this->seedPerformanceReports();
        
        echo "✅ Sample data seeded successfully!\n";
    }
    
    private function seedUsers()
    {
        $users = [
            [
                'uuid' => Str::uuid(),
                'username' => 'admin',
                'first_name' => 'أحمد',
                'last_name' => 'محمد',
                'full_name' => 'أحمد محمد',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'username' => 'agent',
                'first_name' => 'محمد',
                'last_name' => 'علي',
                'full_name' => 'محمد علي',
                'email' => 'agent@example.com',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'username' => 'user',
                'first_name' => 'فاطمة',
                'last_name' => 'أحمد',
                'full_name' => 'فاطمة أحمد',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('users')->insert($users);
        echo "👥 Users seeded!\n";
    }
    
    private function seedAgents()
    {
        $agents = [
            [
                'user_id' => 2,
                'name' => 'محمد علي العقاري',
                'email' => 'agent@example.com',
                'phone' => '+966501234567',
                'license_number' => 'AGR-2024-001',
                'specialization' => 'سكني',
                'experience_years' => 5,
                'rating' => 4.8,
                'total_sales' => 150,
                'total_properties' => 25,
                'commission_rate' => 2.5,
                'hire_date' => now()->subYears(5),
                'status' => 'active',
                'is_verified' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'أحمد محمد',
                'email' => 'admin@example.com',
                'phone' => '+966507654321',
                'license_number' => 'AGR-2024-002',
                'specialization' => 'تجاري',
                'experience_years' => 8,
                'rating' => 4.9,
                'total_sales' => 200,
                'total_properties' => 35,
                'commission_rate' => 3.0,
                'hire_date' => now()->subYears(8),
                'status' => 'active',
                'is_verified' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('agents')->insert($agents);
        echo "🤵 Agents seeded!\n";
    }
    
    private function seedServiceProviders()
    {
        $providers = [
            [
                'name' => 'شركة الصيانة الممتازة',
                'name_ar' => 'شركة الصيانة الممتازة',
                'contact_person' => 'أحمد خالد',
                'phone' => '+966501234567',
                'email' => 'info@maintenance.com',
                'address' => 'الرياض، المملكة العربية السعودية',
                'city' => 'الرياض',
                'state' => 'الرياض',
                'country' => 'المملكة العربية السعودية',
                'specialization' => 'صيانة عامة',
                'specialization_ar' => 'صيانة عامة',
                'status' => 'active',
                'rating' => 4.5,
                'is_verified' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'شركة النظافة الاحترافية',
                'name_ar' => 'شركة النظافة الاحترافية',
                'contact_person' => 'محمد سالم',
                'phone' => '+966507654321',
                'email' => 'info@cleaning.com',
                'address' => 'جدة، المملكة العربية السعودية',
                'city' => 'جدة',
                'state' => 'مكة المكرمة',
                'country' => 'المملكة العربية السعودية',
                'specialization' => 'نظافة',
                'specialization_ar' => 'نظافة',
                'status' => 'active',
                'rating' => 4.7,
                'is_verified' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('service_providers')->insert($providers);
        echo "🔧 Service Providers seeded!\n";
    }
    
    private function seedProperties()
    {
        $properties = [];
        $propertyTypes = [1, 2, 3, 4, 5]; // IDs for property types
        $cities = ['الرياض', 'جدة', 'مكة المكرمة', 'المدينة المنورة', 'الدمام'];
        $statusValues = [1, 2, 3]; // active, sold, pending
        
        for ($i = 1; $i <= 50; $i++) {
            $properties[] = [
                'agent_id' => rand(1, 2),
                'property_code' => 'PROP-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'title' => 'عقار مميز ' . $i,
                'slug' => 'property-' . $i,
                'description' => 'وصف عقار مميز ' . $i . ' مع جميع المرافق',
                'property_type' => $propertyTypes[array_rand($propertyTypes)],
                'listing_type' => rand(0, 1) ? 'sale' : 'rent',
                'price' => rand(100000, 2000000),
                'currency' => 'SAR',
                'area' => rand(100, 1000),
                'area_unit' => '2',
                'bedrooms' => rand(1, 6),
                'bathrooms' => rand(1, 4),
                'floors' => rand(1, 3),
                'year_built' => rand(2000, 2023),
                'status' => $statusValues[array_rand($statusValues)],
                'featured' => rand(0, 1),
                'premium' => rand(0, 1),
                'address' => 'العنوان ' . $i,
                'city' => $cities[array_rand($cities)],
                'state' => 'المنطقة الوسطى',
                'country' => 'المملكة العربية السعودية',
                'views_count' => rand(50, 500),
                'inquiries_count' => rand(5, 50),
                'favorites_count' => rand(10, 100),
                'created_at' => now()->subDays(rand(1, 365)),
                'updated_at' => now(),
            ];
        }
        
        DB::table('properties')->insert($properties);
        echo "🏠 Properties seeded!\n";
    }
    
    private function seedWarranties()
    {
        $warranties = [];
        $warrantyTypes = ['product', 'service', 'workmanship', 'extended'];
        
        for ($i = 1; $i <= 20; $i++) {
            $warranties[] = [
                'warranty_code' => 'WAR-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'warranty_number' => 'W' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'property_id' => rand(1, 50),
                'service_provider_id' => rand(1, 2),
                'warranty_type' => $warrantyTypes[array_rand($warrantyTypes)],
                'title' => 'ضمان عقار ' . $i,
                'description' => 'ضمان شامل للعقار ' . $i,
                'coverage_details' => 'تغطية كاملة لجميع الأعطال',
                'status' => 'active',
                'start_date' => now()->subMonths(rand(1, 12)),
                'end_date' => now()->addMonths(rand(6, 24)),
                'expiry_date' => now()->addMonths(rand(12, 36)),
                'duration_months' => rand(12, 36),
                'coverage_amount' => rand(10000, 100000),
                'deductible' => rand(500, 5000),
                'provider_name' => 'مقدم الضمان',
                'provider_phone' => '+96650000000' . $i,
                'created_by' => 1,
                'created_at' => now()->subDays(rand(1, 180)),
                'updated_at' => now(),
            ];
        }
        
        DB::table('warranties')->insert($warranties);
        echo "🛡️ Warranties seeded!\n";
    }
    
    private function seedReports()
    {
        $reports = [];
        $reportTypes = ['performance', 'sales', 'market', 'custom'];
        
        for ($i = 1; $i <= 15; $i++) {
            $reports[] = [
                'title' => 'تقرير ' . $i,
                'type' => $reportTypes[array_rand($reportTypes)],
                'description' => 'وصف التقرير ' . $i,
                'status' => 'completed',
                'data' => json_encode(['key' => 'value']),
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now(),
            ];
        }
        
        DB::table('reports')->insert($reports);
        echo "📊 Reports seeded!\n";
    }
    
    private function seedPerformanceReports()
    {
        $performanceReports = [];
        
        for ($i = 1; $i <= 25; $i++) {
            $performanceReports[] = [
                'report_id' => rand(1, 15),
                'agent_id' => rand(1, 2),
                'total_sales' => rand(50000, 500000),
                'total_commission' => rand(5000, 50000),
                'properties_listed' => rand(5, 25),
                'properties_sold' => rand(2, 15),
                'conversion_rate' => rand(5, 25) + (rand(0, 99) / 100),
                'average_sale_price' => rand(100000, 1000000),
                'customer_satisfaction' => rand(70, 100) + (rand(0, 99) / 100),
                'leads_generated' => rand(10, 100),
                'appointments_scheduled' => rand(5, 50),
                'period_start' => now()->subMonths(rand(1, 6)),
                'period_end' => now()->subMonths(rand(0, 5)),
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now(),
            ];
        }
        
        DB::table('performance_reports')->insert($performanceReports);
        echo "📈 Performance Reports seeded!\n";
    }
}
