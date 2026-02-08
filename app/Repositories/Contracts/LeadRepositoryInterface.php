<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LeadRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredLeads(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getRecent(int $limit = 10): Collection;
    public function countByStatusName(string $statusName): int;
    public function countConverted(): int;
    public function sumEstimatedValue(): float;
    public function countTotal(): int;
    public function getDashboardStats(): array;
    public function getForExport(array $filters = [], int $chunkSize = 1000): \Generator;
}
