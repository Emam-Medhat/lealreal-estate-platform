<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class AgentCrmService
{
    protected $cacheTTL = 3600; // Cache for 1 hour

    /**
     * Get paginated leads for an agent with filters and caching.
     */
    public function getAgentLeads(Agent $agent, Request $request, int $perPage = 20)
    {
        $cacheKey = 'agent_leads_' . $agent->id . '_' . md5(json_encode($request->all()) . $perPage);

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent, $request, $perPage) {
            $leads = Lead::where('agent_id', $agent->id)
                ->when($request->boolean('only_leads'), function ($query) {
                    $query->where('lead_type', 'buyer');
                })
                ->when($request->status, function ($query, $status) {
                    $query->where('lead_status', $status);
                })
                ->when($request->type, function ($query, $type) {
                    $query->where('lead_type', $type);
                })
                ->when($request->priority, function ($query, $priority) {
                    $query->where('priority', $priority);
                })
                ->when($request->temperature, function ($query, $temperature) {
                    if ($temperature === 'hot') {
                        $query->where('temperature', '>=', 80);
                    } elseif ($temperature === 'warm') {
                        $query->whereBetween('temperature', [40, 79]);
                    } elseif ($temperature === 'cold') {
                        $query->where('temperature', '<', 40);
                    }
                })
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate($perPage);

            return $leads;
        });
    }

    /**
     * Get CRM statistics for an agent with caching.
     */
    public function getCrmStats(Agent $agent): array
    {
        $cacheKey = 'agent_crm_stats_' . $agent->id;

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
            return [
                'total_leads' => $agent->leads()->count(),
                'new_leads_this_month' => $agent->leads()->whereMonth('created_at', now()->month)->count(),
                'hot_leads' => $agent->leads()->where('temperature', '>=', 80)->count(),
                'warm_leads' => $agent->leads()->whereBetween('temperature', [40, 79])->count(),
                'cold_leads' => $agent->leads()->where('temperature', '<', 40)->count(),
                'converted_leads' => $agent->leads()->where('lead_status', 'converted')->count(),
            ];
        });
    }

    /**
     * Invalidate cache for agent leads and CRM stats.
     */
    public function invalidateCache(int $agentId)
    {
        // Invalidate all relevant caches for this agent
        Cache::forget('agent_leads_' . $agentId . '_*'); // Wildcard for all paginated results
        Cache::forget('agent_crm_stats_' . $agentId);
    }
}
