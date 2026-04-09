<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogService;

class TrackUserActivity
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

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
        
        // Create activity record using the new service
        $this->activityLogService->logActivity(
            $activityData['action'],
            $activityData['description'],
            $activityData['metadata'],
            $user->id
        );
    }

    /**
     * Determine if tracking should be skipped
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function shouldSkipTracking($request)
    {
        // Skip AJAX requests
        if ($request->ajax()) {
            return true;
        }

        // Skip GET requests to reduce noise
        if ($request->isMethod('GET')) {
            return true;
        }

        // Skip certain routes
        $skipRoutes = [
            'admin.logs.index',
            'admin.activity.index',
            'login',
            'logout',
            'password.request',
            'password.reset',
        ];

        return in_array($request->route()?->getName(), $skipRoutes);
    }

    /**
     * Get activity data from request and response
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $response
     * @return array
     */
    private function getActivityData($request, $response)
    {
        $routeName = $request->route()?->getName();
        $method = $request->method();
        $status = $response->getStatusCode();

        // Determine action
        $action = $this->getActionFromRoute($routeName, $method);

        // Generate description
        $description = $this->generateDescription($routeName, $method, $status);

        // Build metadata
        $metadata = [
            'method' => $method,
            'route' => $routeName,
            'url' => $request->fullUrl(),
            'status_code' => $status,
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'type' => $this->getActivityType($routeName),
            'priority' => $this->getActivityPriority($routeName, $status),
        ];

        // Add request data for debugging (excluding sensitive fields)
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $requestData = $request->all();
            
            // Remove sensitive fields
            $sensitiveFields = ['password', 'password_confirmation', 'token', 'secret', 'key', 'api_key'];
            foreach ($sensitiveFields as $field) {
                unset($requestData[$field]);
            }
            
            $metadata['request_data'] = $requestData;
        }

        // Add response data for errors
        if ($status >= 400) {
            $metadata['response_data'] = [
                'status' => $status,
                'message' => $response->exception?->getMessage(),
            ];
        }

        return [
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
        ];
    }

    /**
     * Get action from route name and method
     *
     * @param  string|null  $routeName
     * @param  string  $method
     * @return string
     */
    private function getActionFromRoute($routeName, $method)
    {
        if (!$routeName) {
            return $method . '_request';
        }

        // Extract action from route name
        $parts = explode('.', $routeName);
        $action = end($parts);

        // Map common actions
        $actionMap = [
            'store' => 'created',
            'update' => 'updated',
            'destroy' => 'deleted',
            'show' => 'viewed',
            'index' => 'listed',
            'create' => 'viewed_create',
            'edit' => 'viewed_edit',
            'login' => 'login_success',
            'logout' => 'logout_success',
        ];

        return $actionMap[$action] ?? $action;
    }

    /**
     * Generate activity description
     *
     * @param  string|null  $routeName
     * @param  string  $method
     * @param  int  $status
     * @return string
     */
    private function generateDescription($routeName, $method, $status)
    {
        if ($status >= 400) {
            return "Error occurred in {$routeName}: HTTP {$status}";
        }

        if ($method === 'POST') {
            return "Successfully created resource via {$routeName}";
        } elseif ($method === 'PUT' || $method === 'PATCH') {
            return "Successfully updated resource via {$routeName}";
        } elseif ($method === 'DELETE') {
            return "Successfully deleted resource via {$routeName}";
        }

        return "Accessed {$routeName}";
    }

    /**
     * Determine activity type
     *
     * @param  string|null  $routeName
     * @return string
     */
    private function getActivityType($routeName)
    {
        if (!$routeName) {
            return 'general';
        }

        // Financial routes
        $financialRoutes = [
            'payments.',
            'invoices.',
            'financial.',
            'billing.',
            'transactions.',
        ];

        // Security routes
        $securityRoutes = [
            'login',
            'logout',
            'register',
            'password.',
            '2fa.',
            'auth.',
            'admin.users.',
        ];

        // System routes
        $systemRoutes = [
            'admin.system.',
            'admin.settings.',
            'admin.logs.',
            'admin.backup.',
        ];

        foreach ($financialRoutes as $route) {
            if (str_starts_with($routeName, $route)) {
                return 'financial';
            }
        }

        foreach ($securityRoutes as $route) {
            if (str_starts_with($routeName, $route)) {
                return 'security';
            }
        }

        foreach ($systemRoutes as $route) {
            if (str_starts_with($routeName, $route)) {
                return 'system';
            }
        }

        return 'general';
    }

    /**
     * Determine activity priority
     *
     * @param  string|null  $routeName
     * @param  int  $status
     * @return string
     */
    private function getActivityPriority($routeName, $status)
    {
        // High priority for errors
        if ($status >= 400) {
            return 'high';
        }

        // Critical priority for security routes
        $criticalRoutes = [
            'login',
            'logout',
            'register',
            'password.',
            '2fa.',
            'auth.',
        ];

        foreach ($criticalRoutes as $route) {
            if (str_starts_with($routeName, $route)) {
                return 'critical';
            }
        }

        // High priority for financial routes
        $highPriorityRoutes = [
            'payments.',
            'invoices.',
            'financial.',
            'billing.',
            'transactions.',
        ];

        foreach ($highPriorityRoutes as $route) {
            if (str_starts_with($routeName, $route)) {
                return 'high';
            }
        }

        return 'medium';
    }
}
