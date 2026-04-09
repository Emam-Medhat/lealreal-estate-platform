<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\CacheService;

class CacheMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $duration
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $duration = 'short')
    {
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Don't cache authenticated user-specific pages
        if ($request->user() && $this->isUserSpecificRoute($request)) {
            return $next($request);
        }

        $cacheKey = $this->getCacheKey($request);
        
        return CacheService::remember($cacheKey, function () use ($request, $next) {
            return $next($request);
        }, $duration, ['responses']);
    }

    /**
     * Generate cache key for request
     *
     * @param Request $request
     * @return string
     */
    private function getCacheKey(Request $request): string
    {
        return 'response:' . md5($request->fullUrl() . $request->getQueryString());
    }

    /**
     * Check if route is user-specific
     *
     * @param Request $request
     * @return bool
     */
    private function isUserSpecificRoute(Request $request): bool
    {
        $userSpecificRoutes = [
            'dashboard',
            'profile',
            'settings',
            'admin',
            'leads',
            'appointments',
        ];

        foreach ($userSpecificRoutes as $route) {
            if (str_contains($request->path(), $route)) {
                return true;
            }
        }

        return false;
    }
}
