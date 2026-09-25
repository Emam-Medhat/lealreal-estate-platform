<?php

namespace Tests\Feature;

use App\Http\Controllers\CompanyDashboardController;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\CompanyMember;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CompanyDashboardBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for authorization
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'username' => 'admin_user_' . rand(1000, 9999),
            'phone' => '+123456789' . rand(10, 99),
        ]);

        // Create company
        $this->company = Company::create([
            'name' => 'Apex Real Estate',
            'email' => 'contact@apex.com',
            'phone' => '+15550001111',
            'type' => 'agency',
            'created_by' => $this->admin->id,
            'status' => 'active',
        ]);

        // Create 2 company members
        CompanyMember::create([
            'company_id' => $this->company->id,
            'user_id' => $this->admin->id,
            'role' => 'manager',
            'status' => 'active',
        ]);

        $memberUser = User::factory()->create([
            'first_name' => 'Member',
            'last_name' => 'User',
            'username' => 'member_user_' . rand(1000, 9999),
            'phone' => '+123456789' . rand(10, 99),
        ]);

        CompanyMember::create([
            'company_id' => $this->company->id,
            'user_id' => $memberUser->id,
            'role' => 'agent',
            'status' => 'inactive',
        ]);

        // Create 1 branch
        CompanyBranch::create([
            'company_id' => $this->company->id,
            'name' => 'Main Branch',
            'address' => '123 Main St',
            'city' => 'Cairo',
            'country' => 'Egypt',
            'phone' => '+15550002222',
            'is_main' => true,
            'status' => 'active',
        ]);

        // Create properties with various statuses and stats
        Property::create([
            'agent_id' => $this->admin->id,
            'company_id' => $this->company->id,
            'title' => 'Luxury Apartment',
            'description' => 'Beautiful luxury apartment in downtown',
            'property_code' => 'PROP-101',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'status' => 'published',
            'area' => 150,
            'area_unit' => 'sq_m',
            'price' => 5000000,
            'address' => '123 Nile View',
            'city' => 'Cairo',
            'state' => 'Cairo',
            'country' => 'Egypt',
            'views_count' => 100,
            'inquiries_count' => 10,
        ]);

        Property::create([
            'agent_id' => $this->admin->id,
            'company_id' => $this->company->id,
            'title' => 'Suburban Villa',
            'description' => 'Spacious suburban villa with garden',
            'property_code' => 'PROP-102',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'status' => 'published',
            'area' => 350,
            'area_unit' => 'sq_m',
            'price' => 12000000,
            'address' => '456 Palm Hills',
            'city' => 'Giza',
            'state' => 'Giza',
            'country' => 'Egypt',
            'views_count' => 300,
            'inquiries_count' => 30,
        ]);

        Property::create([
            'agent_id' => $this->admin->id,
            'company_id' => $this->company->id,
            'title' => 'Pending Studio',
            'description' => 'Cozy pending studio apartment',
            'property_code' => 'PROP-103',
            'property_type' => 'apartment',
            'listing_type' => 'rent',
            'status' => 'pending',
            'area' => 60,
            'area_unit' => 'sq_m',
            'price' => 25000,
            'address' => '789 Smart City',
            'city' => 'Cairo',
            'state' => 'Cairo',
            'country' => 'Egypt',
            'views_count' => 50,
            'inquiries_count' => 5,
        ]);
    }

    public function test_get_quick_stats_accuracy_and_query_efficiency(): void
    {
        $this->actingAs($this->admin);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $controller = new CompanyDashboardController();
        $response = $controller->getQuickStats($this->company);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertEquals(3, $data['stats']['total_properties']);
        $this->assertEquals(2, $data['stats']['published_properties']);
        $this->assertEquals(2, $data['stats']['total_members']);
        $this->assertEquals(1, $data['stats']['total_branches']);
        $this->assertEquals(450, $data['stats']['total_views']);
        $this->assertEquals(45, $data['stats']['total_inquiries']);

        // Ensure database query count is optimized (<= 4 queries including auth check)
        $this->assertLessThanOrEqual(4, count($queries), 'Quick stats should execute 4 or fewer database queries.');
    }

    public function test_get_performance_metrics_accuracy_and_query_efficiency(): void
    {
        $this->actingAs($this->admin);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $controller = new CompanyDashboardController();
        $response = $controller->getPerformanceMetrics($this->company);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $data = $response->getData(true);
        $this->assertTrue($data['success']);

        $metrics = $data['metrics'];
        $this->assertEquals(150.0, $metrics['average_property_views']); // (100 + 300 + 50) / 3
        $this->assertEquals(15.0, $metrics['average_property_inquiries']); // (10 + 30 + 5) / 3
        $this->assertEquals(10.0, $metrics['conversion_rate']); // (45 / 450) * 100 = 10%
        $this->assertEquals(3.0, $metrics['member_productivity']); // 3 properties / 1 active member

        // Ensure database query count for performance metrics is minimized
        $this->assertLessThanOrEqual(5, count($queries), 'Performance metrics should execute 5 or fewer database queries.');
    }

    public function test_get_analytics_data_executes_without_recursion(): void
    {
        $this->actingAs($this->admin);

        $controller = new CompanyDashboardController();
        $response = $controller->getAnalyticsData($this->company);

        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('analytics', $data);
        $this->assertArrayHasKey('views_data', $data['analytics']);
        $this->assertArrayHasKey('inquiries_data', $data['analytics']);
        $this->assertArrayHasKey('status_data', $data['analytics']);
    }
}
