<?php

namespace App\Repositories\Eloquent;

use App\Models\AdCampaign;
use App\Models\AdClick;
use App\Models\AdConversion;
use App\Models\AdImpression;
use App\Models\Advertisement;
use App\Models\User;
use App\Repositories\Contracts\AdAnalyticsRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            'active_advertisers' => User::whereHas('ads')->count(),
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
        // Optimization: Fetch average base price for each pricing model in a single query (1 query instead of 3)
        $avgPrices = DB::table('ad_placements')
            ->selectRaw('pricing_model, AVG(base_price) as avg_price')
            ->whereIn('pricing_model', ['cpm', 'cpc', 'cpa'])
            ->groupBy('pricing_model')
            ->pluck('avg_price', 'pricing_model');

        $cpmAvgPrice = (float) ($avgPrices->get('cpm') ?? 0);
        $cpcAvgPrice = (float) ($avgPrices->get('cpc') ?? 0);
        $cpaAvgPrice = (float) ($avgPrices->get('cpa') ?? 0);

        // Optimization: Fetch event counts for CPM, CPC, and CPA in a single scalar subselect query (1 query instead of 3)
        $cpmQuery = DB::table('ad_impressions')
            ->join('advertisements', 'ad_impressions.advertisement_id', '=', 'advertisements.id')
            ->join('ad_placement_advertisement', 'advertisements.id', '=', 'ad_placement_advertisement.advertisement_id')
            ->join('ad_placements', 'ad_placement_advertisement.ad_placement_id', '=', 'ad_placements.id')
            ->where('ad_placements.pricing_model', 'cpm')
            ->when($startDate, fn ($q) => $q->where('ad_impressions.viewed_at', '>=', $startDate));

        $cpcQuery = DB::table('ad_clicks')
            ->join('advertisements', 'ad_clicks.advertisement_id', '=', 'advertisements.id')
            ->join('ad_placement_advertisement', 'advertisements.id', '=', 'ad_placement_advertisement.advertisement_id')
            ->join('ad_placements', 'ad_placement_advertisement.ad_placement_id', '=', 'ad_placements.id')
            ->where('ad_placements.pricing_model', 'cpc')
            ->when($startDate, fn ($q) => $q->where('ad_clicks.clicked_at', '>=', $startDate));

        $cpaQuery = DB::table('ad_conversions')
            ->join('advertisements', 'ad_conversions.advertisement_id', '=', 'advertisements.id')
            ->join('ad_placement_advertisement', 'advertisements.id', '=', 'ad_placement_advertisement.advertisement_id')
            ->join('ad_placements', 'ad_placement_advertisement.ad_placement_id', '=', 'ad_placements.id')
            ->where('ad_placements.pricing_model', 'cpa')
            ->when($startDate, fn ($q) => $q->where('ad_conversions.converted_at', '>=', $startDate));

        $counts = DB::query()->selectRaw(
            "({$cpmQuery->selectRaw('COUNT(*)')->toSql()}) as cpm_count, ".
            "({$cpcQuery->selectRaw('COUNT(*)')->toSql()}) as cpc_count, ".
            "({$cpaQuery->selectRaw('COUNT(*)')->toSql()}) as cpa_count",
            array_merge($cpmQuery->getBindings(), $cpcQuery->getBindings(), $cpaQuery->getBindings())
        )->first();

        $cpmCount = (int) ($counts->cpm_count ?? 0);
        $cpcCount = (int) ($counts->cpc_count ?? 0);
        $cpaCount = (int) ($counts->cpa_count ?? 0);

        $cpmRevenue = ($cpmCount / 1000) * $cpmAvgPrice;
        $cpcRevenue = $cpcCount * $cpcAvgPrice;
        $cpaRevenue = $cpaCount * $cpaAvgPrice;

        return (float) ($cpmRevenue + $cpcRevenue + $cpaRevenue);
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
