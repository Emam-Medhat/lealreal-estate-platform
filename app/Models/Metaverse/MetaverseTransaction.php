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

class MetaverseTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'metaverse_property_id',
        'virtual_land_id',
        'metaverse_property_nft_id',
        'metaverse_showroom_id',
        'virtual_property_tour_id',
        'buyer_id',
        'seller_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'transaction_hash',
        'blockchain',
        'gas_fee',
        'confirmation_count',
        'confirmed_at',
        'completed_at',
        'failed_at',
        'cancelled_at',
        'refund_amount',
        'refund_reason',
        'refund_processed_at',
        'notes',
        'message',
        'metadata',
        'additional_data',
        'verification_status',
        'verified_at',
        'fraud_score',
        'risk_level',
        'compliance_status',
        'tax_amount',
        'tax_currency',
        'fee_amount',
        'fee_currency',
        'net_amount',
        'net_currency',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gas_fee' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'confirmation_count' => 'integer',
        'fraud_score' => 'decimal:2',
        'metadata' => 'array',
        'additional_data' => 'array',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refund_processed_at' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'confirmed_at',
        'completed_at',
        'failed_at',
        'cancelled_at',
        'refund_processed_at',
        'verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships
    public function metaverseProperty(): BelongsTo
    {
        return $this->belongsTo(MetaverseProperty::class, 'metaverse_property_id');
    }

    public function virtualLand(): BelongsTo
    {
        return $this->belongsTo(VirtualLand::class, 'virtual_land_id');
    }

    public function nft(): BelongsTo
    {
        return $this->belongsTo(MetaversePropertyNft::class, 'metaverse_property_nft_id');
    }

    public function showroom(): BelongsTo
    {
        return $this->belongsTo(MetaverseShowroom::class, 'metaverse_showroom_id');
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(VirtualPropertyTour::class, 'virtual_property_tour_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function confirmations(): HasMany
    {
        return $this->hasMany(TransactionConfirmation::class, 'metaverse_transaction_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(TransactionRefund::class, 'metaverse_transaction_id');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(TransactionDispute::class, 'metaverse_transaction_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(TransactionAuditLog::class, 'metaverse_transaction_id');
    }

    public function tags(): MorphMany
    {
        return $this->morphMany(MetaverseTag::class, 'taggable');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(TransactionVerification::class, 'metaverse_transaction_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByBlockchain($query, $blockchain)
    {
        return $query->where('blockchain', $blockchain);
    }

    public function scopeByCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    public function scopeByBuyer($query, $buyerId)
    {
        return $query->where('buyer_id', $buyerId);
    }

    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeByAmountRange($query, $minAmount, $maxAmount = null)
    {
        $query->where('amount', '>=', $minAmount);
        if ($maxAmount) {
            $query->where('amount', '<=', $maxAmount);
        }
        return $query;
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeHighRisk($query)
    {
        return $query->where('risk_level', 'high');
    }

    public function scopeMediumRisk($query)
    {
        return $query->where('risk_level', 'medium');
    }

    public function scopeLowRisk($query)
    {
        return $query->where('risk_level', 'low');
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 4) . ' ' . $this->currency;
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return number_format($this->net_amount, 4) . ' ' . $this->net_currency;
    }

    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'property_purchase' => 'شراء عقار',
            'property_sale' => 'بيع عقار',
            'property_offer' => 'عرض على عقار',
            'land_purchase' => 'شراء أرض',
            'land_sale' => 'بيع أرض',
            'land_offer' => 'عرض على أرض',
            'nft_purchase' => 'شراء NFT',
            'nft_sale' => 'بيع NFT',
            'nft_bid' => 'مزايدة على NFT',
            'nft_transfer' => 'نقل NFT',
            'tour_booking' => 'حجز جولة',
            'event_ticket' => 'تذكرة فعالية',
            'showroom_rental' => 'إيجار صالة عرض',
            'service_fee' => 'رسوم خدمة',
            'subscription' => 'اشتراك',
            'royalty_payment' => 'دفع حقوق ملكية',
            'refund' => 'استرداد',
            'donation' => 'تبرع',
            default => $this->type,
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد',
            'disputed' => 'متنازع عليه',
            'investigating' => 'تحت التحقيق',
            'suspended' => 'موقوف',
            'deleted' => 'محذوف',
            default => $this->status,
        };
    }

    public function getPaymentMethodTextAttribute(): string
    {
        return match($this->payment_method) {
            'crypto' => 'عملة مشفرة',
            'credit_card' => 'بطاقة ائتمان',
            'debit_card' => 'بطاقة خصم مباشر',
            'bank_transfer' => 'تحويل بنكي',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'wallet' => 'محفظة إلكترونية',
            'cash' => 'نقدي',
            'check' => 'شيك',
            default => $this->payment_method,
        };
    }

    public function getBlockchainTextAttribute(): string
    {
        return match($this->blockchain) {
            'ethereum' => 'Ethereum',
            'polygon' => 'Polygon',
            'binance_smart_chain' => 'Binance Smart Chain',
            'solana' => 'Solana',
            'avalanche' => 'Avalanche',
            'bitcoin' => 'Bitcoin',
            'cardano' => 'Cardano',
            'polkadot' => 'Polkadot',
            default => $this->blockchain,
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
            'rejected' => 'مرفوض',
            default => $this->verification_status,
        };
    }

    public function getRiskLevelTextAttribute(): string
    {
        return match($this->risk_level) {
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'critical' => 'حرج',
            default => $this->risk_level,
        };
    }

    public function getComplianceStatusTextAttribute(): string
    {
        return match($this->compliance_status) {
            'compliant' => 'متوافق',
            'non_compliant' => 'غير متوافق',
            'under_review' => 'تحت المراجعة',
            'flagged' => 'محظور',
            'suspended' => 'موقوف',
            default => $this->compliance_status,
        };
    }

    public function getIsCryptoTransactionAttribute(): bool
    {
        return in_array($this->payment_method, ['crypto', 'wallet']) || !is_null($this->blockchain);
    }

    public function getIsHighValueAttribute(): bool
    {
        return $this->amount > 10000; // Consider high value if > $10,000
    }

    public function getIsInternationalAttribute(): bool
    {
        return $this->buyer && $this->seller && 
               $this->buyer->country !== $this->seller->country;
    }

    public function getProcessingTimeAttribute(): string
    {
        if (!$this->confirmed_at || !$this->completed_at) {
            return 'N/A';
        }

        $duration = $this->confirmed_at->diffInSeconds($this->completed_at);
        
        if ($duration < 60) {
            return $duration . ' ثانية';
        } elseif ($duration < 3600) {
            return round($duration / 60) . ' دقيقة';
        } else {
            return round($duration / 3600, 1) . ' ساعة';
        }
    }

    // Methods
    public function confirm(string $transactionHash = null): void
    {
        $this->update([
            'status' => 'confirmed',
            'transaction_hash' => $transactionHash ?? $this->transaction_hash,
            'confirmed_at' => now(),
            'confirmation_count' => $this->confirmation_count + 1,
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update related entities
        $this->updateRelatedEntities();
    }

    public function fail(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'notes' => $reason,
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'notes' => $reason,
        ]);
    }

    public function refund(float $amount, string $reason): void
    {
        $this->update([
            'refund_amount' => $amount,
            'refund_reason' => $reason,
            'refund_processed_at' => now(),
            'status' => 'refunded',
        ]);

        // Create refund record
        $this->refunds()->create([
            'amount' => $amount,
            'currency' => $this->currency,
            'reason' => $reason,
            'processed_at' => now(),
        ]);
    }

    public function addConfirmation(string $hash, int $blockNumber): void
    {
        $this->confirmations()->create([
            'hash' => $hash,
            'block_number' => $blockNumber,
            'confirmed_at' => now(),
        ]);

        $this->increment('confirmation_count');
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
            'notes' => $reason,
        ]);
    }

    public function calculateFees(): array
    {
        $platformFee = $this->amount * 0.025; // 2.5% platform fee
        $gasFee = $this->gas_fee ?? 0;
        $taxAmount = $this->tax_amount ?? 0;
        
        $totalFees = $platformFee + $gasFee + $taxAmount;
        $netAmount = $this->amount - $totalFees;

        return [
            'platform_fee' => $platformFee,
            'gas_fee' => $gasFee,
            'tax_amount' => $taxAmount,
            'total_fees' => $totalFees,
            'net_amount' => $netAmount,
        ];
    }

    public function updateNetAmount(): void
    {
        $fees = $this->calculateFees();
        
        $this->update([
            'fee_amount' => $fees['platform_fee'],
            'net_amount' => $fees['net_amount'],
        ]);
    }

    public function assessRisk(): string
    {
        $riskScore = 0;

        // Amount risk
        if ($this->amount > 50000) $riskScore += 30;
        elseif ($this->amount > 10000) $riskScore += 20;
        elseif ($this->amount > 1000) $riskScore += 10;

        // International risk
        if ($this->getIsInternationalAttribute()) $riskScore += 15;

        // Crypto risk
        if ($this->getIsCryptoTransactionAttribute()) $riskScore += 10;

        // New user risk
        if ($this->buyer && $this->buyer->created_at->diffInDays(now()) < 30) {
            $riskScore += 20;
        }

        // Fraud score risk
        $riskScore += $this->fraud_score * 10;

        if ($riskScore >= 70) {
            return 'critical';
        } elseif ($riskScore >= 50) {
            return 'high';
        } elseif ($riskScore >= 30) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function checkCompliance(): string
    {
        // Check various compliance factors
        $issues = [];

        // Check amount limits
        if ($this->amount > 100000) {
            $issues[] = 'High value transaction requires additional verification';
        }

        // Check international compliance
        if ($this->getIsInternationalAttribute()) {
            $issues[] = 'International transaction requires compliance check';
        }

        // Check crypto compliance
        if ($this->getIsCryptoTransactionAttribute()) {
            $issues[] = 'Crypto transaction requires compliance verification';
        }

        // Check user compliance
        if ($this->buyer && !$this->buyer->is_verified) {
            $issues[] = 'Buyer not verified';
        }

        if (empty($issues)) {
            return 'compliant';
        } else {
            return 'under_review';
        }
    }

    public function generateReceipt(): array
    {
        return [
            'transaction_id' => $this->id,
            'type' => $this->getTypeTextAttribute(),
            'amount' => $this->getFormattedAmountAttribute(),
            'net_amount' => $this->getFormattedNetAmountAttribute(),
            'currency' => $this->currency,
            'status' => $this->getStatusTextAttribute(),
            'date' => $this->created_at->format('Y-m-d H:i:s'),
            'buyer' => $this->buyer ? $this->buyer->name : 'N/A',
            'seller' => $this->seller ? $this->seller->name : 'N/A',
            'transaction_hash' => $this->transaction_hash,
            'blockchain' => $this->blockchain,
            'payment_method' => $this->getPaymentMethodTextAttribute(),
            'fees' => $this->calculateFees(),
        ];
    }

    public function getTransactionDetails(): array
    {
        return [
            'basic_info' => [
                'id' => $this->id,
                'type' => $this->type,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'status' => $this->status,
                'payment_method' => $this->payment_method,
            ],
            'parties' => [
                'buyer' => $this->buyer,
                'seller' => $this->seller,
            ],
            'assets' => [
                'property' => $this->metaverseProperty,
                'land' => $this->virtualLand,
                'nft' => $this->nft,
                'showroom' => $this->showroom,
                'tour' => $this->tour,
            ],
            'blockchain' => [
                'hash' => $this->transaction_hash,
                'network' => $this->blockchain,
                'gas_fee' => $this->gas_fee,
                'confirmations' => $this->confirmation_count,
            ],
            'fees' => $this->calculateFees(),
            'timestamps' => [
                'created_at' => $this->created_at,
                'confirmed_at' => $this->confirmed_at,
                'completed_at' => $this->completed_at,
                'failed_at' => $this->failed_at,
                'cancelled_at' => $this->cancelled_at,
            ],
            'risk' => [
                'level' => $this->risk_level,
                'score' => $this->fraud_score,
                'assessment' => $this->assessRisk(),
            ],
            'compliance' => [
                'status' => $this->compliance_status,
                'check' => $this->checkCompliance(),
            ],
        ];
    }

    public function getAuditTrail(): array
    {
        return $this->auditLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function createAuditLog(string $action, string $details = null, User $user = null): void
    {
        $this->auditLogs()->create([
            'action' => $action,
            'details' => $details,
            'user_id' => $user?->id ?? auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    private function updateRelatedEntities(): void
    {
        // Update property ownership if applicable
        if ($this->metaverseProperty && $this->type === 'property_purchase') {
            $this->metaverseProperty->update([
                'owner_id' => $this->buyer_id,
                'sale_price' => $this->amount,
                'sale_currency' => $this->currency,
                'sold_at' => $this->completed_at,
            ]);
        }

        // Update land ownership if applicable
        if ($this->virtualLand && $this->type === 'land_purchase') {
            $this->virtualLand->update([
                'owner_id' => $this->buyer_id,
                'purchase_price' => $this->amount,
                'purchase_currency' => $this->currency,
                'last_purchase_date' => $this->completed_at,
            ]);
        }

        // Update NFT ownership if applicable
        if ($this->nft && $this->type === 'nft_purchase') {
            $this->nft->update([
                'owner_id' => $this->buyer_id,
                'last_sale_price' => $this->amount,
                'last_sale_at' => $this->completed_at,
            ]);
        }
    }

    public function canBeRefunded(): bool
    {
        return in_array($this->status, ['completed', 'failed']) && 
               $this->completed_at && 
               $this->completed_at->diffInHours(now()) <= 24; // 24-hour refund window
    }

    public function canBeDisputed(): bool
    {
        return $this->status === 'completed' && 
               $this->completed_at && 
               $this->completed_at->diffInDays(now()) <= 30; // 30-day dispute window
    }

    public function createDispute(string $reason, string $description): TransactionDispute
    {
        return $this->disputes()->create([
            'initiator_id' => auth()->id(),
            'reason' => $reason,
            'description' => $description,
            'status' => 'open',
            'opened_at' => now(),
        ]);
    }

    public function getTransactionUrl(): string
    {
        if ($this->blockchain === 'ethereum') {
            return "https://etherscan.io/tx/" . $this->transaction_hash;
        } elseif ($this->blockchain === 'polygon') {
            return "https://polygonscan.com/tx/" . $this->transaction_hash;
        } elseif ($this->blockchain === 'binance_smart_chain') {
            return "https://bscscan.com/tx/" . $this->transaction_hash;
        }
        
        return '#';
    }
}
