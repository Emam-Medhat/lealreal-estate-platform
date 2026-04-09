<?php

namespace App\Models\Defi;

use App\Models\User;
use App\Models\Defi\DefiPropertyInvestment;
use App\Models\Defi\PropertyToken;
use App\Models\Defi\FractionalOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Support\Facades\DB;

class CryptoPropertyPayment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'payment_type',
        'amount',
        'currency',
        'blockchain',
        'property_id',
        'property_token_id',
        'defi_property_investment_id',
        'fractional_ownership_id',
        'sender_address',
        'recipient_address',
        'transaction_hash',
        'block_number',
        'gas_fee',
        'fee_amount',
        'fee_percentage',
        'total_amount',
        'payment_method',
        'status',
        'confirmed_at',
        'completed_at',
        'failed_at',
        'cancelled_at',
        'retry_count',
        'max_retries',
        'error_message',
        'description',
        'metadata',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:18',
        'gas_fee' => 'decimal:18',
        'fee_amount' => 'decimal:18',
        'fee_percentage' => 'decimal:5',
        'total_amount' => 'decimal:18',
        'retry_count' => 'integer',
        'max_retries' => 'integer',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'transaction_hash',
        'block_number',
        'error_message',
    ];

    /**
     * Get the user that made the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the property associated with the payment.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Metaverse\MetaverseProperty::class, 'property_id');
    }

    /**
     * Get the token associated with the payment.
     */
    public function token(): BelongsTo
    {
        return $this->belongsTo(PropertyToken::class, 'property_token_id');
    }

    /**
     * Get the investment associated with the payment.
     */
    public function investment(): BelongsTo
    {
        return $this->belongsTo(DefiPropertyInvestment::class, 'defi_property_investment_id');
    }

    /**
     * Get the fractional ownership associated with the payment.
     */
    public function ownership(): BelongsTo
    {
        return $this->belongsTo(FractionalOwnership::class, 'fractional_ownership_id');
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include payments by currency.
     */
    public function scopeByCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope a query to only include payments by blockchain.
     */
    public function scopeByBlockchain($query, $blockchain)
    {
        return $query->where('blockchain', $blockchain);
    }

    /**
     * Scope a query to only include payments by payment type.
     */
    public function scopeByPaymentType($query, $paymentType)
    {
        return $query->where('payment_type', $paymentType);
    }

    /**
     * Scope a query to only include payments by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Get the payment type text attribute.
     */
    public function getPaymentTypeTextAttribute(): string
    {
        return match($this->payment_type) {
            'property_purchase' => 'شراء عقار',
            'property_rental' => 'إيجار عقار',
            'token_purchase' => 'شراء توكن',
            'investment_payment' => 'دفع استثمار',
            'ownership_payment' => 'دفع ملكية جزئية',
            'service_fee' => 'رسوم خدمة',
            'subscription' => 'اشتراك',
            'royalty_payment' => 'دفع حقوق ملكية',
            'refund' => 'استرداد',
            'donation' => 'تبرع',
            default => $this->payment_type,
        };
    }

    /**
     * Get the status text attribute.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد',
            'disputed' => 'تحت النزاع',
            'investigating' => 'تحتقي',
            'suspended' => 'معلق',
            'deleted' => 'محذوف',
            default => $this->status,
        };
    }

    /**
     * Get the blockchain text attribute.
     */
    public function getBlockchainTextAttribute(): string
    {
        return match($this->blockchain) {
            'ethereum' => 'إيثيريوم',
            'polygon' => 'بوليغون',
            'binance_smart_chain' => 'سلسلة بينانس الذكية',
            'solana' => 'سولانا',
            'avalanche' => 'أفالانش',
            'bitcoin' => 'بيتكوين',
            'cardano' => 'كاردانو',
            'polkadot' => 'بولكادوت',
            default => $this->blockchain,
        };
    }

    /**
     * Get the currency text attribute.
     */
    public function getCurrencyTextAttribute(): string
    {
        return match($this->currency) {
            'USD' => 'دولار أمريكي',
            'EUR' => 'يورو',
            'GBP' => 'جنيه إسترليني',
            'ETH' => 'إيثيريوم',
            'BTC' => 'بيتكوين',
            'USDT' => 'تيثر',
            default => $this->currency,
        };
    }

    /**
     * Get the payment method text attribute.
     */
    public function getPaymentMethodTextAttribute(): string
    {
        return match($this->payment_method) {
            'wallet' => 'محفظة',
            'exchange' => 'منصة',
            'defi_protocol' => 'بروتوكول DeFi',
            'crypto_card' => 'بطاقة عملة رقمية',
            'cash' => 'نقدي',
            'check' => 'شيك',
            default => $this->payment_method,
        };
    }

    /**
     * Get the net amount.
     */
    public function getNetAmountAttribute(): float
    {
        return $this->amount - $this->fee_amount - $this->gas_fee;
    }

    /**
     * Get the confirmation count.
     */
    public function getConfirmationCountAttribute(): int
    {
        // This would query the blockchain for confirmation count
        // For now, return a mock count
        if ($this->status === 'completed') {
            return rand(12, 50);
        }
        
        return 0;
    }

    /**
     * Get the estimated completion time.
     */
    public function getEstimatedCompletionTimeAttribute(): string
    {
        if ($this->status === 'completed') {
            return 'مكتمل';
        }

        $blockchain = $this->blockchain;
        $estimatedMinutes = match($blockchain) {
            'ethereum' => 15,
            'polygon' => 2,
            'binance_smart_chain' => 3,
            'solana' => 1,
            'avalanche' => 2,
            'bitcoin' => 60,
            'cardano' => 20,
            'polkadot' => 6,
            default => 10,
        };

        return now()->addMinutes($estimatedMinutes)->format('H:i');
    }

    /**
     * Check if the payment can be retried.
     */
    public function canRetryPayment(): bool
    {
        return $this->status === 'failed' && 
               $this->retry_count < $this->max_retries;
    }

    /**
     * Check if the payment can be cancelled.
     */
    public function canCancelPayment(): bool
    {
        return in_array($this->status, ['pending', 'failed']);
    }

    /**
     * Get the block explorer URL.
     */
    public function getBlockExplorerUrlAttribute(): string
    {
        if (!$this->transaction_hash) {
            return '';
        }

        $explorers = [
            'ethereum' => 'https://etherscan.io/tx/',
            'polygon' => 'https://polygonscan.com/tx/',
            'binance_smart_chain' => 'https://bscscan.com/tx/',
            'solana' => 'https://solscan.io/tx/',
            'avalanche' => 'https://snowtrace.io/tx/',
            'bitcoin' => 'https://blockstream.info/tx/',
            'cardano' => 'https://cardanoscan.io/tx/',
            'polkadot' => 'https://polkascan.io/tx/',
        ];

        $baseUrl = $explorers[$this->blockchain] ?? '';
        return $baseUrl . $this->transaction_hash;
    }

    /**
     * Get the payment progress.
     */
    public function getPaymentProgressAttribute(): array
    {
        return [
            'created' => 100,
            'pending' => $this->status === 'pending' ? 50 : 100,
            'confirmed' => $this->status === 'confirmed' ? 75 : ($this->status === 'completed' ? 100 : 0),
            'completed' => $this->status === 'completed' ? 100 : 0,
        ];
    }

    /**
     * Retry the payment.
     */
    public function retry(): bool
    {
        if (!$this->canRetryPayment()) {
            return false;
        }

        DB::beginTransaction();

        try {
            // Update retry count
            $this->increment('retry_count');
            $this->update([
                'status' => 'pending',
                'transaction_hash' => null,
                'block_number' => null,
                'confirmed_at' => null,
                'completed_at' => null,
                'failed_at' => null,
                'updated_at' => now(),
            ]);

            // Process payment
            $this->processPayment();

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Confirm the payment.
     */
    public function confirm(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        DB::beginTransaction();

        try {
            // Update payment status
            $this->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'updated_at' => now(),
            ]);

            // Complete payment
            $this->completePayment();

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Complete the payment.
     */
    public function completePayment(): bool
    {
        if ($this->status !== 'confirmed') {
            return false;
        }

        // Update related entities based on payment type
        switch ($this->payment_type) {
            case 'property_purchase':
                if ($this->property) {
                    $this->property->update([
                        'owner_id' => $this->user_id,
                        'status' => 'sold',
                        'sold_at' => now(),
                    ]);
                }
                break;

            case 'token_purchase':
                if ($this->token) {
                    // This would handle token purchase logic
                    \Log::info("Token purchase completed for payment {$this->id}");
                }
                break;

            case 'investment_payment':
                if ($this->investment) {
                    $this->investment->update([
                        'status' => 'active',
                        'activated_at' => now(),
                    ]);
                }
                break;

            case 'ownership_payment':
                if ($this->ownership) {
                    $this->ownership->update([
                        'status' => 'active',
                        'activated_at' => now(),
                    ]);
                }
                break;
        }

        // Mark payment as completed
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return true;
    }

    /**
     * Cancel the payment.
     */
    public function cancel($reason = null): bool
    {
        if (!$this->canCancelPayment()) {
            return false;
        }

        return $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Process the payment.
     */
    private function processPayment(): void
    {
        try {
            // This would integrate with blockchain payment processing
            // For now, simulate payment processing
            
            // Generate transaction hash
            $this->update([
                'transaction_hash' => '0x' . bin2hex(random_bytes(32)),
                'block_number' => rand(1000000, 9999999),
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

        } catch (\Exception $e) {
            // Mark payment as failed
            $this->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get payment statistics.
     */
    public static function getStatistics(): array
    {
        $stats = [
            'total_payments' => self::count(),
            'completed_payments' => self::completed()->count(),
            'failed_payments' => self::failed()->count(),
            'total_amount' => self::completed()->sum('amount'),
            'total_fees' => self::completed()->sum('fee_amount'),
            'total_gas' => self::completed()->sum('gas_fee'),
            'pending_payments' => self::pending()->count(),
            'currency_distribution' => self::groupBy('currency')->map->count()->toArray(),
            'blockchain_distribution' => self::groupBy('blockchain')->map->count()->toArray(),
            'payment_type_distribution' => self::groupBy('payment_type')->map->count()->toArray(),
            'average_transaction_time' => self::calculateAverageTransactionTime(),
            'success_rate' => self::calculateSuccessRate(),
            'daily_volume' => self::calculateDailyVolume(),
        ];

        return $stats;
    }

    /**
     * Get monthly payment data.
     */
    public static function getMonthlyData(): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('Y-m'),
                'new_payments' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'completed_payments' => self::whereMonth('completed_at', $date->month)
                    ->whereYear('completed_at', $date->year)
                    ->count(),
                'total_amount' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('amount'),
                'total_fees' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('fee_amount'),
                'total_gas' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('gas_fee'),
            ];
            
            $monthlyData[] = $monthData;
        }

        return $monthlyData;
    }

    /**
     * Calculate average transaction time.
     */
    private static function calculateAverageTransactionTime(): float
    {
        $completedPayments = self::completed()->get();
        
        if ($completedPayments->isEmpty()) {
            return 0;
        }

        $totalTime = $completedPayments->sum(function ($payment) {
            return $payment->created_at->diffInMinutes($payment->completed_at);
        });

        return $totalTime / $completedPayments->count();
    }

    /**
     * Calculate success rate.
     */
    private static function calculateSuccessRate(): float
    {
        $totalPayments = self::count();
        
        if ($totalPayments === 0) {
            return 0;
        }

        $successfulPayments = self::whereIn('status', ['completed', 'confirmed'])->count();
        
        return ($successfulPayments / $totalPayments) * 100;
    }

    /**
     * Calculate daily volume.
     */
    private static function calculateDailyVolume(): float
    {
        return self::where('status', 'completed')
            ->whereDate('created_at', '>=', now()->subDay())
            ->sum('amount');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->retry_count) {
                $payment->retry_count = 0;
            }
            
            if (!$payment->max_retries) {
                $payment->max_retries = 3;
            }
            
            if (!$payment->fee_percentage) {
                $payment->fee_percentage = 1; // Default 1% fee
            }
            
            if (!$payment->fee_amount) {
                $payment->fee_amount = $payment->amount * ($payment->fee_percentage / 100);
            }
            
            if (!$payment->total_amount) {
                $payment->total_amount = $payment->amount + $payment->fee_amount + $payment->gas_fee;
            }
        });

        static::updating(function ($payment) {
            if ($payment->isDirty('status') && $payment->status === 'active') {
                $payment->next_payment_date = now()->addDay();
            }
        });
    }
}
