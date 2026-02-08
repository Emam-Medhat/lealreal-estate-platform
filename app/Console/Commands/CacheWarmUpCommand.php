<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;
use App\Services\LeadService;
use App\Services\PropertyService;

class CacheWarmUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warm-up {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up application cache with commonly accessed data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔥 Starting cache warm-up...');

        if (!$this->option('force')) {
            if (!$this->confirm('This will warm up the application cache. Continue?')) {
                $this->info('Cache warm-up cancelled.');
                return 0;
            }
        }

        $this->warmUpLeadCache();
        $this->warmUpPropertyCache();
        $this->warmUpUserCache();
        $this->warmUpDashboardCache();
        $this->warmUpAnalyticsCache();

        $this->info('✅ Cache warm-up completed successfully!');
        $this->displayCacheStats();

        return 0;
    }

    /**
     * Warm up lead-related cache
     */
    private function warmUpLeadCache(): void
    {
        $this->info('  🔄 Warming up lead cache...');

        try {
            // Warm up dashboard stats
            app(LeadService::class)->getDashboardStats();
            $this->line('    ✓ Lead dashboard stats');

            // Warm up active statuses
            app(LeadService::class)->getActiveStatuses();
            $this->line('    ✓ Lead active statuses');

            // Warm up active sources
            app(LeadService::class)->getActiveSources();
            $this->line('    ✓ Lead active sources');

            // Warm up recent leads
            app(LeadService::class)->getRecentLeads(10);
            $this->line('    ✓ Recent leads');

            // Warm up conversion funnel
            app(LeadService::class)->getConversionFunnel();
            $this->line('    ✓ Conversion funnel');

        } catch (\Exception $e) {
            $this->error('    ✗ Failed to warm up lead cache: ' . $e->getMessage());
        }
    }

    /**
     * Warm up property-related cache
     */
    private function warmUpPropertyCache(): void
    {
        $this->info('  🔄 Warming up property cache...');

        try {
            // Warm up property stats
            app(PropertyService::class)->getPropertyStats();
            $this->line('    ✓ Property stats');

            // Warm up featured properties
            app(PropertyService::class)->getFeaturedProperties(10);
            $this->line('    ✓ Featured properties');

            // Warm up performance metrics
            app(PropertyService::class)->getPropertyPerformanceMetrics();
            $this->line('    ✓ Property performance metrics');

        } catch (\Exception $e) {
            $this->error('    ✗ Failed to warm up property cache: ' . $e->getMessage());
        }
    }

    /**
     * Warm up user-related cache
     */
    private function warmUpUserCache(): void
    {
        $this->info('  🔄 Warming up user cache...');

        try {
            // Warm up available agents
            app(LeadService::class)->getAvailableAgents();
            $this->line('    ✓ Available agents');

        } catch (\Exception $e) {
            $this->error('    ✗ Failed to warm up user cache: ' . $e->getMessage());
        }
    }

    /**
     * Warm up dashboard cache
     */
    private function warmUpDashboardCache(): void
    {
        $this->info('  🔄 Warming up dashboard cache...');

        try {
            // Warm up lead dashboard stats
            app(LeadService::class)->getDashboardStats();
            $this->line('    ✓ Lead dashboard stats');

            // Warm up property dashboard stats
            app(PropertyService::class)->getPropertyStats();
            $this->line('    ✓ Property dashboard stats');

        } catch (\Exception $e) {
            $this->error('    ✗ Failed to warm up dashboard cache: ' . $e->getMessage());
        }
    }

    /**
     * Warm up analytics cache
     */
    private function warmUpAnalyticsCache(): void
    {
        $this->info('  🔄 Warming up analytics cache...');

        try {
            // Warm up lead analytics
            app(LeadService::class)->getConversionFunnel();
            $this->line('    ✓ Lead analytics');

            // Warm up property analytics
            app(PropertyService::class)->getPropertyPerformanceMetrics();
            $this->line('    ✓ Property analytics');

        } catch (\Exception $e) {
            $this->error('    ✗ Failed to warm up analytics cache: ' . $e->getMessage());
        }
    }

    /**
     * Display cache statistics
     */
    private function displayCacheStats(): void
    {
        $this->info('\n📊 Cache Statistics:');

        try {
            $stats = CacheService::getStats();
            
            if (isset($stats['error'])) {
                $this->warn('  Cache stats unavailable: ' . $stats['error']);
            } else {
                $this->line('  Memory Usage: ' . ($stats['used_memory'] ?? 'N/A'));
                $this->line('  Peak Memory: ' . ($stats['used_memory_peak'] ?? 'N/A'));
                $this->line('  Connected Clients: ' . ($stats['connected_clients'] ?? 'N/A'));
                $this->line('  Total Commands: ' . ($stats['total_commands_processed'] ?? 'N/A'));
            }
        } catch (\Exception $e) {
            $this->warn('  Failed to get cache stats: ' . $e->getMessage());
        }

        // Display warm-up tips
        $this->info('\n💡 Cache Warm-up Benefits:');
        $this->line('  • Faster page loads for first visitors');
        $this->line('  • Reduced database load during peak traffic');
        $this->line('    • Better user experience');
        $this->line('  • Improved API response times');
    }
}
