<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivity;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only track for authenticated users
        if (Auth::check()) {
            $this->trackActivity($request, $response);
        }

        return $response;
    }

    /**
     * Track user activity
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $response
     * @return void
     */
    private function trackActivity($request, $response)
    {
        $user = Auth::user();
        
        // Skip tracking for certain routes
        if ($this->shouldSkipTracking($request)) {
            return;
        }

        // Get activity details
        $activityData = $this->getActivityData($request, $response);
        
        // Create activity record
        UserActivity::create(array_merge($activityData, [
            'user_id' => $user->id,
            'session_id' => session()->getId() ?? 'no-session',
            'method' => $request->method(),
            'url' => $request->path(),
            'full_url' => $request->fullUrl(),
            'query_parameters' => $request->query(),
            'request_data' => $request->except(['password', 'password_confirmation', '_token']),
            'response_status' => $response->getStatusCode(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'is_authenticated' => true,
            'is_admin' => $user->is_admin ?? false,
            'is_premium' => $user->is_premium ?? false,
            'subscription_tier' => $user->subscription_tier ?? null,
            'last_activity_at' => now(),
        ]));

        // Update user's last seen timestamp
        if (method_exists($user, 'update')) {
            $user->update(['last_seen_at' => now()]);
        }
    }

    /**
     * Check if tracking should be skipped
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function shouldSkipTracking($request)
    {
        $skipRoutes = [
            'telescope.*',
            'horizon.*',
            'debugbar.*',
            'ignition.*',
            'routes.*',
        ];

        $routeName = $request->route() ? $request->route()->getName() : null;

        // Skip if route is in skip list
        if ($routeName && in_array($routeName, $skipRoutes)) {
            return true;
        }

        // Skip if request is to static assets
        if ($request->is('assets/*') || $request->is('storage/*') || $request->is('css/*') || $request->is('js/*')) {
            return true;
        }

        // Skip if request method is GET and to API endpoints (to reduce noise)
        if ($request->isMethod('GET') && $request->is('api/*')) {
            return true;
        }

        // Skip if request is AJAX and to certain endpoints
        if ($request->ajax() && $this->isAjaxTrackingSkippable($request)) {
            return true;
        }

        return false;
    }

    /**
     * Check if AJAX request should be skipped from tracking
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function isAjaxTrackingSkippable($request)
    {
        $skippablePatterns = [
            'notifications',
            'search',
            'autocomplete',
            'preview',
            'validate',
            'check'
        ];

        foreach ($skippablePatterns as $pattern) {
            if (strpos($request->path(), $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get activity data
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $response
     * @return array
     */
    private function getActivityData($request, $response)
    {
        $routeName = $request->route() ? $request->route()->getName() : 'unknown';
        
        // Get device info
        $deviceInfo = $this->getDeviceInfo($request);
        
        return [
            'activity_type' => $this->getActivityType($request),
            'activity_category' => $this->getActivityCategory($request, $routeName),
            'activity_description' => $this->getActivityDescription($request, $routeName),
            'duration' => $this->getRequestDuration($request),
            'device_type' => $deviceInfo['type'] ?? null,
            'browser' => $deviceInfo['browser'] ?? null,
            'platform' => $deviceInfo['platform'] ?? null,
            'is_mobile' => $deviceInfo['is_mobile'] ?? false,
            'is_tablet' => $deviceInfo['is_tablet'] ?? false,
            'is_desktop' => $deviceInfo['is_desktop'] ?? false,
            'is_bot' => $deviceInfo['is_bot'] ?? false,
            'bot_name' => $deviceInfo['bot_name'] ?? null,
            'location_country' => $this->getLocationCountry($request),
            'location_city' => $this->getLocationCity($request),
            'metadata' => [
                'route_name' => $routeName,
                'is_ajax' => $request->ajax(),
                'is_api' => $request->is('api/*'),
                'response_size' => strlen($response->getContent() ?? ''),
            ]
        ];
    }

    /**
     * Get activity type based on request
     */
    private function getActivityType($request)
    {
        if ($request->isMethod('GET')) {
            return 'page_view';
        } elseif (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            return 'form_submit';
        } elseif ($request->method() === 'DELETE') {
            return 'delete_action';
        } elseif ($request->is('api/*')) {
            return 'api_call';
        }
        
        return 'other';
    }

    /**
     * Get activity category based on route
     */
    private function getActivityCategory($request, $routeName)
    {
        if (!$routeName) return 'general';
        
        if (str_contains($routeName, 'properties')) return 'property';
        if (str_contains($routeName, 'users') || str_contains($routeName, 'profile')) return 'user';
        if (str_contains($routeName, 'admin')) return 'admin';
        if (str_contains($routeName, 'search')) return 'search';
        if (str_contains($routeName, 'analytics')) return 'analytics';
        if (str_contains($routeName, 'reports')) return 'reports';
        if (str_contains($routeName, 'payment')) return 'payment';
        if (str_contains($routeName, 'messages')) return 'communication';
        
        return 'general';
    }

    /**
     * Get activity description
     */
    private function getActivityDescription($request, $routeName)
    {
        $method = $request->method();
        $path = $request->path();
        
        if ($method === 'GET') {
            return "Visited {$path}";
        } elseif ($method === 'POST') {
            return "Submitted form to {$path}";
        } elseif ($method === 'PUT' || $method === 'PATCH') {
            return "Updated resource at {$path}";
        } elseif ($method === 'DELETE') {
            return "Deleted resource at {$path}";
        }
        
        return "Accessed {$path}";
    }

    /**
     * Get request duration
     */
    private function getRequestDuration($request)
    {
        if (defined('LARAVEL_START')) {
            return microtime(true) - LARAVEL_START;
        }
        return null;
    }

    /**
     * Get location country (simplified)
     */
    private function getLocationCountry($request)
    {
        // You can integrate with a GeoIP service here
        return null;
    }

    /**
     * Get location city (simplified)
     */
    private function getLocationCity($request)
    {
        // You can integrate with a GeoIP service here
        return null;
    }

    /**
     * Get device info
     */
    private function getDeviceInfo($request)
    {
        $userAgent = $request->userAgent();
        
        // Simple device detection
        $isMobile = preg_match('/Mobile|Android|iPhone|iPad|iPod/', $userAgent);
        $isTablet = preg_match('/iPad|Tablet/', $userAgent);
        $isDesktop = !$isMobile && !$isTablet;
        $isBot = $this->isBot($userAgent);
        
        return [
            'type' => $isMobile ? 'mobile' : ($isTablet ? 'tablet' : 'desktop'),
            'browser' => $this->getBrowser($userAgent),
            'platform' => $this->getPlatform($userAgent),
            'is_mobile' => $isMobile,
            'is_tablet' => $isTablet,
            'is_desktop' => $isDesktop,
            'is_bot' => $isBot,
            'bot_name' => $isBot ? $this->getBotName($userAgent) : null,
        ];
    }

    /**
     * Check if user agent is a bot
     */
    private function isBot($userAgent)
    {
        $bots = ['Googlebot', 'Bingbot', 'Slurp', 'DuckDuckBot', 'Baiduspider'];
        
        foreach ($bots as $bot) {
            if (strpos($userAgent, $bot) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get browser name
     */
    private function getBrowser($userAgent)
    {
        if (preg_match('/Chrome/', $userAgent)) return 'Chrome';
        if (preg_match('/Firefox/', $userAgent)) return 'Firefox';
        if (preg_match('/Safari/', $userAgent)) return 'Safari';
        if (preg_match('/Edge/', $userAgent)) return 'Edge';
        if (preg_match('/Opera/', $userAgent)) return 'Opera';
        
        return 'Unknown';
    }

    /**
     * Get platform name
     */
    private function getPlatform($userAgent)
    {
        if (preg_match('/Windows/', $userAgent)) return 'Windows';
        if (preg_match('/Mac/', $userAgent)) return 'MacOS';
        if (preg_match('/Linux/', $userAgent)) return 'Linux';
        if (preg_match('/Android/', $userAgent)) return 'Android';
        if (preg_match('/iOS/', $userAgent)) return 'iOS';
        
        return 'Unknown';
    }

    /**
     * Get bot name
     */
    private function getBotName($userAgent)
    {
        if (strpos($userAgent, 'Googlebot') !== false) return 'Googlebot';
        if (strpos($userAgent, 'Bingbot') !== false) return 'Bingbot';
        if (strpos($userAgent, 'Slurp') !== false) return 'Yahoo Slurp';
        if (strpos($userAgent, 'DuckDuckBot') !== false) return 'DuckDuckBot';
        if (strpos($userAgent, 'Baiduspider') !== false) return 'Baidu Spider';
        
        return 'Unknown Bot';
    }
}
