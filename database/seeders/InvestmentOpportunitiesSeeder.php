<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvestmentOpportunitiesSeeder extends Seeder
{
    public function run(): void
    {
        $opportunities = [
            [
                'title' => 'فرصة استثمارية في العقارات السكنية',
                'description' => 'فرصة استثمارية ممتازة في قطاع العقارات السكنية النامي مع عائد استثماري مجزٍ',
                'type' => 'real_estate',
                'min_investment' => 50000.00,
                'max_investment' => 500000.00,
                'expected_return' => 12.50,
                'duration' => '24 شهر',
                'risk_level' => 'medium',
                'status' => 'active',
                'current_investment' => 125000.00,
                'investors_count' => 3,
                'start_date' => now()->subMonth()->toDateString(),
                'end_date' => now()->addMonths(23)->toDateString(),
                'location' => 'الرياض، السعودية',
                'featured' => true,
                'published_at' => now()->subMonth(),
                'created_at' => now()->subMonth(),
                'updated_at' => now(),
            ],
            [
                'title' => 'استثمار في التكنولوجيا الصحية',
                'description' => 'استثمار مبتكر في شركات التكنولوجيا الصحية الناشئة مع إمكانية نمو عالية',
                'type' => 'technology',
                'min_investment' => 25000.00,
                'max_investment' => 250000.00,
                'expected_return' => 18.20,
                'duration' => '36 شهر',
                'risk_level' => 'high',
                'status' => 'active',
                'current_investment' => 75000.00,
                'investors_count' => 5,
                'start_date' => now()->subWeeks(2)->toDateString(),
                'end_date' => now()->addMonths(35)->toDateString(),
                'location' => 'دبي، الإمارات',
                'featured' => true,
                'published_at' => now()->subWeeks(2),
                'created_at' => now()->subWeeks(2),
                'updated_at' => now(),
            ],
            [
                'title' => 'صندوق استثماري متنوع',
                'description' => 'صندوق استثماري متنوع يوفر استقرار وعائد جيد للمستثمرين المحافظين',
                'type' => 'fund',
                'min_investment' => 10000.00,
                'max_investment' => 1000000.00,
                'expected_return' => 8.50,
                'duration' => '12 شهر',
                'risk_level' => 'low',
                'status' => 'active',
                'current_investment' => 450000.00,
                'investors_count' => 12,
                'start_date' => now()->subDays(5)->toDateString(),
                'end_date' => now()->addMonths(11)->toDateString(),
                'location' => 'القاهرة، مصر',
                'featured' => false,
                'published_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now(),
            ],
        ];

        DB::table('investment_opportunities')->insert($opportunities);
    }
}
