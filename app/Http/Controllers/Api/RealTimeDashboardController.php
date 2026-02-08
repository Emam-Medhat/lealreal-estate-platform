<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\IoTDevice;
use App\Models\Auth\UserSession;
use App\Models\SecurityIncident;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RealTimeDashboardController extends Controller
{
    /**
     * Get real-time overview statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = Cache::remember('dashboard_realtime_stats', 30, function () {
            return [
                'active_users' => UserSession::active()->count(),
                'online_devices' => IoTDevice::where('status', 'active')->count(),
                'critical_alerts' => SecurityIncident::where('status', 'open')->where('severity', 'critical')->count(),
                'today_events' => AuditLog::whereDate('created_at', today())->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get recent security feed.
     */
    public function securityFeed(): JsonResponse
    {
        $logs = AuditLog::with('user:id,first_name,last_name,avatar')
            ->whereIn('risk_level', ['high', 'critical'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'user' => $log->user ? $log->user->full_name : 'System',
                    'risk_level' => $log->risk_level,
                    'time' => $log->created_at->diffForHumans(),
                    'details' => json_decode($log->details),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Get IoT network status.
     */
    public function iotStatus(): JsonResponse
    {
        $status = Cache::remember('dashboard_iot_status', 60, function () {
            return IoTDevice::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');
        });

        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }
}
