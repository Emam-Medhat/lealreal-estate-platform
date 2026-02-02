<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Create the table if it doesn't exist
        if (!Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->string('currency', 3)->default('USD');
                $table->integer('duration_days');
                $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly'])->default('monthly');
                $table->json('features')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_popular')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Insert some sample data
        DB::table('subscription_plans')->insert([
            [
                'name' => 'Basic',
                'description' => 'Perfect for individuals getting started',
                'price' => 9.99,
                'currency' => 'USD',
                'duration_days' => 30,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Up to 5 properties',
                    'Basic analytics',
                    'Email support'
                ]),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional',
                'description' => 'Ideal for growing businesses',
                'price' => 29.99,
                'currency' => 'USD',
                'duration_days' => 30,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Up to 50 properties',
                    'Advanced analytics',
                    'Priority support',
                    'API access'
                ]),
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Complete solution for large organizations',
                'price' => 99.99,
                'currency' => 'USD',
                'duration_days' => 30,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Unlimited properties',
                    'Custom analytics',
                    '24/7 phone support',
                    'Full API access',
                    'Custom integrations'
                ]),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
