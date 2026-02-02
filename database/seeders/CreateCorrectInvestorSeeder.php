<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateCorrectInvestorSeeder extends Seeder
{
    public function run(): void
    {
        // Create test investor with correct columns
        $investor = DB::table('investors')->where('id', 1)->first();
        if (!$investor) {
            DB::table('investors')->insert([
                'id' => 1,
                'user_id' => 1, // Use existing user ID
                'first_name' => 'Test',
                'last_name' => 'Investor',
                'email' => 'investor@test.com',
                'phone' => '+1234567890',
                'investor_type' => 'individual',
                'status' => 'active',
                'total_invested' => 100000.00,
                'total_returns' => 15000.00,
                'risk_tolerance' => 'moderate',
                'investment_goals' => json_encode(['long_term_growth', 'retirement']),
                'preferred_sectors' => json_encode(['technology', 'healthcare', 'real_estate']),
                'experience_years' => 5,
                'accredited_investor' => 1,
                'verification_status' => 'verified',
                'verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        echo "Test investor created successfully!\n";
    }
}
