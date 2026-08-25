<?php

namespace Tests\Feature;

use App\Models\ServiceProvider;
use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\Property;
use App\Services\ServiceProviderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ServiceProviderPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_provider_performance_returns_correct_stats_and_reduces_queries(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Property $property */
        $property = Property::create([
            'title' => 'Test Property',
            'description' => 'Test Description',
            'address' => '123 Main St',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'Test Country',
            'area' => 1000,
            'area_unit' => 'sqft',
            'price' => 250000,
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'property_code' => 'PROP-TEST-001',
            'agent_id' => $user->id,
            'status' => 'active',
        ]);

        /** @var ServiceProvider $provider */
        $provider = ServiceProvider::create([
            'name' => 'John Doe Plumbing',
            'contact_person' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'Test Country',
            'specialization' => 'plumbing',
            'provider_code' => 'PROV-2025-0001',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        // Create completed maintenance request inside this month
        MaintenanceRequest::create([
            'service_provider_id' => $provider->id,
            'requested_by' => $user->id,
            'property_id' => $property->id,
            'request_number' => 'MR-001',
            'category' => 'plumbing',
            'priority' => 'medium',
            'title' => 'Pipe Repair 1',
            'description' => 'Fix leaking pipe',
            'status' => 'completed',
            'actual_cost' => 150.00,
            'started_at' => now()->subHours(4),
            'completed_at' => now(),
        ]);

        // Create completed maintenance request in a prior month
        $req2 = MaintenanceRequest::create([
            'service_provider_id' => $provider->id,
            'requested_by' => $user->id,
            'property_id' => $property->id,
            'request_number' => 'MR-002',
            'category' => 'plumbing',
            'priority' => 'medium',
            'title' => 'Pipe Repair 2',
            'description' => 'Fix broken faucet',
            'status' => 'completed',
            'actual_cost' => 200.00,
            'started_at' => now()->subMonths(2)->subHours(2),
            'completed_at' => now()->subMonths(2),
        ]);
        $req2->created_at = now()->subMonths(2);
        $req2->save();

        // Create pending maintenance request in this month
        MaintenanceRequest::create([
            'service_provider_id' => $provider->id,
            'requested_by' => $user->id,
            'property_id' => $property->id,
            'request_number' => 'MR-003',
            'category' => 'plumbing',
            'priority' => 'medium',
            'title' => 'Pipe Repair 3',
            'description' => 'Check water pressure',
            'status' => 'pending',
        ]);

        $service = app(ServiceProviderService::class);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $result = $service->getProviderPerformance($provider);

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Expect exactly 2 queries: 1 consolidated stats query + 1 monthly trends query
        $this->assertLessThanOrEqual(2, $queryCount);

        $stats = $result['stats'];
        $this->assertEquals(2, $stats['monthly_requests']);
        $this->assertEquals(1, $stats['monthly_completed']);
        $this->assertEquals(150.00, $stats['monthly_revenue']);
        $this->assertEquals(66.67, round($stats['completion_rate'], 2)); // 2 completed out of 3 total = 66.67%
        $this->assertNotNull($stats['average_completion_time']);
        $this->assertCount(2, $result['monthlyData']);
    }
}
