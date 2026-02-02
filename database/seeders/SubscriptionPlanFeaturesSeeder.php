<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // Get all plans and features
        $plans = DB::table('subscription_plans')->get();
        $features = DB::table('subscription_features')->get();

        foreach ($plans as $plan) {
            // Assign features to plans based on plan type
            $featureIds = [];
            
            if ($plan->name === 'Basic') {
                // Basic plan gets basic features
                $featureIds = [1]; // Property Listings
            } elseif ($plan->name === 'Professional') {
                // Professional plan gets more features
                $featureIds = [1, 2]; // Property Listings, Analytics Dashboard
            } elseif ($plan->name === 'Enterprise') {
                // Enterprise plan gets all features
                $featureIds = [1, 2, 3, 4]; // All features
            }

            foreach ($featureIds as $index => $featureId) {
                DB::table('subscription_plan_features')->insert([
                    'subscription_plan_id' => $plan->id,
                    'subscription_feature_id' => $featureId,
                    'limit' => null, // No limit for now
                    'included' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
