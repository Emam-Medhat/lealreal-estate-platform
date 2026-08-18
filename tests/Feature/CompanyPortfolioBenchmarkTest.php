<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Company;
use App\Models\Property;
use App\Models\User;
use App\Services\CompanyPortfolioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CompanyPortfolioBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure database is clean and migrated
        $this->artisan('migrate');
    }

    public function test_company_portfolio_value_and_performance_calculation()
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'John Agent',
            'email' => 'johnagent@example.com',
            'phone' => '1234567890',
            'hire_date' => now(),
            'license_number' => 'LIC-123456',
        ]);

        $company = Company::create([
            'name' => 'Acme Real Estate',
            'slug' => 'acme-real-estate',
            'type' => 'agency',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        // Create current properties for the company
        for ($i = 1; $i <= 5; $i++) {
            Property::create([
                'company_id' => $company->id,
                'agent_id' => $agent->id,
                'title' => "Property {$i}",
                'description' => "Description {$i}",
                'property_code' => "PROP-{$i}",
                'address' => "Address {$i}",
                'city' => 'Metropolis',
                'state' => 'NY',
                'country' => 'USA',
                'area' => 100,
                'area_unit' => 'sqm',
                'price' => 200000,
                'property_type' => 'apartment',
                'listing_type' => 'sale',
                'status' => $i <= 3 ? 'active' : 'sold',
                'created_at' => now(),
            ]);
        }

        // Create previous period properties (created 18 months ago)
        for ($i = 6; $i <= 8; $i++) {
            Property::create([
                'company_id' => $company->id,
                'agent_id' => $agent->id,
                'title' => "Property {$i}",
                'description' => "Description {$i}",
                'property_code' => "PROP-{$i}",
                'address' => "Address {$i}",
                'city' => 'Metropolis',
                'state' => 'NY',
                'country' => 'USA',
                'area' => 100,
                'area_unit' => 'sqm',
                'price' => 150000,
                'property_type' => 'villa',
                'listing_type' => 'sale',
                'status' => 'sold',
                'created_at' => now()->subMonths(18),
            ]);
        }

        $service = new CompanyPortfolioService;

        DB::enableQueryLog();
        $portfolioValue = $service->getPortfolioValue($company->id);
        $valueQueryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertEquals(8, $portfolioValue['total_properties']);
        $this->assertGreaterThan(0, $portfolioValue['total_value']);
        $this->assertArrayHasKey('status_breakdown', $portfolioValue);
        $this->assertArrayHasKey('portfolio_growth', $portfolioValue);

        DB::enableQueryLog();
        $performance = $service->getPortfolioPerformance($company->id, 'year');
        $performanceQueryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertArrayHasKey('current_portfolio', $performance);
        $this->assertArrayHasKey('portfolio_growth', $performance);

        // Verify low query counts
        $this->assertLessThanOrEqual(5, $valueQueryCount, "getPortfolioValue took {$valueQueryCount} queries");
        $this->assertLessThanOrEqual(8, $performanceQueryCount, "getPortfolioPerformance took {$performanceQueryCount} queries");
    }
}
