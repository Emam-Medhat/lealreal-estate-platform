<?php

namespace App\Http\Controllers;

use App\Models\SmartSecurity;
use App\Models\SmartProperty;
use App\Models\IotDevice;
use App\Models\IotAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SmartSecurityController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_systems' => SmartSecurity::count(),
            'armed_systems' => SmartSecurity::where('is_armed', true)->count(),
            'active_alerts' => IotAlert::where('type', 'security')->where('status', 'active')->count(),
            'total_cameras' => IotDevice::where('device_type', 'camera')->count(),
            'total_sensors' => IotDevice::where('device_type', 'sensor')->count(),
            'security_score' => $this->getAverageSecurityScore(),
        ];

        $recentSystems = SmartSecurity::with(['property', 'devices'])
            ->latest()
            ->take(10)
            ->get();

        $securityAlerts = $this->getRecentSecurityAlerts();
        $systemStatus = $this->getSecuritySystemStatus();

        return view('iot.security.dashboard', compact(
            'stats', 
            'recentSystems', 
            'securityAlerts', 
            'systemStatus'
        ));
    }

    public function index(Request $request)
    {
        $query = SmartSecurity::with(['property', 'devices']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('system_type')) {
            $query->where('system_type', $request->system_type);
        }

        if ($request->filled('is_armed')) {
            $query->where('is_armed', $request->is_armed);
        }

        $systems = $query->latest()->paginate(12);

        $systemTypes = ['cameras', 'sensors', 'smart_locks', 'alarms', 'monitoring'];
        $statuses = ['active', 'inactive', 'maintenance', 'error'];

        return view('iot.security.index', compact(
            'systems', 
            'systemTypes', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();
        $systemTypes = ['cameras', 'sensors', 'smart_locks', 'alarms', 'monitoring'];
        $devices = IotDevice::where('device_type', 'security')->get();

        return view('iot.security.create', compact(
            'properties', 
            'systemTypes', 
            'devices'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $securityData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'system_name' => 'required|string|max:255',
                'system_type' => 'required|in:cameras,sensors,smart_locks,alarms,monitoring',
                'configuration' => 'nullable|array',
                'sensors' => 'nullable|array',
                'alerts_enabled' => 'required|boolean',
                'is_armed' => 'required|boolean',
                'arming_schedule' => 'nullable|array',
                'notification_settings' => 'nullable|array',
                'status' => 'required|in:active,inactive,maintenance,error',
            ]);

            $securityData['created_by'] = auth()->id();
            $securityData['security_metadata'] = $this->generateSecurityMetadata($request);

            $security = SmartSecurity::create($securityData);

            // Link security devices
            if ($request->has('sensor_ids')) {
                $this->linkSecurityDevices($security, $request->sensor_ids);
            }

            // Set up arming schedule
            if ($request->has('arming_schedule')) {
                $this->setupArmingSchedule($security, $request->arming_schedule);
            }

            // Configure notifications
            if ($request->has('notification_settings')) {
                $this->configureNotifications($security, $request->notification_settings);
            }

            DB::commit();

            return redirect()
                ->route('smart-security.show', $security)
                ->with('success', 'تم إعداد نظام الأمن الذكي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إعداد النظام: ' . $e->getMessage());
        }
    }

    public function show(SmartSecurity $security)
    {
        $security->load(['property', 'devices', 'alerts']);
        $systemStatus = $this->getSystemRealTimeStatus($security);
        $recentAlerts = $this->getSystemAlerts($security);

        return view('iot.security.show', compact(
            'security', 
            'systemStatus', 
            'recentAlerts'
        ));
    }

    public function edit(SmartSecurity $security)
    {
        $properties = SmartProperty::all();
        $systemTypes = ['cameras', 'sensors', 'smart_locks', 'alarms', 'monitoring'];
        $devices = IotDevice::where('device_type', 'security')->get();

        return view('iot.security.edit', compact(
            'security', 
            'properties', 
            'systemTypes', 
            'devices'
        ));
    }

    public function update(Request $request, SmartSecurity $security)
    {
        DB::beginTransaction();
        try {
            $securityData = $request->validate([
                'system_name' => 'required|string|max:255',
                'system_type' => 'required|in:cameras,sensors,smart_locks,alarms,monitoring',
                'configuration' => 'nullable|array',
                'sensors' => 'nullable|array',
                'alerts_enabled' => 'required|boolean',
                'is_armed' => 'required|boolean',
                'arming_schedule' => 'nullable|array',
                'notification_settings' => 'nullable|array',
                'status' => 'required|in:active,inactive,maintenance,error',
            ]);

            $securityData['updated_by'] = auth()->id();
            $securityData['security_metadata'] = $this->generateSecurityMetadata($request);

            $security->update($securityData);

            // Update linked devices
            if ($request->has('sensor_ids')) {
                $this->updateSecurityDevices($security, $request->sensor_ids);
            }

            // Update arming schedule
            if ($request->has('arming_schedule')) {
                $this->updateArmingSchedule($security, $request->arming_schedule);
            }

            // Update notifications
            if ($request->has('notification_settings')) {
                $this->updateNotifications($security, $request->notification_settings);
            }

            DB::commit();

            return redirect()
                ->route('smart-security.show', $security)
                ->with('success', 'تم تحديث نظام الأمن الذكي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث النظام: ' . $e->getMessage());
        }
    }

    public function destroy(SmartSecurity $security)
    {
        try {
            // Unlink devices
            $this->unlinkDevicesFromSecurity($security);

            // Delete security system
            $security->delete();

            return redirect()
                ->route('smart-security.index')
                ->with('success', 'تم حذف نظام الأمن الذكي بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف النظام: ' . $e->getMessage());
        }
    }

    public function armSystem(SmartSecurity $security)
    {
        try {
            $security->update([
                'is_armed' => true,
                'armed_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            // Create arm event
            $this->createSecurityEvent($security, 'system_armed', 'System armed successfully');

            return response()->json([
                'success' => true,
                'message' => 'تم تفعيل نظام الأمن بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function disarmSystem(SmartSecurity $security)
    {
        try {
            $security->update([
                'is_armed' => false,
                'disarmed_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            // Create disarm event
            $this->createSecurityEvent($security, 'system_disarmed', 'System disarmed successfully');

            return response()->json([
                'success' => true,
                'message' => 'تم إيقاف نظام الأمن بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSystemStatus(SmartSecurity $security)
    {
        try {
            $status = $this->getSystemRealTimeStatus($security);

            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function triggerAlert(SmartSecurity $security, Request $request)
    {
        try {
            $alertData = $request->validate([
                'alert_type' => 'required|string',
                'severity' => 'required|in:low,medium,high,critical',
                'message' => 'required|string',
                'sensor_data' => 'nullable|array',
            ]);

            $alert = $this->createSecurityAlert($security, $alertData);

            return response()->json([
                'success' => true,
                'alert' => $alert
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSecurityLogs(SmartSecurity $security)
    {
        try {
            $logs = $security->securityLogs()
                ->latest()
                ->paginate(50);

            return response()->json($logs);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateSecurityMetadata($request)
    {
        return [
            'security_level' => $this->calculateSecurityLevel($request),
            'coverage_area' => $this->calculateCoverageArea($request),
            'sensor_count' => count($request->sensor_ids ?? []),
            'response_time' => $this->estimateResponseTime($request),
            'detection_accuracy' => $this->calculateDetectionAccuracy($request),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function linkSecurityDevices($security, $sensorIds)
    {
        foreach ($sensorIds as $sensorId) {
            $security->devices()->attach($sensorId, [
                'role' => 'security_sensor',
                'created_at' => now(),
            ]);
        }
    }

    private function setupArmingSchedule($security, $schedule)
    {
        $security->update([
            'arming_schedule' => $schedule,
        ]);
    }

    private function configureNotifications($security, $notificationSettings)
    {
        $security->update([
            'notification_settings' => $notificationSettings,
        ]);
    }

    private function createSecurityEvent($security, $eventType, $message)
    {
        $security->securityLogs()->create([
            'event_type' => $eventType,
            'message' => $message,
            'timestamp' => now(),
            'created_by' => auth()->id(),
        ]);
    }

    private function createSecurityAlert($security, $alertData)
    {
        return IotAlert::create([
            'property_id' => $security->property_id,
            'security_system_id' => $security->id,
            'type' => 'security',
            'alert_type' => $alertData['alert_type'],
            'severity' => $alertData['severity'],
            'message' => $alertData['message'],
            'sensor_data' => $alertData['sensor_data'] ?? [],
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);
    }

    private function calculateSecurityLevel($request)
    {
        $score = 0;
        
        // System type contribution
        switch ($request->system_type) {
            case 'cameras':
                $score += 20;
                break;
            case 'sensors':
                $score += 15;
                break;
            case 'smart_locks':
                $score += 25;
                break;
            case 'alarms':
                $score += 20;
                break;
            case 'monitoring':
                $score += 20;
                break;
        }
        
        // Sensor count contribution
        $sensorCount = count($request->sensor_ids ?? []);
        $score += min(25, $sensorCount * 5);
        
        // Alerts enabled contribution
        if ($request->alerts_enabled) {
            $score += 15;
        }
        
        if ($score < 30) return 'basic';
        if ($score < 60) return 'intermediate';
        if ($score < 80) return 'advanced';
        return 'premium';
    }

    private function calculateCoverageArea($request)
    {
        $sensorCount = count($request->sensor_ids ?? []);
        
        // Estimate coverage based on sensor count
        if ($sensorCount < 3) return 'partial';
        if ($sensorCount < 6) return 'standard';
        if ($sensorCount < 10) return 'comprehensive';
        return 'complete';
    }

    private function estimateResponseTime($request)
    {
        $baseTime = 2; // 2 seconds base
        
        $sensorCount = count($request->sensor_ids ?? []);
        if ($sensorCount > 5) $baseTime += 0.5;
        
        return $baseTime;
    }

    private function calculateDetectionAccuracy($request)
    {
        $accuracy = 85; // Base 85%
        
        if ($request->system_type === 'cameras') $accuracy += 10;
        if ($request->system_type === 'sensors') $accuracy += 5;
        
        return min(99, $accuracy);
    }

    private function getSystemRealTimeStatus($security)
    {
        return [
            'system_id' => $security->id,
            'system_name' => $security->system_name,
            'is_armed' => $security->is_armed,
            'armed_at' => $security->armed_at,
            'device_count' => $security->devices()->count(),
            'active_devices' => $security->devices()->where('status', 'active')->count(),
            'last_alert' => $security->alerts()->latest()->first(),
            'system_health' => $this->getSystemHealth($security),
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function getSystemHealth($security)
    {
        $totalDevices = $security->devices()->count();
        $activeDevices = $security->devices()->where('status', 'active')->count();
        
        if ($totalDevices === 0) return 'unknown';
        
        $healthPercentage = ($activeDevices / $totalDevices) * 100;
        
        if ($healthPercentage >= 90) return 'excellent';
        if ($healthPercentage >= 75) return 'good';
        if ($healthPercentage >= 50) return 'fair';
        return 'poor';
    }

    private function updateSecurityDevices($security, $sensorIds)
    {
        $security->devices()->sync($sensorIds);
    }

    private function updateArmingSchedule($security, $schedule)
    {
        $security->update([
            'arming_schedule' => $schedule,
        ]);
    }

    private function updateNotifications($security, $notificationSettings)
    {
        $security->update([
            'notification_settings' => $notificationSettings,
        ]);
    }

    private function unlinkDevicesFromSecurity($security)
    {
        $security->devices()->detach();
    }

    private function getAverageSecurityScore()
    {
        return SmartSecurity::avg('security_score') ?? 0;
    }

    private function getRecentSecurityAlerts()
    {
        return IotAlert::where('type', 'security')
            ->where('status', 'active')
            ->latest()
            ->take(10)
            ->get();
    }

    private function getSecuritySystemStatus()
    {
        return [
            'total_systems' => SmartSecurity::count(),
            'armed_systems' => SmartSecurity::where('is_armed', true)->count(),
            'systems_by_type' => SmartSecurity::select('system_type', DB::raw('COUNT(*) as count'))
                ->groupBy('system_type')
                ->get(),
        ];
    }

    private function getSystemAlerts($security)
    {
        return $security->alerts()
            ->latest()
            ->take(20)
            ->get();
    }
}
