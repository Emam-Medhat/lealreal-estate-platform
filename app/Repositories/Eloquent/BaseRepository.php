<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Cache duration in seconds (default: 1 hour)
     */
    protected $cacheDuration = 3600;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get cache key for queries
     *
     * @param string $method
     * @param array $params
     * @return string
     */
    protected function getCacheKey(string $method, array $params = []): string
    {
        // Filter out non-serializable data and create a simple key
        $filteredParams = array_filter($params, function($param) {
            return !($param instanceof \Closure);
        });
        
        return static::class . ":{$method}:" . md5(json_encode($filteredParams));
    }

    /**
     * Execute query with caching and performance monitoring
     *
     * @param string $method
     * @param callable $callback
     * @param array $params
     * @param int|null $duration
     * @return mixed
     */
    protected function remember(string $method, callable $callback, array $params = [], ?int $duration = null)
    {
        $cacheKey = $this->getCacheKey($method, $params);
        $cacheDuration = $duration ?? $this->cacheDuration;

        return Cache::remember($cacheKey, $cacheDuration, $callback);
    }

    /**
     * Clear cache for this repository
     *
     * @param string|null $method
     * @return void
     */
    public function clearCache(?string $method = null): void
    {
        // For simplicity in this demo, clearing all. 
        // In production, use tags or specific keys.
        Cache::flush();
    }

    /**
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function first(array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->remember('first', function () use ($columns, $relations) {
            return $this->model->with($relations)->first($columns);
        }, ['columns' => $columns, 'relations' => $relations]);
    }

    /**
     * @param string $column
     * @param string|null $key
     * @return array
     */
    public function pluck(string $column, ?string $key = null): array
    {
        return $this->remember('pluck', function () use ($column, $key) {
            return $this->model->pluck($column, $key)->toArray();
        }, ['column' => $column, 'key' => $key]);
    }

    /**
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->remember('all', function () use ($columns, $relations) {
            return $this->model->with($relations)->get($columns);
        }, ['columns' => $columns, 'relations' => $relations]);
    }

    /**
     * @return Collection
     */
    public function allTrashed(): Collection
    {
        return $this->remember('allTrashed', function () {
            return $this->model->onlyTrashed()->get();
        });
    }

    /**
     * @param int $modelId
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model|null
     */
    public function findById(
        int $modelId,
        array $columns = ['id'],
        array $relations = [],
        array $appends = []
    ): ?Model {
        return $this->remember("findById_{$modelId}", function () use ($modelId, $columns, $relations, $appends) {
            return $this->model->select($columns)->with($relations)->find($modelId)?->append($appends);
        }, ['columns' => $columns, 'relations' => $relations, 'appends' => $appends], 1800); // 30 minutes
    }

    /**
     * @param array $payload
     * @return Model|null
     */
    public function create(array $payload): ?Model
    {
        DB::beginTransaction();
        try {
            $model = $this->model->create($payload);

            // Clear relevant caches
            $this->clearCache();

            DB::commit();
            return $model->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $modelId
     * @param array $payload
     * @return bool
     */
    public function update(int $modelId, array $payload): bool
    {
        DB::beginTransaction();
        try {
            $model = $this->findById($modelId);

            if (!$model) {
                return false;
            }

            $result = $model->update($payload);

            // Clear relevant caches
            $this->clearCache();
            $this->clearCache("findById_{$modelId}");

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId): bool
    {
        DB::beginTransaction();
        try {
            $model = $this->findById($modelId);

            if (!$model) {
                return false;
            }

            $result = $model->delete();

            // Clear relevant caches
            $this->clearCache();
            $this->clearCache("findById_{$modelId}");

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $modelId
     * @return bool
     */
    public function restoreById(int $modelId): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->findOnlyTrashedById($modelId)->restore();

            // Clear relevant caches
            $this->clearCache();

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $modelId
     * @return bool
     */
    public function permanentlyDeleteById(int $modelId): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->findOnlyTrashedById($modelId)->forceDelete();

            // Clear relevant caches
            $this->clearCache();

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $modelId
     * @return Model|null
     */
    public function findOnlyTrashedById(int $modelId): ?Model
    {
        return $this->remember("findOnlyTrashedById_{$modelId}", function () use ($modelId) {
            return $this->model->onlyTrashed()->find($modelId);
        }, [], 1800); // 30 minutes
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->remember("paginate_{$perPage}", function () use ($perPage, $columns, $relations) {
            return $this->model->with($relations)->select($columns)->paginate($perPage);
        }, ['columns' => $columns, 'relations' => $relations], 600); // 10 minutes
    }

    /**
     * Bulk insert for performance optimization
     *
     * @param array $data
     * @return bool
     */
    public function bulkInsert(array $data): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->model->insert($data);

            // Clear relevant caches
            $this->clearCache();

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get count with caching
     *
     * @param array $conditions
     * @return int
     */
    public function count(array $conditions = []): int
    {
        return $this->remember('count', function () use ($conditions) {
            $query = $this->model;

            foreach ($conditions as $column => $value) {
                $query->where($column, $value);
            }

            return $query->count();
        }, $conditions, 1800); // 30 minutes
    }
}
