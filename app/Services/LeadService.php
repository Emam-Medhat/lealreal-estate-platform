<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadService
{
    /**
     * Get paginated leads with filtering and caching.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getLeads(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        // We don't cache the actual paginated results easily due to many filter combinations,
        // but we ensure eager loading to prevent N+1.
        return Lead::with(['source', 'status', 'assignedTo'])
            ->filter($filters)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get recent leads with eager loading and caching.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentLeads(int $limit = 10)
    {
        return Cache::remember("recent_leads_{$limit}", 300, function () use ($limit) {
            return Lead::with(['source', 'status', 'assignedTo'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get lead sources statistics with caching.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLeadSourcesStats(int $limit = 5)
    {
        return Cache::remember("lead_sources_stats_{$limit}", 600, function () use ($limit) {
            return LeadSource::withCount('leads')
                ->orderBy('leads_count', 'desc')
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get lead statuses statistics with caching.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLeadStatusesStats()
    {
        return Cache::remember('lead_statuses_stats', 600, function () {
            return LeadStatus::withCount('leads')
                ->orderBy('leads_count', 'desc')
                ->get();
        });
    }

    /**
     * Get lead dashboard statistics with caching.
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('lead_dashboard_stats', 300, function () {
            $totalLeads = Lead::count();
            $newStatus = LeadStatus::where('name', 'جديد')->first();
            $qualifiedStatus = LeadStatus::where('name', 'مؤهل')->first();

            return [
                'total_leads' => $totalLeads,
                'new_leads' => $newStatus ? Lead::where('status_id', $newStatus->id)->count() : 0,
                'qualified_leads' => $qualifiedStatus ? Lead::where('status_id', $qualifiedStatus->id)->count() : 0,
                'converted_leads' => Lead::whereNotNull('converted_date')->count(),
                'conversion_rate' => $totalLeads > 0 
                    ? (Lead::whereNotNull('converted_date')->count() / $totalLeads) * 100 
                    : 0,
            ];
        });
    }

    /**
     * Get active lead sources with caching.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveSources()
    {
        return Cache::remember('active_lead_sources', 3600, function () {
            return LeadSource::active()->get();
        });
    }

    /**
     * Get active lead statuses with caching.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveStatuses()
    {
        return Cache::remember('active_lead_statuses', 3600, function () {
            return LeadStatus::active()->orderBy('order')->get();
        });
    }

    /**
     * Get available agents/admins for assignment with caching.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableAgents()
    {
        return Cache::remember('available_agents_for_leads', 3600, function () {
            return User::whereIn('role', ['agent', 'admin'])->get(['id', 'name', 'email']);
        });
    }

    /**
     * Get pipeline data with eager loading and caching.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPipelineData()
    {
        return Cache::remember('lead_pipeline_data', 600, function () {
            return LeadStatus::with(['leads' => function ($query) {
                $query->with(['source', 'assignedTo'])
                    ->orderBy('priority', 'desc')
                    ->orderBy('created_at', 'desc');
            }])->orderBy('order')->get();
        });
    }

    /**
     * Clear lead-related caches.
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('lead_dashboard_stats');
        Cache::forget('active_lead_sources');
        Cache::forget('active_lead_statuses');
        Cache::forget('available_agents_for_leads');
        Cache::forget('lead_pipeline_data');
    }
}
