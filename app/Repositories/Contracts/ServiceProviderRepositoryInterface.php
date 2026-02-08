<?php

namespace App\Repositories\Contracts;

use App\Models\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ServiceProviderRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaginated(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function getAvailableProviders(string $serviceType, string $date, int $duration): Collection;
    public function findWithDetails(int $id): ?ServiceProvider;
}
