<?php

namespace App\Repositories\Eloquent;

use App\Models\BlockchainTransaction;
use App\Repositories\Contracts\BlockchainTransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BlockchainTransactionRepository extends BaseRepository implements BlockchainTransactionRepositoryInterface
{
    public function __construct(BlockchainTransaction $model)
    {
        parent::__construct($model);
    }

    public function getStatsByNetwork()
    {
        return $this->remember('blockchain_transactions_stats_network', function () {
            return $this->model->select('network', DB::raw('count(*) as count'))
                ->groupBy('network')
                ->pluck('count', 'network')
                ->toArray();
        }, [], 3600);
    }

    public function getGasStats()
    {
        return $this->remember('blockchain_transactions_gas_stats', function () {
            return $this->model->select(
                DB::raw('sum(gas_used) as total_gas_used'),
                DB::raw('avg(gas_used) as average_gas_per_tx'),
                DB::raw('max(gas_used) as max_gas_used'),
                DB::raw('min(gas_used) as min_gas_used')
            )->first()->toArray();
        }, [], 3600);
    }

    public function getCostStats()
    {
        return $this->remember('blockchain_transactions_cost_stats', function () {
            return $this->model->select(
                DB::raw('sum(cost) as total_cost'),
                DB::raw('avg(cost) as average_cost_per_tx'),
                DB::raw('max(cost) as max_cost'),
                DB::raw('min(cost) as min_cost')
            )->first()->toArray();
        }, [], 3600);
    }

    public function getCostByNetwork()
    {
        return $this->remember('blockchain_transactions_cost_network', function () {
            return $this->model->select('network', DB::raw('sum(cost) as total_cost'))
                ->groupBy('network')
                ->pluck('total_cost', 'network')
                ->toArray();
        }, [], 3600);
    }

    public function getCostByContractType()
    {
        return $this->remember('blockchain_transactions_cost_type', function () {
            return $this->model->select('smart_contracts.contract_type', DB::raw('sum(blockchain_transactions.cost) as total_cost'))
                ->join('smart_contracts', 'blockchain_transactions.contract_address', '=', 'smart_contracts.address')
                ->groupBy('smart_contracts.contract_type')
                ->pluck('total_cost', 'smart_contracts.contract_type')
                ->toArray();
        }, [], 3600);
    }

    public function getNetworkAggregates()
    {
        return $this->remember('blockchain_transactions_network_aggregates', function () {
            return $this->model->select('network', 
                DB::raw('count(*) as count'),
                DB::raw('sum(gas_used) as total_gas_used'),
                DB::raw('sum(cost) as total_cost')
            )
            ->groupBy('network')
            ->get()
            ->keyBy('network')
            ->toArray();
        }, [], 3600);
    }

    public function getRecent(int $limit = 10)
    {
        return $this->remember("blockchain_transactions_recent_{$limit}", function () use ($limit) {
            return $this->model->with(['smartContract'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, [], 300);
    }

    public function getPaginated(int $perPage = 15)
    {
        // Pagination cannot be cached easily with unique keys for every page, 
        // but we can cache the first page or just not cache it.
        // For now, let's not cache pagination to avoid complexity.
        return $this->model->with(['smartContract'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByUser(int $userId, int $perPage = 15)
    {
        // Since BlockchainTransaction doesn't have user_id, we can't implement this directly
        // unless we join with something else or if we update the model.
        // For now, returning empty collection or throwing exception.
        // But wait, the interface requires it.
        
        // If we assume we want CryptoTransactions for user, this repository is for BlockchainTransaction.
        // So maybe this method shouldn't be here or we should return nothing.
        
        return $this->model->whereRaw('1 = 0')->paginate($perPage);
    }
}
