<?php

namespace App\Http\Controllers;

use App\Models\SmartLock;
use App\Models\SmartProperty;
use App\Models\IotDevice;
use App\Models\IotAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SmartLockController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_locks' => SmartLock::count(),
            'active_locks' => SmartLock::where('status', 'active')->count(),
            'locked_doors' => SmartLock::where('is_locked', true)->count(),
            'unlocked_doors' => SmartLock::where('is_locked', false)->count(),
            'battery_status' => $this->getBatteryStatusOverview(),
            'access_logs_today' => $this->getTodayAccessLogs(),
        ];

        $recentLocks = SmartLock::with(['property', 'device'])
            ->latest()
            ->take(10)
            ->get();

        $accessActivity = $this->getRecentAccessActivity();
        $lockStatus = $this->getLockSystemStatus();

        return view('iot.smart-lock.dashboard', compact(
            'stats', 
            'recentLocks', 
            'accessActivity', 
            'lockStatus'
        ));
    }

    public function index(Request $request)
    {
        $query = SmartLock::with(['property', 'device']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('lock_type')) {
            $query->where('lock_type', $request->lock_type);
        }

        if ($request->filled('is_locked')) {
            $query->where('is_locked', $request->is_locked);
        }

        $locks = $query->latest()->paginate(12);

        $lockTypes = ['deadbolt', 'smart_lock', 'padlock', 'keypad', 'biometric'];
        $statuses = ['active', 'inactive', 'maintenance', 'error'];

        return view('iot.smart-locks.index', compact(
            'locks', 
            'lockTypes', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();
        $lockTypes = ['deadbolt', 'smart_lock', 'padlock', 'keypad', 'biometric'];
        $devices = IotDevice::where('device_type', 'lock')->get();

        return view('iot.smart-locks.create', compact(
            'properties', 
            'lockTypes', 
            'devices'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $lockData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'device_id' => 'required|exists:iot_devices,id',
                'lock_name' => 'required|string|max:255',
                'lock_type' => 'required|in:deadbolt,smart_lock,padlock,keypad,biometric',
                'location' => 'required|string|max:255',
                'access_methods' => 'nullable|array',
                'auto_lock_timeout' => 'nullable|integer|min:0|max:300',
                'access_schedule' => 'nullable|array',
                'emergency_codes' => 'nullable|array',
                'status' => 'required|in:active,inactive,maintenance,error',
            ]);

            $lockData['created_by'] = auth()->id();
            $lockData['lock_metadata'] = $this->generateLockMetadata($request);

            $lock = SmartLock::create($lockData);

            // Set up access methods
            if ($request->has('access_methods')) {
                $this->setupAccessMethods($lock, $request->access_methods);
            }

            // Set up access schedule
            if ($request->has('access_schedule')) {
                $this->setupAccessSchedule($lock, $request->access_schedule);
            }

            // Configure emergency codes
            if ($request->has('emergency_codes')) {
                $this->configureEmergencyCodes($lock, $request->emergency_codes);
            }

            DB::commit();

            return redirect()
                ->route('smart-lock.show', $lock)
                ->with('success', 'تم تسجيل القفل الذكي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تسجيل القفل: ' . $e->getMessage());
        }
    }

    public function show(SmartLock $lock)
    {
        $lock->load(['property', 'device', 'accessLogs']);
        $currentStatus = $this->getLockRealTimeStatus($lock);
        $recentAccess = $this->getRecentAccessLogs($lock);

        return view('iot.smart-locks.show', compact(
            'lock', 
            'currentStatus', 
            'recentAccess'
        ));
    }

    public function edit(SmartLock $lock)
    {
        $properties = SmartProperty::all();
        $lockTypes = ['deadbolt', 'smart_lock', 'padlock', 'keypad', 'biometric'];
        $devices = IotDevice::where('device_type', 'lock')->get();

        return view('iot.smart-locks.edit', compact(
            'lock', 
            'properties', 
            'lockTypes', 
            'devices'
        ));
    }

    public function update(Request $request, SmartLock $lock)
    {
        DB::beginTransaction();
        try {
            $lockData = $request->validate([
                'lock_name' => 'required|string|max:255',
                'lock_type' => 'required|in:deadbolt,smart_lock,padlock,keypad,biometric',
                'location' => 'required|string|max:255',
                'access_methods' => 'nullable|array',
                'auto_lock_timeout' => 'nullable|integer|min:0|max:300',
                'access_schedule' => 'nullable|array',
                'emergency_codes' => 'nullable|array',
                'status' => 'required|in:active,inactive,maintenance,error',
            ]);

            $lockData['updated_by'] = auth()->id();
            $lockData['lock_metadata'] = $this->generateLockMetadata($request);

            $lock->update($lockData);

            // Update access methods
            if ($request->has('access_methods')) {
                $this->updateAccessMethods($lock, $request->access_methods);
            }

            // Update access schedule
            if ($request->has('access_schedule')) {
                $this->updateAccessSchedule($lock, $request->access_schedule);
            }

            // Update emergency codes
            if ($request->has('emergency_codes')) {
                $this->updateEmergencyCodes($lock, $request->emergency_codes);
            }

            DB::commit();

            return redirect()
                ->route('smart-lock.show', $lock)
                ->with('success', 'تم تحديث القفل الذكي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث القفل: ' . $e->getMessage());
        }
    }

    public function destroy(SmartLock $lock)
    {
        try {
            // Delete access logs
            $this->deleteAccessLogs($lock);

            // Delete lock
            $lock->delete();

            return redirect()
                ->route('smart-lock.index')
                ->with('success', 'تم حذف القفل الذكي بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف القفل: ' . $e->getMessage());
        }
    }

    public function lockDoor(SmartLock $lock, Request $request)
    {
        try {
            $result = $this->executeLockCommand($lock, 'lock');

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function unlockDoor(SmartLock $lock, Request $request)
    {
        try {
            $result = $this->executeLockCommand($lock, 'unlock');

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function grantAccess(SmartLock $lock, Request $request)
    {
        try {
            $accessData = $request->validate([
                'access_method' => 'required|string',
                'access_code' => 'nullable|string',
                'biometric_data' => 'nullable|string',
                'user_id' => 'nullable|exists:users,id',
                'temporary_access' => 'required|boolean',
                'expiry_time' => 'nullable|date',
            ]);

            $result = $this->grantTemporaryAccess($lock, $accessData);

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function revokeAccess(SmartLock $lock, Request $request)
    {
        try {
            $revokeData = $request->validate([
                'access_id' => 'required|integer',
                'reason' => 'nullable|string',
            ]);

            $result = $this->performRevokeAccess($lock, $revokeData);

            return response()->json([
                'success' => true,
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getLockStatus(SmartLock $lock)
    {
        try {
            $status = $this->getLockRealTimeStatus($lock);

            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAccessLogs(SmartLock $lock)
    {
        try {
            $logs = $lock->accessLogs()
                ->with('user')
                ->latest()
                ->paginate(50);

            return response()->json($logs);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateLockMetadata($request)
    {
        return [
            'security_level' => $this->calculateSecurityLevel($request),
            'access_method_count' => count($request->access_methods ?? []),
            'auto_lock_enabled' => $request->auto_lock_timeout > 0,
            'schedule_enabled' => $request->has('access_schedule'),
            'emergency_codes_count' => count($request->emergency_codes ?? []),
            'battery_life_estimate' => $this->estimateBatteryLife($request),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function setupAccessMethods($lock, $accessMethods)
    {
        $lock->update([
            'access_methods' => $accessMethods,
        ]);
    }

    private function setupAccessSchedule($lock, $schedule)
    {
        $lock->update([
            'access_schedule' => $schedule,
        ]);
    }

    private function configureEmergencyCodes($lock, $emergencyCodes)
    {
        $lock->update([
            'emergency_codes' => $emergencyCodes,
        ]);
    }

    private function executeLockCommand($lock, $command)
    {
        $newStatus = $command === 'lock';
        
        $lock->update([
            'is_locked' => $newStatus,
            'last_action_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        // Create access log
        $this->createAccessLog($lock, $command, 'success');

        return [
            'command_id' => uniqid('cmd_'),
            'status' => 'executed',
            'action' => $command,
            'locked' => $newStatus,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function grantTemporaryAccess($lock, $accessData)
    {
        // Create temporary access credential
        $accessId = $lock->temporaryAccess()->create([
            'access_method' => $accessData['access_method'],
            'access_code' => $accessData['access_code'] ?? null,
            'biometric_data' => $accessData['biometric_data'] ?? null,
            'user_id' => $accessData['user_id'] ?? null,
            'is_active' => true,
            'expires_at' => $accessData['expiry_time'],
            'created_by' => auth()->id(),
        ]);

        // Create access log
        $this->createAccessLog($lock, 'access_granted', 'success', $accessData);

        return [
            'access_id' => $accessId->id,
            'status' => 'granted',
            'expires_at' => $accessData['expiry_time'],
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function performRevokeAccess($lock, $revokeData)
    {
        // Deactivate temporary access
        $access = $lock->temporaryAccess()
            ->where('id', $revokeData['access_id'])
            ->first();

        if ($access) {
            $access->update([
                'is_active' => false,
                'revoked_at' => now(),
                'revoke_reason' => $revokeData['reason'],
            ]);

            // Create access log
            $this->createAccessLog($lock, 'access_revoked', 'success', $revokeData);
        }

        return [
            'access_id' => $revokeData['access_id'],
            'status' => 'revoked',
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function createAccessLog($lock, $action, $status, $data = [])
    {
        $lock->accessLogs()->create([
            'action' => $action,
            'status' => $status,
            'user_id' => auth()->id(),
            'access_data' => $data,
            'timestamp' => now(),
        ]);
    }

    private function calculateSecurityLevel($request)
    {
        $score = 0;
        
        // Lock type contribution
        switch ($request->lock_type) {
            case 'biometric':
                $score += 40;
                break;
            case 'smart_lock':
                $score += 30;
                break;
            case 'keypad':
                $score += 25;
                break;
            case 'deadbolt':
                $score += 20;
                break;
            case 'padlock':
                $score += 15;
                break;
        }
        
        // Access methods contribution
        $methodCount = count($request->access_methods ?? []);
        $score += min(30, $methodCount * 10);
        
        // Auto-lock contribution
        if ($request->auto_lock_timeout > 0) {
            $score += 15;
        }
        
        // Schedule contribution
        if ($request->has('access_schedule')) {
            $score += 15;
        }
        
        if ($score < 30) return 'basic';
        if ($score < 60) return 'intermediate';
        if ($score < 80) return 'advanced';
        return 'premium';
    }

    private function estimateBatteryLife($request)
    {
        $baseLife = 365; // 1 year base in days
        
        if ($request->lock_type === 'biometric') $baseLife -= 60;
        if ($request->auto_lock_timeout > 0) $baseLife -= 30;
        
        return $baseLife;
    }

    private function getLockRealTimeStatus($lock)
    {
        return [
            'lock_id' => $lock->id,
            'lock_name' => $lock->lock_name,
            'is_locked' => $lock->is_locked,
            'last_action_at' => $lock->last_action_at,
            'battery_level' => $this->getBatteryLevel($lock),
            'signal_strength' => $this->getSignalStrength($lock),
            'active_access' => $this->getActiveAccessCount($lock),
            'device_status' => $lock->device->status ?? 'unknown',
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function getBatteryLevel($lock)
    {
        // Get battery level from device
        return rand(20, 100); // Placeholder
    }

    private function getSignalStrength($lock)
    {
        // Get signal strength from device
        return rand(1, 5); // 1-5 scale
    }

    private function getActiveAccessCount($lock)
    {
        return $lock->temporaryAccess()
            ->where('is_active', true)
            ->count();
    }

    private function getRecentAccessLogs($lock)
    {
        return $lock->accessLogs()
            ->with('user')
            ->latest()
            ->take(20)
            ->get();
    }

    private function updateAccessMethods($lock, $accessMethods)
    {
        $lock->update([
            'access_methods' => $accessMethods,
        ]);
    }

    private function updateAccessSchedule($lock, $schedule)
    {
        $lock->update([
            'access_schedule' => $schedule,
        ]);
    }

    private function updateEmergencyCodes($lock, $emergencyCodes)
    {
        $lock->update([
            'emergency_codes' => $emergencyCodes,
        ]);
    }

    private function deleteAccessLogs($lock)
    {
        foreach ($lock->accessLogs as $log) {
            $log->delete();
        }
    }

    private function getBatteryStatusOverview()
    {
        return [
            'excellent' => SmartLock::whereJsonContains('lock_metadata->battery_level', '>=', 75)->count(),
            'good' => SmartLock::whereJsonContains('lock_metadata->battery_level', '>=', 50)->count(),
            'low' => SmartLock::whereJsonContains('lock_metadata->battery_level', '<', 25)->count(),
            'critical' => SmartLock::whereJsonContains('lock_metadata->battery_level', '<', 10)->count(),
        ];
    }

    private function getTodayAccessLogs()
    {
        return SmartLock::whereHas('accessLogs', function ($query) {
            $query->whereDate('timestamp', today());
        })->count();
    }

    private function getRecentAccessActivity()
    {
        return [
            'successful_access' => SmartLock::whereHas('accessLogs', function ($query) {
                $query->where('action', 'access_granted')->where('status', 'success');
            })->count(),
            'failed_access' => SmartLock::whereHas('accessLogs', function ($query) {
                $query->where('status', 'failed');
            })->count(),
            'temporary_active' => SmartLock::whereHas('temporaryAccess', function ($query) {
                $query->where('is_active', true);
            })->count(),
        ];
    }

    private function getLockSystemStatus()
    {
        return [
            'total_locks' => SmartLock::count(),
            'locked_doors' => SmartLock::where('is_locked', true)->count(),
            'unlocked_doors' => SmartLock::where('is_locked', false)->count(),
            'locks_by_type' => SmartLock::select('lock_type', DB::raw('COUNT(*) as count'))
                ->groupBy('lock_type')
                ->get(),
        ];
    }
}
