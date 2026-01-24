<?php

namespace App\Models\Defi;

use App\Models\User;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\Defi\DefiCollateral;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Support\Facades\DB;

class DefiPropertyLoan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'property_id',
        'loan_type',
        'amount',
        'currency',
        'interest_rate',
        'loan_term',
        'collateral_type',
        'collateral_value',
        'collateral_details',
        'purpose',
        'risk_assessment',
        'credit_score',
        'status',
        'application_hash',
        'smart_contract_address',
        'approved_at',
        'approved_by',
        'disbursed_at',
        'next_payment_date',
        'amount',
        'repaid_amount',
        'paid_interest',
        'total_interest',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:8',
        'interest_rate' => 'decimal:5',
        'collateral_value' => 'decimal:8',
        'repaid_amount' => 'decimal:8',
        'paid_interest' => 'decimal:8',
        'total_interest' => 'decimal:8',
        'risk_assessment' => AsArrayObject::class,
        'collateral_details' => AsArrayObject::class,
        'approved_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'next_payment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'application_hash',
        'smart_contract_address',
    ];

    /**
     * Get the user that owns the loan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the property associated with the loan.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(MetaverseProperty::class, 'property_id');
    }

    /**
     * Get the collateral for the loan.
     */
    public function collateral(): HasOne
    {
        return $this->hasOne(DefiCollateral::class);
    }

    /**
     * Get the repayments for the loan.
     */
    public function repayments(): HasMany
    {
        return $this->hasMany(DefiLoanRepayment::class);
    }

    /**
     * Get the transactions for the loan.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(DefiLoanTransaction::class);
    }

    /**
     * Scope a query to only include active loans.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include pending loans.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include completed loans.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include loans by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('loan_type', $type);
    }

    /**
     * Scope a query to only include loans by currency.
     */
    public function scopeByCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope a query to only include overdue loans.
     */
    public function scopeOverdue($query)
    {
        return $query->where('next_payment_date', '<', now())
                    ->where('status', 'active');
    }

    /**
     * Get the loan type text attribute.
     */
    public function getLoanTypeTextAttribute(): string
    {
        return match($this->loan_type) {
            'property_purchase' => 'شراء عقار',
            'property_sale' => 'بيع عقار',
            'property_offer' => 'عرض عقاري',
            'land_purchase' => 'شراء أرض',
            'land_sale' => 'بيع أرض',
            'land_offer' => 'عرض أرض',
            'nft_purchase' => 'شراء NFT',
            'nft_sale' => 'بيع NFT',
            'nft_bid' => 'مزايدة NFT',
            'nft_transfer' => 'نقل NFT',
            'tour_booking' => 'حجز جولة',
            'event_ticket' => 'تذكرة فعالية',
            'showroom_rental' => 'إيجار صالة عرض',
            'service_fee' => 'رسوم خدمة',
            'subscription' => 'اشتراك',
            'royalty_payment' => 'دفع حقوق ملكية',
            'refund' => 'استرداد',
            'donation' => 'تبرع',
            default => $this->loan_type,
        };
    }

    /**
     * Get the status text attribute.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'approved' => 'موافق عليه',
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد',
            'disputed' => 'تحت النزاع',
            'investigating' => 'تحت التحقيق',
            'suspended' => 'معلق',
            'deleted' => 'محذوف',
            default => $this->status,
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
     * Get the collateral type text attribute.
     */
    public function getCollateralTypeTextAttribute(): string
    {
        return match($this->collateral_type) {
            'property' => 'عقار',
            'tokens' => 'توكنات',
            'crypto' => 'عملات رقمية',
            'nft' => 'NFT',
            'mixed' => 'مختلط',
            default => $this->collateral_type,
        };
    }

    /**
     * Get the remaining balance.
     */
    public function getRemainingBalanceAttribute(): float
    {
        return $this->amount - $this->repaid_amount;
    }

    /**
     * Get the remaining interest.
     */
    public function getRemainingInterestAttribute(): float
    {
        return $this->total_interest - $this->paid_interest;
    }

    /**
     * Get the total remaining amount.
     */
    public function getTotalRemainingAttribute(): float
    {
        return $this->remaining_balance + $this->remaining_interest;
    }

    /**
     * Get the progress percentage.
     */
    public function getProgressPercentageAttribute(): float
    {
        $total = $this->amount + $this->total_interest;
        $paid = $this->repaid_amount + $this->paid_interest;
        
        return $total > 0 ? ($paid / $total) * 100 : 0;
    }

    /**
     * Get the days overdue.
     */
    public function getDaysOverdueAttribute(): int
    {
        if ($this->status !== 'active' || !$this->next_payment_date) {
            return 0;
        }

        return max(0, now()->diffInDays($this->next_payment_date));
    }

    /**
     * Get the monthly payment amount.
     */
    public function getMonthlyPaymentAttribute(): float
    {
        $totalAmount = $this->amount + $this->total_interest;
        $months = $this->loan_term;
        
        return $months > 0 ? $totalAmount / $months : 0;
    }

    /**
     * Get the daily interest amount.
     */
    public function getDailyInterestAttribute(): float
    {
        $dailyRate = $this->interest_rate / 365 / 100;
        return $this->remaining_balance * $dailyRate;
    }

    /**
     * Get the monthly interest amount.
     */
    public function getMonthlyInterestAttribute(): float
    {
        return $this->daily_interest * 30;
    }

    /**
     * Get the annual percentage rate (APR).
     */
    public function getAprAttribute(): float
    {
        return $this->interest_rate;
    }

    /**
     * Get the loan-to-value ratio.
     */
    public function getLtvRatioAttribute(): float
    {
        if ($this->collateral_value <= 0) {
            return 0;
        }

        return ($this->amount / $this->collateral_value) * 100;
    }

    /**
     * Get the risk level.
     */
    public function getRiskLevelAttribute(): string
    {
        if (!$this->risk_assessment) {
            return 'unknown';
        }

        return $this->risk_assessment->level ?? 'medium';
    }

    /**
     * Get the credit score rating.
     */
    public function getCreditRatingAttribute(): string
    {
        if ($this->credit_score >= 750) {
            return 'excellent';
        } elseif ($this->credit_score >= 700) {
            return 'good';
        } elseif ($this->credit_score >= 650) {
            return 'fair';
        } elseif ($this->credit_score >= 600) {
            return 'poor';
        } else {
            return 'bad';
        }
    }

    /**
     * Check if the loan is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->days_overdue > 0;
    }

    /**
     * Check if the loan can be repaid early.
     */
    public function canBeRepaidEarly(): bool
    {
        return $this->status === 'active' && $this->repaid_amount > 0;
    }

    /**
     * Check if the loan is eligible for refinancing.
     */
    public function isEligibleForRefinancing(): bool
    {
        return $this->status === 'active' && 
               $this->progress_percentage >= 25 && 
               $this->credit_score >= 650;
    }

    /**
     * Calculate the early repayment penalty.
     */
    public function calculateEarlyRepaymentPenalty(): float
    {
        if (!$this->canBeRepaidEarly()) {
            return 0;
        }

        $remainingBalance = $this->remaining_balance;
        $penaltyRate = 0.02; // 2% penalty
        
        return $remainingBalance * $penaltyRate;
    }

    /**
     * Calculate the refinance amount.
     */
    public function calculateRefinanceAmount(): float
    {
        if (!$this->isEligibleForRefinancing()) {
            return 0;
        }

        $remainingBalance = $this->remaining_balance;
        $refinanceRate = 0.95; // 95% of remaining balance
        
        return $remainingBalance * $refinanceRate;
    }

    /**
     * Approve the loan.
     */
    public function approve($approvedBy = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approvedBy,
        ]);
    }

    /**
     * Activate the loan.
     */
    public function activate($smartContractAddress = null): bool
    {
        if ($this->status !== 'approved') {
            return false;
        }

        return $this->update([
            'status' => 'active',
            'smart_contract_address' => $smartContractAddress,
            'disbursed_at' => now(),
            'next_payment_date' => now()->addMonth(),
        ]);
    }

    /**
     * Complete the loan.
     */
    public function complete(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return $this->update([
            'status' => 'completed',
            'repaid_amount' => $this->amount,
            'paid_interest' => $this->total_interest,
        ]);
    }

    /**
     * Cancel the loan.
     */
    public function cancel($reason = null): bool
    {
        if (!in_array($this->status, ['pending', 'approved'])) {
            return false;
        }

        return $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Get loan statistics.
     */
    public static function getStatistics(): array
    {
        $stats = [
            'total_loans' => self::count(),
            'active_loans' => self::active()->count(),
            'pending_loans' => self::pending()->count(),
            'completed_loans' => self::completed()->count(),
            'overdue_loans' => self::overdue()->count(),
            'total_amount' => self::sum('amount'),
            'total_repaid' => self::sum('repaid_amount'),
            'total_interest' => self::sum('total_interest'),
            'average_loan_size' => self::avg('amount'),
            'average_interest_rate' => self::avg('interest_rate'),
            'default_rate' => self::where('status', 'failed')->count() / self::count() * 100,
        ];

        return $stats;
    }

    /**
     * Get monthly loan data.
     */
    public static function getMonthlyData(): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('Y-m'),
                'new_loans' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'total_amount' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('amount'),
                'completed_loans' => self::whereMonth('completed_at', $date->month)
                    ->whereYear('completed_at', $date->year)
                    ->count(),
            ];
            
            $monthlyData[] = $monthData;
        }

        return $monthlyData;
    }

    /**
     * Get loan performance metrics.
     */
    public static function getPerformanceMetrics(): array
    {
        $metrics = [
            'approval_rate' => self::where('status', '!=', 'pending')->count() / self::count() * 100,
            'completion_rate' => self::completed()->count() / self::where('status', '!=', 'pending')->count() * 100,
            'default_rate' => self::where('status', 'failed')->count() / self::where('status', '!=', 'pending')->count() * 100,
            'average_time_to_complete' => self::completed()->avg(DB::raw('DATEDIFF(completed_at, created_at)')),
            'average_loan_to_value' => self::avg('ltv_ratio'),
            'average_credit_score' => self::avg('credit_score'),
        ];

        return $metrics;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loan) {
            if (!$loan->application_hash) {
                $loan->application_hash = '0x' . bin2hex(random_bytes(32));
            }
            
            if (!$loan->total_interest) {
                $loan->total_interest = $loan->amount * ($loan->interest_rate / 100) * ($loan->loan_term / 12);
            }
        });

        static::updating(function ($loan) {
            if ($loan->isDirty('status') && $loan->status === 'active') {
                $loan->next_payment_date = now()->addMonth();
            }
        });
    }
}
