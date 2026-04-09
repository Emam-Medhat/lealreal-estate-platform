<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Developer;
use Illuminate\Support\Facades\Hash;

class DeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a sample developer user first
        $developerUser = User::firstOrCreate([
            'email' => 'developer1@example.com',
        ], [
            'username' => 'dev_ahmed_2024',
            'password' => Hash::make('password'),
            'first_name' => 'Ahmed',
            'last_name' => 'Developer',
            'full_name' => 'Ahmed Developer',
            'phone' => '+966501234567',
            'user_type' => 'developer',
            'is_developer' => true,
            'email_verified_at' => now(),
        ]);

        // Create the developer profile if not exists
        if (!Developer::where('user_id', $developerUser->id)->withoutGlobalScopes()->exists()) {
            Developer::create([
                'user_id' => $developerUser->id,
                'company_name' => 'Ahmed Development Co.',
                'company_name_ar' => 'شركة أحمد للتطوير',
                'license_number' => 'DEV-2024-001',
                'commercial_register' => 'CR-2024-001',
                'tax_number' => 'TAX-2024-001',
                'developer_type' => 'residential',
                'status' => 'active',
                'phone' => '+966501234567',
                'email' => 'developer1@example.com',
                'website' => 'https://ahmeddev.com',
                'description' => 'Leading real estate development company specializing in residential projects.',
                'description_ar' => 'شركة رائدة في تطوير العقارات متخصصة في المشاريع السكنية.',
                'address' => json_encode([
                    'street' => 'King Fahd Road',
                    'city' => 'Riyadh',
                    'state' => 'Riyadh',
                    'country' => 'Saudi Arabia',
                    'postal_code' => '12345'
                ]),
                'contact_person' => json_encode([
                    'name' => 'Ahmed Developer',
                    'title' => 'CEO',
                    'phone' => '+966501234567',
                    'email' => 'ahmed@ahmeddev.com'
                ]),
                'established_year' => 2020,
                'total_projects' => 15,
                'total_investment' => 50000000.00,
                'specializations' => json_encode(['residential', 'commercial', 'mixed']),
                'certifications' => json_encode(['ISO 9001', 'LEED Certified']),
                'is_verified' => true,
                'is_featured' => true,
                'rating' => 4.5,
                'review_count' => 25,
                'social_media' => json_encode([
                    'facebook' => 'https://facebook.com/ahmeddev',
                    'twitter' => 'https://twitter.com/ahmeddev',
                    'linkedin' => 'https://linkedin.com/company/ahmeddev'
                ]),
                'verified_at' => now(),
            ]);

            // Create developer profile
            \DB::table('developer_profiles')->insert([
                'developer_id' => $developerUser->id,
                'company_name_ar' => 'شركة أحمد للتطوير',
                'about_us' => 'Leading real estate development company specializing in residential projects.',
                'about_us_ar' => 'شركة رائدة في تطوير العقارات متخصصة في المشاريع السكنية.',
                'vision' => 'To become the leading real estate developer in the region.',
                'vision_ar' => 'أن نصبح المطور العقاري الرائد في المنطقة.',
                'mission' => 'To deliver high-quality residential projects that exceed customer expectations.',
                'mission_ar' => 'تقديم مشاريع سكنية عالية الجودة تتجاوز توقعات العملاء.',
                'established_year' => 2020,
                'employees_count' => 50,
                'engineers_count' => 15,
                'headquarters_address' => json_encode([
                    'street' => 'King Fahd Road',
                    'city' => 'Riyadh',
                    'state' => 'Riyadh',
                    'country' => 'Saudi Arabia',
                    'postal_code' => '12345'
                ]),
                'contact_information' => json_encode([
                    'phone' => '+966501234567',
                    'email' => 'info@ahmeddev.com',
                    'address' => 'King Fahd Road, Riyadh, Saudi Arabia',
                    'working_hours' => 'Sun-Thu: 9AM-6PM'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create another developer
        $developerUser2 = User::firstOrCreate([
            'email' => 'developer2@example.com',
        ], [
            'username' => 'dev_mohammed_2024',
            'password' => Hash::make('password'),
            'first_name' => 'Mohammed',
            'last_name' => 'Builder',
            'full_name' => 'Mohammed Builder',
            'phone' => '+966507654321',
            'user_type' => 'developer',
            'is_developer' => true,
            'email_verified_at' => now(),
        ]);

        // Create the developer profile if not exists
        if (!Developer::where('user_id', $developerUser2->id)->withoutGlobalScopes()->exists()) {
            Developer::create([
                'user_id' => $developerUser2->id,
                'company_name' => 'Mohammed Builders',
                'company_name_ar' => 'محمد للمقاولات',
                'license_number' => 'DEV-2024-002',
                'commercial_register' => 'CR-2024-002',
                'developer_type' => 'commercial',
                'status' => 'pending',
                'phone' => '+966507654321',
                'email' => 'developer2@example.com',
                'address' => json_encode([
                    'street' => 'Olaya Street',
                    'city' => 'Riyadh',
                    'state' => 'Riyadh',
                    'country' => 'Saudi Arabia',
                    'postal_code' => '54321'
                ]),
                'contact_person' => json_encode([
                    'name' => 'Mohammed Builder',
                    'title' => 'Managing Director',
                    'phone' => '+966507654321',
                    'email' => 'mohammed@builders.com'
                ]),
                'established_year' => 2018,
                'total_projects' => 8,
                'total_investment' => 30000000.00,
                'specializations' => json_encode(['commercial', 'industrial']),
                'rating' => 4.2,
                'review_count' => 12,
            ]);

            // Create developer profile
            \DB::table('developer_profiles')->insert([
                'developer_id' => $developerUser2->id,
                'company_name_ar' => 'محمد للمقاولات',
                'about_us' => 'Specialized in commercial and industrial construction projects.',
                'about_us_ar' => 'متخصص في مشاريع البناء التجارية والصناعية.',
                'vision' => 'To be the preferred construction partner for commercial projects.',
                'vision_ar' => 'أن نكون الشريك المفضل للمشاريع التجارية.',
                'mission' => 'Delivering high-quality commercial construction on time and within budget.',
                'mission_ar' => 'تقديم بناء تجاري عالي الجودة في الوقت المحدد وضمن الميزانية.',
                'established_year' => 2018,
                'employees_count' => 30,
                'engineers_count' => 8,
                'headquarters_address' => json_encode([
                    'street' => 'Olaya Street',
                    'city' => 'Riyadh',
                    'state' => 'Riyadh',
                    'country' => 'Saudi Arabia',
                    'postal_code' => '54321'
                ]),
                'contact_information' => json_encode([
                    'phone' => '+966507654321',
                    'email' => 'info@builders.com',
                    'address' => 'Olaya Street, Riyadh, Saudi Arabia',
                    'working_hours' => 'Sun-Thu: 8AM-5PM'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
