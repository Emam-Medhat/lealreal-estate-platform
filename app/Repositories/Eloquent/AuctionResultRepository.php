<?php

namespace App\Repositories\Eloquent;

use App\Models\AuctionResult;
use App\Repositories\Contracts\AuctionResultRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AuctionResultRepository extends BaseRepository implements AuctionResultRepositoryInterface
{
    public function __construct(AuctionResult $model)
    {
        parent::__construct($model);
    }

    public function getFilteredResults(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->remember('getFilteredResults', function () use ($filters, $perPage) {
            $query = $this->model->with(['auction.property', 'winner', 'auction.creator'])
                ->whereHas('auction', function ($query) {
                    $query->where('status', 'completed');
                });

            if (isset($filters['winner_id'])) {
                $query->where('winner_id', $filters['winner_id']);
            }

            if (isset($filters['creator_id'])) {
                $query->whereHas('auction', function ($query) use ($filters) {
                    $query->where('seller_id', $filters['creator_id']);
                });
            }

            if (isset($filters['date_from'])) {
                $query->whereDate('result_time', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query->whereDate('result_time', '<=', $filters['date_to']);
            }

            if (isset($filters['min_price'])) {
                $query->where('final_price', '>=', $filters['min_price']);
            }

            if (isset($filters['max_price'])) {
                $query->where('final_price', '<=', $filters['max_price']);
            }

            return $query->orderBy('result_time', 'desc')->paginate($perPage);
        }, ['filters' => $filters, 'perPage' => $perPage]);
    }

    public function getWonByUser(int $userId): Collection
    {
        return $this->remember('getWonByUser', function () use ($userId) {
            return $this->model->where('winner_id', $userId)
                ->with(['auction.property', 'contract'])
                ->orderBy('result_time', 'desc')
                ->get();
        }, ['userId' => $userId]);
    }

    public function getCreatedByUser(int $userId): Collection
    {
        return $this->remember('getCreatedByUser', function () use ($userId) {
            return $this->model->whereHas('auction', function ($query) use ($userId) {
                $query->where('seller_id', $userId);
            })->with(['auction.property', 'winner', 'contract'])
              ->orderBy('result_time', 'desc')
              ->get();
        }, ['userId' => $userId]);
    }

    /**
     * Get aggregated auction result stats.
     *
     * Performance Optimization:
     * Consolidate 9 separate database queries (count, sum, avg, max, min, this_month, last_month, plus growth rate subqueries)
     * into a single raw SQL query with conditional aggregations and date range filtering.
     */
    public function getStats(): array
    {
        return $this->remember('getStats', function () {
            $startOfThisMonth = now()->startOfMonth()->toDateTimeString();
            $endOfThisMonth = now()->endOfMonth()->toDateTimeString();
            $startOfLastMonth = now()->subMonth()->startOfMonth()->toDateTimeString();
            $endOfLastMonth = now()->subMonth()->endOfMonth()->toDateTimeString();

            $stats = $this->model->newQuery()
                ->selectRaw('
                    COUNT(*) as total_completed,
                    COALESCE(SUM(final_price), 0) as total_revenue,
                    COALESCE(AVG(final_price), 0) as average_price,
                    COALESCE(MAX(final_price), 0) as highest_price,
                    COALESCE(MIN(final_price), 0) as lowest_price,
                    COUNT(CASE WHEN result_time >= ? AND result_time <= ? THEN 1 END) as this_month,
                    COUNT(CASE WHEN result_time >= ? AND result_time <= ? THEN 1 END) as last_month
                ', [
                    $startOfThisMonth,
                    $endOfThisMonth,
                    $startOfLastMonth,
                    $endOfLastMonth
                ])
                ->first();

            $thisMonth = (int) ($stats->this_month ?? 0);
            $lastMonth = (int) ($stats->last_month ?? 0);
            $growthRate = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2) : 0.0;

            return [
                'total_completed' => (int) ($stats->total_completed ?? 0),
                'total_revenue' => (float) ($stats->total_revenue ?? 0),
                'average_price' => (float) ($stats->average_price ?? 0),
                'highest_price' => (float) ($stats->highest_price ?? 0),
                'lowest_price' => (float) ($stats->lowest_price ?? 0),
                'this_month' => $thisMonth,
                'last_month' => $lastMonth,
                'growth_rate' => (float) $growthRate,
            ];
        }, [], 3600);
    }

    /**
     * Calculate growth rate between current month and previous month.
     * Reuses cached stats to avoid extra database queries.
     */
    public function calculateGrowthRate(): float
    {
        $stats = $this->getStats();
        return (float) ($stats['growth_rate'] ?? 0.0);
    }
}
