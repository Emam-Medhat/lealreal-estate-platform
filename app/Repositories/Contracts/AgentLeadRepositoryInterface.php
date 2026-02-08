<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AgentLeadRepositoryInterface extends BaseRepositoryInterface
{
    public function getFiltered(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function updateStatus(int $id, string $status): bool;
    public function updatePriority(int $id, string $priority): bool;
}
