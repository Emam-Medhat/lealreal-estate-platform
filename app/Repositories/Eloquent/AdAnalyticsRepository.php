<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\AdAnalyticsRepositoryInterface;
use App\Models\Advertisement;
use App\Models\AdCampaign;
use App\Models\AdPlacement;
use App\Models\AdClick;
use App\Models\AdImpression;
use App\Models\AdConversion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdAnalyticsRepository implements AdAnalyticsRepositoryInterface
{
    public function getOverviewStats(?\DateTime $startDate = null): array
    {
        $queryImpressions = AdImpression::query();
        $queryClicks = AdClick::query();
        $queryConversions = AdConversion::query();
        $queryCampaigns = AdCampaign::where('status', 'active');

        if ($startDate) {
            $queryImpressions->where('viewed_at', '>=', $startDate);
            $queryClicks->where('clicked_at', '>=', $startDate);
            $queryConversions->where('converted_at', '>=', $startDate);
        }

        return [
            'total_ads' => Advertisement::count(),
            'active_campaigns' => $queryCampaigns->count(),
            'total_impressions' => $queryImpressions->count(),
            'total_clicks' => $queryClicks->count(),
            'total_conversions' => $queryConversions->count(),
            'total_revenue' => $this->calculateRevenue($startDate),
            'active_advertisers' => User::whereHas('ads')->count()
        ];
    }

    public function getRecentPerformance(int $days = 30): \Illuminate\Support\Collection
    {
        $startDate = Carbon::now()->subDays($days);

        return DB::table('ad_impressions')
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as impressions')
            ->where('viewed_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getTopCampaigns(int $limit = 10): \Illuminate\Support\Collection
    {
        return AdCampaign::with(['ads', 'user'])
            ->withSum('ads', 'impressions_count')
            ->withSum('ads', 'clicks_count')
            ->orderBy('ads_sum_impressions_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTopAds(int $limit = 10): \Illuminate\Support\Collection
    {
        return Advertisement::with(['campaign', 'user'])
            ->orderBy('impressions_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTopAdvertisers(int $limit = 10): \Illuminate\Support\Collection
    {
        return User::with(['ads', 'campaigns'])
            ->withCount(['ads', 'campaigns'])
            ->withSum('ads', 'impressions_count')
            ->orderBy('ads_sum_impressions_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function calculateRevenue(?\DateTime $startDate = null): float
    {
        $cpmRevenue = $this->calculateModelRevenue('cpm', $startDate);
        $cpcRevenue = $this->calculateModelRevenue('cpc', $startDate);
        $cpaRevenue = $this->calculateModelRevenue('cpa', $startDate);

        return (float) ($cpmRevenue + $cpcRevenue + $cpaRevenue);
    }

    protected function calculateModelRevenue(string $model, ?\DateTime $startDate = null): float
    {
        $tableMap = [
            'cpm' => 'ad_impressions',
            'cpc' => 'ad_clicks',
            'cpa' => 'ad_conversions'
        ];

        $dateColumnMap = [
            'cpm' => 'viewed_at',
            'cpc' => 'clicked_at',
            'cpa' => 'converted_at'
        ];

        $table = $tableMap[$model];
        $dateColumn = $dateColumnMap[$model];

        $query = DB::table($table)
            ->join('advertisements', "{$table}.advertisement_id", '=', 'advertisements.id')
            ->join('ad_placement_advertisement', 'advertisements.id', '=', 'ad_placement_advertisement.advertisement_id')
            ->join('ad_placements', 'ad_placement_advertisement.ad_placement_id', '=', 'ad_placements.id')
            ->where('ad_placements.pricing_model', $model);

        if ($startDate) {
            $query->where("{$table}.{$dateColumn}", '>=', $startDate);
        }

        $count = $query->count();
        $avgPrice = AdPlacement::where('pricing_model', $model)->avg('base_price') ?? 0;

        return $model === 'cpm' ? ($count / 1000) * $avgPrice : $count * $avgPrice;
    }

    public function getRealTimeMetrics(?\DateTime $sinceDate = null): array
    {
        $since = $sinceDate ?? Carbon::now()->subHour();

        return [
            'active_impressions' => AdImpression::where('viewed_at', '>=', $since)->count(),
            'active_clicks' => AdClick::where('clicked_at', '>=', $since)->count(),
            'active_conversions' => AdConversion::where('converted_at', '>=', $since)->count(),
            'active_campaigns' => AdCampaign::where('status', 'active')->count(),
            'active_ads' => Advertisement::where('status', 'active')->count(),
        ];
    }
}
