<?php

namespace App\Repositories\Eloquent;

use App\Models\CryptoTransaction;
use App\Repositories\Contracts\CryptoTransactionRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class CryptoTransactionRepository extends BaseRepository implements CryptoTransactionRepositoryInterface
{
    public function __construct(CryptoTransaction $model)
    {
        parent::__construct($model);
    }

    public function getByAddress(string $address, int $perPage = 50)
    {
        return $this->model->with(['user', 'wallet'])
            ->where('from_address', $address)
            ->orWhere('to_address', $address)
            ->latest()
            ->paginate($perPage);
    }

    public function getRecent(int $limit = 10)
    {
        return $this->remember("recent_crypto_transactions_{$limit}", function () use ($limit) {
            return $this->model->with(['user', 'wallet'])
                ->latest()
                ->limit($limit)
                ->get();
        }, [], 300); // Cache for 5 minutes
    }

    public function getStats(string $period = '30d')
    {
        $startDate = $this->getStartDate($period);
        
        return $this->remember("crypto_transaction_stats_full_{$period}", function () use ($startDate) {
            $basicStats = $this->model->where('created_at', '>=', $startDate)
                ->selectRaw('
                    COUNT(*) as count,
                    SUM(amount) as volume,
                    AVG(gas_price) as avg_gas,
                    SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed_count,
                    SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count,
                    SUM(gas_used) as total_gas_used
                ')
                ->first();

            return [
                'count' => $basicStats->count ?? 0,
                'volume' => $basicStats->volume ?? 0,
                'avg_gas' => $basicStats->avg_gas ?? 0,
                'total_transactions' => $basicStats->count ?? 0,
                'confirmed_transactions' => $basicStats->confirmed_count ?? 0,
                'failed_transactions' => $basicStats->failed_count ?? 0,
                'pending_transactions' => $basicStats->pending_count ?? 0,
                'total_volume' => $basicStats->volume ?? 0,
                'total_gas_used' => $basicStats->total_gas_used ?? 0,
                'average_gas_price' => $basicStats->avg_gas ?? 0,
                'transaction_types' => $this->getTransactionTypes($startDate),
                'currencies' => $this->getCurrencies($startDate),
                'hourly_volume' => $this->getHourlyVolume($startDate),
                'top_addresses' => $this->getTopAddresses($startDate),
                'average_transaction_fee' => $this->getAverageTransactionFee($startDate)
            ];
        }, [], 3600);
    }

    public function findByHash(string $hash)
    {
        return $this->remember("crypto_transaction_{$hash}", function () use ($hash) {
            return $this->model->where('hash', $hash)->with(['user', 'wallet'])->first();
        }, [], 3600);
    }

    public function getByBlockHash(string $blockHash)
    {
        return $this->remember("crypto_transactions_block_{$blockHash}", function () use ($blockHash) {
            return $this->model->where('block_hash', $blockHash)->with(['user', 'wallet'])->get();
        }, [], 3600);
    }

    public function getFiltered(array $filters, int $perPage = 50)
    {
        $query = $this->model->with(['user', 'wallet', 'payment']);

        if (isset($filters['from_address'])) {
            $query->where('from_address', $filters['from_address']);
        }
        
        if (isset($filters['to_address'])) {
            $query->where('to_address', $filters['to_address']);
        }
        
        if (isset($filters['hash'])) {
            $query->where('hash', $filters['hash']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }
        
        if (isset($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }
        
        if (isset($filters['amount_min'])) {
            $query->where('amount', '>=', $filters['amount_min']);
        }
        
        if (isset($filters['amount_max'])) {
            $query->where('amount', '<=', $filters['amount_max']);
        }
        
        if (isset($filters['gas_price_min'])) {
            $query->where('gas_price', '>=', $filters['gas_price_min']);
        }
        
        if (isset($filters['gas_price_max'])) {
            $query->where('gas_price', '<=', $filters['gas_price_max']);
        }
        
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['contract_address'])) {
             $query->where('contract_address', $filters['contract_address']);
        }

        if (isset($filters['blockchain'])) {
            $query->where('blockchain', $filters['blockchain']);
        }

        if (isset($filters['address'])) {
            $address = $filters['address'];
            $query->where(function($q) use ($address) {
                $q->where('from_address', $address)
                  ->orWhere('to_address', $address);
            });
        }
        
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('hash', 'like', "%{$search}%")
                  ->orWhere('from_address', 'like', "%{$search}%")
                  ->orWhere('to_address', 'like', "%{$search}%")
                  ->orWhere('memo', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Support explicit limit for non-paginated results
        if (isset($filters['limit'])) {
            return $query->latest()->limit($filters['limit'])->get();
        }

        return $query->latest()->paginate($perPage);
    }

    public function getUserStats(int $userId)
    {
        return $this->remember("user_crypto_stats_{$userId}", function () use ($userId) {
            $stats = $this->model->where('user_id', $userId)
                ->selectRaw('
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN type = "send" THEN 1 ELSE 0 END) as sent_transactions,
                    SUM(CASE WHEN type = "receive" THEN 1 ELSE 0 END) as received_transactions,
                    SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_transactions,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_transactions,
                    SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_transactions,
                    SUM(CASE WHEN type = "send" THEN amount ELSE 0 END) as total_sent,
                    SUM(CASE WHEN type = "receive" THEN amount ELSE 0 END) as total_received,
                    SUM(gas_fee) as total_gas_fees
                ')->first();
            
            // By currency
            $byCurrency = $this->model->where('user_id', $userId)
                ->selectRaw('currency, COUNT(*) as count, SUM(amount) as total_amount')
                ->groupBy('currency')
                ->get();
                
            // By blockchain
            $byBlockchain = $this->model->where('user_id', $userId)
                ->selectRaw('blockchain, COUNT(*) as count, SUM(amount) as total_amount')
                ->groupBy('blockchain')
                ->get();
                
            // Monthly stats
            $monthlyStats = $this->model->where('user_id', $userId)
                ->where('created_at', '>=', now()->subYear())
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            return [
                'total_transactions' => $stats->total_transactions ?? 0,
                'sent_transactions' => $stats->sent_transactions ?? 0,
                'received_transactions' => $stats->received_transactions ?? 0,
                'completed_transactions' => $stats->completed_transactions ?? 0,
                'pending_transactions' => $stats->pending_transactions ?? 0,
                'failed_transactions' => $stats->failed_transactions ?? 0,
                'total_sent' => $stats->total_sent ?? 0,
                'total_received' => $stats->total_received ?? 0,
                'total_gas_fees' => $stats->total_gas_fees ?? 0,
                'by_currency' => $byCurrency,
                'by_blockchain' => $byBlockchain,
                'monthly_stats' => $monthlyStats
            ];
        }, [], 300);
    }

    public function getTokenStats(string $contractAddress)
    {
        return $this->remember("token_stats_{$contractAddress}", function () use ($contractAddress) {
            $totalTransactions = $this->model->where('contract_address', $contractAddress)->count();
            
            $transactions24h = $this->model->where('contract_address', $contractAddress)
                ->where('created_at', '>=', now()->subDay())
                ->count();
                
            $uniqueAddresses = $this->model->where('contract_address', $contractAddress)
                ->distinct('from_address')
                ->count('from_address');
                
            return [
                'total_transactions' => $totalTransactions,
                'transactions_24h' => $transactions24h,
                'unique_addresses' => $uniqueAddresses
            ];
        }, [], 300);
    }

    public function totalVolume()
    {
        return $this->remember("total_volume", function () {
            return $this->model->sum('amount');
        }, [], 3600);
    }

    private function getStartDate($period)
    {
        return match($period) {
            '1h' => now()->subHour(),
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDay()
        };
    }

    private function getTransactionTypes($startDate)
    {
        return $this->model->where('created_at', '>=', $startDate)
            ->selectRaw('transaction_type, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('transaction_type')
            ->orderByDesc('total_amount')
            ->get();
    }

    private function getCurrencies($startDate)
    {
        return $this->model->where('created_at', '>=', $startDate)
            ->selectRaw('currency, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('currency')
            ->orderByDesc('total_amount')
            ->get();
    }

    private function getHourlyVolume($startDate)
    {
        return $this->model->where('created_at', '>=', $startDate)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour, SUM(amount) as volume, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    private function getTopAddresses($startDate)
    {
        return $this->model->where('created_at', '>=', $startDate)
            ->selectRaw('from_address as address, SUM(amount) as total_volume, COUNT(*) as transaction_count')
            ->groupBy('from_address')
            ->orderByDesc('total_volume')
            ->limit(10)
            ->get();
    }

    private function getAverageTransactionFee($startDate)
    {
        return $this->model->where('created_at', '>=', $startDate)
            ->where('status', 'confirmed')
            ->selectRaw('AVG(COALESCE(gas_used, 0) * COALESCE(gas_price, 0)) as avg_fee')
            ->value('avg_fee') ?? 0;
    }
}
