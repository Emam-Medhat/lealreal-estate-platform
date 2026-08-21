<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AgentAppointmentService
{
    protected $cacheTTL = 3600; // Cache for 1 hour

    /**
     * Get paginated appointments for an agent with filters and caching.
     */
    public function getAgentAppointments(Agent $agent, Request $request, int $perPage = 20)
    {
        $cacheKey = 'agent_appointments_'.$agent->id.'_'.md5(json_encode($request->all()).$perPage);

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent, $request, $perPage) {
            $appointments = $agent->appointments()
                ->with(['lead', 'property'])
                ->when($request->status, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->when($request->type, function ($query, $type) {
                    $query->where('appointment_type', $type);
                })
                ->when($request->date_range, function ($query, $range) {
                    switch ($range) {
                        case 'today':
                            $query->whereDate('start_datetime', today());
                            break;
                        case 'tomorrow':
                            $query->whereDate('start_datetime', today()->addDay());
                            break;
                        case 'this_week':
                            $query->whereBetween('start_datetime', [now()->startOfWeek(), now()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $query->whereMonth('start_datetime', now()->month)
                                ->whereYear('start_datetime', now()->year);
                            break;
                    }
                })
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhereHas('lead', function ($subQuery) use ($search) {
                                $subQuery->where('full_name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('property', function ($subQuery) use ($search) {
                                $subQuery->where('title', 'like', "%{$search}%");
                            });
                    });
                })
                ->orderBy('start_datetime', 'desc')
                ->paginate($perPage);

            return $appointments;
        });
    }

    /**
     * Get appointment statistics for an agent with caching.
     */
    public function getAppointmentStats(Agent $agent): array
    {
        $cacheKey = 'agent_appointment_stats_'.$agent->id;

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
            $today = today()->toDateString();
            $now = now()->toDateTimeString();

            // Bolt Optimization: Replace 6 sequential database queries with a single conditional aggregation query
            $stats = $agent->appointments()
                ->selectRaw("
                    COUNT(*) as total_appointments,
                    COUNT(CASE WHEN DATE(start_datetime) = ? THEN 1 END) as today_appointments,
                    COUNT(CASE WHEN start_datetime > ? THEN 1 END) as upcoming_appointments,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_appointments,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_appointments,
                    COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_appointments
                ", [$today, $now])
                ->first();

            return [
                'total_appointments' => (int) ($stats->total_appointments ?? 0),
                'today_appointments' => (int) ($stats->today_appointments ?? 0),
                'upcoming_appointments' => (int) ($stats->upcoming_appointments ?? 0),
                'completed_appointments' => (int) ($stats->completed_appointments ?? 0),
                'pending_appointments' => (int) ($stats->pending_appointments ?? 0),
                'confirmed_appointments' => (int) ($stats->confirmed_appointments ?? 0),
            ];
        });
    }

    /**
     * Invalidate cache for agent appointments and stats.
     */
    public function invalidateCache(int $agentId)
    {
        // Invalidate all relevant caches for this agent
        Cache::forget('agent_appointments_'.$agentId.'_*'); // Wildcard for all paginated results
        Cache::forget('agent_appointment_stats_'.$agentId);
    }
}
