<?php

namespace App\Services;

use App\Models\Marketing\VirtualOpenHouseMarketing;
use Illuminate\Support\Facades\Cache;

class MarketingService
{
    /**
     * Get paginated virtual open house campaigns with caching.
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getVirtualOpenHouseCampaigns(int $perPage = 10)
    {
        // Pagination is hard to cache effectively, but we ensure eager loading.
        return VirtualOpenHouseMarketing::with(['property'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get active properties for marketing campaigns with caching.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveProperties()
    {
        return Cache::remember('active_properties_for_marketing', 3600, function () {
            return \App\Models\Property\Property::where('status', 'active')->get();
        });
    }

    /**
     * Get virtual open house marketing stats with caching.
     *
     * @return array
     */
    public function getVirtualOpenHouseStats(): array
    {
        return Cache::remember('virtual_open_house_stats', 600, function () {
            // Bolt Optimization: Consolidate 6 individual aggregate queries into a single SQL query
            // using conditional aggregations, reducing database overhead from 6 queries to 1 query.
            $stats = VirtualOpenHouseMarketing::query()
                ->selectRaw("
                    COUNT(*) as total_campaigns,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_campaigns,
                    COUNT(CASE WHEN status = 'scheduled' THEN 1 END) as scheduled_campaigns,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_campaigns,
                    COALESCE(SUM(total_attendees), 0) as total_attendees,
                    COALESCE(SUM(total_views), 0) as total_views
                ")
                ->first();

            return [
                'total_campaigns' => (int) ($stats->total_campaigns ?? 0),
                'active_campaigns' => (int) ($stats->active_campaigns ?? 0),
                'scheduled_campaigns' => (int) ($stats->scheduled_campaigns ?? 0),
                'completed_campaigns' => (int) ($stats->completed_campaigns ?? 0),
                'total_attendees' => (int) ($stats->total_attendees ?? 0),
                'total_views' => (int) ($stats->total_views ?? 0),
            ];
        });
    }

    /**
     * Clear marketing-related caches.
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('virtual_open_house_stats');
    }
}
