<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NFTTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nft_id',
        'from_address',
        'to_address',
        'tx_hash',
        'gas_used',
        'cost',
        'transferred_at'
    ];

    protected $casts = [
        'gas_used' => 'integer',
        'cost' => 'decimal:8',
        'transferred_at' => 'datetime'
    ];

    public function nft()
    {
        return $this->belongsTo(NFT::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_address', 'wallet_address');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_address', 'wallet_address');
    }

    public function scopeForNFT($query, $nftId)
    {
        return $query->where('nft_id', $nftId);
    }

    public function scopeFromAddress($query, string $address)
    {
        return $query->where('from_address', $address);
    }

    public function scopeToAddress($query, string $address)
    {
        return $query->where('to_address', $address);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('transferred_at', '>=', now()->subDays($days));
    }

    public function getExplorerUrl(): string
    {
        $nft = $this->nft;
        if (!$nft) {
            return '#';
        }

        $explorers = [
            'ethereum' => 'https://etherscan.io',
            'polygon' => 'https://polygonscan.com',
            'bsc' => 'https://bscscan.com',
            'arbitrum' => 'https://arbiscan.io',
            'optimism' => 'https://optimistic.etherscan.io'
        ];

        $baseUrl = $explorers[$nft->network] ?? 'https://etherscan.io';
        return "{$baseUrl}/tx/{$this->tx_hash}";
    }

    public function getFormattedCost(): string
    {
        $currency = Currency::where('code', 'ETH')->first();
        return $currency ? $currency->formatAmount($this->cost) : number_format($this->cost, 8) . ' ETH';
    }

    public function isValidTransfer(): bool
    {
        return !empty($this->tx_hash) && 
               !empty($this->from_address) && 
               !empty($this->to_address) && 
               $this->from_address !== $this->to_address;
    }

    public static function getTransferHistory(int $nftId, int $limit = 50): array
    {
        return static::where('nft_id', $nftId)
            ->with(['fromUser', 'toUser'])
            ->orderBy('transferred_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($transfer) {
                return [
                    'id' => $transfer->id,
                    'from_address' => $transfer->from_address,
                    'to_address' => $transfer->to_address,
                    'from_user' => $transfer->fromUser ? [
                        'id' => $transfer->fromUser->id,
                        'name' => $transfer->fromUser->name
                    ] : null,
                    'to_user' => $transfer->toUser ? [
                        'id' => $transfer->toUser->id,
                        'name' => $transfer->toUser->name
                    ] : null,
                    'tx_hash' => $transfer->tx_hash,
                    'gas_used' => $transfer->gas_used,
                    'cost' => $transfer->cost,
                    'transferred_at' => $transfer->transferred_at->toISOString(),
                    'explorer_url' => $transfer->getExplorerUrl()
                ];
            })
            ->toArray();
    }
}
