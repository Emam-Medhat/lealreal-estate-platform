<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create subscription tiers
        DB::table('subscription_tiers')->insert([
            [
                'name' => 'Basic',
                'description' => 'Entry level tier',
                'slug' => 'basic',
                'level' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional',
                'description' => 'Professional tier',
                'slug' => 'professional',
                'level' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Enterprise tier',
                'slug' => 'enterprise',
                'level' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create subscription features
        DB::table('subscription_features')->insert([
            [
                'name' => 'Property Listings',
                'description' => 'List properties on the platform',
                'slug' => 'property-listings',
                'icon' => 'home',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Analytics Dashboard',
                'description' => 'View detailed analytics',
                'slug' => 'analytics-dashboard',
                'icon' => 'chart-bar',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'API Access',
                'description' => 'Access to REST API',
                'slug' => 'api-access',
                'icon' => 'code',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Priority Support',
                'description' => '24/7 priority customer support',
                'slug' => 'priority-support',
                'icon' => 'headset',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Update plans with tier_id
        $basicPlan = DB::table('subscription_plans')->where('name', 'Basic')->first();
        $professionalPlan = DB::table('subscription_plans')->where('name', 'Professional')->first();
        $enterprisePlan = DB::table('subscription_plans')->where('name', 'Enterprise')->first();

        $basicTier = DB::table('subscription_tiers')->where('slug', 'basic')->first();
        $professionalTier = DB::table('subscription_tiers')->where('slug', 'professional')->first();
        $enterpriseTier = DB::table('subscription_tiers')->where('slug', 'enterprise')->first();

        if ($basicPlan && $basicTier) {
            DB::table('subscription_plans')->where('id', $basicPlan->id)->update(['tier_id' => $basicTier->id]);
        }
        if ($professionalPlan && $professionalTier) {
            DB::table('subscription_plans')->where('id', $professionalPlan->id)->update(['tier_id' => $professionalTier->id]);
        }
        if ($enterprisePlan && $enterpriseTier) {
            DB::table('subscription_plans')->where('id', $enterprisePlan->id)->update(['tier_id' => $enterpriseTier->id]);
        }
    }
}
