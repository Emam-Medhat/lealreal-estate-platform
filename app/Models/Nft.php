<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Nft extends Model
{
    use HasFactory;

    protected $fillable = [
        'token_id',
        'name',
        'description',
        'contract_address',
        'creator_address',
        'owner_address',
        'mint_tx_hash',
        'mint_block',
        'mint_timestamp',
        'token_uri',
        'image_url',
        'image_hash',
        'animation_url',
        'attributes',
        'metadata',
        'collection_name',
        'collection_id',
        'rarity',
        'price',
        'currency',
        'sale_type',
        'sale_start_time',
        'sale_end_time',
        'highest_bid',
        'highest_bidder',
        'bid_end_time',
        'transfer_count',
        'last_transfer_timestamp',
        'status',
        'is_verified',
        'verification_status',
        'tags',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'attributes' => 'array',
        'metadata' => 'array',
        'price' => 'decimal:18',
        'highest_bid' => 'decimal:18',
        'mint_timestamp' => 'datetime',
        'sale_start_time' => 'datetime',
        'sale_end_time' => 'datetime',
        'bid_end_time' => 'datetime',
        'last_transfer_timestamp' => 'datetime',
        'is_verified' => 'boolean',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeByContract($query, $address)
    {
        return $query->where('contract_address', $address);
    }

    public function scopeByOwner($query, $address)
    {
        return $query->where('owner_address', $address);
    }

    public function scopeByCreator($query, $address)
    {
        return $query->where('creator_address', $address);
    }

    public function scopeForSale($query)
    {
        return $query->where('sale_type', '!=', 'not_for_sale')
                    ->where('sale_end_time', '>', now());
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByRarity($query, $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return $this->price ? number_format($this->price, 8) : '0';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'minted' => 'تم الصك',
            'listed' => 'معروض للبيع',
            'sold' => 'تم البيع',
            'burned' => 'تم الحرق'
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getSaleTypeLabelAttribute()
    {
        $labels = [
            'fixed_price' => 'سعر ثابت',
            'auction' => 'مزاد',
            'not_for_sale' => 'غير معروض للبيع'
        ];
        return $labels[$this->sale_type] ?? $this->sale_type;
    }

    public function getRarityLabelAttribute()
    {
        $labels = [
            'common' => 'شائع',
            'uncommon' => 'غير شائع',
            'rare' => 'نادر',
            'epic' => 'أسطوري',
            'legendary' => 'خارق'
        ];
        return $labels[$this->rarity] ?? $this->rarity;
    }

    public function getNftUrlAttribute()
    {
        return "https://opensea.io/assets/ethereum/{$this->contract_address}/{$this->token_id}";
    }

    public function getContractUrlAttribute()
    {
        return "https://etherscan.io/address/{$this->contract_address}";
    }

    public function getIsForSaleAttribute()
    {
        return $this->sale_type !== 'not_for_sale' && 
               (!$this->sale_end_time || $this->sale_end_time > now());
    }

    // Methods
    public function isVerified()
    {
        return $this->is_verified;
    }

    public function isForSale()
    {
        return $this->is_for_sale;
    }

    // Relationships
    public function smartContract(): BelongsTo
    {
        return $this->belongsTo(SmartContract::class, 'contract_address', 'address');
    }

    public function blockchainRecord(): BelongsTo
    {
        return $this->belongsTo(BlockchainRecord::class, 'mint_tx_hash', 'transaction_hash');
    }

    // Static Methods
    public static function getStats()
    {
        return [
            'total_nfts' => self::count(),
            'verified_nfts' => self::verified()->count(),
            'for_sale_nfts' => self::forSale()->count(),
            'total_value' => self::sum('price'),
            'nfts_today' => self::whereDate('created_at', today())->count(),
            'unique_owners' => self::distinct('owner_address')->count(),
            'unique_creators' => self::distinct('creator_address')->count(),
        ];
    }

    public static function getRecentNfts($limit = 20)
    {
        return self::orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function searchNfts($query, $limit = 50)
    {
        return self::where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
    }
}
