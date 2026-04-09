<?php

namespace App\Repositories\Eloquent;

use App\Models\Investor;
use App\Repositories\Contracts\InvestorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class InvestorRepository extends BaseRepository implements InvestorRepositoryInterface
{
    /**
     * InvestorRepository constructor.
     *
     * @param Investor $model
     */
    public function __construct(Investor $model)
    {
        parent::__construct($model);
    }

    /**
     * Get recent investors
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 5): Collection
    {
        return $this->model->latest()->take($limit)->get();
    }

    /**
     * Get investors count by date
     *
     * @param string $date
     * @return int
     */
    public function countByDate(string $date): int
    {
        return $this->model->whereDate('created_at', $date)->count();
    }
}
