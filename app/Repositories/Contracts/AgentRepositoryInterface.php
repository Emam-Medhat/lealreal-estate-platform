<?php

namespace App\Repositories\Contracts;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AgentRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaginated(array $filters, int $perPage = 12, bool $forDirectory = false): LengthAwarePaginator;
    public function getFiltered(array $filters): Collection;
    public function getSpecializations(): \Illuminate\Support\Collection;
    public function findByUserId(int $userId): ?Agent;
}
