<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ServiceProviderRepositoryInterface;
use App\Models\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class ServiceProviderRepository extends BaseRepository implements ServiceProviderRepositoryInterface
{
    public function __construct(ServiceProvider $model)
    {
        parent::__construct($model);
    }

    public function getPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['maintenanceRequests', 'schedules']);

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['service_type'])) {
            $query->where('service_type', $filters['service_type']);
        }

        if (isset($filters['rating'])) {
            $query->where('rating', '>=', $filters['rating']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getAvailableProviders(string $serviceType, string $date, int $duration): Collection
    {
        return $this->model->where('is_active', true)
            ->where(function ($query) use ($serviceType) {
                $query->where('service_type', $serviceType)
                    ->orWhere('service_type', 'all');
            })
            ->whereDoesntHave('schedules', function ($query) use ($date, $duration) {
                $query->where('status', 'in_progress')
                    ->where('scheduled_date', '<=', Carbon::parse($date)->addMinutes($duration))
                    ->where('scheduled_date', '>=', Carbon::parse($date)->subMinutes($duration));
            })
            ->withCount([
                'maintenanceRequests' => function ($query) {
                    $query->where('status', 'completed');
                }
            ])
            ->orderBy('rating', 'desc')
            ->get();
    }

    public function findWithDetails(int $id): ?ServiceProvider
    {
        return $this->model->with([
            'maintenanceRequests' => function ($query) {
                $query->latest()->take(10);
            },
            'schedules' => function ($query) {
                $query->where('scheduled_date', '>=', now())->orderBy('scheduled_date')->take(10);
            }
        ])->find($id);
    }
}
