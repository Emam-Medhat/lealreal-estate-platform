<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    /**
     * Cache tags for different data types
     */
    const TAGS = [
        'leads' => 'leads',
        'users' => 'users',
        'properties' => 'properties',
        'reports' => 'reports',
        'dashboard' => 'dashboard',
        'analytics' => 'analytics',
    ];

    /**
     * Default cache durations in seconds
     */
    const DURATIONS = [
        'short' => 300,      // 5 minutes
        'medium' => 1800,    // 30 minutes
        'long' => 3600,      // 1 hour
        'extended' => 86400, // 24 hours
    ];

    /**
     * Remember data with automatic cache invalidation
     *
     * @param string $key
     * @param callable $callback
     * @param string $duration
     * @param array $tags
     * @return mixed
     */
    public static function remember(string $key, callable $callback, string $duration = 'medium', array $tags = [])
    {
        $cacheDuration = self::DURATIONS[$duration] ?? self::DURATIONS['medium'];
        
        // Use Redis tags if available
        if (self::supportsTags() && !empty($tags)) {
            try {
                return Cache::tags($tags)->remember($key, $cacheDuration, $callback);
            } catch (\Exception $e) {
                // Fallback to regular cache if tags fail
                return Cache::remember($key, $cacheDuration, $callback);
            }
        }
        
        return Cache::remember($key, $cacheDuration, $callback);
    }

    /**
     * Remember data forever (until manually cleared)
     *
     * @param string $key
     * @param callable $callback
     * @param array $tags
     * @return mixed
     */
    public static function rememberForever(string $key, callable $callback, array $tags = [])
    {
        if (self::supportsTags() && !empty($tags)) {
            try {
                return Cache::tags($tags)->rememberForever($key, $callback);
            } catch (\Exception $e) {
                // Fallback to regular cache if tags fail
                return Cache::rememberForever($key, $callback);
            }
        }
        
        return Cache::rememberForever($key, $callback);
    }

    /**
     * Cache data with automatic serialization
     *
     * @param string $key
     * @param mixed $data
     * @param string $duration
     * @param array $tags
     * @return bool
     */
    public static function put(string $key, $data, string $duration = 'medium', array $tags = []): bool
    {
        $cacheDuration = self::DURATIONS[$duration] ?? self::DURATIONS['medium'];
        
        if (self::supportsTags() && !empty($tags)) {
            try {
                return Cache::tags($tags)->put($key, $data, $cacheDuration);
            } catch (\Exception $e) {
                // Fallback to regular cache if tags fail
                return Cache::put($key, $data, $cacheDuration);
            }
        }
        
        return Cache::put($key, $data, $cacheDuration);
    }

    /**
     * Get cached data
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::get($key, $default);
    }

    /**
     * Check if key exists in cache
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return Cache::has($key);
    }

    /**
     * Remove data from cache
     *
     * @param string $key
     * @return bool
     */
    public static function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Clear cache by tags
     *
     * @param array $tags
     * @return bool
     */
    public static function clearTags(array $tags): bool
    {
        if (!self::supportsTags()) {
            return false;
        }
        
        try {
            return Cache::tags($tags)->flush();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Clear all cache
     *
     * @return bool
     */
    public static function clear(): bool
    {
        return Cache::flush();
    }

    /**
     * Get cache statistics
     *
     * @return array
     */
    public static function getStats(): array
    {
        try {
            // Check if Redis extension is loaded and configured
            if (extension_loaded('redis') && config('cache.default') === 'redis') {
                try {
                    $redis = \Illuminate\Support\Facades\Redis::connection();
                    // Just check connection without calling methods that might fail if not fully supported
                    $redis->ping(); 
                    
                    // Only try to get info if we are sure it's safe
                    try {
                        $info = $redis->info('memory');
                        return [
                            'driver' => 'redis',
                            'supports_tags' => true,
                            'used_memory' => $info['used_memory_human'] ?? 'N/A',
                            'used_memory_peak' => $info['used_memory_peak_human'] ?? 'N/A',
                            'connected_clients' => $info['connected_clients'] ?? 'N/A',
                            'total_commands_processed' => $info['total_commands_processed'] ?? 'N/A',
                        ];
                    } catch (\Exception $e) {
                        // Fallback if info command fails
                    }
                } catch (\Exception $e) {
                    // Redis connection failed
                }
            }
            
            // Return basic info for other drivers or if Redis fails
            return [
                'driver' => config('cache.default'),
                'supports_tags' => self::supportsTags(),
                'message' => 'Basic cache statistics - advanced stats not available for this driver',
            ];
        } catch (\Exception $e) {
            return [
                'driver' => config('cache.default'),
                'supports_tags' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cache query results with automatic key generation
     *
     * @param string $query
     * @param array $bindings
     * @param callable $callback
     * @param string $duration
     * @return mixed
     */
    public static function rememberQuery(string $query, array $bindings, callable $callback, string $duration = 'medium')
    {
        $key = 'query:' . md5($query . serialize($bindings));
        
        return self::remember($key, $callback, $duration, ['queries']);
    }

    /**
     * Cache dashboard data with automatic invalidation
     *
     * @param string $dashboard
     * @param callable $callback
     * @param string $duration
     * @return mixed
     */
    public static function rememberDashboard(string $dashboard, callable $callback, string $duration = 'short')
    {
        $key = "dashboard:{$dashboard}";
        
        return self::remember($key, $callback, $duration, [self::TAGS['dashboard']]);
    }

    /**
     * Cache analytics data
     *
     * @param string $analytics
     * @param callable $callback
     * @param string $duration
     * @return mixed
     */
    public static function rememberAnalytics(string $analytics, callable $callback, string $duration = 'long')
    {
        $key = "analytics:{$analytics}";
        
        return self::remember($key, $callback, $duration, [self::TAGS['analytics']]);
    }

    /**
     * Cache lead data
     *
     * @param string $type
     * @param callable $callback
     * @param string $duration
     * @return mixed
     */
    public static function rememberLeads(string $type, callable $callback, string $duration = 'medium')
    {
        $key = "leads:{$type}";
        
        return self::remember($key, $callback, $duration, [self::TAGS['leads']]);
    }

    /**
     * Cache user data
     *
     * @param string $type
     * @param callable $callback
     * @param string $duration
     * @return mixed
     */
    public static function rememberUsers(string $type, callable $callback, string $duration = 'long')
    {
        $key = "users:{$type}";
        
        return self::remember($key, $callback, $duration, [self::TAGS['users']]);
    }

    /**
     * Cache property data
     *
     * @param string $type
     * @param callable $callback
     * @param string $duration
     * @return mixed
     */
    public static function rememberProperties(string $type, callable $callback, string $duration = 'medium')
    {
        $key = "properties:{$type}";
        
        return self::remember($key, $callback, $duration, [self::TAGS['properties']]);
    }

    /**
     * Check if cache driver supports tags
     *
     * @return bool
     */
    private static function supportsTags(): bool
    {
        return method_exists(Cache::driver(), 'tags');
    }

    /**
     * Warm up cache with common data
     *
     * @return void
     */
    public static function warmUp(): void
    {
        // This method can be called from a command or job
        // to pre-populate cache with commonly accessed data
        
        try {
            // Warm up dashboard stats
            if (class_exists(\App\Services\DashboardService::class)) {
                dispatch(function () {
                    app(\App\Services\DashboardService::class)->getStats();
                });
            }

            // Warm up lead statistics
            if (class_exists(\App\Services\LeadService::class)) {
                dispatch(function () {
                    app(\App\Services\LeadService::class)->getDashboardStats();
                });
            }

            // Add more warm-up tasks as needed
            
        } catch (\Exception $e) {
            logger()->error('Cache warm-up failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate cache key from parameters
     *
     * @param string $prefix
     * @param array $params
     * @return string
     */
    public static function generateKey(string $prefix, array $params = []): string
    {
        return $prefix . ':' . md5(serialize($params));
    }
}
