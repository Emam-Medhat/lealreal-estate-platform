<?php

namespace App\Repositories\Contracts;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaginated(array $filters, int $perPage = 20): LengthAwarePaginator;
    public function getActiveCompanies(array $filters): Collection;
    public function findByUserId(int $userId): ?Company;
    public function findWithDetails(int $id): ?Company;
    public function countByDate(string $date): int;
    public function getRecent(int $limit = 5): Collection;
}
