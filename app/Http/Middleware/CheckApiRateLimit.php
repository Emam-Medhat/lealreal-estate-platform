<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CheckApiRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $limit
     * @param  string  $window
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $limit = '60', $window = '1')
    {
        // Only apply to API routes
        if (!$request->is('api/*')) {
            return $next($request);
        }

        $user = Auth::user();
        $key = $this->getRateLimitKey($request, $user);
        
        // Get rate limit configuration
        $maxRequests = $this->getMaxRequests($limit, $user);
        $windowMinutes = (int) $window;
        
        // Check current usage
        $current = $this->getCurrentUsage($key, $windowMinutes);
        
        if ($current >= $maxRequests) {
            $this->logRateLimitExceeded($request, $user, $maxRequests, $windowMinutes);
            
            return response()->json([
                'success' => false,
                'message' => 'تم تجاوز الحد المسموح من الطلبات. يرجى المحاولة مرة أخرى لاحقاً',
                'code' => 429,
                'retry_after' => $this->getRetryAfter($key, $windowMinutes),
                'limit' => $maxRequests,
                'remaining' => 0,
                'reset' => $this->getResetTime($key, $windowMinutes)
            ], 429)
            ->header('X-RateLimit-Limit', $maxRequests)
            ->header('X-RateLimit-Remaining', 0)
            ->header('X-RateLimit-Reset', $this->getResetTime($key, $windowMinutes))
            ->header('Retry-After', $this->getRetryAfter($key, $windowMinutes));
        }
        
        // Increment counter
        $this->incrementCounter($key, $windowMinutes);
        
        // Add rate limit headers
        $remaining = $maxRequests - ($current + 1);
        
        $response = $next($request);
        
        $response->headers->set('X-RateLimit-Limit', $maxRequests);
        $response->headers->set('X-RateLimit-Remaining', $remaining);
        $response->headers->set('X-RateLimit-Reset', $this->getResetTime($key, $windowMinutes));
        
        return $response;
    }
    
    /**
     * Get rate limit key
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User|null  $user
     * @return string
     */
    private function getRateLimitKey($request, $user)
    {
        if ($user) {
            return "api_rate_limit:user:{$user->id}:{$request->ip()}";
        }
        
        return "api_rate_limit:ip:{$request->ip()}";
    }
    
    /**
     * Get maximum requests based on user and limit
     *
     * @param  string  $limit
     * @param  \App\Models\User|null  $user
     * @return int
     */
    private function getMaxRequests($limit, $user)
    {
        // If user is authenticated, check subscription limits
        if ($user) {
            $subscription = $user->activeSubscription;
            
            if ($subscription) {
                $limits = $subscription->getLimits();
                
                if (isset($limits['api_calls_per_minute'])) {
                    return $limits['api_calls_per_minute'];
                }
            }
            
            // Default limits by role
            $roleLimits = [
                'admin' => 1000,
                'company' => 500,
                'agent' => 300,
                'user' => 100
            ];
            
            return $roleLimits[$user->role] ?? 100;
        }
        
        // Default limit for unauthenticated users
        return (int) $limit;
    }
    
    /**
     * Get current usage count
     *
     * @param  string  $key
     * @param  int  $windowMinutes
     * @return int
     */
    private function getCurrentUsage($key, $windowMinutes)
    {
        if (Redis::connection()) {
            return (int) Redis::get($key) ?: 0;
        }
        
        // Fallback to cache
        return Cache::get($key, 0);
    }
    
    /**
     * Increment counter
     *
     * @param  string  $key
     * @param  int  $windowMinutes
     * @return void
     */
    private function incrementCounter($key, $windowMinutes)
    {
        $ttl = $windowMinutes * 60;
        
        if (Redis::connection()) {
            Redis::incr($key);
            Redis::expire($key, $ttl);
        } else {
            // Fallback to cache
            Cache::increment($key);
            Cache::put($key, Cache::get($key, 1), now()->addMinutes($windowMinutes));
        }
    }
    
    /**
     * Get retry after seconds
     *
     * @param  string  $key
     * @param  int  $windowMinutes
     * @return int
     */
    private function getRetryAfter($key, $windowMinutes)
    {
        if (Redis::connection()) {
            $ttl = Redis::ttl($key);
            return $ttl > 0 ? $ttl : $windowMinutes * 60;
        }
        
        // Fallback calculation
        $windowStart = now()->subMinutes($windowMinutes);
        $keyWithWindow = $key . ':' . $windowStart->format('Y-m-d H:i');
        
        $remaining = Cache::get($keyWithWindow, 0);
        return $windowMinutes * 60;
    }
    
    /**
     * Get reset time timestamp
     *
     * @param  string  $key
     * @param  int  $windowMinutes
     * @return int
     */
    private function getResetTime($key, $windowMinutes)
    {
        $resetTime = now()->addMinutes($windowMinutes);
        return $resetTime->timestamp;
    }
    
    /**
     * Log rate limit exceeded
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User|null  $user
     * @param  int  $limit
     * @param  int  $windowMinutes
     * @return void
     */
    private function logRateLimitExceeded($request, $user, $limit, $windowMinutes)
    {
        activity()
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'limit' => $limit,
                'window' => $windowMinutes,
                'user_id' => $user ? $user->id : null,
                'is_authenticated' => $user ? true : false
            ])
            ->log('تم تجاوز الحد المسموح من طلبات API');
        
        // Also log to security log
        if (class_exists('\App\Models\SecurityLog')) {
            \App\Models\SecurityLog::create([
                'type' => 'rate_limit_exceeded',
                'description' => 'API rate limit exceeded',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => $user ? $user->id : null,
                'metadata' => json_encode([
                    'endpoint' => $request->path(),
                    'method' => $request->method(),
                    'limit' => $limit,
                    'window' => $windowMinutes
                ])
            ]);
        }
    }
}
