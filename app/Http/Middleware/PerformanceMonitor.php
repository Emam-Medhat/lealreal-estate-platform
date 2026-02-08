<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PerformanceMonitor
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        // Enable query logging
        DB::enableQueryLog();

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = $endMemory - $startMemory;
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        $totalQueryTime = array_sum(array_column($queries, 'time'));

        // Log performance metrics
        $this->logPerformanceMetrics([
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'execution_time_ms' => round($executionTime, 2),
            'memory_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'query_count' => $queryCount,
            'total_query_time_ms' => round($totalQueryTime, 2),
            'status_code' => $response->getStatusCode(),
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
        ]);

        // Add performance headers for debugging
        $response->headers->set('X-Execution-Time', round($executionTime, 2) . 'ms');
        $response->headers->set('X-Memory-Usage', round($memoryUsage / 1024 / 1024, 2) . 'MB');
        $response->headers->set('X-Query-Count', $queryCount);
        $response->headers->set('X-Query-Time', round($totalQueryTime, 2) . 'ms');

        // Alert on slow queries
        if ($executionTime > 1000) { // More than 1 second
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'execution_time' => $executionTime,
                'query_count' => $queryCount,
                'queries' => $queries,
            ]);
        }

        // Alert on N+1 queries
        if ($queryCount > 20) {
            Log::warning('High query count detected', [
                'url' => $request->fullUrl(),
                'query_count' => $queryCount,
                'queries' => $queries,
            ]);
        }

        // Clear query log for next request
        DB::flushQueryLog();

        return $response;
    }

    /**
     * Log performance metrics
     *
     * @param array $metrics
     * @return void
     */
    private function logPerformanceMetrics(array $metrics): void
    {
        // Only log in non-production environments or for slow requests
        if (app()->environment(['local', 'testing', 'staging']) || $metrics['execution_time_ms'] > 500) {
            Log::info('Performance metrics', $metrics);
        }

        // Store metrics for monitoring dashboard (if needed)
        if (app()->environment(['production', 'staging'])) {
            // You could store these in Redis, database, or a monitoring service
            // For now, we'll just log slow requests
            if ($metrics['execution_time_ms'] > 1000) {
                Log::channel('performance')->warning('Performance alert', $metrics);
            }
        }
    }
}
