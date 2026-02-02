<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateSubscriptionTiersSeeder extends Seeder
{
    public function run(): void
    {
        // Update existing tiers with colors
        DB::table('subscription_tiers')
            ->where('slug', 'basic')
            ->update(['color' => '#6c757d']);
            
        DB::table('subscription_tiers')
            ->where('slug', 'professional')
            ->update(['color' => '#0d6efd']);
            
        DB::table('subscription_tiers')
            ->where('slug', 'enterprise')
            ->update(['color' => '#198754']);
    }
}
