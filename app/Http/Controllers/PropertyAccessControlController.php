<?php

namespace App\Http\Controllers;

use App\Models\PropertyAccessControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PropertyAccessControlController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $accessStats = [
            'total_properties' => PropertyAccessControl::where('user_id', $user->id)->count(),
            'active_controls' => PropertyAccessControl::where('user_id', $user->id)
                ->where('status', 'active')
                ->count(),
            'restricted_properties' => PropertyAccessControl::where('user_id', $user->id)
                ->where('access_level', 'restricted')
                ->count(),
            'recent_access_attempts' => PropertyAccessControl::where('user_id', $user->id)
                ->where('last_access_attempt', '>=', now()->subDays(7))
                ->count(),
        ];

        $accessControls = PropertyAccessControl::where('user_id', $user->id)
            ->with(['property', 'accessLogs' => function($query) {
                $query->orderBy('created_at', 'desc')->take(5);
            }])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('security.access-control.index', compact('accessStats', 'accessControls'));
    }

    public function create()
    {
        return view('security.access-control.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'access_level' => 'required|in:public,restricted,private,confidential',
            'access_permissions' => 'required|array',
            'access_permissions.*.user_id' => 'required|exists:users,id',
            'access_permissions.*.permission_level' => 'required|in:read,write,admin,full',
            'access_permissions.*.expires_at' => 'nullable|date|after:today',
            'time_restrictions' => 'nullable|array',
            'time_restrictions.start_time' => 'nullable|date_format:H:i',
            'time_restrictions.end_time' => 'nullable|date_format:H:i|after:time_restrictions.start_time',
            'time_restrictions.allowed_days' => 'nullable|array',
            'time_restrictions.allowed_days.*' => 'integer|min:1|max:7',
            'ip_restrictions' => 'nullable|array',
            'ip_restrictions.allowed_ips' => 'nullable|array',
            'ip_restrictions.allowed_ips.*' => 'ip',
            'ip_restrictions.blocked_ips' => 'nullable|array',
            'ip_restrictions.blocked_ips.*' => 'ip',
            'device_restrictions' => 'nullable|array',
            'device_restrictions.allowed_devices' => 'nullable|array',
            'device_restrictions.allowed_devices.*' => 'string|max:255',
            'device_restrictions.require_device_verification' => 'boolean',
            'location_restrictions' => 'nullable|array',
            'location_restrictions.allowed_locations' => 'nullable|array',
            'location_restrictions.allowed_locations.*' => 'string|max:255',
            'location_restrictions.max_distance' => 'nullable|numeric|min:0',
            'biometric_required' => 'boolean',
            'two_factor_required' => 'boolean',
            'session_timeout' => 'nullable|integer|min:5|max:1440',
            'concurrent_sessions' => 'nullable|integer|min:1|max:10',
            'emergency_access' => 'boolean',
            'emergency_contacts' => 'nullable|array',
            'emergency_contacts.*.name' => 'required|string|max:100',
            'emergency_contacts.*.phone' => 'required|string|max:20',
            'emergency_contacts.*.email' => 'required|email|max:255',
            'audit_frequency' => 'required|in:real_time,daily,weekly,monthly',
            'notification_settings' => 'nullable|array',
            'notification_settings.access_granted' => 'boolean',
            'notification_settings.access_denied' => 'boolean',
            'notification_settings.suspicious_activity' => 'boolean',
            'access_reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $accessControl = PropertyAccessControl::create([
            'user_id' => Auth::id(),
            'property_id' => $validated['property_id'],
            'access_level' => $validated['access_level'],
            'access_permissions' => json_encode($validated['access_permissions']),
            'time_restrictions' => json_encode($validated['time_restrictions'] ?? []),
            'ip_restrictions' => json_encode($validated['ip_restrictions'] ?? []),
            'device_restrictions' => json_encode($validated['device_restrictions'] ?? []),
            'location_restrictions' => json_encode($validated['location_restrictions'] ?? []),
            'biometric_required' => $validated['biometric_required'] ?? false,
            'two_factor_required' => $validated['two_factor_required'] ?? false,
            'session_timeout' => $validated['session_timeout'] ?? 60,
            'concurrent_sessions' => $validated['concurrent_sessions'] ?? 3,
            'emergency_access' => $validated['emergency_access'] ?? false,
            'emergency_contacts' => json_encode($validated['emergency_contacts'] ?? []),
            'audit_frequency' => $validated['audit_frequency'],
            'notification_settings' => json_encode($validated['notification_settings'] ?? []),
            'access_reason' => $validated['access_reason'],
            'notes' => $validated['notes'],
            'status' => 'active',
            'access_code' => $this->generateAccessCode(),
            'qr_code' => $this->generateQRCode(),
        ]);

        // Log the access control setup
        Log::info('Property access control setup completed', [
            'user_id' => Auth::id(),
            'property_id' => $validated['property_id'],
            'access_control_id' => $accessControl->id,
            'access_level' => $validated['access_level'],
        ]);

        return redirect()->route('security.access-control.show', $accessControl)
            ->with('success', 'تم إعداد التحكم في الوصول بنجاح');
    }

    public function show(PropertyAccessControl $accessControl)
    {
        $this->authorize('view', $accessControl);
        
        $accessControl->load(['property', 'accessLogs' => function($query) {
            $query->orderBy('created_at', 'desc')->take(20);
        }]);

        return view('security.access-control.show', compact('accessControl'));
    }

    public function edit(PropertyAccessControl $accessControl)
    {
        $this->authorize('update', $accessControl);
        
        return view('security.access-control.edit', compact('accessControl'));
    }

    public function update(Request $request, PropertyAccessControl $accessControl)
    {
        $this->authorize('update', $accessControl);

        $validated = $request->validate([
            'access_level' => 'required|in:public,restricted,private,confidential',
            'access_permissions' => 'required|array',
            'access_permissions.*.user_id' => 'required|exists:users,id',
            'access_permissions.*.permission_level' => 'required|in:read,write,admin,full',
            'access_permissions.*.expires_at' => 'nullable|date|after:today',
            'time_restrictions' => 'nullable|array',
            'ip_restrictions' => 'nullable|array',
            'device_restrictions' => 'nullable|array',
            'location_restrictions' => 'nullable|array',
            'biometric_required' => 'boolean',
            'two_factor_required' => 'boolean',
            'session_timeout' => 'nullable|integer|min:5|max:1440',
            'concurrent_sessions' => 'nullable|integer|min:1|max:10',
            'emergency_access' => 'boolean',
            'emergency_contacts' => 'nullable|array',
            'audit_frequency' => 'required|in:real_time,daily,weekly,monthly',
            'notification_settings' => 'nullable|array',
            'access_reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $accessControl->update([
            'access_level' => $validated['access_level'],
            'access_permissions' => json_encode($validated['access_permissions']),
            'time_restrictions' => json_encode($validated['time_restrictions'] ?? []),
            'ip_restrictions' => json_encode($validated['ip_restrictions'] ?? []),
            'device_restrictions' => json_encode($validated['device_restrictions'] ?? []),
            'location_restrictions' => json_encode($validated['location_restrictions'] ?? []),
            'biometric_required' => $validated['biometric_required'] ?? $accessControl->biometric_required,
            'two_factor_required' => $validated['two_factor_required'] ?? $accessControl->two_factor_required,
            'session_timeout' => $validated['session_timeout'] ?? $accessControl->session_timeout,
            'concurrent_sessions' => $validated['concurrent_sessions'] ?? $accessControl->concurrent_sessions,
            'emergency_access' => $validated['emergency_access'] ?? $accessControl->emergency_access,
            'emergency_contacts' => json_encode($validated['emergency_contacts'] ?? []),
            'audit_frequency' => $validated['audit_frequency'],
            'notification_settings' => json_encode($validated['notification_settings'] ?? []),
            'access_reason' => $validated['access_reason'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('security.access-control.show', $accessControl)
            ->with('success', 'تم تحديث إعدادات التحكم في الوصول بنجاح');
    }

    public function grantAccess(Request $request, PropertyAccessControl $accessControl)
    {
        $this->authorize('manage', $accessControl);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission_level' => 'required|in:read,write,admin,full',
            'expires_at' => 'nullable|date|after:today',
            'access_reason' => 'required|string|max:500',
            'temporary_access' => 'boolean',
            'access_duration' => 'nullable|integer|min:1|max:720', // minutes
        ]);

        $permissions = json_decode($accessControl->access_permissions, true) ?? [];
        
        $newPermission = [
            'user_id' => $validated['user_id'],
            'permission_level' => $validated['permission_level'],
            'expires_at' => $validated['expires_at'],
            'access_reason' => $validated['access_reason'],
            'temporary_access' => $validated['temporary_access'] ?? false,
            'access_duration' => $validated['access_duration'] ?? null,
            'granted_at' => now(),
            'granted_by' => Auth::id(),
        ];

        $permissions[] = $newPermission;
        $accessControl->update(['access_permissions' => json_encode($permissions)]);

        // Log the access grant
        $this->logAccessAttempt($accessControl, 'access_granted', $validated['user_id'], true);

        return redirect()->route('security.access-control.show', $accessControl)
            ->with('success', 'تم منح الوصول بنجاح');
    }

    public function revokeAccess(Request $request, PropertyAccessControl $accessControl)
    {
        $this->authorize('manage', $accessControl);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'revocation_reason' => 'required|string|max:500',
        ]);

        $permissions = json_decode($accessControl->access_permissions, true) ?? [];
        
        // Remove permission for the specified user
        $permissions = array_filter($permissions, function($permission) use ($validated) {
            return $permission['user_id'] != $validated['user_id'];
        });

        $accessControl->update(['access_permissions' => json_encode(array_values($permissions))]);

        // Log the access revocation
        $this->logAccessAttempt($accessControl, 'access_revoked', $validated['user_id'], true);

        return redirect()->route('security.access-control.show', $accessControl)
            ->with('success', 'تم سحب الوصول بنجاح');
    }

    public function checkAccess(Request $request, PropertyAccessControl $accessControl)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'access_type' => 'required|in:property_view,property_edit,document_access,data_access',
            'ip_address' => 'required|ip',
            'device_info' => 'nullable|array',
            'location_data' => 'nullable|array',
        ]);

        $accessResult = $this->evaluateAccess($accessControl, $validated);

        // Log the access attempt
        $this->logAccessAttempt($accessControl, 'access_checked', $validated['user_id'], $accessResult['granted']);

        return response()->json($accessResult);
    }

    public function emergencyAccess(Request $request, PropertyAccessControl $accessControl)
    {
        $this->authorize('emergency', $accessControl);

        $validated = $request->validate([
            'emergency_reason' => 'required|string|max:500',
            'contact_method' => 'required|in:phone,email,sms',
            'emergency_duration' => 'required|integer|min:1|max:168', // hours
        ]);

        // Generate emergency access code
        $emergencyCode = $this->generateEmergencyCode();

        $accessControl->update([
            'emergency_access_code' => $emergencyCode,
            'emergency_access_expires' => now()->addHours($validated['emergency_duration']),
            'emergency_reason' => $validated['emergency_reason'],
            'emergency_granted_by' => Auth::id(),
        ]);

        // Notify emergency contacts
        $this->notifyEmergencyContacts($accessControl, $validated);

        return response()->json([
            'success' => true,
            'emergency_code' => $emergencyCode,
            'expires_at' => $accessControl->emergency_access_expires,
        ]);
    }

    public function auditLog(PropertyAccessControl $accessControl)
    {
        $this->authorize('view', $accessControl);

        $accessLogs = $accessControl->accessLogs()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('security.access-control.audit', compact('accessControl', 'accessLogs'));
    }

    public function analytics(PropertyAccessControl $accessControl)
    {
        $this->authorize('view', $accessControl);

        $analytics = [
            'access_patterns' => $this->getAccessPatterns($accessControl),
            'user_activity' => $this->getUserActivity($accessControl),
            'security_incidents' => $this->getSecurityIncidents($accessControl),
            'compliance_metrics' => $this->getComplianceMetrics($accessControl),
        ];

        return view('security.access-control.analytics', compact('accessControl', 'analytics'));
    }

    private function generateAccessCode()
    {
        return 'ACC-' . Str::upper(Str::random(8)) . '-' . date('Ymd');
    }

    private function generateQRCode()
    {
        // Generate QR code for access
        return 'QR-' . Str::upper(Str::random(12));
    }

    private function generateEmergencyCode()
    {
        return 'EMG-' . Str::upper(Str::random(6)) . '-' . time();
    }

    private function evaluateAccess(PropertyAccessControl $accessControl, $requestData)
    {
        $result = [
            'granted' => false,
            'reason' => '',
            'access_level' => null,
            'expires_at' => null,
            'additional_requirements' => [],
        ];

        // Check if user has permission
        $permissions = json_decode($accessControl->access_permissions, true) ?? [];
        $userPermission = collect($permissions)->firstWhere('user_id', $requestData['user_id']);

        if (!$userPermission) {
            $result['reason'] = 'المستخدم ليس لديه صلاحية الوصول';
            return $result;
        }

        // Check if permission has expired
        if ($userPermission['expires_at'] && now()->isAfter($userPermission['expires_at'])) {
            $result['reason'] = 'انتهت صلاحية الوصول';
            return $result;
        }

        // Check time restrictions
        if ($accessControl->time_restrictions) {
            $timeCheck = $this->checkTimeRestrictions($accessControl, $requestData);
            if (!$timeCheck['passed']) {
                $result['reason'] = $timeCheck['reason'];
                return $result;
            }
        }

        // Check IP restrictions
        if ($accessControl->ip_restrictions) {
            $ipCheck = $this->checkIPRestrictions($accessControl, $requestData);
            if (!$ipCheck['passed']) {
                $result['reason'] = $ipCheck['reason'];
                return $result;
            }
        }

        // Check device restrictions
        if ($accessControl->device_restrictions) {
            $deviceCheck = $this->checkDeviceRestrictions($accessControl, $requestData);
            if (!$deviceCheck['passed']) {
                $result['reason'] = $deviceCheck['reason'];
                return $result;
            }
        }

        // Check location restrictions
        if ($accessControl->location_restrictions) {
            $locationCheck = $this->checkLocationRestrictions($accessControl, $requestData);
            if (!$locationCheck['passed']) {
                $result['reason'] = $locationCheck['reason'];
                return $result;
            }
        }

        // All checks passed
        $result['granted'] = true;
        $result['access_level'] = $userPermission['permission_level'];
        $result['expires_at'] = $userPermission['expires_at'];

        // Add additional requirements
        if ($accessControl->biometric_required) {
            $result['additional_requirements'][] = 'biometric_verification';
        }
        if ($accessControl->two_factor_required) {
            $result['additional_requirements'][] = 'two_factor_authentication';
        }

        return $result;
    }

    private function checkTimeRestrictions(PropertyAccessControl $accessControl, $requestData)
    {
        $timeRestrictions = json_decode($accessControl->time_restrictions, true);
        
        if (!$timeRestrictions) {
            return ['passed' => true];
        }

        $currentTime = now();
        
        // Check time range
        if (isset($timeRestrictions['start_time']) && isset($timeRestrictions['end_time'])) {
            $startTime = now()->setTimeFromTimeString($timeRestrictions['start_time']);
            $endTime = now()->setTimeFromTimeString($timeRestrictions['end_time']);
            
            if (!$currentTime->between($startTime, $endTime)) {
                return ['passed' => false, 'reason' => 'الوصول مسموح فقط في الفترة الزمنية المحددة'];
            }
        }

        // Check allowed days
        if (isset($timeRestrictions['allowed_days'])) {
            $currentDay = $currentTime->dayOfWeek;
            if (!in_array($currentDay, $timeRestrictions['allowed_days'])) {
                return ['passed' => false, 'reason' => 'الوصول غير مسموح في هذا اليوم'];
            }
        }

        return ['passed' => true];
    }

    private function checkIPRestrictions(PropertyAccessControl $accessControl, $requestData)
    {
        $ipRestrictions = json_decode($accessControl->ip_restrictions, true);
        
        if (!$ipRestrictions) {
            return ['passed' => true];
        }

        $clientIP = $requestData['ip_address'];

        // Check blocked IPs
        if (isset($ipRestrictions['blocked_ips']) && in_array($clientIP, $ipRestrictions['blocked_ips'])) {
            return ['passed' => false, 'reason' => 'IP محظور'];
        }

        // Check allowed IPs
        if (isset($ipRestrictions['allowed_ips']) && !empty($ipRestrictions['allowed_ips'])) {
            if (!in_array($clientIP, $ipRestrictions['allowed_ips'])) {
                return ['passed' => false, 'reason' => 'IP غير مصرح به'];
            }
        }

        return ['passed' => true];
    }

    private function checkDeviceRestrictions(PropertyAccessControl $accessControl, $requestData)
    {
        $deviceRestrictions = json_decode($accessControl->device_restrictions, true);
        
        if (!$deviceRestrictions) {
            return ['passed' => true];
        }

        $deviceInfo = $requestData['device_info'] ?? [];

        // Check allowed devices
        if (isset($deviceRestrictions['allowed_devices']) && !empty($deviceRestrictions['allowed_devices'])) {
            $deviceFingerprint = $deviceInfo['fingerprint'] ?? '';
            if (!in_array($deviceFingerprint, $deviceRestrictions['allowed_devices'])) {
                return ['passed' => false, 'reason' => 'جهاز غير مصرح به'];
            }
        }

        return ['passed' => true];
    }

    private function checkLocationRestrictions(PropertyAccessControl $accessControl, $requestData)
    {
        $locationRestrictions = json_decode($accessControl->location_restrictions, true);
        
        if (!$locationRestrictions) {
            return ['passed' => true];
        }

        $locationData = $requestData['location_data'] ?? [];

        // Check allowed locations
        if (isset($locationRestrictions['allowed_locations']) && !empty($locationRestrictions['allowed_locations'])) {
            $currentLocation = $locationData['location'] ?? '';
            if (!in_array($currentLocation, $locationRestrictions['allowed_locations'])) {
                return ['passed' => false, 'reason' => 'موقع غير مصرح به'];
            }
        }

        // Check distance restriction
        if (isset($locationRestrictions['max_distance']) && isset($locationData['distance'])) {
            if ($locationData['distance'] > $locationRestrictions['max_distance']) {
                return ['passed' => false, 'reason' => 'الموقع بعيد جداً'];
            }
        }

        return ['passed' => true];
    }

    private function logAccessAttempt(PropertyAccessControl $accessControl, $action, $userId, $success)
    {
        $accessControl->accessLogs()->create([
            'user_id' => $userId,
            'action' => $action,
            'success' => $success,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'location_data' => json_encode(request()->header('X-Location') ?? []),
            'device_info' => json_encode(request()->header('X-Device-Info') ?? []),
        ]);
    }

    private function notifyEmergencyContacts(PropertyAccessControl $accessControl, $requestData)
    {
        $emergencyContacts = json_decode($accessControl->emergency_contacts, true) ?? [];
        
        foreach ($emergencyContacts as $contact) {
            // Send notification based on contact method
            // Implementation would depend on notification system
        }
    }

    private function getAccessPatterns(PropertyAccessControl $accessControl)
    {
        return $accessControl->accessLogs()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getUserActivity(PropertyAccessControl $accessControl)
    {
        return $accessControl->accessLogs()
            ->with(['user'])
            ->selectRaw('user_id, COUNT(*) as access_count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('user_id')
            ->orderBy('access_count', 'desc')
            ->take(10)
            ->get();
    }

    private function getSecurityIncidents(PropertyAccessControl $accessControl)
    {
        return $accessControl->accessLogs()
            ->where('success', false)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
    }

    private function getComplianceMetrics(PropertyAccessControl $accessControl)
    {
        return [
            'total_access_attempts' => $accessControl->accessLogs()->count(),
            'successful_access' => $accessControl->accessLogs()->where('success', true)->count(),
            'failed_access' => $accessControl->accessLogs()->where('success', false)->count(),
            'compliance_rate' => $this->calculateComplianceRate($accessControl),
        ];
    }

    private function calculateComplianceRate(PropertyAccessControl $accessControl)
    {
        $total = $accessControl->accessLogs()->count();
        $successful = $accessControl->accessLogs()->where('success', true)->count();
        
        return $total > 0 ? ($successful / $total) * 100 : 0;
    }
}
