<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    public function __construct(Company $model)
    {
        parent::__construct($model);
    }

    public function getPaginated(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with('profile');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getActiveCompanies(array $filters): Collection
    {
        $query = $this->model->with('profile')->where('status', 'active');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->get(['id', 'name', 'type', 'email']);
    }

    public function findByUserId(int $userId): ?Company
    {
        // Assuming relationship via created_by or member
        return $this->model->where('created_by', $userId)->first();
    }

    public function findWithDetails(int $id): ?Company
    {
        return $this->model->with([
            'profile',
            'branches',
            'members.user',
            'properties' => function ($query) {
                $query->latest()->limit(10);
            }
        ])->find($id);
    }

    /**
     * Count companies by date.
     *
     * @param string $date
     * @return int
     */
    public function countByDate(string $date): int
    {
        return $this->model->whereDate('created_at', $date)->count();
    }

    /**
     * Get recent companies.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 5): Collection
    {
        return $this->model->latest()->take($limit)->get();
    }
}
