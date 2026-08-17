<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use App\Services\CompanyAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CompanyAnalyticsBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_analytics_get_performance_metrics()
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
        ]);

        $company = Company::create([
            'name' => 'Acme Real Estate',
            'email' => 'acme@example.com',
            'phone' => '123456789',
            'type' => 'agency',
            'created_by' => $user->id,
            'status' => 'active',
        ]);

        $member = CompanyMember::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'role' => 'agent',
            'status' => 'active',
        ]);

        Property::create([
            'company_id' => $company->id,
            'agent_id' => $user->id,
            'assigned_agent_id' => $user->id,
            'title' => 'Test Property 1',
            'description' => 'A test property',
            'price' => 100000,
            'area' => 120,
            'area_unit' => 'sq_m',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'status' => 'sold',
            'address' => '123 Main St',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'property_code' => 'PROP-001',
        ]);

        Property::create([
            'company_id' => $company->id,
            'agent_id' => $user->id,
            'assigned_agent_id' => $user->id,
            'title' => 'Test Property 2',
            'description' => 'Another test property',
            'price' => 200000,
            'area' => 150,
            'area_unit' => 'sq_m',
            'property_type' => 'house',
            'listing_type' => 'sale',
            'status' => 'pending',
            'address' => '456 Side St',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'property_code' => 'PROP-002',
        ]);

        Lead::create([
            'assigned_to' => $user->id,
            'first_name' => 'Lead',
            'last_name' => 'One',
            'email' => 'lead1@example.com',
            'lead_status' => 'converted',
            'estimated_value' => 50000,
            'created_by' => $user->id,
        ]);

        $service = new CompanyAnalyticsService;

        DB::flushQueryLog();
        DB::enableQueryLog();

        $metrics = $service->getPerformanceMetrics($company->id, 'month');

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $this->assertEquals(2, $metrics['properties']['total']);
        $this->assertEquals(1, $metrics['properties']['sold']);
        $this->assertEquals(1, $metrics['properties']['pending']);
        $this->assertEquals(150000, $metrics['properties']['average_price']);
        $this->assertEquals(300000, $metrics['properties']['total_value']);

        // Assert query efficiency
        $this->assertLessThanOrEqual(12, $queryCount, "Query count was {$queryCount}");
    }

    public function test_get_team_performance_handles_empty_and_active_teams()
    {
        $user = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'username' => 'janesmith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'phone' => '0987654321',
        ]);

        $company = Company::create([
            'name' => 'Apex Agency',
            'email' => 'apex@example.com',
            'phone' => '987654321',
            'type' => 'agency',
            'created_by' => $user->id,
            'status' => 'active',
        ]);

        $service = new CompanyAnalyticsService;

        // Test empty team performance
        $teamPerformance = $service->getTeamPerformance($company->id);
        $this->assertEquals(0, $teamPerformance['team_size']);
        $this->assertEquals(0, $teamPerformance['average_productivity']);

        // Add member and test
        CompanyMember::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'role' => 'agent',
            'status' => 'active',
        ]);

        $teamPerformance = $service->getTeamPerformance($company->id);
        $this->assertEquals(1, $teamPerformance['team_size']);
        $this->assertArrayHasKey('average_productivity', $teamPerformance);
    }
}
