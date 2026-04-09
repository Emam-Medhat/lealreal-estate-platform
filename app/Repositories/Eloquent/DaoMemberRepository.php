<?php

namespace App\Repositories\Eloquent;

use App\Models\DaoMember;
use App\Repositories\Contracts\DaoMemberRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DaoMemberRepository extends BaseRepository implements DaoMemberRepositoryInterface
{
    public function __construct(DaoMember $model)
    {
        parent::__construct($model);
    }

    public function getByDaoId(int $daoId, int $perPage = 20)
    {
        return $this->remember("dao_members_{$daoId}_page_" . request()->get('page', 1), function () use ($daoId, $perPage) {
            return $this->model->where('dao_id', $daoId)
                ->with('user')
                ->latest()
                ->paginate($perPage);
        }, [], 300);
    }

    public function getByUserId(int $userId, int $perPage = 20)
    {
        return $this->remember("user_dao_memberships_{$userId}_page_" . request()->get('page', 1), function () use ($userId, $perPage) {
            return $this->model->where('user_id', $userId)
                ->with('dao')
                ->latest()
                ->paginate($perPage);
        }, [], 300);
    }

    public function findByDaoAndUser(int $daoId, int $userId)
    {
        return $this->remember("dao_member_{$daoId}_{$userId}", function () use ($daoId, $userId) {
            return $this->model->where('dao_id', $daoId)
                ->where('user_id', $userId)
                ->first();
        }, [], 3600);
    }

    public function getAllByDaoId(int $daoId)
    {
        return $this->remember("dao_members_all_{$daoId}", function () use ($daoId) {
            return $this->model->where('dao_id', $daoId)
                ->with('user')
                ->get();
        }, [], 300);
    }

    public function getMemberStats(int $daoId)
    {
        return $this->remember("dao_member_stats_{$daoId}", function () use ($daoId) {
            // Mock implementation as per controller logic
            return [
                'total_members' => $this->model->where('dao_id', $daoId)->count(),
                'active_members' => rand(50, 5000), // Placeholder logic
                'new_members_30d' => $this->model->where('dao_id', $daoId)->where('joined_at', '>=', now()->subDays(30))->count(),
                'average_voting_power' => $this->model->where('dao_id', $daoId)->avg('voting_power') ?? 0,
                'total_voting_power' => $this->model->where('dao_id', $daoId)->sum('voting_power')
            ];
        }, [], 3600);
    }
}
