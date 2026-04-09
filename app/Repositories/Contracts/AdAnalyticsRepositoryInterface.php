<?php

namespace App\Repositories\Contracts;

interface AdAnalyticsRepositoryInterface
{
    public function getOverviewStats(?\DateTime $startDate = null): array;
    public function getRecentPerformance(int $days = 30): \Illuminate\Support\Collection;
    public function getTopCampaigns(int $limit = 10): \Illuminate\Support\Collection;
    public function getTopAds(int $limit = 10): \Illuminate\Support\Collection;
    public function getTopAdvertisers(int $limit = 10): \Illuminate\Support\Collection;
    public function calculateRevenue(?\DateTime $startDate = null): float;
    public function getRealTimeMetrics(?\DateTime $sinceDate = null): array;
}
