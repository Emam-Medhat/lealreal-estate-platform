<?php

namespace App\Repositories\Eloquent;

use App\Models\SmartContract;
use App\Repositories\Contracts\SmartContractRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SmartContractRepository extends BaseRepository implements SmartContractRepositoryInterface
{
    /**
     * SmartContractRepository constructor.
     *
     * @param SmartContract $model
     */
    public function __construct(SmartContract $model)
    {
        parent::__construct($model);
    }

    /**
     * Get verified contracts.
     *
     * @param int $limit
     * @return Collection
     */
    public function getVerified(int $limit = 10): Collection
    {
        return $this->remember("smart_contracts_verified_{$limit}", function () use ($limit) {
            return $this->model->where('verified', true)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, [], 3600);
    }

    public function getRecent(int $limit = 10): Collection
    {
        return $this->remember("smart_contracts_recent_{$limit}", function () use ($limit) {
            return $this->model->with(['deployer'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, [], 300);
    }

    public function getByType(string $type, int $limit = 10): Collection
    {
        return $this->remember("smart_contracts_type_{$type}_{$limit}", function () use ($type, $limit) {
            return $this->model->where('contract_type', $type)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, [], 3600);
    }

    public function getFiltered(array $filters, int $perPage = 20)
    {
        $query = $this->model->with(['deployer']);

        if (isset($filters['network'])) {
            $query->where('network', $filters['network']);
        }

        if (isset($filters['type'])) {
            $query->where('contract_type', $filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findByAddress(string $address)
    {
        return $this->model->with(['deployer', 'transactions'])
            ->where('address', $address)
            ->first();
    }

    public function findByAddressAndNetwork(string $address, string $network)
    {
        return $this->model->where('address', $address)
            ->where('network', $network)
            ->firstOrFail();
    }

    public function getNetworkStats()
    {
        return $this->remember('smart_contracts_stats_network', function () {
            return $this->model->select('network', DB::raw('count(*) as count'))
                ->groupBy('network')
                ->pluck('count', 'network')
                ->toArray();
        }, [], 3600);
    }

    public function getTypeStats()
    {
        return $this->remember('smart_contracts_stats_type', function () {
            return $this->model->select('contract_type', DB::raw('count(*) as count'))
                ->groupBy('contract_type')
                ->pluck('count', 'contract_type')
                ->toArray();
        }, [], 3600);
    }
}
