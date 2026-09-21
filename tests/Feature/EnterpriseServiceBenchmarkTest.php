<?php

namespace Tests\Feature;

use App\Models\EnterpriseAccount;
use App\Models\Subscription;
use App\Services\EnterpriseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EnterpriseServiceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_dashboard_stats_accuracy_and_query_count(): void
    {
        // 1. Create test records
        // Active account created today (this month)
        $account1 = EnterpriseAccount::create([
            'company_name' => 'Acme Corp',
            'company_type' => 'Corporation',
            'industry' => 'Tech',
            'size' => '50-100',
            'phone' => '123456789',
            'address' => '123 Main St',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'contact_person' => 'John Doe',
            'contact_email' => 'john@acme.com',
            'payment_method' => 'credit_card',
            'subscription_plan' => 'enterprise',
            'status' => 'active',
            'created_at' => now(),
        ]);

        // Pending account created today (this month)
        $account2 = EnterpriseAccount::create([
            'company_name' => 'Beta Ltd',
            'company_type' => 'LLC',
            'industry' => 'Finance',
            'size' => '10-50',
            'phone' => '987654321',
            'address' => '456 Side St',
            'city' => 'Jeddah',
            'country' => 'Saudi Arabia',
            'contact_person' => 'Jane Doe',
            'contact_email' => 'jane@beta.com',
            'payment_method' => 'bank_transfer',
            'subscription_plan' => 'starter',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        // Active account created last month
        $account3 = EnterpriseAccount::create([
            'company_name' => 'Gamma Inc',
            'company_type' => 'Corporation',
            'industry' => 'Real Estate',
            'size' => '100+',
            'phone' => '555666777',
            'address' => '789 High St',
            'city' => 'Dammam',
            'country' => 'Saudi Arabia',
            'contact_person' => 'Bob Smith',
            'contact_email' => 'bob@gamma.com',
            'payment_method' => 'credit_card',
            'subscription_plan' => 'professional',
            'status' => 'active',
            'created_at' => now()->subMonth()->startOfMonth(),
        ]);

        // Subscriptions linked to enterprise accounts
        $user = \App\Models\User::factory()->create();

        $plan1Id = DB::table('subscription_plans')->insertGetId([
            'name' => 'Enterprise Plan',
            'slug' => 'enterprise-plan',
            'price' => 999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $plan2Id = DB::table('subscription_plans')->insertGetId([
            'name' => 'Professional Plan',
            'slug' => 'professional-plan',
            'price' => 299.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan1Id,
            'enterprise_id' => $account1->id,
            'plan' => 'enterprise',
            'price' => 999.00,
            'amount' => 999.00,
            'currency' => 'USD',
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'starts_at' => now(),
            'next_billing_at' => now()->addMonth(),
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan2Id,
            'enterprise_id' => $account3->id,
            'plan' => 'professional',
            'price' => 299.00,
            'amount' => 299.00,
            'currency' => 'USD',
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'starts_at' => now(),
            'next_billing_at' => now()->addMonth(),
        ]);

        // 2. Execute service method while monitoring query count
        DB::enableQueryLog();

        $service = new EnterpriseService();
        $stats = $service->getDashboardStats();

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // 3. Verify results accuracy
        $this->assertEquals(3, $stats['total_accounts']);
        $this->assertEquals(2, $stats['active_accounts']);
        $this->assertEquals(2, $stats['new_this_month']);
        $this->assertEquals(1298.00, $stats['total_revenue']);

        // 4. Verify query count reduction (should be exactly 2 queries: 1 for EnterpriseAccount, 1 for Subscription sum)
        $this->assertLessThanOrEqual(2, $queryCount, "Expected at most 2 queries, got {$queryCount}");
    }
}
