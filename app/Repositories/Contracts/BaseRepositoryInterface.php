<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all models.
     *
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get all models trashed.
     *
     * @return Collection
     */
    public function allTrashed(): Collection;

    /**
     * Find model by id.
     *
     * @param int|string $modelId
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model|null
     */
    public function findById(
        int|string $modelId,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): ?Model;

    /**
     * Create a model.
     *
     * @param array $payload
     * @return Model|null
     */
    public function create(array $payload): ?Model;

    /**
     * Update existing model.
     *
     * @param int|string $modelId
     * @param array $payload
     * @return bool
     */
    public function update(int|string $modelId, array $payload): bool;

    /**
     * Delete model by id.
     *
     * @param int|string $modelId
     * @return bool
     */
    public function deleteById(int|string $modelId): bool;

    /**
     * Restore model by id.
     *
     * @param int|string $modelId
     * @return bool
     */
    public function restoreById(int|string $modelId): bool;

    /**
     * Permanently delete model by id.
     *
     * @param int|string $modelId
     * @return bool
     */
    public function permanentlyDeleteById(int|string $modelId): bool;

    /**
     * Get paginated models.
     * 
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator;
    public function first(array $columns = ['*'], array $relations = []): ?Model;
    public function pluck(string $column, ?string $key = null): array;
    public function count(array $conditions = []): int;
}
