<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Commission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agent_id',
        'property_id',
        'lead_id',
        'appointment_id',
        'type',
        'amount',
        'percentage',
        'status',
        'due_date',
        'paid_date',
        'payment_method',
        'transaction_id',
        'notes',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'invoice_number',
        'tax_amount',
        'net_amount',
        'currency',
        'exchange_rate',
        'payment_terms',
        'late_fee',
        'early_payment_discount',
        'dispute_status',
        'dispute_reason',
        'dispute_resolved_by',
        'dispute_resolved_at',
        'split_commission',
        'split_agent_id',
        'split_percentage',
        'referral_agent_id',
        'referral_fee',
        'bonus_amount',
        'penalty_amount',
        'adjustment_amount',
        'final_amount',
        'commission_period',
        'invoice_sent_at',
        'payment_reminder_sent_at',
        'auto_processed',
        'processed_at',
        'reconciled_at',
        'reconciled_by',
        'batch_id',
        'export_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'late_fee' => 'decimal:2',
        'early_payment_discount' => 'decimal:2',
        'dispute_resolved_at' => 'datetime',
        'split_percentage' => 'decimal:2',
        'referral_fee' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'invoice_sent_at' => 'datetime',
        'payment_reminder_sent_at' => 'datetime',
        'auto_processed' => 'boolean',
        'processed_at' => 'datetime',
        'reconciled_at' => 'datetime',
    ];

    // Relationships
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function splitAgent()
    {
        return $this->belongsTo(User::class, 'split_agent_id');
    }

    public function referralAgent()
    {
        return $this->belongsTo(User::class, 'referral_agent_id');
    }

    public function disputeResolver()
    {
        return $this->belongsTo(User::class, 'dispute_resolved_by');
    }

    public function reconciledBy()
    {
        return $this->belongsTo(User::class, 'reconciled_by');
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

    public function scopeByLead($query, $leadId)
    {
        return $query->where('lead_id', $leadId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'paid');
    }

    public function scopeDueThisMonth($query)
    {
        return $query->whereMonth('due_date', now()->month)
                    ->whereYear('due_date', now()->year);
    }

    public function scopePaidThisMonth($query)
    {
        return $query->whereMonth('paid_date', now()->month)
                    ->whereYear('paid_date', now()->year);
    }

    public function scopeInDispute($query)
    {
        return $query->where('dispute_status', 'open');
    }

    public function scopeSplit($query)
    {
        return $query->where('split_commission', true);
    }

    public function scopeWithReferral($query)
    {
        return $query->whereNotNull('referral_agent_id');
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByCommissionPeriod($query, $period)
    {
        return $query->where('commission_period', $period);
    }

    // Helper Methods
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getFormattedPercentageAttribute()
    {
        return number_format($this->percentage, 2) . '%';
    }

    public function getFormattedTaxAmountAttribute()
    {
        return number_format($this->tax_amount, 2);
    }

    public function getFormattedNetAmountAttribute()
    {
        return number_format($this->net_amount, 2);
    }

    public function getFormattedFinalAmountAttribute()
    {
        return number_format($this->final_amount, 2);
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'paid' => 'Paid',
            'rejected' => 'Rejected',
            'disputed' => 'Disputed',
            'cancelled' => 'Cancelled',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'approved' => 'green',
            'paid' => 'blue',
            'rejected' => 'red',
            'disputed' => 'orange',
            'cancelled' => 'gray',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getTypeLabelAttribute()
    {
        $labels = [
            'sale' => 'Sale Commission',
            'rental' => 'Rental Commission',
            'referral' => 'Referral Fee',
            'bonus' => 'Bonus',
            'penalty' => 'Penalty',
            'adjustment' => 'Adjustment',
        ];

        return $labels[$this->type] ?? 'Unknown';
    }

    public function getDaysOverdueAttribute()
    {
        if ($this->due_date && $this->status !== 'paid') {
            return now()->diffInDays($this->due_date, false);
        }
        return 0;
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date < now() && $this->status !== 'paid';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isInDispute()
    {
        return $this->dispute_status === 'open';
    }

    public function isSplit()
    {
        return $this->split_commission && $this->split_agent_id;
    }

    public function hasReferral()
    {
        return $this->referral_agent_id;
    }

    public function canBeApproved()
    {
        return $this->status === 'pending' && !$this->isInDispute();
    }

    public function canBeRejected()
    {
        return $this->status === 'pending' && !$this->isInDispute();
    }

    public function canBePaid()
    {
        return $this->status === 'approved';
    }

    public function canBeDisputed()
    {
        return in_array($this->status, ['approved', 'paid']) && !$this->isInDispute();
    }

    public function approve($approvedBy, $notes = null)
    {
        $this->status = 'approved';
        $this->approved_by = $approvedBy;
        $this->approved_at = now();
        if ($notes) {
            $this->notes = $notes;
        }
        $this->save();
    }

    public function reject($rejectedBy, $reason)
    {
        $this->status = 'rejected';
        $this->rejected_by = $rejectedBy;
        $this->rejected_at = now();
        $this->rejection_reason = $reason;
        $this->save();
    }

    public function markAsPaid($paymentMethod, $transactionId = null, $paidDate = null)
    {
        $this->status = 'paid';
        $this->paid_date = $paidDate ?: now();
        $this->payment_method = $paymentMethod;
        if ($transactionId) {
            $this->transaction_id = $transactionId;
        }
        $this->save();
    }

    public function openDispute($reason)
    {
        $this->dispute_status = 'open';
        $this->dispute_reason = $reason;
        $this->save();
    }

    public function resolveDispute($resolvedBy, $resolution)
    {
        $this->dispute_status = 'resolved';
        $this->dispute_resolved_by = $resolvedBy;
        $this->dispute_resolved_at = now();
        $this->notes .= "\n\nDispute Resolution: " . $resolution;
        $this->save();
    }

    public function calculateFinalAmount()
    {
        $amount = $this->amount;
        
        // Add bonus
        if ($this->bonus_amount) {
            $amount += $this->bonus_amount;
        }
        
        // Subtract penalty
        if ($this->penalty_amount) {
            $amount -= $this->penalty_amount;
        }
        
        // Add/subtract adjustment
        if ($this->adjustment_amount) {
            $amount += $this->adjustment_amount;
        }
        
        // Apply early payment discount
        if ($this->early_payment_discount && $this->paid_date && $this->paid_date <= $this->due_date) {
            $amount -= $this->early_payment_discount;
        }
        
        // Apply late fee
        if ($this->late_fee && $this->isOverdue()) {
            $amount += $this->late_fee;
        }
        
        $this->final_amount = $amount;
        $this->save();
        
        return $amount;
    }

    public function generateInvoiceNumber()
    {
        if (!$this->invoice_number) {
            $this->invoice_number = 'INV-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->save();
        }
        
        return $this->invoice_number;
    }

    public function sendInvoice()
    {
        $this->invoice_sent_at = now();
        $this->generateInvoiceNumber();
        $this->save();
        
        // Logic to send invoice email would go here
    }

    public function sendPaymentReminder()
    {
        $this->payment_reminder_sent_at = now();
        $this->save();
        
        // Logic to send payment reminder email would go here
    }

    public function markAsReconciled($reconciledBy)
    {
        $this->reconciled_at = now();
        $this->reconciled_by = $reconciledBy;
        $this->save();
    }

    public function getCommissionPeriod()
    {
        if ($this->commission_period) {
            return $this->commission_period;
        }
        
        // Generate period based on creation date
        return $this->created_at->format('Y-m');
    }

    public function scopeWithOutstandingBalance($query)
    {
        return $query->where('status', '!=', 'paid')
                    ->where('status', '!=', 'rejected')
                    ->where('status', '!=', 'cancelled');
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    public function getPaymentMethodLabelAttribute()
    {
        $methods = [
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'check' => 'Check',
            'credit_card' => 'Credit Card',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'other' => 'Other',
        ];

        return $methods[$this->payment_method] ?? 'Unknown';
    }

    public function getDisputeStatusLabelAttribute()
    {
        $statuses = [
            'open' => 'Open',
            'under_review' => 'Under Review',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ];

        return $statuses[$this->dispute_status] ?? 'None';
    }

    public function getDisputeStatusColorAttribute()
    {
        $colors = [
            'open' => 'red',
            'under_review' => 'yellow',
            'resolved' => 'green',
            'closed' => 'gray',
        ];

        return $colors[$this->dispute_status] ?? 'gray';
    }
}
