<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateSimpleInvestorSeeder extends Seeder
{
    public function run(): void
    {
        // Create test investor without user dependency
        $investor = DB::table('investors')->where('id', 1)->first();
        if (!$investor) {
            DB::table('investors')->insert([
                'id' => 1,
                'user_id' => 1, // Use existing user ID
                'investor_type' => 'individual',
                'investment_portfolio_value' => 100000.00,
                'currency' => 'USD',
                'risk_tolerance' => 'moderate',
                'investment_goals' => json_encode(['long_term_growth', 'retirement']),
                'preferred_sectors' => json_encode(['technology', 'healthcare', 'real_estate']),
                'verified_at' => now(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        echo "Test investor created successfully!\n";
    }
}
