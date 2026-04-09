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
            return [
                'total_campaigns' => VirtualOpenHouseMarketing::count(),
                'active_campaigns' => VirtualOpenHouseMarketing::where('status', 'active')->count(),
                'scheduled_campaigns' => VirtualOpenHouseMarketing::where('status', 'scheduled')->count(),
                'completed_campaigns' => VirtualOpenHouseMarketing::where('status', 'completed')->count(),
                'total_attendees' => VirtualOpenHouseMarketing::sum('total_attendees'),
                'total_views' => VirtualOpenHouseMarketing::sum('total_views'),
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
