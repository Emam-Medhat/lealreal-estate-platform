<?php

namespace Database\Seeders;

use App\Models\Request as RequestModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleRequests = [
            [
                'request_id' => 'req_' . uniqid() . '_1',
                'method' => 'GET',
                'url' => 'http://127.0.0.1:8000/',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'headers' => ['accept' => 'text/html,application/xhtml+xml'],
                'status' => RequestModel::STATUS_COMPLETED,
                'response_code' => 200,
                'response_time' => 145.5,
                'started_at' => now()->subMinutes(2),
                'completed_at' => now()->subMinutes(2),
                'created_at' => now()->subMinutes(2),
            ],
            [
                'request_id' => 'req_' . uniqid() . '_2',
                'method' => 'POST',
                'url' => 'http://127.0.0.1:8000/login',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'headers' => ['content-type' => 'application/x-www-form-urlencoded'],
                'payload' => ['email' => 'test@example.com', 'password' => '***HIDDEN***'],
                'status' => RequestModel::STATUS_COMPLETED,
                'response_code' => 302,
                'response_time' => 230.8,
                'started_at' => now()->subMinutes(5),
                'completed_at' => now()->subMinutes(5),
                'created_at' => now()->subMinutes(5),
            ],
            [
                'request_id' => 'req_' . uniqid() . '_3',
                'method' => 'GET',
                'url' => 'http://127.0.0.1:8000/properties',
                'ip_address' => '10.0.0.15',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0)',
                'headers' => ['accept' => 'application/json'],
                'status' => RequestModel::STATUS_PROCESSING,
                'response_code' => null,
                'response_time' => null,
                'started_at' => now()->subMinute(),
                'completed_at' => null,
                'created_at' => now()->subMinute(),
            ],
            [
                'request_id' => 'req_' . uniqid() . '_4',
                'method' => 'GET',
                'url' => 'http://127.0.0.1:8000/dashboard',
                'ip_address' => '172.16.0.45',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64)',
                'headers' => ['authorization' => 'Bearer token123'],
                'status' => RequestModel::STATUS_FAILED,
                'response_code' => 401,
                'response_time' => 89.3,
                'error_message' => 'Unauthorized',
                'started_at' => now()->subMinutes(8),
                'completed_at' => now()->subMinutes(8),
                'created_at' => now()->subMinutes(8),
            ],
            [
                'request_id' => 'req_' . uniqid() . '_5',
                'method' => 'POST',
                'url' => 'http://127.0.0.1:8000/api/properties/search',
                'ip_address' => '203.0.113.1',
                'user_agent' => 'curl/7.68.0',
                'headers' => ['content-type' => 'application/json'],
                'payload' => ['city' => 'Riyadh', 'price_min' => 100000],
                'status' => RequestModel::STATUS_COMPLETED,
                'response_code' => 200,
                'response_time' => 456.2,
                'started_at' => now()->subMinutes(10),
                'completed_at' => now()->subMinutes(10),
                'created_at' => now()->subMinutes(10),
            ],
            [
                'request_id' => 'req_' . uniqid() . '_6',
                'method' => 'GET',
                'url' => 'http://127.0.0.1:8000/agents',
                'ip_address' => '198.51.100.22',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0',
                'headers' => ['accept-language' => 'en-US,en;q=0.9'],
                'status' => RequestModel::STATUS_COMPLETED,
                'response_code' => 200,
                'response_time' => 178.9,
                'started_at' => now()->subMinutes(15),
                'completed_at' => now()->subMinutes(15),
                'created_at' => now()->subMinutes(15),
            ],
            [
                'request_id' => 'req_' . uniqid() . '_7',
                'method' => 'PUT',
                'url' => 'http://127.0.0.1:8000/api/user/profile',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Android 12; Mobile)',
                'headers' => ['authorization' => 'Bearer token456'],
                'payload' => ['name' => 'John Doe', 'email' => 'john@example.com'],
                'status' => RequestModel::STATUS_PENDING,
                'response_code' => null,
                'response_time' => null,
                'started_at' => now()->subSeconds(30),
                'completed_at' => null,
                'created_at' => now()->subSeconds(30),
            ],
            [
                'request_id' => 'req_' . uniqid() . '_8',
                'method' => 'DELETE',
                'url' => 'http://127.0.0.1:8000/api/properties/123',
                'ip_address' => '192.168.1.200',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/121.0',
                'headers' => ['x-requested-with' => 'XMLHttpRequest'],
                'status' => RequestModel::STATUS_COMPLETED,
                'response_code' => 204,
                'response_time' => 298.7,
                'started_at' => now()->subMinutes(20),
                'completed_at' => now()->subMinutes(20),
                'created_at' => now()->subMinutes(20),
            ],
        ];

        foreach ($sampleRequests as $requestData) {
            RequestModel::create($requestData);
        }

        $this->command->info('Sample requests created successfully!');
    }
}
