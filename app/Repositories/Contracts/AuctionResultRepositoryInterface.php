<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AuctionResultRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredResults(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getWonByUser(int $userId): Collection;
    public function getCreatedByUser(int $userId): Collection;
    public function getStats(): array;
    public function calculateGrowthRate(): float;
}
