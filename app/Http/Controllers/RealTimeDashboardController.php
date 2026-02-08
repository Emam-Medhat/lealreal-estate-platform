<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\IoTDevice;
use App\Models\EnergyMonitoringData;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RealTimeDashboardController extends Controller
{
    /**
     * Get real-time system stats.
     */
    public function stats()
    {
        $stats = Cache::remember('realtime_stats', 60, function () {
            return [
                'active_users' => 0, // Placeholder, needs session tracking
                'active_devices' => IoTDevice::where('status', 'active')->count(),
                'total_energy_today' => EnergyMonitoringData::whereDate('recorded_at', today())->sum('daily_usage_kwh'),
                'security_alerts' => AuditLog::highRisk()->whereDate('created_at', today())->count(),
                'system_health' => 98.5, // Mock value
                'last_updated' => now()->toIso8601String(),
            ];
        });

        return response()->json($stats);
    }

    /**
     * Get real-time security feed.
     */
    public function securityFeed()
    {
        $logs = AuditLog::with(['user:id,name', 'auditable'])
            ->whereIn('risk_level', ['high', 'critical'])
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'user' => $log->user ? $log->user->name : 'System',
                    'risk_level' => $log->risk_level,
                    'details' => $log->details,
                    'timestamp' => $log->created_at->toIso8601String(),
                    'ip_address' => $log->ip_address,
                ];
            });

        return response()->json($logs);
    }

    /**
     * Get real-time IoT device status.
     */
    public function iotStatus()
    {
        $devices = IoTDevice::select('id', 'device_type', 'status', 'battery_level', 'last_seen_at', 'location_within_property')
            ->where('status', '!=', 'inactive')
            ->orderBy('last_seen_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($devices);
    }
}
