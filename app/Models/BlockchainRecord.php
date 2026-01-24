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
        'block_hash',
        'block_number',
        'transaction_hash',
        'from_address',
        'to_address',
        'value',
        'gas_used',
        'gas_price',
        'nonce',
        'block_timestamp',
        'status',
        'data',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'value' => 'decimal:18',
        'gas_price' => 'decimal:18',
        'data' => 'array',
        'block_timestamp' => 'datetime',
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
        return $query->where('from_address', $address)
                    ->orWhere('to_address', $address);
    }

    public function scopeByBlockRange($query, $from, $to)
    {
        return $query->whereBetween('block_number', [$from, $to]);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hours));
    }

    // Accessors
    public function getValueAttribute($value)
    {
        return $value ? number_format($value, 18, '.', '') : '0';
    }

    public function getGasPriceAttribute($value)
    {
        return $value ? number_format($value, 18, '.', '') : '0';
    }

    public function getFormattedValueAttribute()
    {
        return $this->value ? number_format($this->value, 8) : '0';
    }

    public function getFormattedGasPriceAttribute()
    {
        return $this->gas_price ? number_format($this->gas_price, 9) : '0';
    }

    public function getGasCostAttribute()
    {
        return $this->gas_used * $this->gas_price;
    }

    public function getFormattedGasCostAttribute()
    {
        return number_format($this->gas_cost, 8);
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'failed' => 'فشل',
            'reverted' => 'تم الرجوع'
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getTransactionUrlAttribute()
    {
        return "https://etherscan.io/tx/{$this->transaction_hash}";
    }

    public function getBlockUrlAttribute()
    {
        return "https://etherscan.io/block/{$this->block_number}";
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

    public function isFailed()
    {
        return in_array($this->status, ['failed', 'reverted']);
    }

    public function getConfirmationTime()
    {
        return $this->created_at->diffInSeconds($this->block_timestamp);
    }

    public function getFormattedConfirmationTime()
    {
        $seconds = $this->getConfirmationTime();
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
            'failed_records' => self::byStatus('failed')->count(),
            'total_value' => self::where('status', 'confirmed')->sum('value'),
            'total_gas_cost' => self::where('status', 'confirmed')->sumRaw('gas_used * gas_price'),
            'avg_gas_price' => self::where('status', 'confirmed')->avg('gas_price'),
            'latest_block' => self::max('block_number'),
            'records_today' => self::whereDate('created_at', today())->count(),
            'records_this_week' => self::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'records_this_month' => self::whereMonth('created_at', now()->month)->count(),
        ];
    }

    public static function getTopTransactions($limit = 10)
    {
        return self::where('status', 'confirmed')
                   ->orderBy('value', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getRecentTransactions($limit = 20)
    {
        return self::orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getTransactionsByAddress($address, $limit = 50)
    {
        return self::byAddress($address)
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getBlockTransactions($blockNumber)
    {
        return self::where('block_number', $blockNumber)
                   ->orderBy('nonce')
                   ->get();
    }

    public static function getFailedTransactions($limit = 20)
    {
        return self::where('status', 'failed')
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getPendingTransactions($limit = 20)
    {
        return self::where('status', 'pending')
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getHighGasTransactions($limit = 20)
    {
        return self::where('status', 'confirmed')
                   ->orderBy('gas_price', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function searchTransactions($query, $limit = 50)
    {
        return self::where(function($q) use ($query) {
                    $q->where('transaction_hash', 'like', "%{$query}%")
                      ->orWhere('block_hash', 'like', "%{$query}%")
                      ->orWhere('from_address', 'like', "%{$query}%")
                      ->orWhere('to_address', 'like', "%{$query}%");
                })
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
    }

    public static function getDailyTransactionCount($days = 30)
    {
        return self::where('created_at', '>=', now()->subDays($days))
                   ->groupBy('date')
                   ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                   ->orderBy('date', 'desc')
                   ->get();
    }

    public static function getHourlyTransactionCount($hours = 24)
    {
        return self::where('created_at', '>=', now()->subHours($hours))
                   ->groupBy('hour')
                   ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                   ->orderBy('hour', 'desc')
                   ->get();
    }

    public static function getAddressStats($address)
    {
        $sent = self::where('from_address', $address)->sum('value');
        $received = self::where('to_address', $address)->sum('value');
        $transactions = self::byAddress($address)->count();
        
        return [
            'total_sent' => $sent,
            'total_received' => $received,
            'net_balance' => $received - $sent,
            'total_transactions' => $transactions,
            'sent_transactions' => self::where('from_address', $address)->count(),
            'received_transactions' => self::where('to_address', $address)->count(),
            'first_transaction' => self::byAddress($address)->min('created_at'),
            'last_transaction' => self::byAddress($address)->max('created_at'),
        ];
    }

    // Export Methods
    public static function exportToCsv($records)
    {
        $headers = [
            'Block Hash', 'Block Number', 'Transaction Hash', 'From Address', 
            'To Address', 'Value', 'Gas Used', 'Gas Price', 'Status', 
            'Block Timestamp', 'Created At'
        ];

        $rows = $records->map(function ($record) {
            return [
                $record->block_hash,
                $record->block_number,
                $record->transaction_hash,
                $record->from_address,
                $record->to_address,
                $record->formatted_value,
                $record->gas_used,
                $record->formatted_gas_price,
                $record->status_label,
                $record->block_timestamp,
                $record->created_at
            ];
        });

        return collect([$headers])->concat($rows);
    }

    // Validation Methods
    public function validateTransaction()
    {
        $errors = [];
        
        if (empty($this->transaction_hash)) {
            $errors[] = 'Transaction hash is required';
        }
        
        if (empty($this->from_address)) {
            $errors[] = 'From address is required';
        }
        
        if (empty($this->to_address)) {
            $errors[] = 'To address is required';
        }
        
        if ($this->value < 0) {
            $errors[] = 'Value must be positive';
        }
        
        if ($this->gas_used < 0) {
            $errors[] = 'Gas used must be positive';
        }
        
        if ($this->gas_price < 0) {
            $errors[] = 'Gas price must be positive';
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
            'block_number' => $this->block_number,
            'transaction_hash' => $this->transaction_hash,
            'status' => $this->status,
            'gas_used' => $this->gas_used,
            'gas_price' => $this->gas_price
        ];
    }

    public function getTransactionReceipt()
    {
        // This would get actual transaction receipt from blockchain
        return [
            'transaction_hash' => $this->transaction_hash,
            'block_hash' => $this->block_hash,
            'block_number' => $this->block_number,
            'gas_used' => $this->gas_used,
            'gas_price' => $this->gas_price,
            'status' => $this->status,
            'logs' => $this->data['logs'] ?? [],
            'contract_address' => $this->data['contract_address'] ?? null
        ];
    }
}
