<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_routes()
    {
        $user = User::factory()->create(['role' => 'admin', 'user_type' => 'admin']); // Assuming role based access or similar

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        // Check if any route() call in the view failed
        // Usually if route() fails, it throws 500
        if ($response->status() === 500) {
            dump($response->exception->getMessage());
        }

        $response->assertStatus(200);
    }
}
