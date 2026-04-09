<?php

namespace App\Repositories\Eloquent;

use App\Models\CryptoWallet;
use App\Repositories\Contracts\CryptoWalletRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CryptoWalletRepository extends BaseRepository implements CryptoWalletRepositoryInterface
{
    /**
     * CryptoWalletRepository constructor.
     *
     * @param CryptoWallet $model
     */
    public function __construct(CryptoWallet $model)
    {
        parent::__construct($model);
    }

    public function getByAddress(string $address)
    {
        return $this->remember("wallet_address_{$address}", function () use ($address) {
            return $this->model->where('address', $address)->first();
        }, [], 3600);
    }

    public function getUserWallet(int $userId)
    {
        return $this->remember("user_wallet_{$userId}", function () use ($userId) {
            return $this->model->where('user_id', $userId)->first();
        }, [], 3600);
    }

    public function getWallets(array $filters = [], int $perPage = 20)
    {
        // Cache key based on filters and page
        $page = request()->get('page', 1);
        $cacheKey = "wallets_" . md5(json_encode($filters)) . "_page_{$page}";

        return $this->remember($cacheKey, function () use ($filters, $perPage) {
            $query = $this->model->with(['user', 'transactions']);

            if (isset($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }
            
            if (isset($filters['wallet_type'])) {
                $query->where('wallet_type', $filters['wallet_type']);
            }
            
            if (isset($filters['network'])) {
                $query->where('network', $filters['network']);
            }
            
            if (isset($filters['is_active']) && $filters['is_active'] !== null) {
                $query->where('is_active', $filters['is_active']);
            }

            return $query->latest()->paginate($perPage);
        }, [], 300); // Cache for 5 minutes
    }

    public function getWallet(int $id = null, string $address = null)
    {
        $cacheKey = "wallet_" . ($id ? "id_{$id}" : "addr_{$address}");
        
        return $this->remember($cacheKey, function () use ($id, $address) {
            return $this->model->with(['user', 'transactions'])
                ->where(function ($query) use ($id, $address) {
                    if ($id) {
                        $query->orWhere('id', $id);
                    }
                    if ($address) {
                        $query->orWhere('address', $address);
                    }
                })
                ->first();
        }, [], 3600);
    }

    public function getWalletTypes($startDate)
    {
        return $this->remember("wallet_types_" . $startDate->timestamp, function () use ($startDate) {
            return $this->model->where('created_at', '>=', $startDate)
                ->selectRaw('wallet_type, COUNT(*) as count')
                ->groupBy('wallet_type')
                ->orderByDesc('count')
                ->get();
        }, [], 3600);
    }

    public function getNetworks($startDate)
    {
        return $this->remember("networks_" . $startDate->timestamp, function () use ($startDate) {
            return $this->model->where('created_at', '>=', $startDate)
                ->selectRaw('network, COUNT(*) as count')
                ->groupBy('network')
                ->orderByDesc('count')
                ->get();
        }, [], 3600);
    }

    public function getCurrencies($startDate)
    {
        return $this->remember("currencies_" . $startDate->timestamp, function () use ($startDate) {
            return $this->model->where('created_at', '>=', $startDate)
                ->selectRaw('currency, COUNT(*) as count, SUM(balance) as total_balance')
                ->groupBy('currency')
                ->orderByDesc('total_balance')
                ->get();
        }, [], 3600);
    }

    public function getTopWallets($startDate)
    {
        return $this->remember("top_wallets_" . $startDate->timestamp, function () use ($startDate) {
            return $this->model->where('created_at', '>=', $startDate)
                ->with('user')
                ->orderByDesc('balance')
                ->limit(10)
                ->get();
        }, [], 3600);
    }

    public function countActive()
    {
        return $this->remember("count_active_wallets", function () {
            return $this->model->where('is_active', true)->count();
        }, [], 3600);
    }

    public function sumBalance()
    {
        return $this->remember("sum_balance_wallets", function () {
            return $this->model->sum('balance');
        }, [], 3600);
    }

    public function countNewWallets($startDate)
    {
        return $this->remember("count_new_wallets_" . $startDate->timestamp, function () use ($startDate) {
            return $this->model->where('created_at', '>=', $startDate)->count();
        }, [], 3600);
    }

    public function averageBalance()
    {
        return $this->remember("avg_balance_wallets", function () {
            return $this->model->avg('balance');
        }, [], 3600);
    }
}
