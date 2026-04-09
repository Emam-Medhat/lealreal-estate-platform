<?php

namespace App\Repositories\Eloquent;

use App\Models\Nft;
use App\Repositories\Contracts\NftRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class NftRepository extends BaseRepository implements NftRepositoryInterface
{
    /**
     * NftRepository constructor.
     *
     * @param Nft $model
     */
    public function __construct(Nft $model)
    {
        parent::__construct($model);
    }

    /**
     * Get recent NFTs.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->remember("nfts_recent_{$limit}", function () use ($limit) {
            return $this->model->with(['owner', 'smartContract', 'property'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, [], 3600);
    }

    /**
     * Get NFTs by standard.
     *
     * @param string $standard
     * @param int $limit
     * @return Collection
     */
    public function getByStandard(string $standard, int $limit = 10): Collection
    {
        return $this->remember("nfts_standard_{$standard}_{$limit}", function () use ($standard, $limit) {
            return $this->model->with(['owner', 'smartContract'])
                ->where('token_standard', $standard)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, [], 3600);
    }

    public function getByPropertyId(int $propertyId): Collection
    {
        return $this->remember("nfts_property_{$propertyId}", function () use ($propertyId) {
            return $this->model->where('property_id', $propertyId)
                ->with(['property', 'owner'])
                ->orderBy('created_at', 'desc')
                ->get();
        }, [], 3600);
    }

    public function getPaginated(int $perPage = 20)
    {
        return $this->model->with(['owner'])->latest()->paginate($perPage);
    }

    public function countListed()
    {
        return $this->remember('nfts_count_listed', function () {
            return $this->model->where('status', 'listed')->count();
        }, [], 300);
    }

    public function sumPrice()
    {
        return $this->remember('nfts_sum_price', function () {
            return $this->model->sum('price');
        }, [], 300);
    }

    public function countOwners()
    {
        return $this->remember('nfts_count_owners', function () {
            return $this->model->distinct('owner_address')->count('owner_address');
        }, [], 300);
    }

    public function getFiltered(array $filters, int $perPage = 15)
    {
        $query = $this->model->with(['owner', 'smartContract', 'property']);

        if (isset($filters['standard'])) {
            $query->where('token_standard', $filters['standard']);
        }

        if (isset($filters['network'])) {
            $query->where('network', $filters['network']);
        }

        if (isset($filters['collection_id'])) {
            $query->where('collection_id', $filters['collection_id']);
        }

        if (isset($filters['owner_address'])) {
            $query->where('owner_address', $filters['owner_address']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getStats()
    {
        return $this->remember('nfts_stats_all', function () {
            $stats = $this->model->selectRaw('
                COUNT(*) as total_nfts,
                SUM(CASE WHEN status = "minted" THEN 1 ELSE 0 END) as minted_nfts,
                SUM(CASE WHEN status = "transferred" THEN 1 ELSE 0 END) as transferred_nfts,
                SUM(CASE WHEN status = "burned" THEN 1 ELSE 0 END) as burned_nfts,
                SUM(CASE WHEN status = "listed" THEN 1 ELSE 0 END) as listed_nfts,
                SUM(price) as total_volume,
                COUNT(DISTINCT owner_address) as unique_owners,
                SUM(gas_used) as gas_used_total,
                SUM(minting_cost) as minting_costs_total
            ')->first();

            return [
                'total_nfts' => $stats->total_nfts ?? 0,
                'minted_nfts' => $stats->minted_nfts ?? 0,
                'transferred_nfts' => $stats->transferred_nfts ?? 0,
                'burned_nfts' => $stats->burned_nfts ?? 0,
                'listed_nfts' => $stats->listed_nfts ?? 0,
                'total_volume' => $stats->total_volume ?? 0,
                'unique_owners' => $stats->unique_owners ?? 0,
                'gas_used_total' => $stats->gas_used_total ?? 0,
                'minting_costs_total' => $stats->minting_costs_total ?? 0,
                'network_stats' => $this->getNetworkStats(),
                'standard_stats' => $this->getStandardStats()
            ];
        }, [], 300);
    }

    public function search(array $filters, int $perPage = 20)
    {
        $query = $this->model->with(['owner']);
        
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('token_id', 'like', "%{$search}%");
            });
        }
        
        if (isset($filters['collection_id'])) {
            $query->where('collection_id', $filters['collection_id']);
        }
        
        if (isset($filters['creator_address'])) {
            $query->where('creator_address', $filters['creator_address']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getNetworkStats()
    {
        return $this->remember('nfts_stats_network', function () {
            return $this->model->select('network', DB::raw('count(*) as count'))
                ->groupBy('network')
                ->pluck('count', 'network')
                ->toArray();
        }, [], 3600);
    }

    public function getStandardStats()
    {
        return $this->remember('nfts_stats_standard', function () {
            return $this->model->select('token_standard', DB::raw('count(*) as count'))
                ->groupBy('token_standard')
                ->pluck('count', 'token_standard')
                ->toArray();
        }, [], 3600);
    }
}
