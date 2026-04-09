<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'EWAN Agency',
                'email' => 'info@ewan.com',
                'phone' => '+966 12 345 6789',
                'website' => 'https://ewan.com',
                'type' => 'agency',
                'registration_number' => 'REG-001',
                'status' => 'active',
                'description' => 'Professional real estate agency with expertise in residential and commercial properties.',
                'founded_date' => '2015-01-15',
                'employee_count' => 50,
                'city' => 'Riyadh',
                'country' => 'المملكة العربية السعودية',
                'is_verified' => true,
                'verification_level' => 3,
                'rating' => 4.8,
                'total_reviews' => 125,
                'subscription_plan' => 'professional',
                'created_by' => 1,
            ],
            [
                'name' => 'Luxury Homes International',
                'email' => 'contact@luxuryhomes.com',
                'phone' => '+966 50 123 4567',
                'website' => 'https://luxuryhomes.com',
                'type' => 'agency',
                'registration_number' => 'REG-002',
                'status' => 'active',
                'description' => 'Specialized in luxury and high-end properties across the Kingdom.',
                'founded_date' => '2018-06-20',
                'employee_count' => 25,
                'city' => 'Jeddah',
                'country' => 'المملكة العربية السعودية',
                'is_verified' => true,
                'verification_level' => 4,
                'rating' => 4.9,
                'total_reviews' => 89,
                'subscription_plan' => 'enterprise',
                'created_by' => 1,
            ],
            [
                'name' => 'Property Management Solutions',
                'email' => 'info@propertymanagement.com',
                'phone' => '+966 55 987 6543',
                'website' => 'https://propertymanagement.com',
                'type' => 'property_management',
                'registration_number' => 'REG-003',
                'status' => 'active',
                'description' => 'Comprehensive property management services for residential and commercial properties.',
                'founded_date' => '2012-03-10',
                'employee_count' => 75,
                'city' => 'Dammam',
                'country' => 'المملكة العربية السعودية',
                'is_verified' => true,
                'verification_level' => 3,
                'rating' => 4.6,
                'total_reviews' => 203,
                'subscription_plan' => 'professional',
                'created_by' => 1,
            ],
            [
                'name' => 'Golden Gate Developers',
                'email' => 'projects@goldengate.com',
                'phone' => '+966 13 456 7890',
                'website' => 'https://goldengate.com',
                'type' => 'development',
                'registration_number' => 'REG-004',
                'status' => 'active',
                'description' => 'Leading property development company with focus on residential communities.',
                'founded_date' => '2010-09-05',
                'employee_count' => 120,
                'city' => 'Riyadh',
                'country' => 'المملكة العربية السعودية',
                'is_verified' => true,
                'verification_level' => 4,
                'rating' => 4.7,
                'total_reviews' => 156,
                'subscription_plan' => 'enterprise',
                'created_by' => 1,
            ],
            [
                'name' => 'Al-Mashreq Real Estate',
                'email' => 'info@almashreq.com',
                'phone' => '+966 11 234 5678',
                'website' => 'https://almashreq.com',
                'type' => 'agency',
                'registration_number' => 'REG-005',
                'status' => 'active',
                'description' => 'Traditional real estate agency with deep local market knowledge.',
                'founded_date' => '2005-12-01',
                'employee_count' => 30,
                'city' => 'Mecca',
                'country' => 'المملكة العربية السعودية',
                'is_verified' => true,
                'verification_level' => 2,
                'rating' => 4.5,
                'total_reviews' => 67,
                'subscription_plan' => 'basic',
                'created_by' => 1,
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
