<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateTestInvestorSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test user first if not exists
        $user = DB::table('users')->where('email', 'investor@test.com')->first();
        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Test Investor',
                'email' => 'investor@test.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $userId = $user->id;
        }

        // Create test investor
        $investor = DB::table('investors')->where('user_id', $userId)->first();
        if (!$investor) {
            DB::table('investors')->insert([
                'user_id' => $userId,
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
