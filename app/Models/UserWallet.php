<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'frozen_balance',
        'pending_balance',
        'total_deposited',
        'total_withdrawn',
        'total_spent',
        'total_earned',
        'wallet_type',
        'is_active',
        'is_verified',
        'verification_level',
        'daily_limit',
        'monthly_limit',
        'transaction_count',
        'last_transaction_at',
        'wallet_address',
        'blockchain_network',
        'private_key_encrypted',
        'public_key',
        'mnemonic_encrypted',
        'metadata',
    ];

    protected $casts = [
        'balance' => 'decimal:8',
        'frozen_balance' => 'decimal:8',
        'pending_balance' => 'decimal:8',
        'total_deposited' => 'decimal:8',
        'total_withdrawn' => 'decimal:8',
        'total_spent' => 'decimal:8',
        'total_earned' => 'decimal:8',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'daily_limit' => 'decimal:8',
        'monthly_limit' => 'decimal:8',
        'transaction_count' => 'integer',
        'last_transaction_at' => 'datetime',
        'metadata' => 'json',
    ];

    protected $attributes = [
        'balance' => 0,
        'frozen_balance' => 0,
        'pending_balance' => 0,
        'total_deposited' => 0,
        'total_withdrawn' => 0,
        'total_spent' => 0,
        'total_earned' => 0,
        'wallet_type' => 'fiat',
        'is_active' => true,
        'is_verified' => false,
        'verification_level' => 0,
        'daily_limit' => 10000,
        'monthly_limit' => 100000,
        'transaction_count' => 0,
        'currency' => 'USD',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(UserTransaction::class, 'wallet_id');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(UserTransaction::class, 'wallet_id')->where('transaction_type', 'deposit');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(UserTransaction::class, 'wallet_id')->where('transaction_type', 'withdrawal');
    }

    public function getAvailableBalanceAttribute(): float
    {
        return $this->balance - $this->frozen_balance;
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 8) . ' ' . $this->currency;
    }

    public function getFormattedAvailableBalanceAttribute(): string
    {
        return number_format($this->available_balance, 8) . ' ' . $this->currency;
    }

    public function getWalletTypeLabelAttribute(): string
    {
        return match($this->wallet_type) {
            'fiat' => __('Fiat Currency'),
            'crypto' => __('Cryptocurrency'),
            'mixed' => __('Mixed Wallet'),
            default => __('Unknown')
        };
    }

    public function getVerificationLevelLabelAttribute(): string
    {
        return match($this->verification_level) {
            0 => __('Not Verified'),
            1 => __('Basic'),
            2 => __('Standard'),
            3 => __('Premium'),
            default => __('Unknown')
        };
    }

    public function canDeposit(float $amount): bool
    {
        return $this->is_active && $amount > 0;
    }

    public function canWithdraw(float $amount): bool
    {
        return $this->is_active 
               && $this->available_balance >= $amount 
               && $this->isWithinDailyLimit($amount)
               && $this->isWithinMonthlyLimit($amount);
    }

    public function isWithinDailyLimit(float $amount): bool
    {
        $todaySpent = $this->getTodaySpent();
        return ($todaySpent + $amount) <= $this->daily_limit;
    }

    public function isWithinMonthlyLimit(float $amount): bool
    {
        $monthSpent = $this->getMonthSpent();
        return ($monthSpent + $amount) <= $this->monthly_limit;
    }

    public function getTodaySpent(): float
    {
        return $this->transactions()
                    ->where('transaction_type', 'withdrawal')
                    ->whereDate('created_at', today())
                    ->sum('amount');
    }

    public function getMonthSpent(): float
    {
        return $this->transactions()
                    ->where('transaction_type', 'withdrawal')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount');
    }

    public function deposit(float $amount, string $description = null): bool
    {
        if (!$this->canDeposit($amount)) {
            return false;
        }

        return $this->update([
            'balance' => $this->balance + $amount,
            'total_deposited' => $this->total_deposited + $amount,
            'transaction_count' => $this->transaction_count + 1,
            'last_transaction_at' => now(),
        ]);
    }

    public function withdraw(float $amount, string $description = null): bool
    {
        if (!$this->canWithdraw($amount)) {
            return false;
        }

        return $this->update([
            'balance' => $this->balance - $amount,
            'total_withdrawn' => $this->total_withdrawn + $amount,
            'transaction_count' => $this->transaction_count + 1,
            'last_transaction_at' => now(),
        ]);
    }

    public function freezeAmount(float $amount): bool
    {
        if ($this->available_balance < $amount) {
            return false;
        }

        return $this->update([
            'frozen_balance' => $this->frozen_balance + $amount,
        ]);
    }

    public function unfreezeAmount(float $amount): bool
    {
        if ($this->frozen_balance < $amount) {
            return false;
        }

        return $this->update([
            'frozen_balance' => $this->frozen_balance - $amount,
        ]);
    }

    public function verify(): void
    {
        $this->update([
            'is_verified' => true,
            'verification_level' => 1,
        ]);
    }

    public function upgradeVerificationLevel(int $level): bool
    {
        if ($level <= $this->verification_level) {
            return false;
        }

        return $this->update([
            'verification_level' => $level,
            'daily_limit' => $this->getDailyLimitForLevel($level),
            'monthly_limit' => $this->getMonthlyLimitForLevel($level),
        ]);
    }

    private function getDailyLimitForLevel(int $level): float
    {
        return match($level) {
            1 => 1000,
            2 => 10000,
            3 => 100000,
            default => 1000
        };
    }

    private function getMonthlyLimitForLevel(int $level): float
    {
        return match($level) {
            1 => 10000,
            2 => 100000,
            3 => 1000000,
            default => 10000
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    public function scopeByWalletType($query, string $walletType)
    {
        return $query->where('wallet_type', $walletType);
    }

    public function scopeWithBalance($query, float $minBalance = 0)
    {
        return $query->where('balance', '>=', $minBalance);
    }
}
