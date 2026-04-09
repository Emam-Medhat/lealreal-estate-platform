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

    public function getStats(): array
    {
        return $this->remember('getStats', function () {
            return [
                'total_completed' => $this->model->count(),
                'total_revenue' => $this->model->sum('final_price'),
                'average_price' => $this->model->avg('final_price'),
                'highest_price' => $this->model->max('final_price'),
                'lowest_price' => $this->model->min('final_price'),
                'this_month' => $this->model->whereMonth('result_time', now()->month)->count(),
                'last_month' => $this->model->whereMonth('result_time', now()->subMonth())->count(),
                'growth_rate' => $this->calculateGrowthRate()
            ];
        }, [], 3600);
    }

    public function calculateGrowthRate(): float
    {
        $thisMonth = $this->model->whereMonth('result_time', now()->month)->count();
        $lastMonth = $this->model->whereMonth('result_time', now()->subMonth())->count();

        if ($lastMonth === 0) {
            return 0;
        }

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
    }
}
