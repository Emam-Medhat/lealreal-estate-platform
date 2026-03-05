<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Agent;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Models\Property;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardService();
        $this->user = User::factory()->create();
    }

    public function test_get_user_dashboard_data_returns_correct_structure()
    {
        // Create an agent for the property
        $agentUser = User::factory()->create();
        $agent = Agent::create([
            'user_id' => $agentUser->id,
            'name' => 'Test Agent',
            'email' => 'agent@example.com',
            'phone' => '1234567890',
            'license_number' => 'LIC-123',
            'hire_date' => now(),
            'is_active' => true,
            'is_verified' => true,
        ]);

        // Mock some data
        UserActivityLog::create([
            'user_id' => $this->user->id,
            'action' => 'property_view',
            'created_at' => now(),
        ]);

        // Create property with required fields
        $property = Property::create([
            'agent_id' => $agent->id,
            'title' => 'Test Property',
            'description' => 'Test Description',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'area' => 100,
            'price' => 100000,
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'Test Country',
            'status' => 'active',
            'property_code' => 'TEST-123',
            'slug' => 'test-property',
        ]);

        UserFavorite::create([
            'user_id' => $this->user->id,
            'favoritable_type' => Property::class,
            'favoritable_id' => $property->id,
        ]);

        UserNotification::create([
            'user_id' => $this->user->id,
            'title' => 'Test notification title',
            'message' => 'Test notification',
            'type' => 'info',
            'is_read' => false,
        ]);

        UserWallet::create([
            'user_id' => $this->user->id,
            'balance' => 100.00,
        ]);

        $data = $this->service->getUserDashboardData($this->user);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('properties_viewed_today', $data);
        $this->assertArrayHasKey('saved_properties', $data);
        $this->assertArrayHasKey('unread_notifications', $data);
        $this->assertArrayHasKey('wallet_balance', $data);

        $this->assertEquals(1, $data['properties_viewed_today']);
        $this->assertEquals(1, $data['saved_properties']);
        $this->assertEquals(1, $data['unread_notifications']);
        $this->assertEquals(100.00, $data['wallet_balance']);
    }

    public function test_get_quick_stats_returns_correct_data()
    {
        UserWallet::create([
            'user_id' => $this->user->id,
            'balance' => 250.50,
        ]);

        $stats = $this->service->getQuickStats($this->user);

        $this->assertIsArray($stats);
        $this->assertEquals(250.50, $stats['wallet_balance']);
    }
}
