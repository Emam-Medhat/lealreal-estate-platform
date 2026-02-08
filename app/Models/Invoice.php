<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Core Identifiers
        'invoice_number',
        'user_id',
        'client_id',
        'agent_id',
        'company_id',
        'property_id',
        'contract_id',
        'transaction_id',
        
        // Invoice Details
        'type', // subscription, property, service, penalty, other, maintenance, consultation, commission
        'category', // primary, secondary, recurring, one_time
        'title',
        'description',
        'items', // JSON array of invoice items
        'line_items', // Detailed line items with descriptions
        
        // Financial Information
        'amount', // Total amount (legacy support)
        'currency',
        'subtotal',
        'tax_amount',
        'tax_rate',
        'discount_amount',
        'discount_rate',
        'total', // Final total amount
        'total_amount', // Legacy support
        'outstanding_amount',
        'paid_amount',
        'balance_due',
        
        // Property-specific Information
        'property_address',
        'property_reference',
        'property_type',
        'service_period_start',
        'service_period_end',
        'billing_period', // monthly, quarterly, annually, custom
        
        // Commission Information (for agent invoices)
        'commission_rate',
        'commission_amount',
        'commission_type', // percentage, fixed, tiered
        'commission_split',
        
        // Dates
        'issue_date',
        'due_date',
        'paid_date',
        'sent_date',
        'viewed_date',
        'reminder_sent_at',
        
        // Status and Workflow
        'status', // draft, pending, processing, paid, cancelled, refunded, overdue
        'payment_status', // unpaid, partially_paid, paid, overpaid, refunded
        'approval_status', // pending, approved, rejected
        'approval_required',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        
        // Payment Terms
        'payment_terms', // net_15, net_30, net_60, due_on_receipt
        'late_fee_rate',
        'late_fee_amount',
        'grace_period_days',
        'auto_reminder_enabled',
        'auto_late_fee_enabled',
        
        // Tax Information
        'tax_exempt',
        'tax_id',
        'tax_registration',
        'reverse_tax_applicable',
        'tax_jurisdiction',
        
        // Discount Information
        'discount_type', // percentage, fixed, volume, early_payment
        'discount_reason',
        'discount_valid_until',
        'early_payment_discount',
        'early_payment_discount_days',
        
        // Recurring Invoice Information
        'is_recurring',
        'recurring_frequency', // weekly, monthly, quarterly, yearly
        'recurring_interval',
        'recurring_start_date',
        'recurring_end_date',
        'recurring_next_date',
        'recurring_count',
        'recurring_remaining',
        
        // Multi-currency Support
        'exchange_rate',
        'original_currency',
        'original_amount',
        
        // Notes and Metadata
        'notes',
        'internal_notes',
        'client_notes',
        'terms',
        'conditions',
        'custom_fields',
        'metadata',
        
        // Integration
        'external_system_id',
        'integration_type',
        'sync_status',
        'last_synced_at',
        
        // Audit
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'items' => 'array',
        'line_items' => 'array',
        'metadata' => 'json',
        'custom_fields' => 'json',
        'amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'discount_amount' => 'decimal:2',
        'discount_rate' => 'decimal:4',
        'total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'commission_rate' => 'decimal:4',
        'commission_amount' => 'decimal:2',
        'late_fee_rate' => 'decimal:4',
        'late_fee_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'original_amount' => 'decimal:2',
        'early_payment_discount' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'datetime',
        'sent_date' => 'datetime',
        'viewed_date' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'service_period_start' => 'date',
        'service_period_end' => 'date',
        'recurring_start_date' => 'date',
        'recurring_end_date' => 'date',
        'recurring_next_date' => 'date',
        'discount_valid_until' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'tax_exempt' => 'boolean',
        'reverse_tax_applicable' => 'boolean',
        'is_recurring' => 'boolean',
        'auto_reminder_enabled' => 'boolean',
        'auto_late_fee_enabled' => 'boolean',
        'approval_required' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Core Relationships
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

    // Payment Relationships
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function successfulPayments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', 'completed');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', 'refunded');
    }

    // Document Relationships
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function invoiceDocuments(): HasMany
    {
        return $this->hasMany(Document::class)->where('type', 'invoice');
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
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
                    ->where('due_date', '<', now());
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('payment_status', 'partially_paid');
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

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('issue_date', [$startDate, $endDate]);
    }

    public function scopeDueDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('due_date', [$startDate, $endDate]);
    }

    public function scopeByAmountRange($query, $minAmount, $maxAmount = null)
    {
        $query->where('total', '>=', $minAmount);
        
        if ($maxAmount) {
            $query->where('total', '<=', $maxAmount);
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

    // Helper Methods
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2) . ' ' . $this->currency;
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 2) . ' ' . $this->currency;
    }

    public function getFormattedTaxAmountAttribute(): string
    {
        return number_format($this->tax_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedDiscountAmountAttribute(): string
    {
        return number_format($this->discount_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedOutstandingAmountAttribute(): string
    {
        return number_format($this->outstanding_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return number_format($this->paid_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedBalanceDueAttribute(): string
    {
        return number_format($this->balance_due, 2) . ' ' . $this->currency;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'paid';
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    public function getIsPartiallyPaidAttribute(): bool
    {
        return $this->payment_status === 'partially_paid';
    }

    public function getIsUnpaidAttribute(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->getIsOverdueAttribute()) {
            return 0;
        }
        
        return $this->due_date->diffInDays(now());
    }

    public function getPaymentProgressPercentageAttribute(): float
    {
        if ($this->total <= 0) {
            return 0;
        }
        
        return ($this->paid_amount / $this->total) * 100;
    }

    public function getLateFeeAmountAttribute(): float
    {
        if (!$this->getIsOverdueAttribute()) {
            return 0;
        }
        
        $daysOverdue = $this->getDaysOverdueAttribute();
        $dailyRate = $this->late_fee_rate / 100 / 365;
        
        return $this->balance_due * $dailyRate * $daysOverdue;
    }

    public function getEarlyPaymentDiscountAmountAttribute(): float
    {
        if (!$this->early_payment_discount || !$this->early_payment_discount_days) {
            return 0;
        }
        
        $daysUntilDue = $this->due_date->diffInDays(now());
        
        if ($daysUntilDue <= $this->early_payment_discount_days) {
            return $this->total * ($this->early_payment_discount / 100);
        }
        
        return 0;
    }

    public function getNetAmountAttribute(): float
    {
        return $this->total - $this->discount_amount + $this->tax_amount + $this->getLateFeeAmountAttribute() - $this->getEarlyPaymentDiscountAmountAttribute();
    }

    public function getTypeLabelAttribute(): string
    {
        $types = [
            'subscription' => 'Subscription',
            'property' => 'Property',
            'service' => 'Service',
            'penalty' => 'Penalty',
            'other' => 'Other',
            'maintenance' => 'Maintenance',
            'consultation' => 'Consultation',
            'commission' => 'Commission',
        ];

        return $types[$this->type] ?? ucfirst($this->type);
    }

    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'draft' => 'Draft',
            'pending' => 'Pending',
            'processing' => 'Processing',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
            'overdue' => 'Overdue',
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        $statuses = [
            'unpaid' => 'Unpaid',
            'partially_paid' => 'Partially Paid',
            'paid' => 'Paid',
            'overpaid' => 'Overpaid',
            'refunded' => 'Refunded',
        ];

        return $statuses[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    public function getPaymentTermsLabelAttribute(): string
    {
        $terms = [
            'net_15' => 'Net 15',
            'net_30' => 'Net 30',
            'net_60' => 'Net 60',
            'due_on_receipt' => 'Due on Receipt',
        ];

        return $terms[$this->payment_terms] ?? ucfirst($this->payment_terms);
    }

    // Business Logic Methods
    public function approveInvoice(User $approver, string $notes = ''): bool
    {
        return $this->update([
            'approval_status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function rejectInvoice(User $rejecter, string $reason): bool
    {
        return $this->update([
            'approval_status' => 'rejected',
            'rejected_by' => $rejecter->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function markAsPaid(): bool
    {
        return $this->update([
            'status' => 'paid',
            'payment_status' => 'paid',
            'paid_date' => now(),
            'outstanding_amount' => 0,
            'balance_due' => 0,
        ]);
    }

    public function markAsPartiallyPaid(float $amount): bool
    {
        $newPaidAmount = $this->paid_amount + $amount;
        $newOutstandingAmount = max(0, $this->total - $newPaidAmount);
        
        $paymentStatus = $newOutstandingAmount <= 0 ? 'paid' : 'partially_paid';
        
        return $this->update([
            'payment_status' => $paymentStatus,
            'paid_amount' => $newPaidAmount,
            'outstanding_amount' => $newOutstandingAmount,
            'balance_due' => $newOutstandingAmount,
        ]);
    }

    public function cancelInvoice(string $reason): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'notes' => $reason,
        ]);
    }

    public function addPayment(array $paymentData): Payment
    {
        return $this->payments()->create([
            'amount' => $paymentData['amount'],
            'payment_method' => $paymentData['payment_method'],
            'description' => $paymentData['description'] ?? '',
            'notes' => $paymentData['notes'] ?? '',
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);
    }

    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $sequence = str_pad(static::withTrashed()->count() + 1, 8, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    public function calculateTaxAmount(): float
    {
        if ($this->tax_exempt) {
            return 0;
        }
        
        $taxableAmount = $this->subtotal - $this->discount_amount;
        return $taxableAmount * ($this->tax_rate / 100);
    }

    public function calculateTotal(): float
    {
        return $this->subtotal - $this->discount_amount + $this->calculateTaxAmount();
    }

    public function calculateOutstandingAmount(): float
    {
        return $this->total - $this->paid_amount;
    }

    public function sendInvoice(): bool
    {
        // This would integrate with email service
        return $this->update([
            'sent_date' => now(),
        ]);
    }

    public function markAsViewed(): bool
    {
        return $this->update([
            'viewed_date' => now(),
        ]);
    }

    public function sendReminder(): bool
    {
        // This would integrate with notification service
        return $this->update([
            'reminder_sent_at' => now(),
        ]);
    }

    public function createRecurringInvoice(): Invoice
    {
        if (!$this->is_recurring) {
            return $this;
        }

        return $this->replicate([
            'invoice_number' => $this->generateInvoiceNumber(),
            'issue_date' => $this->recurring_next_date,
            'due_date' => $this->recurring_next_date->addDays($this->getDueDateOffset()),
            'status' => 'draft',
            'payment_status' => 'unpaid',
            'paid_amount' => 0,
            'outstanding_amount' => $this->total,
            'balance_due' => $this->total,
            'recurring_count' => $this->recurring_count + 1,
            'recurring_remaining' => max(0, $this->recurring_remaining - 1),
        ]);
    }

    public function getDueDateOffset(): int
    {
        switch ($this->payment_terms) {
            case 'net_15':
                return 15;
            case 'net_30':
                return 30;
            case 'net_60':
                return 60;
            case 'due_on_receipt':
                return 0;
            default:
                return 30;
        }
    }

    public function updatePaymentStatus(): bool
    {
        $totalPaid = $this->payments()->where('status', 'completed')->sum('amount');
        
        if ($totalPaid >= $this->total) {
            return $this->markAsPaid();
        } elseif ($totalPaid > 0) {
            return $this->markAsPartiallyPaid($totalPaid - $this->paid_amount);
        }
        
        return $this->update([
            'paid_amount' => $totalPaid,
            'outstanding_amount' => $this->calculateOutstandingAmount(),
            'balance_due' => $this->calculateOutstandingAmount(),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = $invoice->generateInvoiceNumber();
            }
            
            if (empty($invoice->issue_date)) {
                $invoice->issue_date = now();
            }
            
            if (empty($invoice->due_date)) {
                $invoice->due_date = $invoice->issue_date->addDays($invoice->getDueDateOffset());
            }
            
            if (empty($invoice->status)) {
                $invoice->status = 'draft';
            }
            
            if (empty($invoice->payment_status)) {
                $invoice->payment_status = 'unpaid';
            }
            
            if (empty($invoice->currency)) {
                $invoice->currency = 'USD';
            }
            
            // Calculate totals if not set
            if (empty($invoice->total)) {
                $invoice->total = $invoice->calculateTotal();
            }
            
            if (empty($invoice->outstanding_amount)) {
                $invoice->outstanding_amount = $invoice->calculateOutstandingAmount();
            }
            
            if (empty($invoice->balance_due)) {
                $invoice->balance_due = $invoice->calculateOutstandingAmount();
            }
        });

        static::updating(function ($invoice) {
            // Update payment status when payments change
            if ($invoice->isDirty('paid_amount')) {
                $invoice->updatePaymentStatus();
            }
            
            // Update recurring next date if needed
            if ($invoice->is_recurring && $invoice->isDirty('recurring_next_date')) {
                $invoice->recurring_next_date = $invoice->calculateNextRecurringDate();
            }
        });
    }

    private function calculateNextRecurringDate(): Carbon
    {
        switch ($this->recurring_frequency) {
            case 'weekly':
                return $this->recurring_next_date->addWeek();
            case 'monthly':
                return $this->recurring_next_date->addMonth();
            case 'quarterly':
                return $this->recurring_next_date->addQuarter();
            case 'yearly':
                return $this->recurring_next_date->addYear();
            default:
                return $this->recurring_next_date->addMonth();
        }
    }
}
