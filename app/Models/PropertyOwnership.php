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

class PropertyOwnership extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Core Identifiers
        'property_id',
        'previous_owner_id',
        'new_owner_id',
        'transfer_type', // sale, inheritance, gift, foreclosure, auction
        'transfer_reason',
        
        // Financial Information
        'transfer_amount',
        'currency',
        'market_value',
        'property_tax_assessment',
        'capital_gains_tax',
        
        // Transfer Details
        'transfer_date',
        'effective_date',
        'registration_date',
        'deed_number',
        'registration_number',
        'title_number',
        
        // Parties Involved
        'agent_id',
        'company_id',
        'lawyer_id',
        'notary_id',
        'witnesses',
        
        // Legal Information
        'contract_id',
        'contract_type',
        'legal_description',
        'restrictions',
        'easements',
        'liens',
        'mortgages',
        
        // Status and Workflow
        'status', // pending, approved, registered, completed, cancelled
        'approval_status', // pending, approved, rejected
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        
        // Financial Breakdown
        'sale_price',
        'down_payment',
        'financing_amount',
        'closing_costs',
        'agent_commission',
        'legal_fees',
        'transfer_taxes',
        'other_fees',
        
        // Property Details at Transfer
        'property_condition',
        'included_items',
        'excluded_items',
        'warranty_information',
        'disclosure_statements',
        
        // Documents
        'deed_document_id',
        'title_document_id',
        'contract_document_id',
        'survey_document_id',
        'other_documents',
        
        // Metadata
        'notes',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transfer_amount' => 'decimal:2',
        'market_value' => 'decimal:2',
        'property_tax_assessment' => 'decimal:2',
        'capital_gains_tax' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'financing_amount' => 'decimal:2',
        'closing_costs' => 'decimal:2',
        'agent_commission' => 'decimal:2',
        'legal_fees' => 'decimal:2',
        'transfer_taxes' => 'decimal:2',
        'other_fees' => 'decimal:2',
        'transfer_date' => 'datetime',
        'effective_date' => 'datetime',
        'registration_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'witnesses' => 'array',
        'restrictions' => 'array',
        'easements' => 'array',
        'liens' => 'array',
        'mortgages' => 'array',
        'included_items' => 'array',
        'excluded_items' => 'array',
        'warranty_information' => 'array',
        'disclosure_statements' => 'array',
        'other_documents' => 'array',
        'metadata' => 'json',
    ];

    // Core Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function previousOwner(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'previous_owner_id');
    }

    public function newOwner(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'new_owner_id');
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

    public function notary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'notary_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    // Document Relationships
    public function deedDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'deed_document_id');
    }

    public function titleDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'title_document_id');
    }

    public function contractDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'contract_document_id');
    }

    public function surveyDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'survey_document_id');
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

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('previous_owner_id', $ownerId)
                    ->orWhere('new_owner_id', $ownerId);
    }

    public function scopeByTransferType($query, $type)
    {
        return $query->where('transfer_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transfer_date', [$startDate, $endDate]);
    }

    public function scopeByAmountRange($query, $minAmount, $maxAmount = null)
    {
        $query->where('transfer_amount', '>=', $minAmount);
        
        if ($maxAmount) {
            $query->where('transfer_amount', '<=', $maxAmount);
        }
        
        return $query;
    }

    // Methods
    public function getFormattedTransferAmountAttribute(): string
    {
        return number_format($this->transfer_amount, 2) . ' ' . $this->currency;
    }

    public function getCapitalGainsAmountAttribute(): float
    {
        return max(0, $this->transfer_amount - $this->property_tax_assessment);
    }

    public function getNetProceedsAttribute(): float
    {
        $totalCosts = $this->closing_costs + $this->agent_commission + 
                      $this->legal_fees + $this->transfer_taxes + $this->other_fees;
        
        return $this->transfer_amount - $totalCosts;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->property_tax_assessment <= 0) {
            return 0;
        }
        
        return (($this->transfer_amount - $this->property_tax_assessment) / $this->property_tax_assessment) * 100;
    }

    public function getDaysToRegistrationAttribute(): int
    {
        if (!$this->transfer_date || !$this->registration_date) {
            return 0;
        }
        
        return $this->transfer_date->diffInDays($this->registration_date);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed' && !is_null($this->registration_date);
    }

    public function getIsPendingApprovalAttribute(): bool
    {
        return $this->status === 'pending' && $this->approval_status === 'pending';
    }

    public function getTransferTypeLabelAttribute(): string
    {
        $types = [
            'sale' => 'Sale',
            'inheritance' => 'Inheritance',
            'gift' => 'Gift',
            'foreclosure' => 'Foreclosure',
            'auction' => 'Auction',
        ];

        return $types[$this->transfer_type] ?? ucfirst($this->transfer_type);
    }

    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'registered' => 'Registered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    // Business Logic Methods
    public function approveTransfer(User $approver, string $notes = ''): bool
    {
        return $this->update([
            'status' => 'approved',
            'approval_status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function rejectTransfer(User $rejecter, string $reason): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'approval_status' => 'rejected',
            'rejected_by' => $rejecter->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function completeTransfer(array $registrationData = []): bool
    {
        return $this->update([
            'status' => 'completed',
            'registration_date' => $registrationData['registration_date'] ?? now(),
            'registration_number' => $registrationData['registration_number'] ?? null,
            'title_number' => $registrationData['title_number'] ?? null,
        ]);
    }

    public function calculateTransferTaxes(): float
    {
        // This would typically be based on local tax rates
        $taxRate = 0.02; // 2% transfer tax
        return $this->transfer_amount * $taxRate;
    }

    public function generateTransferReference(): string
    {
        $prefix = 'TRF';
        $year = date('Y');
        $sequence = str_pad(static::withTrashed()->count() + 1, 8, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    public function hasAllRequiredDocuments(): bool
    {
        $requiredDocuments = [
            'deed_document_id',
            'title_document_id',
            'contract_document_id',
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
            'deed_document_id' => 'Deed Document',
            'title_document_id' => 'Title Document',
            'contract_document_id' => 'Contract Document',
        ];

        foreach ($requiredDocuments as $field => $label) {
            if (empty($this->$field)) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    public function getTotalFeesAttribute(): float
    {
        return $this->closing_costs + $this->agent_commission + 
               $this->legal_fees + $this->transfer_taxes + $this->other_fees;
    }

    public function getFinancingRatioAttribute(): float
    {
        if ($this->transfer_amount <= 0) {
            return 0;
        }
        
        return ($this->financing_amount / $this->transfer_amount) * 100;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ownership) {
            if (empty($ownership->transfer_date)) {
                $ownership->transfer_date = now();
            }
            
            if (empty($ownership->currency)) {
                $ownership->currency = 'USD';
            }
            
            if (empty($ownership->status)) {
                $ownership->status = 'pending';
            }
            
            if (empty($ownership->approval_status)) {
                $ownership->approval_status = 'pending';
            }
        });

        static::updated(function ($ownership) {
            // Update property owner when transfer is completed
            if ($ownership->wasChanged('status') && $ownership->status === 'completed') {
                $ownership->property->update(['owner_id' => $ownership->new_owner_id]);
            }
        });
    }
}
