<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PropertyRepositoryInterface extends BaseRepositoryInterface
{
    public function getFeatured(int $limit = 6): Collection;
    public function getLatestActive(int $limit = 6): Collection;
    public function getByTypeSlug(string $slug, int $limit = 3): Collection;
    public function getAgentPropertiesPaginated(int $agentId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getActiveProperties(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getFilteredProperties(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function searchProperties(string $query, array $filters = [], int $limit = 50): Collection;
    public function getPropertiesByLocation(string $city, string $state = null, int $limit = 20): Collection;
    public function getPropertyStats(): array;
    public function getPropertiesForExport(array $filters = []): \Generator;
    public function getPropertyPerformanceMetrics(): array;
    public function getPropertyRecommendations(array $preferences, int $limit = 10): Collection;
    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model;
    public function getLatest(int $limit = 6, array $relations = []): Collection;
    public function getPopular(int $limit = 6, array $relations = []): Collection;
    public function getMarketMetrics(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate, ?string $marketArea = null): array;
}
