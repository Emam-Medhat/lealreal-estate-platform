<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface InvestorRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get recent investors
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 5): Collection;

    /**
     * Get investors count by date
     *
     * @param string $date
     * @return int
     */
    public function countByDate(string $date): int;
}
