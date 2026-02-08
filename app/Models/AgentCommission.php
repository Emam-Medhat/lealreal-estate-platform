<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentCommission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Core Identifiers
        'agent_id',
        'property_id',
        'transaction_id',
        'contract_id',
        'client_id',
        'company_id',
        'commission_number',
        
        // Commission Details
        'commission_type', // percentage, fixed, tiered, bonus, referral
        'commission_rate',
        'commission_amount',
        'currency',
        'base_amount',
        
        // Transaction Information
        'transaction_amount',
        'transaction_type',
        'transaction_date',
        'closing_date',
        
        // Commission Structure
        'tier_level',
        'tier_rate',
        'bonus_amount',
        'referral_fee',
        'override_amount',
        
        // Payment Information
        'payment_status', // pending, approved, paid, partially_paid, overdue
        'payment_method',
        'payment_date',
        'paid_amount',
        'outstanding_amount',
        'due_date',
        
        // Commission Split
        'split_type', // none, team, broker, referral
        'split_percentage',
        'agent_share',
        'broker_share',
        'referral_share',
        'team_share',
        
        // Performance Metrics
        'target_amount',
        'target_achieved',
        'performance_bonus',
        'quarterly_bonus',
        'annual_bonus',
        
        // Dates and Deadlines
        'earned_date',
        'approved_date',
        'due_date',
        'paid_date',
        'void_date',
        
        // Status and Workflow
        'status', // earned, pending_approval, approved, paid, voided, disputed
        'approval_status', // pending, approved, rejected
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        
        // Adjustments
        'adjustment_amount',
        'adjustment_reason',
        'adjustment_date',
        'adjusted_by',
        
        // Tax Information
        'tax_rate',
        'tax_amount',
        'net_amount',
        'withholding_tax',
        
        // Notes and Metadata
        'notes',
        'internal_notes',
        'description',
        'terms',
        'conditions',
        'metadata',
        
        // Legacy Fields (for backward compatibility)
        'type',
        'amount',
        'percentage',
        'commission_date',
        'payment_reference',
        'client_name',
        'property_address',
        'transaction_value',
        'commission_rate',
        'bonus_amount',
        'deduction_amount',
        'invoice_number',
        'receipt_number',
        'bank_account',
        'payment_terms',
        'split_with_agent_id',
        
        // Audit
        'created_by',
        'updated_by',
        'paid_by',
        'voided_by',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:4',
        'commission_amount' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'transaction_amount' => 'decimal:2',
        'tier_rate' => 'decimal:4',
        'bonus_amount' => 'decimal:2',
        'referral_fee' => 'decimal:2',
        'override_amount' => 'decimal:2',
        'split_percentage' => 'decimal:4',
        'agent_share' => 'decimal:2',
        'broker_share' => 'decimal:2',
        'referral_share' => 'decimal:2',
        'team_share' => 'decimal:2',
        'target_amount' => 'decimal:2',
        'target_achieved' => 'decimal:2',
        'performance_bonus' => 'decimal:2',
        'quarterly_bonus' => 'decimal:2',
        'annual_bonus' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'withholding_tax' => 'decimal:2',
        'transaction_date' => 'datetime',
        'closing_date' => 'datetime',
        'earned_date' => 'datetime',
        'approved_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_date' => 'datetime',
        'void_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'adjustment_date' => 'datetime',
        'terms' => 'array',
        'conditions' => 'array',
        'metadata' => 'json',
        
        // Legacy fields for backward compatibility
        'amount' => 'decimal:15,2',
        'percentage' => 'decimal:5,2',
        'base_amount' => 'decimal:15,2',
        'commission_date' => 'date',
        'payment_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'transaction_value' => 'decimal:15,2',
        'commission_rate' => 'decimal:5,2',
        'bonus_amount' => 'decimal:15,2',
        'deduction_amount' => 'decimal:15,2',
        'tax_amount' => 'decimal:15,2',
        'net_amount' => 'decimal:15,2',
        'split_percentage' => 'decimal:5,2',
        'next_due_date' => 'date',
        'is_recurring' => 'boolean',
        'custom_fields' => 'json',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function splitWithAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'split_with_agent_id');
    }

    public function parentCommission(): BelongsTo
    {
        return $this->belongsTo(AgentCommission::class, 'parent_commission_id');
    }

    public function childCommissions(): HasMany
    {
        return $this->hasMany(AgentCommission::class, 'parent_commission_id');
    }

    // Scopes
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByTransaction($query, $transactionId)
    {
        return $query->where('transaction_id', $transactionId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('commission_date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('commission_date', [$startDate, $endDate]);
    }

    public function scopeByDueDate($query, $date)
    {
        return $query->whereDate('due_date', $date);
    }

    public function scopeByDueDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('due_date', [$startDate, $endDate]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('payment_status', 'partially_paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', 'unpaid')
                    ->where('due_date', '<', today());
    }

    public function scopeDueToday($query)
    {
        return $query->where('payment_status', 'unpaid')
                    ->whereDate('due_date', today());
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('payment_status', 'unpaid')
                    ->whereBetween('due_date', [today(), today()->addDays($days)]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('commission_date', now()->month)
                    ->whereYear('commission_date', now()->year);
    }

    public function scopeLastMonth($query)
    {
        return $query->whereMonth('commission_date', now()->subMonth()->month)
                    ->whereYear('commission_date', now()->subMonth()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('commission_date', now()->year);
    }

    public function scopeSales($query)
    {
        return $query->where('type', 'sale');
    }

    public function scopeRental($query)
    {
        return $query->where('type', 'rental');
    }

    public function scopeReferral($query)
    {
        return $query->where('type', 'referral');
    }

    public function scopeBonus($query)
    {
        return $query->where('type', 'bonus');
    }

    public function scopeDeduction($query)
    {
        return $query->where('type', 'deduction');
    }

    public function scopeWithSplit($query)
    {
        return $query->whereNotNull('split_with_agent_id');
    }

    public function scopeWithoutSplit($query)
    {
        return $query->whereNull('split_with_agent_id');
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeOneTime($query)
    {
        return $query->where('is_recurring', false);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('description', 'like', '%' . $term . '%')
              ->orWhere('notes', 'like', '%' . $term . '%')
              ->orWhere('client_name', 'like', '%' . $term . '%')
              ->orWhere('property_address', 'like', '%' . $term . '%')
              ->orWhere('invoice_number', 'like', '%' . $term . '%')
              ->orWhere('receipt_number', 'like', '%' . $term . '%')
              ->orWhere('payment_reference', 'like', '%' . $term . '%');
        });
    }

    // Helper Methods
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' SAR';
    }

    public function getFormattedBaseAmountAttribute(): string
    {
        return number_format($this->base_amount, 2) . ' SAR';
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return number_format($this->net_amount, 2) . ' SAR';
    }

    public function getFormattedTransactionValueAttribute(): string
    {
        return number_format($this->transaction_value, 2) . ' SAR';
    }

    public function getFormattedBonusAmountAttribute(): string
    {
        return number_format($this->bonus_amount, 2) . ' SAR';
    }

    public function getFormattedDeductionAmountAttribute(): string
    {
        return number_format($this->deduction_amount, 2) . ' SAR';
    }

    public function getFormattedTaxAmountAttribute(): string
    {
        return number_format($this->tax_amount, 2) . ' SAR';
    }

    public function getFormattedPercentageAttribute(): string
    {
        return number_format($this->percentage, 2) . '%';
    }

    public function getFormattedCommissionRateAttribute(): string
    {
        return number_format($this->commission_rate, 2) . '%';
    }

    public function getFormattedSplitPercentageAttribute(): string
    {
        return number_format($this->split_percentage, 2) . '%';
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date || $this->payment_status === 'paid') {
            return null;
        }

        $diff = $this->due_date->diffInDays(today(), false);
        
        return $diff >= 0 ? $diff : -$diff;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->payment_status === 'unpaid' && 
               $this->due_date && 
               $this->due_date->isPast();
    }

    public function getIsDueTodayAttribute(): bool
    {
        return $this->payment_status === 'unpaid' && 
               $this->due_date && 
               $this->due_date->isToday();
    }

    public function getIsDueSoonAttribute(): bool
    {
        return $this->payment_status === 'unpaid' && 
               $this->due_date && 
               $this->due_date->isBetween(today(), today()->addDays(7));
    }

    public function getHasSplitAttribute(): bool
    {
        return !empty($this->split_with_agent_id);
    }

    public function getIsRecurringAttribute(): bool
    {
        return $this->is_recurring;
    }

    public function getSplitAmountAttribute(): float
    {
        if (!$this->has_split) {
            return 0;
        }

        return $this->amount * ($this->split_percentage / 100);
    }

    public function getFormattedSplitAmountAttribute(): string
    {
        return number_format($this->split_amount, 2) . ' SAR';
    }

    public function getAgentShareAttribute(): float
    {
        if (!$this->has_split) {
            return $this->amount;
        }

        return $this->amount - $this->split_amount;
    }

    public function getFormattedAgentShareAttribute(): string
    {
        return number_format($this->agent_share, 2) . ' SAR';
    }

    public function getStatusColorAttribute(): string
    {
        switch ($this->status) {
            case 'pending':
                return 'yellow';
            case 'approved':
                return 'green';
            case 'rejected':
                return 'red';
            default:
                return 'gray';
        }
    }

    public function getPaymentStatusColorAttribute(): string
    {
        switch ($this->payment_status) {
            case 'unpaid':
                return $this->is_overdue ? 'red' : 'yellow';
            case 'partially_paid':
                return 'orange';
            case 'paid':
                return 'green';
            default:
                return 'gray';
        }
    }

    public function getTypeIconAttribute(): string
    {
        switch ($this->type) {
            case 'sale':
                return 'home';
            case 'rental':
                return 'calendar';
            case 'referral':
                return 'users';
            case 'bonus':
                return 'gift';
            case 'deduction':
                return 'minus-circle';
            default:
                return 'dollar-sign';
        }
    }

    public function getCustomFieldsListAttribute(): array
    {
        return $this->custom_fields ?? [];
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPartiallyPaid(): bool
    {
        return $this->payment_status === 'partially_paid';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    public function canBePaid(): bool
    {
        return $this->status === 'approved' && $this->payment_status === 'unpaid';
    }

    public function canBePartiallyPaid(): bool
    {
        return $this->status === 'approved' && in_array($this->payment_status, ['unpaid', 'partially_paid']);
    }

    public function approve($approvedBy = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    public function reject($reason = null, $rejectedBy = null): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_by' => $rejectedBy,
            'rejected_at' => now(),
        ]);
    }

    public function markAsPaid($paymentDate = null, $paymentMethod = null, $paymentReference = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_date' => $paymentDate ?? today(),
            'payment_date' => $paymentDate ?? today(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);
    }

    public function markAsPartiallyPaid($amount, $paymentDate = null, $paymentMethod = null, $paymentReference = null): void
    {
        $this->update([
            'payment_status' => 'partially_paid',
            'payment_date' => $paymentDate ?? today(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);
    }

    public function calculateNetAmount(): void
    {
        $netAmount = $this->amount;
        
        if ($this->bonus_amount) {
            $netAmount += $this->bonus_amount;
        }
        
        if ($this->deduction_amount) {
            $netAmount -= $this->deduction_amount;
        }
        
        if ($this->tax_amount) {
            $netAmount -= $this->tax_amount;
        }

        $this->update(['net_amount' => $netAmount]);
    }

    public function createSplitCommission(): ?AgentCommission
    {
        if (!$this->has_split || !$this->split_with_agent_id) {
            return null;
        }

        return self::create([
            'agent_id' => $this->split_with_agent_id,
            'property_id' => $this->property_id,
            'transaction_id' => $this->transaction_id,
            'type' => $this->type,
            'amount' => $this->split_amount,
            'percentage' => $this->percentage,
            'base_amount' => $this->base_amount,
            'commission_date' => $this->commission_date,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'description' => 'Split commission from: ' . $this->description,
            'notes' => 'Split commission from agent ID: ' . $this->agent_id,
            'client_name' => $this->client_name,
            'property_address' => $this->property_address,
            'transaction_value' => $this->transaction_value,
            'commission_rate' => $this->commission_rate,
            'due_date' => $this->due_date,
            'paid_date' => $this->paid_date,
            'parent_commission_id' => $this->id,
            'is_recurring' => false,
        ]);
    }

    public function createNextRecurringCommission(): ?AgentCommission
    {
        if (!$this->is_recurring || !$this->next_due_date) {
            return null;
        }

        $nextDate = $this->next_due_date;
        
        return self::create([
            'agent_id' => $this->agent_id,
            'property_id' => $this->property_id,
            'transaction_id' => $this->transaction_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'percentage' => $this->percentage,
            'base_amount' => $this->base_amount,
            'commission_date' => $nextDate,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'description' => 'Recurring commission: ' . $this->description,
            'notes' => 'Auto-generated recurring commission',
            'client_name' => $this->client_name,
            'property_address' => $this->property_address,
            'transaction_value' => $this->transaction_value,
            'commission_rate' => $this->commission_rate,
            'due_date' => $nextDate,
            'parent_commission_id' => $this->id,
            'is_recurring' => true,
            'recurrence_period' => $this->recurrence_period,
            'next_due_date' => $this->calculateNextDueDate($nextDate),
        ]);
    }

    public function calculateNextDueDate($currentDate): ?string
    {
        if (!$this->recurrence_period) {
            return null;
        }

        switch ($this->recurrence_period) {
            case 'daily':
                return $currentDate->addDay()->format('Y-m-d');
            case 'weekly':
                return $currentDate->addWeek()->format('Y-m-d');
            case 'monthly':
                return $currentDate->addMonth()->format('Y-m-d');
            case 'quarterly':
                return $currentDate->addMonths(3)->format('Y-m-d');
            case 'yearly':
                return $currentDate->addYear()->format('Y-m-d');
            default:
                return null;
        }
    }

    public function setCustomField(string $key, $value): void
    {
        $customFields = $this->custom_fields ?? [];
        $customFields[$key] = $value;
        $this->update(['custom_fields' => $customFields]);
    }

    public function getCustomField(string $key, $default = null)
    {
        $customFields = $this->custom_fields ?? [];
        return $customFields[$key] ?? $default;
    }

    public function generateInvoiceNumber(): void
    {
        if (empty($this->invoice_number)) {
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->update(['invoice_number' => $invoiceNumber]);
        }
    }

    public function generateReceiptNumber(): void
    {
        if (empty($this->receipt_number) && $this->payment_status === 'paid') {
            $receiptNumber = 'REC-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->update(['receipt_number' => $receiptNumber]);
        }
    }
}
