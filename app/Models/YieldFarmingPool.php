<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class YieldFarmingPool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'contract_address',
        'token0_address',
        'token1_address',
        'lp_token_address',
        'reward_token_address',
        'total_liquidity',
        'total_rewards',
        'apr',
        'apy',
        'reward_rate',
        'reward_period',
        'minimum_deposit',
        'maximum_deposit',
        'deposit_fee',
        'withdrawal_fee',
        'harvest_fee',
        'lock_period',
        'compounding_enabled',
        'auto_compound',
        'total_farmers',
        'active_farmers',
        'pool_status',
        'start_time',
        'end_time',
        'last_reward_timestamp',
        'total_deposits',
        'total_withdrawals',
        'total_harvests',
        'average_deposit_amount',
        'total_reward_paid',
        'pending_rewards',
        'fee_collected',
        'is_verified',
        'verification_status',
        'tags',
        'metadata',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'total_liquidity' => 'decimal:18',
        'total_rewards' => 'decimal:18',
        'apr' => 'decimal:8',
        'apy' => 'decimal:8',
        'reward_rate' => 'decimal:18',
        'minimum_deposit' => 'decimal:18',
        'maximum_deposit' => 'decimal:18',
        'deposit_fee' => 'decimal:5',
        'withdrawal_fee' => 'decimal:5',
        'harvest_fee' => 'decimal:5',
        'last_reward_timestamp' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'compounding_enabled' => 'boolean',
        'auto_compound' => 'boolean',
        'is_verified' => 'boolean',
        'tags' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('pool_status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Accessors
    public function getFormattedTotalLiquidityAttribute()
    {
        return number_format($this->total_liquidity, 8);
    }

    public function getFormattedAprAttribute()
    {
        return number_format($this->apr, 2) . '%';
    }

    public function getPoolStatusLabelAttribute()
    {
        $labels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'paused' => 'مؤقت',
            'closed' => 'مغلق'
        ];
        return $labels[$this->pool_status] ?? $this->pool_status;
    }

    // Methods
    public function isVerified()
    {
        return $this->is_verified;
    }

    public function isActive()
    {
        return $this->pool_status === 'active';
    }

    // Relationships
    public function smartContract(): BelongsTo
    {
        return $this->belongsTo(SmartContract::class, 'contract_address', 'address');
    }

    public function token0(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'token0_address', 'address');
    }

    public function token1(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'token1_address', 'address');
    }

    // Static Methods
    public static function getStats()
    {
        return [
            'total_pools' => self::count(),
            'active_pools' => self::active()->count(),
            'verified_pools' => self::verified()->count(),
            'total_liquidity' => self::sum('total_liquidity'),
            'average_apr' => self::avg('apr'),
        ];
    }

    public static function getTopPools($limit = 20)
    {
        return self::orderBy('total_liquidity', 'desc')
                   ->limit($limit)
                   ->get();
    }
}
