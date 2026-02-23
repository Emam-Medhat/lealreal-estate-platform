<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AuctionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active auctions with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getActiveAuctions(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    /**
     * Get auctions created by a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getBySeller(int $userId): Collection;

    /**
     * Get auctions a user has participated in.
     *
     * @param int $userId
     * @return Collection
     */
    public function getParticipatedByUser(int $userId): Collection;

    /**
     * Get similar auctions based on property type or location.
     *
     * @param int $excludeAuctionId
     * @param int $propertyId
     * @param int $limit
     * @return Collection
     */
    public function getSimilar(int $excludeAuctionId, int $propertyId, int $limit = 3): Collection;

    /**
     * Get bid history for an auction.
     *
     * @param int $auctionId
     * @param int $limit
     * @return Collection
     */
    public function getBidHistory(int $auctionId, int $limit = 10): Collection;
}
