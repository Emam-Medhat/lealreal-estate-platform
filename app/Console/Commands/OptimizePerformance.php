<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\CacheService;

class OptimizePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:optimize {--force : Force optimization without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize application performance with indexes, caching, and configurations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Performance Optimization...');

        if (!$this->option('force')) {
            if (!$this->confirm('This will optimize your application performance. Continue?')) {
                $this->info('Performance optimization cancelled.');
                return 0;
            }
        }

        $this->optimizeDatabase();
        $this->optimizeCache();
        $this->optimizeConfiguration();
        $this->warmUpCache();

        $this->info('✅ Performance optimization completed successfully!');
        $this->displayPerformanceStats();

        return 0;
    }

    /**
     * Optimize database with indexes
     */
    private function optimizeDatabase(): void
    {
        $this->info('📊 Optimizing database indexes...');

        $indexes = [
            'leads' => [
                'leads_status_index' => ['lead_status'],
                'leads_priority_index' => ['priority'],
                'leads_assigned_to_index' => ['assigned_to'],
                'leads_status_priority_index' => ['lead_status', 'priority'],
                'leads_priority_created_index' => ['priority', 'created_at'],
                'leads_name_index' => ['first_name', 'last_name'],
                'leads_email_index' => ['email'],
                'leads_created_at_index' => ['created_at'],
            ],
            'users' => [
                'users_role_index' => ['role'],
                'users_status_index' => ['account_status'],
                'users_agent_index' => ['is_agent'],
                'users_email_index' => ['email'],
            ],
            'properties' => [
                'properties_status_index' => ['status'],
                'properties_type_index' => ['type'],
                'properties_agent_index' => ['agent_id'],
                'properties_city_index' => ['city'],
                'properties_price_index' => ['price'],
                'properties_created_at_index' => ['created_at'],
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            foreach ($tableIndexes as $indexName => $columns) {
                try {
                    if (!Schema::hasIndex($table, $indexName)) {
                        DB::statement("ALTER TABLE {$table} ADD INDEX {$indexName} (" . implode(',', $columns) . ")");
                        $this->line("  ✓ Created index: {$indexName}");
                    } else {
                        $this->line("  - Index already exists: {$indexName}");
                    }
                } catch (\Exception $e) {
                    $this->warn("  ✗ Failed to create index {$indexName}: " . $e->getMessage());
                }
            }
        }

        $this->info('Database optimization completed.');
    }

    /**
     * Optimize cache configuration
     */
    private function optimizeCache(): void
    {
        $this->info('💾 Optimizing cache configuration...');

        try {
            // Clear all caches
            CacheService::clear();
            $this->line('  ✓ Cleared all caches');

            // Test cache service
            CacheService::remember('test', function () {
                return 'performance_test';
            }, 'short');
            
            $result = CacheService::get('test');
            if ($result === 'performance_test') {
                $this->line('  ✓ Cache service working correctly');
                CacheService::forget('test');
            } else {
                $this->warn('  ✗ Cache service test failed');
            }

        } catch (\Exception $e) {
            $this->warn('  ✗ Cache optimization failed: ' . $e->getMessage());
        }

        $this->info('Cache optimization completed.');
    }

    /**
     * Optimize application configuration
     */
    private function optimizeConfiguration(): void
    {
        $this->info('⚙️ Optimizing application configuration...');

        // Check if Redis is available
        try {
            $redis = app('redis');
            $redis->ping();
            $this->line('  ✓ Redis connection successful');
        } catch (\Exception $e) {
            $this->warn('  ✗ Redis not available: ' . $e->getMessage());
        }

        // Check database connection
        try {
            DB::select('SELECT 1');
            $this->line('  ✓ Database connection successful');
        } catch (\Exception $e) {
            $this->warn('  ✗ Database connection failed: ' . $e->getMessage());
        }

        // Check file permissions
        $storagePath = storage_path();
        if (is_writable($storagePath)) {
            $this->line('  ✓ Storage directory writable');
        } else {
            $this->warn('  ✗ Storage directory not writable');
        }

        $this->info('Configuration optimization completed.');
    }

    /**
     * Warm up cache with common data
     */
    private function warmUpCache(): void
    {
        $this->info('🔥 Warming up cache...');

        try {
            CacheService::warmUp();
            $this->line('  ✓ Cache warm-up initiated');
        } catch (\Exception $e) {
            $this->warn('  ✗ Cache warm-up failed: ' . $e->getMessage());
        }

        $this->info('Cache warm-up completed.');
    }

    /**
     * Display performance statistics
     */
    private function displayPerformanceStats(): void
    {
        $this->info('\n📈 Performance Statistics:');

        try {
            $stats = CacheService::getStats();
            
            if (isset($stats['error'])) {
                $this->warn('  Cache stats unavailable: ' . $stats['error']);
            } else {
                $this->line('  Memory Usage: ' . ($stats['used_memory'] ?? 'N/A'));
                $this->line('  Peak Memory: ' . ($stats['used_memory_peak'] ?? 'N/A'));
                $this->line('  Connected Clients: ' . ($stats['connected_clients'] ?? 'N/A'));
            }
        } catch (\Exception $e) {
            $this->warn('  Failed to get performance stats: ' . $e->getMessage());
        }

        // Display optimization tips
        $this->info('\n💡 Optimization Tips:');
        $this->line('  • Use eager loading to prevent N+1 queries');
        $this->line('  • Implement proper caching strategies');
        $this->line('  • Monitor slow queries regularly');
        $this->line('  • Use database indexes for frequently queried columns');
        $this->line('  • Consider Redis for session and cache storage');
        $this->line('  • Enable query logging in development');
    }
}
