<?php

namespace App\Repositories\Eloquent;

use App\Models\Dao;
use App\Repositories\Contracts\DaoRepositoryInterface;

class DaoRepository extends BaseRepository implements DaoRepositoryInterface
{
    /**
     * DaoRepository constructor.
     *
     * @param Dao $model
     */
    public function __construct(Dao $model)
    {
        parent::__construct($model);
    }

    public function getByIdOrAddress($id = null, $address = null)
    {
        $cacheKey = "dao_" . ($id ? "id_{$id}" : ($address ? "addr_{$address}" : "null"));
        return $this->remember($cacheKey, function () use ($id, $address) {
            $query = $this->model->with(['creator', 'members', 'proposals']);
            
            if ($id) {
                return $query->where('id', $id)->first();
            }
            if ($address) {
                return $query->where('address', $address)->first();
            }
            return null;
        }, [], 3600);
    }

    public function getAll(int $perPage = 20)
    {
        return $this->remember("daos_all_page_" . request()->get('page', 1), function () use ($perPage) {
            return $this->model->with(['creator', 'members', 'proposals'])
                ->latest()
                ->paginate($perPage);
        }, [], 300);
    }

    public function findByIdWithRelations(int $id)
    {
        return $this->remember("dao_full_{$id}", function () use ($id) {
            return $this->model->with(['creator', 'members', 'proposals'])
                ->findOrFail($id);
        }, [], 3600);
    }

    public function getTreasuryStats(int $id)
    {
        return $this->remember("dao_treasury_stats_{$id}", function () use ($id) {
            $dao = $this->model->find($id);
            if (!$dao) {
                return [
                    'total_balance' => 0,
                    'eth_balance' => 0,
                    'token_balance' => 0,
                    'usd_value' => 0,
                    'last_updated' => now()
                ];
            }

            return [
                'total_balance' => $dao->treasury_balance,
                'eth_balance' => $dao->treasury_balance, // Assuming primary balance is in native currency
                'token_balance' => 0, // Requires DaoTreasuryTransaction or specific token field
                'usd_value' => $dao->treasury_value_in_usd,
                'last_updated' => $dao->updated_at
            ];
        }, [], 300);
    }

    public function getVotingStats(int $id)
    {
        return $this->remember("dao_voting_stats_{$id}", function () use ($id) {
            $dao = $this->model->find($id);
            if (!$dao) {
                return [
                    'total_votes_cast' => 0,
                    'votes_30d' => 0,
                    'average_participation' => 0,
                    'quorum_met_rate' => 0,
                    'proposal_success_rate' => 0
                ];
            }

            return [
                'total_votes_cast' => $dao->votes()->count(), // Assuming relation works, otherwise 0
                'votes_30d' => $dao->votes()->where('created_at', '>=', now()->subDays(30))->count(),
                'average_participation' => $dao->participation_rate,
                'quorum_met_rate' => 0, // Complex calculation, placeholder
                'proposal_success_rate' => $dao->proposal_success_rate
            ];
        }, [], 3600);
    }
}
