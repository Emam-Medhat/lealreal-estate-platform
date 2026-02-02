<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FixSubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        // Update existing plans with proper slugs
        $plans = DB::table('subscription_plans')->get();
        
        foreach ($plans as $plan) {
            DB::table('subscription_plans')
                ->where('id', $plan->id)
                ->update([
                    'slug' => Str::slug($plan->name) . '-' . $plan->id,
                    'trial_days' => 0,
                    'setup_fee' => 0,
                    'max_users' => 1,
                    'storage_limit' => 0,
                    'bandwidth_limit' => 0,
                    'api_calls_limit' => 0,
                ]);
        }
    }
}
