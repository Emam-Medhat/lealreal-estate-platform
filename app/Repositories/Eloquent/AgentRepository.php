<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\AgentRepositoryInterface;
use App\Models\Agent;
use App\Models\AgentProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AgentRepository extends BaseRepository implements AgentRepositoryInterface
{
    public function __construct(Agent $model)
    {
        parent::__construct($model);
    }

    public function getPaginated(array $filters, int $perPage = 12, bool $forDirectory = false): LengthAwarePaginator
    {
        $query = $this->model->with(['profile', 'user', 'company'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        if ($forDirectory) {
            $query->where('status', 'active');
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhereHas('profile', function ($q) use ($search) {
                $q->where('license_number', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        if (isset($filters['specialization'])) {
            $specialization = $filters['specialization'];
            $query->whereHas('profile', function ($q) use ($specialization) {
                $q->whereJsonContains('specializations', $specialization);
            });
        }

        if (isset($filters['location'])) {
            $location = $filters['location'];
            $query->whereHas('profile', function ($q) use ($location) {
                $q->whereJsonContains('service_areas', $location);
            });
        }

        if (isset($filters['rating'])) {
            $rating = $filters['rating'];
            $query->whereHas('reviews', function ($q) use ($rating) {
                $q->havingRaw('AVG(rating) >= ?', [$rating]);
            });
        }

        if (isset($filters['status']) && !$forDirectory) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if ($forDirectory) {
            // Note: ordering by subquery might be slow on large datasets, consider caching or indexed column
            $query->orderByRaw('(SELECT COALESCE(AVG(rating), 0) FROM agent_reviews WHERE agent_id = agents.id) DESC');
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    public function getFiltered(array $filters): Collection
    {
        $query = $this->model->with(['user', 'company'])
            ->where('status', 'active');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        return $query->get(['id', 'user_id', 'company_id', 'license_number']);
    }

    public function getSpecializations(): \Illuminate\Support\Collection
    {
        return AgentProfile::whereNotNull('specializations')
            ->get()
            ->flatMap(function ($profile) {
                return $profile->specializations ?? [];
            })
            ->unique()
            ->sort()
            ->values();
    }

    public function findByUserId(int $userId): ?Agent
    {
        return $this->model->where('user_id', $userId)->first();
    }
}
