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
            // Consolidate 6 individual count queries into a single SQL selectRaw aggregation query
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();

            $stats = $agent->leads()
                ->selectRaw("
                    COUNT(*) as total_leads,
                    COUNT(CASE WHEN created_at >= ? AND created_at <= ? THEN 1 END) as new_leads_this_month,
                    COUNT(CASE WHEN temperature >= 80 THEN 1 END) as hot_leads,
                    COUNT(CASE WHEN temperature >= 40 AND temperature <= 79 THEN 1 END) as warm_leads,
                    COUNT(CASE WHEN temperature < 40 THEN 1 END) as cold_leads,
                    COUNT(CASE WHEN lead_status = 'converted' THEN 1 END) as converted_leads
                ", [$startOfMonth, $endOfMonth])
                ->first();

            return [
                'total_leads' => (int) ($stats->total_leads ?? 0),
                'new_leads_this_month' => (int) ($stats->new_leads_this_month ?? 0),
                'hot_leads' => (int) ($stats->hot_leads ?? 0),
                'warm_leads' => (int) ($stats->warm_leads ?? 0),
                'cold_leads' => (int) ($stats->cold_leads ?? 0),
                'converted_leads' => (int) ($stats->converted_leads ?? 0),
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
