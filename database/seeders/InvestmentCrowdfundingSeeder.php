<?php

namespace Database\Seeders;

use App\Models\InvestmentCrowdfunding;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvestmentCrowdfundingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first(); // Get the first user as creator

        $campaigns = [
            [
                'campaign_name' => 'Luxury Dubai Marina Development',
                'description' => 'A premium residential development in the heart of Dubai Marina featuring 150 luxury apartments with stunning sea views and world-class amenities.',
                'category' => 'Real Estate',
                'funding_goal' => 5000000.00,
                'total_raised' => 3200000.00,
                'investor_count' => 45,
                'minimum_investment' => 10000.00,
                'maximum_investment' => 500000.00,
                'equity_offered' => 15.5000,
                'projected_return_rate' => 18.7500,
                'risk_level' => 'medium',
                'status' => 'published',
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(4),
                'published_at' => now()->subMonths(2),
                'location' => 'Dubai Marina, UAE',
                'tags' => json_encode(['luxury', 'real-estate', 'dubai', 'investment']),
                'created_by' => $user?->id,
                'updated_by' => $user?->id,
            ],
            [
                'campaign_name' => 'Solar Energy Farm Project',
                'description' => 'A large-scale solar energy farm project spanning 200 acres, providing clean energy to over 50,000 homes and generating sustainable returns.',
                'category' => 'Renewable Energy',
                'funding_goal' => 8500000.00,
                'total_raised' => 6100000.00,
                'investor_count' => 78,
                'minimum_investment' => 25000.00,
                'maximum_investment' => 1000000.00,
                'equity_offered' => 20.0000,
                'projected_return_rate' => 22.5000,
                'risk_level' => 'low',
                'status' => 'published',
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonths(3),
                'published_at' => now()->subMonths(3),
                'location' => 'Riyadh Province, Saudi Arabia',
                'tags' => json_encode(['solar', 'renewable', 'energy', 'sustainable']),
                'created_by' => $user?->id,
                'updated_by' => $user?->id,
            ],
            [
                'campaign_name' => 'Tech Startup AI Platform',
                'description' => 'An innovative AI-powered SaaS platform for business automation, targeting the global enterprise market with proven traction and revenue.',
                'category' => 'Technology',
                'funding_goal' => 2500000.00,
                'total_raised' => 1800000.00,
                'investor_count' => 32,
                'minimum_investment' => 5000.00,
                'maximum_investment' => 250000.00,
                'equity_offered' => 12.5000,
                'projected_return_rate' => 28.0000,
                'risk_level' => 'high',
                'status' => 'published',
                'start_date' => now()->subMonth(),
                'end_date' => now()->addMonths(5),
                'published_at' => now()->subMonth(),
                'location' => 'Silicon Valley, California',
                'tags' => json_encode(['AI', 'SaaS', 'technology', 'startup']),
                'created_by' => $user?->id,
                'updated_by' => $user?->id,
            ],
        ];

        foreach ($campaigns as $campaign) {
            InvestmentCrowdfunding::create($campaign);
        }
    }
}
