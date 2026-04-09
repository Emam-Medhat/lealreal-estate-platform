<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class CheckMaintenanceMode
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
        // Check if application is in maintenance mode
        if ($this->isInMaintenanceMode()) {
            return $this->handleMaintenanceMode($request, $next);
        }

        // Check if specific module is in maintenance mode
        $moduleMaintenance = $this->getModuleMaintenanceStatus($request);
        if ($moduleMaintenance) {
            return $this->handleModuleMaintenance($request, $moduleMaintenance, $next);
        }

        return $next($request);
    }

    /**
     * Check if application is in maintenance mode
     *
     * @return bool
     */
    private function isInMaintenanceMode()
    {
        // Check Laravel's built-in maintenance mode
        if (app()->isDownForMaintenance()) {
            return true;
        }

        // Check custom maintenance mode from database/cache
        return Cache::get('maintenance_mode', false);
    }

    /**
     * Handle application maintenance mode
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function handleMaintenanceMode($request, $next)
    {
        $maintenanceData = $this->getMaintenanceData();

        // Allow access to maintenance page
        if ($request->is('maintenance') || $request->is('maintenance/*')) {
            return $next($request);
        }

        // Allow access for admin users
        if ($this->isAdminUser($request)) {
            return $next($request);
        }

        // Allow access to API health check
        if ($request->is('api/health') || $request->is('api/status')) {
            return response()->json([
                'status' => 'maintenance',
                'message' => 'System is under maintenance'
            ], 503);
        }

        // Return maintenance response
        if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $maintenanceData['message'] ?? 'النظام تحت الصيانة حالياً',
                'code' => 503,
                'maintenance_mode' => true,
                'estimated_completion' => $maintenanceData['estimated_completion'] ?? null
            ], 503);
        }

        // Redirect to maintenance page
        return response()->view('errors.maintenance', [
            'message' => $maintenanceData['message'] ?? 'النظام تحت الصيانة حالياً',
            'estimated_completion' => $maintenanceData['estimated_completion'] ?? null,
            'contact_info' => $maintenanceData['contact_info'] ?? null
        ], 503);
    }

    /**
     * Get maintenance data
     *
     * @return array
     */
    private function getMaintenanceData()
    {
        return Cache::get('maintenance_data', [
            'message' => 'النظام تحت الصيانة حالياً. نعتذر عن الإزعاج وسنعود قريباً',
            'estimated_completion' => null,
            'contact_info' => [
                'email' => 'support@example.com',
                'phone' => '+966500000000'
            ]
        ]);
    }

    /**
     * Check if user is admin
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function isAdminUser($request)
    {
        $user = $request->user();

        if (!$user) {
            return false;
        }

        return $user->hasRole('admin') || $user->is_super_admin;
    }

    /**
     * Get module maintenance status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|null
     */
    private function getModuleMaintenanceStatus($request)
    {
        $routeName = $request->route() ? $request->route()->getName() : '';
        $path = $request->path();

        // Define module patterns
        $modulePatterns = [
            'properties' => ['properties', 'property'],
            'users' => ['users', 'user'],
            'companies' => ['companies', 'company'],
            'reports' => ['reports', 'report'],
            'payments' => ['payments', 'payment', 'billing'],
            'documents' => ['documents', 'document'],
            'notifications' => ['notifications', 'notification'],
            'analytics' => ['analytics', 'statistics'],
            'api' => ['api'],
        ];

        foreach ($modulePatterns as $module => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($routeName, $pattern) !== false || strpos($path, $pattern) !== false) {
                    $moduleMaintenance = Cache::get("maintenance_mode:{$module}");

                    if ($moduleMaintenance) {
                        return [
                            'module' => $module,
                            'data' => $moduleMaintenance
                        ];
                    }
                }
            }
        }

        return null;
    }

    /**
     * Handle module maintenance
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $moduleMaintenance
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function handleModuleMaintenance($request, $moduleMaintenance, $next)
    {
        $module = $moduleMaintenance['module'];
        $data = $moduleMaintenance['data'];

        // Allow access for admin users
        if ($this->isAdminUser($request)) {
            // Add warning message for admin
            if ($request->session()) {
                $request->session()->flash('warning', "وحدة {$module} تحت الصيانة حالياً");
            }
            return $next($request);
        }

        // Return module maintenance response
        if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? "وحدة {$module} تحت الصيانة حالياً",
                'code' => 503,
                'maintenance_mode' => true,
                'module' => $module,
                'estimated_completion' => $data['estimated_completion'] ?? null
            ], 503);
        }

        // Redirect back with message
        return redirect()->back()
            ->with('error', $data['message'] ?? "وحدة {$module} تحت الصيانة حالياً")
            ->with('maintenance_info', [
                'module' => $module,
                'estimated_completion' => $data['estimated_completion'] ?? null
            ]);
    }

    /**
     * Enable maintenance mode
     *
     * @param  array  $data
     * @return void
     */
    public static function enableMaintenanceMode($data = [])
    {
        $defaultData = [
            'message' => 'النظام تحت الصيانة حالياً. نعتذر عن الإزعاج وسنعود قريباً',
            'estimated_completion' => null,
            'contact_info' => [
                'email' => 'support@example.com',
                'phone' => '+966500000000'
            ],
            'enabled_at' => now()->toDateTimeString(),
            'enabled_by' => auth()->user() ? auth()->user()->id : null
        ];

        Cache::put('maintenance_mode', true, now()->addDays(7));
        Cache::put('maintenance_data', array_merge($defaultData, $data), now()->addDays(7));
    }

    /**
     * Disable maintenance mode
     *
     * @return void
     */
    public static function disableMaintenanceMode()
    {
        Cache::forget('maintenance_mode');
        Cache::forget('maintenance_data');
    }

    /**
     * Enable module maintenance mode
     *
     * @param  string  $module
     * @param  array  $data
     * @return void
     */
    public static function enableModuleMaintenanceMode($module, $data = [])
    {
        $defaultData = [
            'message' => "وحدة {$module} تحت الصيانة حالياً",
            'estimated_completion' => null,
            'enabled_at' => now()->toDateTimeString(),
            'enabled_by' => auth()->user() ? auth()->user()->id : null
        ];

        Cache::put("maintenance_mode:{$module}", array_merge($defaultData, $data), now()->addDays(7));
    }

    /**
     * Disable module maintenance mode
     *
     * @param  string  $module
     * @return void
     */
    public static function disableModuleMaintenanceMode($module)
    {
        Cache::forget("maintenance_mode:{$module}");
    }
}
