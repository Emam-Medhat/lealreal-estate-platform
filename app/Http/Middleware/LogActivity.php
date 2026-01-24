<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class LogActivity
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

        // Only log if user is authenticated
        if (Auth::check()) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * Log user activity
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $response
     * @return void
     */
    private function logActivity($request, $response)
    {
        $user = Auth::user();
        
        // Get activity details
        $activity = $this->getActivityDetails($request);
        
        // Skip logging for certain routes
        if ($this->shouldSkipLogging($request)) {
            return;
        }

        // Create activity log
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $activity['action'],
            'description' => $activity['description'],
            'subject_type' => $activity['subject_type'],
            'subject_id' => $activity['subject_id'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route_name' => $request->route() ? $request->route()->getName() : null,
            'status_code' => $response->getStatusCode(),
            'response_time' => $this->getResponseTime($request),
            'properties' => json_encode([
                'request_data' => $this->sanitizeRequestData($request),
                'response_data' => $this->getResponseData($response),
                'session_id' => session()->getId(),
                'device_info' => $this->getDeviceInfo($request),
                'location' => $this->getLocation($request)
            ])
        ]);
    }

    /**
     * Get activity details based on request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private function getActivityDetails($request)
    {
        $routeName = $request->route() ? $request->route()->getName() : 'unknown';
        $method = $request->method();
        
        $activities = [
            // Properties
            'properties.index' => ['action' => 'view', 'description' => 'عرض قائمة العقارات', 'subject_type' => 'Property'],
            'properties.show' => ['action' => 'view', 'description' => 'عرض تفاصيل العقار', 'subject_type' => 'Property'],
            'properties.create' => ['action' => 'create', 'description' => 'إنشاء عقار جديد', 'subject_type' => 'Property'],
            'properties.store' => ['action' => 'create', 'description' => 'حفظ عقار جديد', 'subject_type' => 'Property'],
            'properties.edit' => ['action' => 'edit', 'description' => 'تعديل عقار', 'subject_type' => 'Property'],
            'properties.update' => ['action' => 'edit', 'description' => 'تحديث عقار', 'subject_type' => 'Property'],
            'properties.destroy' => ['action' => 'delete', 'description' => 'حذف عقار', 'subject_type' => 'Property'],
            
            // Users
            'users.index' => ['action' => 'view', 'description' => 'عرض قائمة المستخدمين', 'subject_type' => 'User'],
            'users.show' => ['action' => 'view', 'description' => 'عرض تفاصيل المستخدم', 'subject_type' => 'User'],
            'users.create' => ['action' => 'create', 'description' => 'إنشاء مستخدم جديد', 'subject_type' => 'User'],
            'users.store' => ['action' => 'create', 'description' => 'حفظ مستخدم جديد', 'subject_type' => 'User'],
            'users.edit' => ['action' => 'edit', 'description' => 'تعديل مستخدم', 'subject_type' => 'User'],
            'users.update' => ['action' => 'edit', 'description' => 'تحديث مستخدم', 'subject_type' => 'User'],
            'users.destroy' => ['action' => 'delete', 'description' => 'حذف مستخدم', 'subject_type' => 'User'],
            
            // Login/Logout
            'login' => ['action' => 'login', 'description' => 'تسجيل الدخول', 'subject_type' => 'Auth'],
            'logout' => ['action' => 'logout', 'description' => 'تسجيل الخروج', 'subject_type' => 'Auth'],
            
            // Dashboard
            'dashboard' => ['action' => 'view', 'description' => 'عرض لوحة التحكم', 'subject_type' => 'Dashboard'],
        ];

        // Get activity from predefined list or generate generic one
        if (isset($activities[$routeName])) {
            $activity = $activities[$routeName];
        } else {
            $activity = [
                'action' => strtolower($method),
                'description' => $this->generateDescription($request),
                'subject_type' => $this->getSubjectType($request)
            ];
        }

        // Add subject ID if available
        if ($request->route('id')) {
            $activity['subject_id'] = $request->route('id');
        } elseif ($request->input('id')) {
            $activity['subject_id'] = $request->input('id');
        }

        return $activity;
    }

    /**
     * Check if logging should be skipped
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function shouldSkipLogging($request)
    {
        $skipRoutes = [
            'activity.logs',
            'logs.index',
            'api.activity',
            'heartbeat',
            'ping'
        ];

        $routeName = $request->route() ? $request->route()->getName() : null;

        // Skip if route is in skip list
        if ($routeName && in_array($routeName, $skipRoutes)) {
            return true;
        }

        // Skip if request is to static assets
        if ($request->is('assets/*') || $request->is('storage/*')) {
            return true;
        }

        // Skip if request method is GET and to API endpoints (to reduce noise)
        if ($request->isMethod('GET') && $request->is('api/*')) {
            return true;
        }

        return false;
    }

    /**
     * Generate description for unknown routes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function generateDescription($request)
    {
        $routeName = $request->route() ? $request->route()->getName() : 'unknown';
        $method = $request->method();
        
        $descriptions = [
            'GET' => 'الوصول إلى',
            'POST' => 'إنشاء',
            'PUT' => 'تحديث',
            'PATCH' => 'تعديل',
            'DELETE' => 'حذف'
        ];

        return ($descriptions[$method] ?? 'تنفيذ إجراء') . ' ' . $routeName;
    }

    /**
     * Get subject type from request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function getSubjectType($request)
    {
        $routeName = $request->route() ? $request->route()->getName() : '';
        
        if (strpos($routeName, 'properties') !== false) {
            return 'Property';
        } elseif (strpos($routeName, 'users') !== false) {
            return 'User';
        } elseif (strpos($routeName, 'companies') !== false) {
            return 'Company';
        } elseif (strpos($routeName, 'reports') !== false) {
            return 'Report';
        } elseif (strpos($routeName, 'login') !== false || strpos($routeName, 'logout') !== false) {
            return 'Auth';
        }
        
        return 'System';
    }

    /**
     * Sanitize request data for logging
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private function sanitizeRequestData($request)
    {
        $data = $request->all();
        
        // Remove sensitive data
        $sensitiveKeys = ['password', 'password_confirmation', 'api_key', 'secret', 'token'];
        
        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '***';
            }
        }
        
        // Remove file uploads
        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $data[$key] = '[FILE: ' . $request->file($key)->getClientOriginalName() . ']';
            }
        }
        
        return $data;
    }

    /**
     * Get response data for logging
     *
     * @param  mixed  $response
     * @return array
     */
    private function getResponseData($response)
    {
        if (method_exists($response, 'getData')) {
            $data = $response->getData();
            
            // Limit response data size
            if (is_array($data) || is_object($data)) {
                $data = json_decode(json_encode($data), true);
                
                // Truncate large arrays
                if (is_array($data) && count($data) > 10) {
                    $data = array_slice($data, 0, 10) + ['...' => 'truncated'];
                }
            }
            
            return is_array($data) ? $data : ['data' => $data];
        }
        
        return [];
    }

    /**
     * Get device information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private function getDeviceInfo($request)
    {
        $userAgent = $request->userAgent();
        
        return [
            'browser' => $this->getBrowser($userAgent),
            'platform' => $this->getPlatform($userAgent),
            'mobile' => $this->isMobile($userAgent),
            'tablet' => $this->isTablet($userAgent)
        ];
    }

    /**
     * Get location information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private function getLocation($request)
    {
        // This is a placeholder - you might want to use a geo IP service
        return [
            'ip' => $request->ip(),
            'country' => null,
            'city' => null
        ];
    }

    /**
     * Get response time
     *
     * @param  \Illuminate\Http\Request  $request
     * @return float
     */
    private function getResponseTime($request)
    {
        if (defined('LARAVEL_START')) {
            return microtime(true) - LARAVEL_START;
        }
        
        return 0;
    }

    /**
     * Get browser from user agent
     */
    private function getBrowser($userAgent)
    {
        // Simple browser detection
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        }
        
        return 'Unknown';
    }

    /**
     * Get platform from user agent
     */
    private function getPlatform($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            return 'Mac';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android';
        } elseif (strpos($userAgent, 'iOS') !== false) {
            return 'iOS';
        }
        
        return 'Unknown';
    }

    /**
     * Check if mobile
     */
    private function isMobile($userAgent)
    {
        return strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false;
    }

    /**
     * Check if tablet
     */
    private function isTablet($userAgent)
    {
        return strpos($userAgent, 'iPad') !== false || strpos($userAgent, 'Tablet') !== false;
    }
}
