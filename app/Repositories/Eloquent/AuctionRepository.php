<?php

namespace App\Repositories\Eloquent;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Repositories\Contracts\AuctionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AuctionRepository extends BaseRepository implements AuctionRepositoryInterface
{
    public function __construct(Auction $model)
    {
        parent::__construct($model);
    }

    public function getActiveAuctions(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->remember('getActiveAuctions', function () use ($filters, $perPage) {
            $query = $this->model->with(['property', 'highestBid.user', 'participants.user'])
                ->where('status', 'active');

            if (isset($filters['type']) && $filters['type']) {
                $query->where('type', $filters['type']);
            }
            
            if (isset($filters['min_price']) && $filters['min_price']) {
                $query->where('starting_price', '>=', $filters['min_price']);
            }
            
            if (isset($filters['max_price']) && $filters['max_price']) {
                $query->where('starting_price', '<=', $filters['max_price']);
            }
            
            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['sort'])) {
                if ($filters['sort'] === 'ending_soon') {
                    $query->orderBy('end_time', 'asc');
                } elseif ($filters['sort'] === 'newest') {
                    $query->orderBy('created_at', 'desc');
                } elseif ($filters['sort'] === 'price_high') {
                    $query->orderBy('current_price', 'desc');
                } else {
                    $query->orderBy('created_at', 'desc');
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            return $query->paginate($perPage);
        }, ['filters' => $filters, 'perPage' => $perPage], 300); // Cache for 5 minutes
    }

    public function getBySeller(int $userId): Collection
    {
        return $this->remember('getBySeller', function () use ($userId) {
            return $this->model->where('seller_id', $userId)
                ->with(['property', 'highestBid.user'])
                ->orderBy('created_at', 'desc')
                ->get();
        }, ['userId' => $userId]);
    }

    public function getParticipatedByUser(int $userId): Collection
    {
        return $this->remember('getParticipatedByUser', function () use ($userId) {
            return $this->model->whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->with(['property', 'highestBid.user'])
              ->orderBy('created_at', 'desc')
              ->get();
        }, ['userId' => $userId]);
    }

    public function getSimilar(int $excludeAuctionId, int $propertyId, int $limit = 3): Collection
    {
        return $this->remember('getSimilar', function () use ($excludeAuctionId, $propertyId, $limit) {
            return $this->model->where('property_id', '!=', $propertyId)
                ->where('status', 'active')
                ->with('property')
                ->limit($limit)
                ->get();
        }, ['excludeAuctionId' => $excludeAuctionId, 'propertyId' => $propertyId, 'limit' => $limit], 1800);
    }

    public function getBidHistory(int $auctionId, int $limit = 10): Collection
    {
        return $this->remember('getBidHistory', function () use ($auctionId, $limit) {
            return AuctionBid::where('auction_id', $auctionId)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, ['auctionId' => $auctionId, 'limit' => $limit], 60); // Short cache for bids
    }
}
