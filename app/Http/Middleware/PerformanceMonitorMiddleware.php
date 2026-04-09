<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class PerformanceMonitorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        // Start query monitoring
        DB::enableQueryLog();
        $queryCount = 0;
        $slowQueries = [];

        // Get initial system metrics
        $initialMetrics = $this->getSystemMetrics();

        // Process the request
        $response = $next($request);

        // Calculate performance metrics
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds
        $memoryUsage = round(($endMemory - $startMemory) / 1024 / 1024, 2); // in MB

        // Get query information
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        $totalQueryTime = array_sum(array_column($queries, 'time'));
        $slowQueries = array_filter($queries, fn($query) => $query['time'] > 100); // queries > 100ms

        // Get final system metrics
        $finalMetrics = $this->getSystemMetrics();

        // Log performance data
        $this->logPerformanceData($request, $response, [
            'execution_time' => $executionTime,
            'memory_usage' => $memoryUsage,
            'query_count' => $queryCount,
            'total_query_time' => $totalQueryTime,
            'slow_queries' => $slowQueries,
            'initial_metrics' => $initialMetrics,
            'final_metrics' => $finalMetrics
        ]);

        // Store performance analytics
        $this->storePerformanceAnalytics($request, $response, [
            'execution_time' => $executionTime,
            'memory_usage' => $memoryUsage,
            'query_count' => $queryCount,
            'total_query_time' => $totalQueryTime,
            'slow_queries_count' => count($slowQueries)
        ]);

        // Add performance headers to response
        $response->headers->set('X-Execution-Time', $executionTime . 'ms');
        $response->headers->set('X-Memory-Usage', $memoryUsage . 'MB');
        $response->headers->set('X-Query-Count', $queryCount);
        $response->headers->set('X-Total-Query-Time', $totalQueryTime . 'ms');

        // Alert on performance issues
        $this->checkPerformanceIssues($request, [
            'execution_time' => $executionTime,
            'memory_usage' => $memoryUsage,
            'query_count' => $queryCount,
            'slow_queries_count' => count($slowQueries)
        ]);

        return $response;
    }

    /**
     * Get system metrics
     */
    protected function getSystemMetrics(): array
    {
        $metrics = [
            'timestamp' => now()->toISOString(),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'cpu_usage' => $this->getCpuUsage(),
            'disk_usage' => $this->getDiskUsage(),
            'network_connections' => $this->getNetworkConnections(),
            'cache_hit_rate' => $this->getCacheHitRate(),
            'active_connections' => $this->getActiveConnections()
        ];

        return $metrics;
    }

    /**
     * Get CPU usage
     */
    protected function getCpuUsage(): float
    {
        try {
            // Linux/Unix systems
            if (function_exists('sys_getloadavg')) {
                $load = sys_getloadavg();
                return round($load[0] * 100, 2); // Convert to percentage
            }

            // Fallback for other systems
            return 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Get disk usage
     */
    protected function getDiskUsage(): array
    {
        try {
            $total = disk_total_space('/');
            $free = disk_free_space('/');
            $used = $total - $free;

            return [
                'total' => $total,
                'used' => $used,
                'free' => $free,
                'usage_percentage' => $total > 0 ? round(($used / $total) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'used' => 0,
                'free' => 0,
                'usage_percentage' => 0
            ];
        }
    }

    /**
     * Get network connections count
     */
    protected function getNetworkConnections(): int
    {
        try {
            // This would require system-level access
            // For now, return a placeholder
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get cache hit rate
     */
    protected function getCacheHitRate(): float
    {
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Redis::connection();
                $stats = $redis->info('stats');
                
                if (isset($stats['keyspace_hits']) && isset($stats['keyspace_misses'])) {
                    $hits = (int) $stats['keyspace_hits'];
                    $misses = (int) $stats['keyspace_misses'];
                    $total = $hits + $misses;
                    
                    return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
                }
            }

            return 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Get active database connections
     */
    protected function getActiveConnections(): int
    {
        try {
            $connections = DB::select('SHOW STATUS LIKE "Threads_connected"');
            return (int) ($connections[0]->Value ?? 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Log performance data
     */
    protected function logPerformanceData(Request $request, $response, array $metrics): void
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'status_code' => $response->getStatusCode(),
            'response_size' => strlen($response->getContent()),
            'execution_time' => $metrics['execution_time'],
            'memory_usage' => $metrics['memory_usage'],
            'query_count' => $metrics['query_count'],
            'total_query_time' => $metrics['total_query_time'],
            'slow_queries_count' => count($metrics['slow_queries']),
            'system_metrics' => [
                'initial' => $metrics['initial_metrics'],
                'final' => $metrics['final_metrics']
            ]
        ];

        // Log slow requests
        if ($metrics['execution_time'] > 1000) { // > 1 second
            Log::warning('Slow request detected', $logData);
        } elseif ($metrics['execution_time'] > 500) { // > 500ms
            Log::info('Performance warning', $logData);
        }

        // Log memory-intensive requests
        if ($metrics['memory_usage'] > 50) { // > 50MB
            Log::warning('High memory usage detected', $logData);
        }

        // Log query-intensive requests
        if ($metrics['query_count'] > 20) {
            Log::warning('High query count detected', $logData);
        }

        // Log slow queries
        if (!empty($metrics['slow_queries'])) {
            Log::warning('Slow queries detected', [
                'url' => $request->fullUrl(),
                'slow_queries' => $metrics['slow_queries']
            ]);
        }

        // Store in performance log table if exists
        try {
            \App\Models\PerformanceLog::create([
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
                'status_code' => $response->getStatusCode(),
                'execution_time' => $metrics['execution_time'],
                'memory_usage' => $metrics['memory_usage'],
                'query_count' => $metrics['query_count'],
                'total_query_time' => $metrics['total_query_time'],
                'slow_queries_count' => count($metrics['slow_queries']),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            // Ignore if table doesn't exist
        }
    }

    /**
     * Store performance analytics
     */
    protected function storePerformanceAnalytics(Request $request, $response, array $metrics): void
    {
        try {
            $key = 'performance_analytics:' . date('Y-m-d-H');
            $existing = Cache::get($key, []);

            $existing[] = [
                'timestamp' => now()->toISOString(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'status_code' => $response->getStatusCode(),
                'execution_time' => $metrics['execution_time'],
                'memory_usage' => $metrics['memory_usage'],
                'query_count' => $metrics['query_count'],
                'total_query_time' => $metrics['total_query_time'],
                'slow_queries_count' => $metrics['slow_queries_count']
            ];

            // Keep only last 1000 entries
            if (count($existing) > 1000) {
                $existing = array_slice($existing, -1000);
            }

            Cache::put($key, $existing, 3600); // Store for 1 hour

            // Update hourly aggregates
            $this->updateHourlyAggregates($metrics);

        } catch (\Exception $e) {
            Log::error('Failed to store performance analytics: ' . $e->getMessage());
        }
    }

    /**
     * Update hourly aggregates
     */
    protected function updateHourlyAggregates(array $metrics): void
    {
        try {
            $hourKey = 'performance_hourly:' . date('Y-m-d-H');
            $aggregates = Cache::get($hourKey, [
                'total_requests' => 0,
                'total_execution_time' => 0,
                'total_memory_usage' => 0,
                'total_query_count' => 0,
                'total_slow_queries' => 0,
                'max_execution_time' => 0,
                'max_memory_usage' => 0,
                'max_query_count' => 0
            ]);

            $aggregates['total_requests']++;
            $aggregates['total_execution_time'] += $metrics['execution_time'];
            $aggregates['total_memory_usage'] += $metrics['memory_usage'];
            $aggregates['total_query_count'] += $metrics['query_count'];
            $aggregates['total_slow_queries'] += $metrics['slow_queries_count'];
            $aggregates['max_execution_time'] = max($aggregates['max_execution_time'], $metrics['execution_time']);
            $aggregates['max_memory_usage'] = max($aggregates['max_memory_usage'], $metrics['memory_usage']);
            $aggregates['max_query_count'] = max($aggregates['max_query_count'], $metrics['query_count']);

            Cache::put($hourKey, $aggregates, 7200); // Store for 2 hours

        } catch (\Exception $e) {
            Log::error('Failed to update hourly aggregates: ' . $e->getMessage());
        }
    }

    /**
     * Check for performance issues and alert
     */
    protected function checkPerformanceIssues(Request $request, array $metrics): void
    {
        $issues = [];

        // Check execution time
        if ($metrics['execution_time'] > 5000) { // > 5 seconds
            $issues[] = 'Critical: Very slow execution time (' . $metrics['execution_time'] . 'ms)';
        } elseif ($metrics['execution_time'] > 2000) { // > 2 seconds
            $issues[] = 'Warning: Slow execution time (' . $metrics['execution_time'] . 'ms)';
        }

        // Check memory usage
        if ($metrics['memory_usage'] > 100) { // > 100MB
            $issues[] = 'Critical: High memory usage (' . $metrics['memory_usage'] . 'MB)';
        } elseif ($metrics['memory_usage'] > 50) { // > 50MB
            $issues[] = 'Warning: High memory usage (' . $metrics['memory_usage'] . 'MB)';
        }

        // Check query count
        if ($metrics['query_count'] > 50) {
            $issues[] = 'Critical: Very high query count (' . $metrics['query_count'] . ')';
        } elseif ($metrics['query_count'] > 20) {
            $issues[] = 'Warning: High query count (' . $metrics['query_count'] . ')';
        }

        // Check slow queries
        if ($metrics['slow_queries_count'] > 5) {
            $issues[] = 'Critical: Multiple slow queries (' . $metrics['slow_queries_count'] . ')';
        } elseif ($metrics['slow_queries_count'] > 0) {
            $issues[] = 'Warning: Slow queries detected (' . $metrics['slow_queries_count'] . ')';
        }

        // Log issues if any
        if (!empty($issues)) {
            Log::error('Performance issues detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'issues' => $issues,
                'metrics' => $metrics
            ]);

            // Send alert to monitoring system (implementation would depend on your setup)
            $this->sendPerformanceAlert($request, $issues, $metrics);
        }
    }

    /**
     * Send performance alert
     */
    protected function sendPerformanceAlert(Request $request, array $issues, array $metrics): void
    {
        try {
            // Implementation would depend on your alerting system
            // For example: Slack, email, monitoring service, etc.
            
            Log::info('Performance alert sent', [
                'url' => $request->fullUrl(),
                'issues' => $issues,
                'metrics' => $metrics
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send performance alert: ' . $e->getMessage());
        }
    }
}
