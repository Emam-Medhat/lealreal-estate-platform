<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class RequestLoggerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = $endMemory - $startMemory;
        $queryCount = \DB::getQueryCount();

        // Log request details
        $this->logRequest($request, $response, $executionTime, $memoryUsage, $queryCount);

        // Add performance headers
        $response->headers->set('X-Execution-Time', round($executionTime, 2) . 'ms');
        $response->headers->set('X-Memory-Usage', round($memoryUsage / 1024 / 1024, 2) . 'MB');
        $response->headers->set('X-Query-Count', $queryCount);
        $response->headers->set('X-Request-ID', uniqid());

        return $response;
    }

    /**
     * Log request details
     */
    private function logRequest(Request $request, $response, float $executionTime, int $memoryUsage, int $queryCount): void
    {
        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => $response->getStatusCode(),
            'execution_time_ms' => round($executionTime, 2),
            'memory_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'query_count' => $queryCount,
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ];

        // Log slow requests
        if ($executionTime > 1000) {
            Log::warning('Slow request detected', $logData);
        }

        // Log high memory usage
        if ($memoryUsage > 50 * 1024 * 1024) { // 50MB
            Log::warning('High memory usage detected', $logData);
        }

        // Log high query count
        if ($queryCount > 50) {
            Log::warning('High query count detected', $logData);
        }

        // Log in development
        if (app()->environment('local', 'testing')) {
            Log::debug('Request logged', $logData);
        }
    }
}
