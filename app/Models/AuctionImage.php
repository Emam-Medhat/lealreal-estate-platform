<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'path',
        'caption',
        'is_primary',
        'sort_order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function getUrl(): string
    {
        return asset('storage/' . $this->path);
    }

    public function getThumbnailUrl(): string
    {
        return asset('storage/' . str_replace('auctions/', 'auctions/thumbnails/', $this->path));
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByAuction($query, $auctionId)
    {
        return $query->where('auction_id', $auctionId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
