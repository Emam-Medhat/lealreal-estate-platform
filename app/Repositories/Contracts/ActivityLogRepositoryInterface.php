<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ActivityLogRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get recent activities.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 10): Collection;
}
