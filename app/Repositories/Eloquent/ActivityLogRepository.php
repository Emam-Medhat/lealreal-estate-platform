<?php

namespace App\Repositories\Eloquent;

use App\Models\ActivityLog;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ActivityLogRepository extends BaseRepository implements ActivityLogRepositoryInterface
{
    /**
     * ActivityLogRepository constructor.
     *
     * @param ActivityLog $model
     */
    public function __construct(ActivityLog $model)
    {
        parent::__construct($model);
    }

    /**
     * Get recent activities.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->with(['user', 'subject'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Override soft delete methods since ActivityLog does not use SoftDeletes.
     */
    public function allTrashed(): Collection
    {
        return new Collection();
    }

    public function restoreById(int|string $modelId): bool
    {
        return false;
    }

    public function permanentlyDeleteById(int|string $modelId): bool
    {
        return $this->deleteById($modelId);
    }
}
