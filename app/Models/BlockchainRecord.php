<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class BlockchainRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'hash',
        'previous_hash',
        'height',
        'data',
        'type',
        'difficulty',
        'nonce',
        'miner',
        'timestamp',
        'transaction_count',
        'size',
        'merkle_root',
        'metadata',
        'status',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'data' => 'array',
        'metadata' => 'array',
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByAddress($query, $address)
    {
        // This scope doesn't apply to blocks, but to transactions
        return $query;
    }

    public function scopeByBlockRange($query, $from, $to)
    {
        return $query->whereBetween('height', [$from, $to]);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hours));
    }

    // Accessors
    public function getFormattedSizeAttribute()
    {
        return number_format($this->size, 2) . ' KB';
    }

    public function getFormattedDifficultyAttribute()
    {
        return number_format($this->difficulty);
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'orphaned' => 'يتيم'
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getBlockUrlAttribute()
    {
        return "https://etherscan.io/block/{$this->height}";
    }

    // Methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isOrphaned()
    {
        return $this->status === 'orphaned';
    }

    public function getMiningTime()
    {
        return $this->created_at->diffInSeconds($this->timestamp);
    }

    public function getFormattedMiningTime()
    {
        $seconds = $this->getMiningTime();
        if ($seconds < 60) {
            return "{$seconds} ثانية";
        } elseif ($seconds < 3600) {
            return floor($seconds / 60) . " دقيقة";
        } else {
            return floor($seconds / 3600) . " ساعة";
        }
    }

    // Relationships
    public function smartContracts(): HasMany
    {
        return $this->hasMany(SmartContract::class, 'deployment_tx_hash', 'transaction_hash');
    }

    public function nfts(): HasMany
    {
        return $this->hasMany(Nft::class, 'mint_tx_hash', 'transaction_hash');
    }

    public function cryptoTransactions(): HasMany
    {
        return $this->hasMany(CryptoTransaction::class, 'tx_hash', 'transaction_hash');
    }

    // Static Methods
    public static function getStats()
    {
        return [
            'total_records' => self::count(),
            'pending_records' => self::byStatus('pending')->count(),
            'confirmed_records' => self::byStatus('confirmed')->count(),
            'orphaned_records' => self::byStatus('orphaned')->count(),
            'total_transactions' => self::sum('transaction_count'),
            'total_size' => self::sum('size'),
            'average_size' => self::avg('size'),
            'average_difficulty' => self::avg('difficulty'),
            'latest_height' => self::max('height'),
            'total_miners' => self::distinct('miner')->count('miner'),
            'records_today' => self::whereDate('created_at', today())->count(),
            'records_this_week' => self::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'records_this_month' => self::whereMonth('created_at', now()->month)->count(),
        ];
    }

    public static function getTopBlocks($limit = 10)
    {
        return self::where('status', 'confirmed')
                   ->orderBy('transaction_count', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getRecentBlocks($limit = 20)
    {
        return self::orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getBlocksByMiner($miner, $limit = 50)
    {
        return self::where('miner', $miner)
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getBlockByHeight($height)
    {
        return self::where('height', $height)->first();
    }

    public static function getPendingBlocks($limit = 20)
    {
        return self::where('status', 'pending')
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getOrphanedBlocks($limit = 20)
    {
        return self::where('status', 'orphaned')
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getHighDifficultyBlocks($limit = 20)
    {
        return self::where('status', 'confirmed')
                   ->orderBy('difficulty', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function searchBlocks($query, $limit = 50)
    {
        return self::where(function($q) use ($query) {
                    $q->where('hash', 'like', "%{$query}%")
                      ->orWhere('previous_hash', 'like', "%{$query}%")
                      ->orWhere('miner', 'like', "%{$query}%")
                      ->orWhere('height', 'like', "%{$query}%");
                })
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
    }

    public static function getDailyBlockCount($days = 30)
    {
        return self::where('created_at', '>=', now()->subDays($days))
                   ->groupBy('date')
                   ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                   ->orderBy('date', 'desc')
                   ->get();
    }

    public static function getHourlyBlockCount($hours = 24)
    {
        return self::where('created_at', '>=', now()->subHours($hours))
                   ->groupBy('hour')
                   ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                   ->orderBy('hour', 'desc')
                   ->get();
    }

    public static function getMinerStats($miner)
    {
        $blocks = self::where('miner', $miner)->count();
        $transactions = self::where('miner', $miner)->sum('transaction_count');
        $totalSize = self::where('miner', $miner)->sum('size');
        
        return [
            'total_blocks' => $blocks,
            'total_transactions' => $transactions,
            'total_size' => $totalSize,
            'average_transactions_per_block' => $blocks > 0 ? $transactions / $blocks : 0,
            'first_block' => self::where('miner', $miner)->min('height'),
            'last_block' => self::where('miner', $miner)->max('height'),
        ];
    }

    // Export Methods
    public static function exportToCsv($records)
    {
        $headers = [
            'Hash', 'Previous Hash', 'Height', 'Type', 'Miner', 
            'Transaction Count', 'Size', 'Difficulty', 'Status', 
            'Timestamp', 'Created At'
        ];

        $rows = $records->map(function ($record) {
            return [
                $record->hash,
                $record->previous_hash,
                $record->height,
                $record->type,
                $record->miner,
                $record->transaction_count,
                $record->formatted_size,
                $record->formatted_difficulty,
                $record->status_label,
                $record->timestamp,
                $record->created_at
            ];
        });

        return collect([$headers])->concat($rows);
    }

    // Validation Methods
    public function validateBlock()
    {
        $errors = [];
        
        if (empty($this->hash)) {
            $errors[] = 'Block hash is required';
        }
        
        if (empty($this->previous_hash) && $this->height > 1) {
            $errors[] = 'Previous hash is required for blocks other than genesis';
        }
        
        if ($this->height < 1) {
            $errors[] = 'Height must be positive';
        }
        
        if ($this->difficulty < 0) {
            $errors[] = 'Difficulty must be positive';
        }
        
        if ($this->transaction_count < 0) {
            $errors[] = 'Transaction count must be positive';
        }
        
        if ($this->size < 0) {
            $errors[] = 'Size must be positive';
        }
        
        return $errors;
    }

    // Blockchain Methods
    public function verifyOnChain()
    {
        // This would integrate with actual blockchain API
        // For now, return simulated verification
        return [
            'verified' => true,
            'height' => $this->height,
            'hash' => $this->hash,
            'previous_hash' => $this->previous_hash,
            'difficulty' => $this->difficulty,
            'transaction_count' => $this->transaction_count
        ];
    }

    public function getBlockInfo()
    {
        // This would get actual block info from blockchain
        return [
            'hash' => $this->hash,
            'previous_hash' => $this->previous_hash,
            'height' => $this->height,
            'miner' => $this->miner,
            'transaction_count' => $this->transaction_count,
            'size' => $this->size,
            'difficulty' => $this->difficulty,
            'timestamp' => $this->timestamp,
            'status' => $this->status,
            'merkle_root' => $this->merkle_root,
            'nonce' => $this->nonce,
            'data' => $this->data ?? []
        ];
    }
}
