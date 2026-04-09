<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PropertyTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Core Identifiers
        'property_id',
        'transaction_number',
        'transaction_type', // sale, purchase, rental, lease, mortgage, refinance
        'transaction_category', // primary, secondary, investment, commercial
        
        // Parties Involved
        'buyer_id',
        'seller_id',
        'agent_id',
        'company_id',
        'lawyer_id',
        'broker_id',
        
        // Financial Information
        'transaction_amount',
        'currency',
        'original_price',
        'final_price',
        'discount_amount',
        'commission_rate',
        'commission_amount',
        'tax_amount',
        'fees',
        
        // Transaction Details
        'transaction_date',
        'closing_date',
        'possession_date',
        'financing_type', // cash, mortgage, loan, owner_financing
        'loan_amount',
        'loan_term',
        'interest_rate',
        'down_payment',
        
        // Property Information at Transaction
        'property_condition',
        'property_value',
        'assessed_value',
        'market_value',
        'appraisal_value',
        
        // Location and Legal
        'jurisdiction',
        'registration_number',
        'deed_number',
        'title_number',
        'parcel_number',
        
        // Status and Workflow
        'status', // pending, in_progress, completed, cancelled, failed
        'stage', // initial, negotiation, due_diligence, financing, closing, post_closing
        'priority', // low, medium, high, urgent
        
        // Dates and Deadlines
        'offer_date',
        'acceptance_date',
        'contingency_deadline',
        'financing_deadline',
        'inspection_deadline',
        'appraisal_deadline',
        
        // Contingencies
        'financing_contingency',
        'inspection_contingency',
        'appraisal_contingency',
        'title_contingency',
        'survey_contingency',
        'other_contingencies',
        
        // Documents
        'contract_id',
        'agreement_id',
        'disclosure_id',
        'inspection_report_id',
        'appraisal_report_id',
        'title_report_id',
        
        // Notes and Metadata
        'notes',
        'internal_notes',
        'client_notes',
        'terms',
        'conditions',
        'special_instructions',
        'metadata',
        
        // Audit
        'created_by',
        'updated_by',
        'approved_by',
        'completed_by',
    ];

    protected $casts = [
        'transaction_amount' => 'decimal:2',
        'original_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'commission_rate' => 'decimal:4',
        'commission_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'fees' => 'decimal:2',
        'property_value' => 'decimal:2',
        'assessed_value' => 'decimal:2',
        'market_value' => 'decimal:2',
        'appraisal_value' => 'decimal:2',
        'loan_amount' => 'decimal:2',
        'loan_term' => 'integer',
        'interest_rate' => 'decimal:4',
        'down_payment' => 'decimal:2',
        'transaction_date' => 'datetime',
        'closing_date' => 'datetime',
        'possession_date' => 'datetime',
        'offer_date' => 'datetime',
        'acceptance_date' => 'datetime',
        'contingency_deadline' => 'datetime',
        'financing_deadline' => 'datetime',
        'inspection_deadline' => 'datetime',
        'appraisal_deadline' => 'datetime',
        'financing_contingency' => 'boolean',
        'inspection_contingency' => 'boolean',
        'appraisal_contingency' => 'boolean',
        'title_contingency' => 'boolean',
        'survey_contingency' => 'boolean',
        'other_contingencies' => 'array',
        'terms' => 'array',
        'conditions' => 'array',
        'special_instructions' => 'array',
        'metadata' => 'json',
    ];

    // Core Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'seller_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function broker(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'broker_id');
    }

    // Document Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'agreement_id');
    }

    public function disclosure(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'disclosure_id');
    }

    public function inspectionReport(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'inspection_report_id');
    }

    public function appraisalReport(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'appraisal_report_id');
    }

    public function titleReport(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'title_report_id');
    }

    // Related Transactions
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AgentCommission::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(TransactionExpense::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TransactionDocument::class);
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByBuyer($query, $buyerId)
    {
        return $query->where('buyer_id', $buyerId);
    }

    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeByAmountRange($query, $minAmount, $maxAmount = null)
    {
        $query->where('transaction_amount', '>=', $minAmount);
        
        if ($maxAmount) {
            $query->where('transaction_amount', '<=', $maxAmount);
        }
        
        return $query;
    }

    public function scopeHighValue($query)
    {
        return $query->where('transaction_amount', '>', 1000000);
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    // Methods
    public function getFormattedTransactionAmountAttribute(): string
    {
        return number_format($this->transaction_amount, 2) . ' ' . $this->currency;
    }

    public function getProfitAmountAttribute(): float
    {
        return $this->final_price - $this->original_price;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->original_price <= 0) {
            return 0;
        }
        
        return ($this->getProfitAmountAttribute() / $this->original_price) * 100;
    }

    public function getDiscountPercentageAttribute(): float
    {
        if ($this->original_price <= 0) {
            return 0;
        }
        
        return ($this->discount_amount / $this->original_price) * 100;
    }

    public function getNetAmountAttribute(): float
    {
        return $this->final_price - $this->commission_amount - $this->tax_amount - $this->fees;
    }

    public function getLoanToValueRatioAttribute(): float
    {
        if ($this->final_price <= 0) {
            return 0;
        }
        
        return ($this->loan_amount / $this->final_price) * 100;
    }

    public function getDownPaymentPercentageAttribute(): float
    {
        if ($this->final_price <= 0) {
            return 0;
        }
        
        return ($this->down_payment / $this->final_price) * 100;
    }

    public function getDaysToClosingAttribute(): int
    {
        if (!$this->offer_date || !$this->closing_date) {
            return 0;
        }
        
        return $this->offer_date->diffInDays($this->closing_date);
    }

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->closing_date) {
            return false;
        }
        
        return $this->status !== 'completed' && $this->closing_date->isPast();
    }

    public function getHasActiveContingenciesAttribute(): bool
    {
        return $this->financing_contingency || 
               $this->inspection_contingency || 
               $this->appraisal_contingency || 
               $this->title_contingency || 
               $this->survey_contingency;
    }

    public function getTransactionTypeLabelAttribute(): string
    {
        $types = [
            'sale' => 'Sale',
            'purchase' => 'Purchase',
            'rental' => 'Rental',
            'lease' => 'Lease',
            'mortgage' => 'Mortgage',
            'refinance' => 'Refinance',
        ];

        return $types[$this->transaction_type] ?? ucfirst($this->transaction_type);
    }

    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'failed' => 'Failed',
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    // Business Logic Methods
    public function moveToStage(string $stage, array $data = []): bool
    {
        return $this->update([
            'stage' => $stage,
            'metadata' => array_merge($this->metadata ?? [], [
                'stage_history' => array_merge($this->metadata['stage_history'] ?? [], [
                    'stage' => $this->stage,
                    'moved_at' => now()->toISOString(),
                    'moved_by' => auth()->id(),
                ]),
            ]),
            ...$data,
        ]);
    }

    public function completeTransaction(array $completionData = []): bool
    {
        return $this->update([
            'status' => 'completed',
            'stage' => 'post_closing',
            'closing_date' => $completionData['closing_date'] ?? now(),
            'possession_date' => $completionData['possession_date'] ?? null,
            'completed_by' => auth()->id(),
        ]);
    }

    public function cancelTransaction(string $reason): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'metadata' => array_merge($this->metadata ?? [], [
                'cancellation_reason' => $reason,
                'cancelled_at' => now()->toISOString(),
                'cancelled_by' => auth()->id(),
            ]),
        ]);
    }

    public function addContingency(string $type, array $details): bool
    {
        $contingencies = $this->other_contingencies ?? [];
        $contingencies[] = [
            'type' => $type,
            'details' => $details,
            'added_at' => now()->toISOString(),
            'added_by' => auth()->id(),
        ];

        return $this->update(['other_contingencies' => $contingencies]);
    }

    public function removeContingency(string $type): bool
    {
        $contingencies = $this->other_contingencies ?? [];
        $contingencies = array_filter($contingencies, function ($contingency) use ($type) {
            return $contingency['type'] !== $type;
        });

        return $this->update(['other_contingencies' => array_values($contingencies)]);
    }

    public function generateTransactionNumber(): string
    {
        $prefix = 'TXN';
        $year = date('Y');
        $sequence = str_pad(static::withTrashed()->count() + 1, 8, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    public function calculateCommission(): float
    {
        return $this->final_price * ($this->commission_rate / 100);
    }

    public function hasAllRequiredDocuments(): bool
    {
        $requiredDocuments = [
            'contract_id',
            'disclosure_id',
        ];

        foreach ($requiredDocuments as $document) {
            if (empty($this->$document)) {
                return false;
            }
        }

        return true;
    }

    public function getMissingDocuments(): array
    {
        $missing = [];
        $requiredDocuments = [
            'contract_id' => 'Contract',
            'disclosure_id' => 'Disclosure',
        ];

        foreach ($requiredDocuments as $field => $label) {
            if (empty($this->$field)) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    public function getTotalExpensesAttribute(): float
    {
        return $this->expenses()->sum('amount');
    }

    public function getNetProfitAttribute(): float
    {
        return $this->getProfitAmountAttribute() - $this->getTotalExpensesAttribute();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = $transaction->generateTransactionNumber();
            }
            
            if (empty($transaction->currency)) {
                $transaction->currency = 'USD';
            }
            
            if (empty($transaction->status)) {
                $transaction->status = 'pending';
            }
            
            if (empty($transaction->stage)) {
                $transaction->stage = 'initial';
            }
            
            if (empty($transaction->priority)) {
                $transaction->priority = 'medium';
            }
        });

        static::updated(function ($transaction) {
            // Update property status when transaction is completed
            if ($transaction->wasChanged('status') && $transaction->status === 'completed') {
                if ($transaction->transaction_type === 'sale') {
                    $transaction->property->update(['status' => 'sold']);
                } elseif ($transaction->transaction_type === 'rental') {
                    $transaction->property->update(['status' => 'rented']);
                }
            }
        });
    }
}
