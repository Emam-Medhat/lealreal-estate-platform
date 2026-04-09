<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, int $seconds = 300): Response
    {
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Don't cache authenticated pages
        if (auth()->check()) {
            return $next($request);
        }

        // Don't cache pages with form submissions
        if ($request->has('_token') || $request->has('password')) {
            return $next($request);
        }

        // Generate cache key
        $cacheKey = $this->generateCacheKey($request);

        // Check if response is cached
        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            
            // Rebuild response from cached data
            $response = new \Illuminate\Http\Response(
                $cachedData['content'],
                $cachedData['status'],
                $cachedData['headers']
            );
            
            return $response;
        }

        // Get response
        $response = $next($request);

        // Only cache successful HTML responses
        if ($response->isSuccessful() && str_contains($response->getContent(), '<!DOCTYPE')) {
            // Cache only the content, not the full response object
            $cachedData = [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
            ];
            
            Cache::put($cacheKey, $cachedData, $seconds);
        }

        return $response;
    }

    /**
     * Generate cache key based on request
     */
    private function generateCacheKey(Request $request): string
    {
        $key = 'response_' . str_replace('/', '_', $request->path());
        
        if ($request->query->count() > 0) {
            // Use json_encode instead of serialize to avoid closure issues
            $key .= '_' . md5(json_encode($request->query->all()));
        }
        
        return $key;
    }
}
