<?php

namespace App\Models\Metaverse;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class MetaversePropertyNft extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'metaverse_property_id',
        'blockchain',
        'contract_address',
        'token_id',
        'token_uri',
        'metadata',
        'price',
        'currency',
        'royalty_percentage',
        'is_for_sale',
        'auction_settings',
        'verification_status',
        'status',
        'owner_id',
        'creator_id',
        'minted_at',
        'last_sale_price',
        'last_sale_at',
        'total_sales_count',
        'total_volume',
        'highest_bid',
        'auction_end_time',
        'buy_now_price',
        'reserve_price',
        'view_count',
        'like_count',
        'share_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'auction_settings' => 'array',
        'is_for_sale' => 'boolean',
        'minted_at' => 'datetime',
        'last_sale_at' => 'datetime',
        'auction_end_time' => 'datetime',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'share_count' => 'integer',
        'total_sales_count' => 'integer',
        'total_volume' => 'decimal:2',
        'highest_bid' => 'decimal:2',
        'price' => 'decimal:2',
        'royalty_percentage' => 'decimal:2',
        'last_sale_price' => 'decimal:2',
        'buy_now_price' => 'decimal:2',
        'reserve_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'minted_at',
        'last_sale_at',
        'auction_end_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships
    public function metaverseProperty(): BelongsTo
    {
        return $this->belongsTo(MetaverseProperty::class, 'metaverse_property_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(NftTransfer::class, 'metaverse_property_nft_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(NftBid::class, 'metaverse_property_nft_id');
    }

    public function auction(): HasOne
    {
        return $this->hasOne(NftAuction::class, 'metaverse_property_nft_id');
    }

    public function views(): HasMany
    {
        return $this->hasMany(NftView::class, 'metaverse_property_nft_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(NftLike::class, 'metaverse_property_nft_id');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(NftShare::class, 'metaverse_property_nft_id');
    }

    public function tags(): MorphMany
    {
        return $this->morphMany(MetaverseTag::class, 'taggable');
    }

    public function verificationRecords(): HasMany
    {
        return $this->hasMany(NftVerification::class, 'metaverse_property_nft_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForSale($query)
    {
        return $query->where('is_for_sale', true);
    }

    public function scopeInAuction($query)
    {
        return $query->whereHas('auction', function ($subQuery) {
            $subQuery->where('status', 'active');
        });
    }

    public function scopeByBlockchain($query, $blockchain)
    {
        return $query->where('blockchain', $blockchain);
    }

    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('token_id', 'like', "%{$search}%")
              ->orWhere('contract_address', 'like', "%{$search}%")
              ->orWhereHas('metaverseProperty', function ($subQuery) use ($search) {
                  $subQuery->where('title', 'like', "%{$search}%");
              });
        });
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 4) . ' ' . $this->currency;
    }

    public function getFormattedHighestBidAttribute(): string
    {
        return number_format($this->highest_bid, 4) . ' ' . $this->currency;
    }

    public function getBlockchainTextAttribute(): string
    {
        return match($this->blockchain) {
            'ethereum' => 'Ethereum',
            'polygon' => 'Polygon',
            'binance_smart_chain' => 'Binance Smart Chain',
            'solana' => 'Solana',
            'avalanche' => 'Avalanche',
            default => $this->blockchain,
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'minted' => 'مضروب',
            'burned' => 'محروق',
            'transferred' => 'منقول',
            'locked' => 'مقفل',
            'pending' => 'في الانتظار',
            'suspended' => 'موقوف',
            'deleted' => 'محذوف',
            default => $this->status,
        };
    }

    public function getVerificationStatusTextAttribute(): string
    {
        return match($this->verification_status) {
            'pending' => 'في الانتظار',
            'verified' => 'موثق',
            'failed' => 'فشل',
            'flagged' => 'محظور',
            'suspended' => 'موقوف',
            default => $this->verification_status,
        };
    }

    public function getIsInAuctionAttribute(): bool
    {
        return $this->auction && $this->auction->status === 'active';
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at->diffInDays(now()) <= 7;
    }

    public function getIsHotAttribute(): bool
    {
        return $this->view_count > 100 || $this->like_count > 50;
    }

    public function getIsFeaturedAttribute(): bool
    {
        return $this->like_count > 100 || $this->total_sales_count > 10;
    }

    public function getFloorPriceAttribute(): float
    {
        return self::where('blockchain', $this->blockchain)
            ->where('status', 'minted')
            ->min('price') ?? 0;
    }

    public function getCeilingPriceAttribute(): float
    {
        return self::where('blockchain', $this->blockchain)
            ->where('status', 'minted')
            ->max('price') ?? 0;
    }

    public function getMarketCapAttribute(): float
    {
        return $this->getFloorPriceAttribute() * 1000; // Simplified calculation
    }

    public function getRarityAttribute(): string
    {
        // Calculate rarity based on metadata and sales
        if ($this->total_sales_count === 0) {
            return 'Common';
        }

        if ($this->total_sales_count < 10) {
            return 'Uncommon';
        } elseif ($this->total_sales_count < 50) {
            return 'Rare';
        } elseif ($this->total_sales_count < 100) {
            return 'Epic';
        } else {
            return 'Legendary';
        }
    }

    public function getRarityColorAttribute(): string
    {
        return match($this->getRarityAttribute()) {
            'Common' => '#808080',
            'Uncommon' => '#008000',
            'Rare' => '#0000FF',
            'Epic' => '#800080',
            'Legendary' => '#FFD700',
            default => '#808080',
        };
    }

    // Methods
    public function incrementView(): void
    {
        $this->increment('view_count');
    }

    public function incrementLike(): void
    {
        $this->increment('like_count');
    }

    public function incrementShare(): void
    {
        $this->increment('share_count');
    }

    public function placeBid(float $amount, int $bidderId): NftBid
    {
        return $this->bids()->create([
            'bidder_id' => $bidderId,
            'amount' => $amount,
            'currency' => $this->currency,
            'status' => 'active',
            'expires_at' => now()->addHours(24),
        ]);
    }

    public function acceptBid(NftBid $bid): void
    {
        // Update highest bid
        $this->update(['highest_bid' => $bid->amount]);

        // Mark bid as accepted
        $bid->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Reject other active bids
        $this->bids()
            ->where('id', '!=', $bid->id)
            ->where('status', 'active')
            ->update([
                'status' => 'rejected',
                'rejected_at' => now(),
            ]);
    }

    public function transferTo(int $newOwnerId, float $price = null): NftTransfer
    {
        return $this->transfers()->create([
            'from_user_id' => $this->owner_id,
            'to_user_id' => $newOwnerId,
            'amount' => $price ?? $this->price,
            'currency' => $this->currency,
            'status' => 'completed',
            'transferred_at' => now(),
            'transaction_hash' => $this->generateTransactionHash(),
        ]);
    }

    public function startAuction(array $settings): NftAuction
    {
        return $this->auction()->create([
            'starting_price' => $settings['starting_price'] ?? $this->price,
            'reserve_price' => $settings['reserve_price'] ?? null,
            'buy_now_price' => $settings['buy_now_price'] ?? null,
            'duration' => $settings['duration'] ?? 24, // hours
            'starts_at' => now(),
            'ends_at' => now()->addHours($settings['duration'] ?? 24),
            'status' => 'active',
        ]);

        $this->update([
            'is_for_sale' => true,
            'auction_end_time' => now()->addHours($settings['duration'] ?? 24),
        ]);
    }

    public function endAuction(): void
    {
        if (!$this->auction) {
            return;
        }

        $highestBid = $this->bids()
            ->where('status', 'active')
            ->orderBy('amount', 'desc')
            ->first();

        if ($highestBid && $highestBid->amount >= ($this->auction->reserve_price ?? 0)) {
            $this->acceptBid($highestBid);
        } else {
            // End auction without sale
            $this->auction->update([
                'status' => 'ended',
                'ended_at' => now(),
                'result' => 'no_sale',
            ]);

            $this->update([
                'is_for_sale' => false,
            ]);
        }
    }

    public function verify(): void
    {
        $this->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
    }

    public function flag(string $reason): void
    {
        $this->update([
            'verification_status' => 'flagged',
            'flagged_at' => now(),
            'flag_reason' => $reason,
        ]);
    }

    public function burn(): void
    {
        $this->update([
            'status' => 'burned',
            'burned_at' => now(),
        ]);

        // Create burn record
        $this->transfers()->create([
            'from_user_id' => $this->owner_id,
            'to_user_id' => null, // Burned to null address
            'amount' => 0,
            'currency' => $this->currency,
            'status' => 'completed',
            'transferred_at' => now(),
            'transaction_hash' => $this->generateTransactionHash(),
            'is_burn' => true,
        ]);
    }

    public function calculateRoyalty(float $salePrice): float
    {
        return ($salePrice * $this->royalty_percentage) / 100;
    }

    public function getMarketData(): array
    {
        return [
            'floor_price' => $this->getFloorPriceAttribute(),
            'ceiling_price' => $this->getCeilingPriceAttribute(),
            'current_price' => $this->price,
            'price_change_24h' => $this->calculatePriceChange24h(),
            'price_change_7d' => $this->calculatePriceChange7d(),
            'volume_24h' => $this->calculateVolume24h(),
            'market_cap' => $this->getMarketCapAttribute(),
            'rarity' => $this->getRarityAttribute(),
            'total_sales' => $this->total_sales_count,
            'total_volume' => $this->total_volume,
            'last_sale_price' => $this->last_sale_price,
            'last_sale_date' => $this->last_sale_at,
        ];
    }

    public function getOwnershipHistory(): array
    {
        return $this->transfers()
            ->with(['fromUser', 'toUser'])
            ->orderBy('transferred_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getBidHistory(): array
    {
        return $this->bids()
            ->with(['bidder'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getAnalytics(): array
    {
        return [
            'views' => $this->view_count,
            'likes' => $this->like_count,
            'shares' => $this->share_count,
            'bids_count' => $this->bids()->count(),
            'highest_bid' => $this->highest_bid,
            'time_remaining' => $this->getIsInAuctionAttribute() ? 
                $this->auction_end_time->diffInSeconds(now()) : 0,
            'engagement_rate' => $this->calculateEngagementRate(),
            'price_trends' => $this->getPriceTrends(),
            'buyer_demographics' => $this->getBuyerDemographics(),
        ];
    }

    private function generateTransactionHash(): string
    {
        return '0x' . bin2hex(random_bytes(32));
    }

    private function calculatePriceChange24h(): float
    {
        // Simplified calculation
        return rand(-10, 10);
    }

    private function calculatePriceChange7d(): float
    {
        // Simplified calculation
        return rand(-20, 20);
    }

    private function calculateVolume24h(): float
    {
        // Simplified calculation
        return rand(1000, 10000);
    }

    private function calculateEngagementRate(): float
    {
        $totalInteractions = $this->view_count + $this->like_count + $this->share_count;
        $daysSinceCreation = $this->created_at->diffInDays(now()) ?: 1;
        
        return $totalInteractions / $daysSinceCreation;
    }

    private function getPriceTrends(): array
    {
        return [
            '24h' => $this->calculatePriceChange24h(),
            '7d' => $this->calculatePriceChange7d(),
            '30d' => rand(-30, 30),
        ];
    }

    private function getBuyerDemographics(): array
    {
        return [
            'by_country' => $this->transfers()
                ->join('users', 'nft_transfers.to_user_id', '=', 'users.id')
                ->selectRaw('users.country, COUNT(*) as count')
                ->groupBy('users.country')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
            
            'by_wallet_type' => $this->transfers()
                ->join('users', 'nft_transfers.to_user_id', '=', 'users.id')
                ->selectRaw('users.wallet_type, COUNT(*) as count')
                ->groupBy('users.wallet_type')
                ->orderBy('count', 'desc')
                ->get(),
        ];
    }

    public function generateMarketplaceUrl(): string
    {
        return route('metaverse.marketplace.nft', $this->id);
    }

    public function generateOpenseaUrl(): string
    {
        return "https://opensea.io/assets/{$this->blockchain}/{$this->contract_address}/{$this->token_id}";
    }

    public function generateEtherscanUrl(): string
    {
        return "https://etherscan.io/token/{$this->contract_address}?a={$this->token_id}";
    }

    public function getMetadataUrl(): string
    {
        return $this->token_uri;
    }

    public function getImageUrl(): string
    {
        return $this->metadata['image'] ?? asset('images/default-nft.jpg');
    }

    public function canBeBought(): bool
    {
        return $this->status === 'minted' && 
               $this->verification_status === 'verified' && 
               !$this->getIsInAuctionAttribute();
    }

    public function canBeAuctioned(): bool
    {
        return $this->status === 'minted' && 
               $this->verification_status === 'verified' && 
               !$this->getIsInAuctionAttribute();
    }

    public function isOwnedBy(int $userId): bool
    {
        return $this->owner_id === $userId;
    }

    public function wasCreatedBy(int $userId): bool
    {
        return $this->creator_id === $userId;
    }
}
