<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Traits\Auditable;

class Payment extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        // Core Identifiers
        'payment_number',
        'invoice_id',
        'user_id',
        'client_id',
        'agent_id',
        'company_id',
        'property_id',
        'contract_id',
        'transaction_id',
        
        // Payment Details
        'amount',
        'currency',
        'description',
        'notes',
        'internal_notes',
        
        // Payment Method Information
        'payment_method', // cash, bank_transfer, credit_card, check, online, crypto, mobile_money
        'payment_gateway', // stripe, paypal, paymob, manual, local_bank
        'gateway_transaction_id',
        'gateway_response', // JSON response from gateway
        'transaction_reference',
        'authorization_code',
        'payment_token',
        
        // Status and Workflow
        'status', // pending, processing, completed, failed, cancelled, refunded, partial_refund
        'payment_status', // initiated, authorized, captured, settled, voided
        'verification_status', // unverified, verified, flagged, suspicious
        'approval_status', // pending, approved, rejected
        'approval_required',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        
        // Financial Information
        'fees', // Processing fees
        'tax_amount',
        'net_amount', // Amount after fees
        'refund_amount',
        'exchange_rate',
        'original_currency',
        'original_amount',
        
        // Property-specific Information
        'property_address',
        'property_reference',
        'payment_purpose', // rent, deposit, purchase, commission, service, maintenance
        'payment_category', // primary, secondary, recurring, one_time
        
        // Payment Schedule Information
        'scheduled_date',
        'processed_date',
        'settled_date',
        'due_date',
        'grace_period_days',
        
        // Payment Source Information
        'source_type', // bank_account, credit_card, debit_card, digital_wallet
        'source_details', // JSON with source information
        'bank_name',
        'bank_account_number',
        'card_last_four',
        'card_brand',
        'card_expiry',
        
        // Recurring Payment Information
        'is_recurring',
        'recurring_frequency', // daily, weekly, monthly, quarterly, yearly
        'recurring_interval',
        'recurring_start_date',
        'recurring_end_date',
        'recurring_next_date',
        'recurring_count',
        'recurring_remaining',
        'recurring_template_id',
        
        // Refund Information
        'refund_reason',
        'refund_date',
        'refund_method',
        'refund_reference',
        'partial_refund_allowed',
        
        // Security and Fraud Detection
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'risk_score',
        'fraud_check_status',
        'fraud_check_details',
        
        // Multi-currency Support
        'conversion_rate',
        'base_currency',
        'converted_amount',
        
        // Integration
        'external_system_id',
        'integration_type',
        'sync_status',
        'last_synced_at',
        
        // Metadata and Custom Fields
        'metadata',
        'custom_fields',
        'tags',
        
        // Audit
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'fees' => 'decimal:8',
        'tax_amount' => 'decimal:8',
        'net_amount' => 'decimal:8',
        'refund_amount' => 'decimal:8',
        'exchange_rate' => 'decimal:6',
        'conversion_rate' => 'decimal:6',
        'original_amount' => 'decimal:8',
        'converted_amount' => 'decimal:8',
        'risk_score' => 'decimal:2',
        'gateway_response' => 'json',
        'source_details' => 'json',
        'fraud_check_details' => 'json',
        'metadata' => 'json',
        'custom_fields' => 'json',
        'tags' => 'array',
        'scheduled_date' => 'datetime',
        'processed_date' => 'datetime',
        'settled_date' => 'datetime',
        'due_date' => 'datetime',
        'recurring_start_date' => 'date',
        'recurring_end_date' => 'date',
        'recurring_next_date' => 'date',
        'refund_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'is_recurring' => 'boolean',
        'partial_refund_allowed' => 'boolean',
        'approval_required' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Core Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PropertyTransaction::class);
    }

    // Audit Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByGateway($query, $gateway)
    {
        return $query->where('payment_gateway', $gateway);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByAmountRange($query, $minAmount, $maxAmount = null)
    {
        $query->where('amount', '>=', $minAmount);
        
        if ($maxAmount) {
            $query->where('amount', '<=', $maxAmount);
        }
        
        return $query;
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeHighRisk($query)
    {
        return $query->where('risk_score', '>=', 7);
    }

    // Helper Methods
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return number_format($this->net_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedFeesAttribute(): string
    {
        return number_format($this->fees, 2) . ' ' . $this->currency;
    }

    public function getFormattedTaxAmountAttribute(): string
    {
        return number_format($this->tax_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedRefundAmountAttribute(): string
    {
        return number_format($this->refund_amount, 2) . ' ' . $this->currency;
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    public function getIsRefundedAttribute(): bool
    {
        return $this->status === 'refunded';
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function getIsHighRiskAttribute(): bool
    {
        return $this->risk_score >= 7;
    }

    public function getIsRecurringAttribute(): bool
    {
        return $this->is_recurring;
    }

    public function getRequiresApprovalAttribute(): bool
    {
        return $this->approval_required && $this->approval_status === 'pending';
    }

    public function getCanRefundAttribute(): bool
    {
        return $this->status === 'completed' && 
               ($this->partial_refund_allowed || $this->refund_amount === 0);
    }

    public function getRefundableAmountAttribute(): float
    {
        return max(0, $this->net_amount - $this->refund_amount);
    }

    public function getProcessingTimeAttribute(): ?int
    {
        if (!$this->processed_date) {
            return null;
        }
        
        return $this->created_at->diffInSeconds($this->processed_date);
    }

    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
            'partial_refund' => 'Partial Refund',
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        $statuses = [
            'initiated' => 'Initiated',
            'authorized' => 'Authorized',
            'captured' => 'Captured',
            'settled' => 'Settled',
            'voided' => 'Voided',
        ];

        return $statuses[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    public function getVerificationStatusLabelAttribute(): string
    {
        $statuses = [
            'unverified' => 'Unverified',
            'verified' => 'Verified',
            'flagged' => 'Flagged',
            'suspicious' => 'Suspicious',
        ];

        return $statuses[$this->verification_status] ?? ucfirst($this->verification_status);
    }

    public function getMethodLabelAttribute(): string
    {
        $methods = [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'check' => 'Check',
            'online' => 'Online Payment',
            'crypto' => 'Cryptocurrency',
            'mobile_money' => 'Mobile Money',
        ];

        return $methods[$this->payment_method] ?? ucfirst($this->payment_method);
    }

    public function getGatewayLabelAttribute(): string
    {
        $gateways = [
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'paymob' => 'PayMob',
            'manual' => 'Manual',
            'local_bank' => 'Local Bank',
        ];

        return $gateways[$this->payment_gateway] ?? ucfirst($this->payment_gateway);
    }

    public function getPurposeLabelAttribute(): string
    {
        $purposes = [
            'rent' => 'Rent',
            'deposit' => 'Deposit',
            'purchase' => 'Purchase',
            'commission' => 'Commission',
            'service' => 'Service Fee',
            'maintenance' => 'Maintenance',
        ];

        return $purposes[$this->payment_purpose] ?? ucfirst($this->payment_purpose);
    }

    // Business Logic Methods
    public function approvePayment(User $approver, string $notes = ''): bool
    {
        return $this->update([
            'approval_status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function rejectPayment(User $rejecter, string $reason): bool
    {
        return $this->update([
            'approval_status' => 'rejected',
            'rejected_by' => $rejecter->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed',
            'payment_status' => 'settled',
            'processed_date' => now(),
            'settled_date' => now(),
        ]);
    }

    public function markAsFailed(string $reason): bool
    {
        return $this->update([
            'status' => 'failed',
            'notes' => $reason,
        ]);
    }

    public function processRefund(float $amount, string $reason): bool
    {
        if (!$this->getCanRefundAttribute()) {
            return false;
        }

        $refundAmount = min($amount, $this->getRefundableAmountAttribute());
        
        return $this->update([
            'refund_amount' => $this->refund_amount + $refundAmount,
            'refund_reason' => $reason,
            'refund_date' => now(),
            'status' => $this->refund_amount + $refundAmount >= $this->net_amount ? 'refunded' : 'partial_refund',
        ]);
    }

    public function verifyPayment(User $verifier): bool
    {
        return $this->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
    }

    public function flagAsSuspicious(string $reason): bool
    {
        return $this->update([
            'verification_status' => 'suspicious',
            'risk_score' => min(10, $this->risk_score + 3),
            'notes' => $reason,
        ]);
    }

    public function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $year = date('Y');
        $sequence = str_pad(static::withTrashed()->count() + 1, 8, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    public function calculateNetAmount(): float
    {
        return $this->amount - $this->fees + $this->tax_amount;
    }

    public function calculateRiskScore(): float
    {
        $score = 0;
        
        // Amount-based risk
        if ($this->amount > 10000) $score += 2;
        if ($this->amount > 50000) $score += 3;
        
        // Method-based risk
        if ($this->payment_method === 'crypto') $score += 3;
        if ($this->payment_method === 'cash') $score += 1;
        
        // Gateway-based risk
        if ($this->payment_gateway === 'manual') $score += 2;
        
        // Time-based risk
        if ($this->created_at->hour >= 22 || $this->created_at->hour <= 6) $score += 1;
        
        return min(10, $score);
    }

    public function createRecurringPayment(): Payment
    {
        if (!$this->is_recurring) {
            return $this;
        }

        return $this->replicate([
            'payment_number' => $this->generatePaymentNumber(),
            'status' => 'pending',
            'payment_status' => 'initiated',
            'verification_status' => 'unverified',
            'approval_status' => 'pending',
            'processed_date' => null,
            'settled_date' => null,
            'refund_amount' => 0,
            'recurring_count' => $this->recurring_count + 1,
            'recurring_remaining' => max(0, $this->recurring_remaining - 1),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = $payment->generatePaymentNumber();
            }
            
            if (empty($payment->currency)) {
                $payment->currency = 'USD';
            }
            
            if (empty($payment->status)) {
                $payment->status = 'pending';
            }
            
            if (empty($payment->payment_status)) {
                $payment->payment_status = 'initiated';
            }
            
            if (empty($payment->verification_status)) {
                $payment->verification_status = 'unverified';
            }
            
            if (empty($payment->approval_status)) {
                $payment->approval_status = 'pending';
            }
            
            // Calculate net amount if not set
            if (empty($payment->net_amount)) {
                $payment->net_amount = $payment->calculateNetAmount();
            }
            
            // Calculate risk score if not set
            if (empty($payment->risk_score)) {
                $payment->risk_score = $payment->calculateRiskScore();
            }
            
            // Set IP and user agent
            if (request()) {
                $payment->ip_address = request()->ip();
                $payment->user_agent = request()->userAgent();
            }
        });

        static::updating(function ($payment) {
            // Update net amount when amount or fees change
            if ($payment->isDirty(['amount', 'fees', 'tax_amount'])) {
                $payment->net_amount = $payment->calculateNetAmount();
            }
            
            // Update risk score when relevant fields change
            if ($payment->isDirty(['amount', 'payment_method', 'payment_gateway'])) {
                $payment->risk_score = $payment->calculateRiskScore();
            }
        });
    }
}
