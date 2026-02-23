<?php

namespace App\Repositories\Eloquent;

use App\Models\BlockchainVerification;
use App\Repositories\Contracts\BlockchainVerificationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BlockchainVerificationRepository extends BaseRepository implements BlockchainVerificationRepositoryInterface
{
    /**
     * BlockchainVerificationRepository constructor.
     *
     * @param BlockchainVerification $model
     */
    public function __construct(BlockchainVerification $model)
    {
        parent::__construct($model);
    }

    public function getVerificationStats(int $userId)
    {
        return $this->remember("verification_stats_{$userId}", function () use ($userId) {
            $stats = $this->model->where('user_id', $userId)
                ->selectRaw('
                    COUNT(*) as total_verifications,
                    SUM(CASE WHEN verification_status = "verified" THEN 1 ELSE 0 END) as verified_transactions,
                    SUM(CASE WHEN verification_status = "pending" THEN 1 ELSE 0 END) as pending_verifications,
                    SUM(CASE WHEN verification_status = "failed" THEN 1 ELSE 0 END) as failed_verifications
                ')
                ->first();

            return [
                'total_verifications' => $stats->total_verifications ?? 0,
                'verified_transactions' => $stats->verified_transactions ?? 0,
                'pending_verifications' => $stats->pending_verifications ?? 0,
                'failed_verifications' => $stats->failed_verifications ?? 0,
            ];
        }, [], 300);
    }

    public function getRecentVerifications(int $userId, int $limit = 10)
    {
        return $this->remember("recent_verifications_{$userId}_{$limit}", function () use ($userId, $limit) {
            return $this->model->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, [], 300);
    }

    public function getVerificationTrends(int $userId)
    {
        return $this->remember("verification_trends_{$userId}", function () use ($userId) {
            return $this->model->where('user_id', $userId)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->pluck('count', 'date')
                ->toArray();
        }, [], 3600);
    }

    public function getNetworkDistribution(int $userId)
    {
        return $this->remember("network_distribution_{$userId}", function () use ($userId) {
            return $this->model->where('user_id', $userId)
                ->select('blockchain_network', DB::raw('count(*) as count'))
                ->groupBy('blockchain_network')
                ->pluck('count', 'blockchain_network')
                ->toArray();
        }, [], 3600);
    }

    public function getVerificationTypes(int $userId)
    {
        return $this->remember("verification_types_{$userId}", function () use ($userId) {
            return $this->model->where('user_id', $userId)
                ->select('verification_type', DB::raw('count(*) as count'))
                ->groupBy('verification_type')
                ->pluck('count', 'verification_type')
                ->toArray();
        }, [], 3600);
    }

    public function getSuccessRates(int $userId)
    {
        return $this->remember("success_rates_{$userId}", function () use ($userId) {
            $total = $this->model->where('user_id', $userId)->count();
            if ($total === 0) return 0;
            
            $verified = $this->model->where('user_id', $userId)
                ->where('verification_status', 'verified')
                ->count();
                
            return round(($verified / $total) * 100, 2);
        }, [], 3600);
    }

    public function getGasUsageAnalysis(int $userId)
    {
        return $this->remember("gas_usage_analysis_{$userId}", function () use ($userId) {
            return $this->model->where('user_id', $userId)
                ->select(
                    DB::raw('AVG(gas_used) as avg_gas'),
                    DB::raw('MAX(gas_used) as max_gas'),
                    DB::raw('MIN(gas_used) as min_gas')
                )
                ->first()
                ->toArray();
        }, [], 3600);
    }

    public function getBlockchainPerformance(int $userId)
    {
        return $this->remember("blockchain_performance_{$userId}", function () use ($userId) {
            return $this->model->where('user_id', $userId)
                ->select(
                    DB::raw('AVG(confirmation_blocks) as avg_confirmations'),
                    DB::raw('AVG(transaction_fee) as avg_fee')
                )
                ->first()
                ->toArray();
        }, [], 3600);
    }

    public function getFilteredVerifications(array $filters)
    {
        $query = $this->model->newQuery();
        
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['verification_status'])) {
            $query->where('verification_status', $filters['verification_status']);
        }

        if (isset($filters['blockchain_network'])) {
            $query->where('blockchain_network', $filters['blockchain_network']);
        }

        if (isset($filters['date_range'])) {
            switch ($filters['date_range']) {
                case 'last_week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'last_month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
                case 'last_quarter':
                    $query->where('created_at', '>=', now()->subQuarter());
                    break;
                case 'last_year':
                    $query->where('created_at', '>=', now()->subYear());
                    break;
                case 'custom':
                    if (isset($filters['start_date']) && isset($filters['end_date'])) {
                        $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
                    }
                    break;
            }
        }

        return $query->get();
    }
}
