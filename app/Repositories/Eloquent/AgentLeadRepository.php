<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\AgentLeadRepositoryInterface;
use App\Models\AgentLead;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AgentLeadRepository extends BaseRepository implements AgentLeadRepositoryInterface
{
    public function __construct(AgentLead $model)
    {
        parent::__construct($model);
    }

    public function getFiltered(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->remember('agent_leads_filtered_' . md5(serialize($filters) . $perPage), function () use ($filters, $perPage) {
            $query = $this->model->with(['property', 'source']);

            if (isset($filters['agent_id'])) {
                $query->where('agent_id', $filters['agent_id']);
            }

            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['priority'])) {
                $query->where('priority', $filters['priority']);
            }

            if (isset($filters['source_id'])) {
                $query->where('source_id', $filters['source_id']);
            }

            if (isset($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            return $query->latest()->paginate($perPage);
        }, ['agent_leads'], 600);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $lead = $this->findById($id);
        if ($lead) {
            $updated = $lead->update([
                'status' => $status,
                'status_updated_at' => now(),
            ]);
            $this->clearCache(['agent_leads']);
            return $updated;
        }
        return false;
    }

    public function updatePriority(int $id, string $priority): bool
    {
        $updated = $this->update($id, ['priority' => $priority]);
        if ($updated) {
            $this->clearCache(['agent_leads']);
        }
        return (bool) $updated;
    }
}
