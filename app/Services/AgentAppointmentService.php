<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class AgentAppointmentService
{
    protected $cacheTTL = 3600; // Cache for 1 hour

    /**
     * Get paginated appointments for an agent with filters and caching.
     */
    public function getAgentAppointments(Agent $agent, Request $request, int $perPage = 20)
    {
        $cacheKey = 'agent_appointments_' . $agent->id . '_' . md5(json_encode($request->all()) . $perPage);

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
        $cacheKey = 'agent_appointment_stats_' . $agent->id;

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
            return [
                'total_appointments' => $agent->appointments()->count(),
                'today_appointments' => $agent->appointments()->whereDate('start_datetime', today())->count(),
                'upcoming_appointments' => $agent->appointments()->where('start_datetime', '>', now())->count(),
                'completed_appointments' => $agent->appointments()->where('status', 'completed')->count(),
                'pending_appointments' => $agent->appointments()->where('status', 'pending')->count(),
                'confirmed_appointments' => $agent->appointments()->where('status', 'confirmed')->count(),
            ];
        });
    }

    /**
     * Invalidate cache for agent appointments and stats.
     */
    public function invalidateCache(int $agentId)
    {
        // Invalidate all relevant caches for this agent
        Cache::forget('agent_appointments_' . $agentId . '_*'); // Wildcard for all paginated results
        Cache::forget('agent_appointment_stats_' . $agentId);
    }
}
