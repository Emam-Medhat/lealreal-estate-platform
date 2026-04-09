<?php

namespace App\Services;

use App\Repositories\Contracts\AdAnalyticsRepositoryInterface;
use Carbon\Carbon;

class AdAnalyticsService
{
    protected $repository;

    public function __construct(AdAnalyticsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get dashboard data for the analytics view.
     */
    public function getDashboardData(): array
    {
        return CacheService::rememberAnalytics('dashboard_overview', function () {
            return [
                'overview' => $this->repository->getOverviewStats(),
                'recentPerformance' => $this->repository->getRecentPerformance(30),
                'topCampaigns' => $this->repository->getTopCampaigns(10),
                'topAds' => $this->repository->getTopAds(10),
                'topAdvertisers' => $this->repository->getTopAdvertisers(10),
            ];
        }, 'short');
    }

    /**
     * Get real-time metrics for the analytics view.
     */
    public function getRealTimeMetrics(): array
    {
        $metrics = $this->repository->getRealTimeMetrics();

        // Calculate CTR and ECPM
        $impressions = $metrics['active_impressions'];
        $clicks = $metrics['active_clicks'];

        $metrics['current_ctr'] = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;

        // Placeholder for ECPM as it requires revenue in the last hour
        $metrics['current_ecpm'] = 0;

        return $metrics;
    }

    /**
     * Calculate start date based on timeframe.
     */
    public function getStartDate(string $timeframe): Carbon
    {
        switch ($timeframe) {
            case '7days':
                return Carbon::now()->subDays(7);
            case '30days':
                return Carbon::now()->subDays(30);
            case '90days':
                return Carbon::now()->subDays(90);
            case '1year':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subDays(30);
        }
    }

    /**
     * Get platform analytics data.
     */
    public function getPlatformAnalytics(string $timeframe): array
    {
        $startDate = $this->getStartDate($timeframe);

        return CacheService::rememberAnalytics("platform_analytics_{$timeframe}", function () use ($startDate) {
            return [
                'overview' => $this->repository->getOverviewStats($startDate),
                'performance' => $this->getPerformanceStats($startDate),
                // Add more detailed stats as needed
            ];
        }, 'medium');
    }

    protected function getPerformanceStats(Carbon $startDate): array
    {
        $stats = $this->repository->getOverviewStats($startDate);
        $impressions = $stats['total_impressions'];
        $clicks = $stats['total_clicks'];
        $conversions = $stats['total_conversions'];
        $revenue = $stats['total_revenue'];

        return [
            'ctr' => $impressions > 0 ? ($clicks / $impressions) * 100 : 0,
            'cpc' => $clicks > 0 ? $revenue / $clicks : 0,
            'cpa' => $conversions > 0 ? $revenue / $conversions : 0,
            'ecpm' => $impressions > 0 ? ($revenue / $impressions) * 1000 : 0,
            'conversion_rate' => $clicks > 0 ? ($conversions / $clicks) * 100 : 0
        ];
    }
}
