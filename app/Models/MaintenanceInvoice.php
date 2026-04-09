<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_code',
        'invoice_number',
        'maintenance_request_id',
        'work_order_id',
        'service_provider_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_terms',
        'notes',
        'status',
        'sent_at',
        'sent_to',
        'paid_at',
        'payment_method',
        'payment_reference',
        'payment_notes',
        'overdue_at',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'overdue_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(MaintenanceInvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(MaintenanceInvoicePayment::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByProvider($query, $providerId)
    {
        return $query->where('service_provider_id', $providerId);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'overdue']);
    }

    public function scopeOverdueDate($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['paid', 'cancelled']);
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'مسودة',
            'sent' => 'مرسلة',
            'paid' => 'مدفوعة',
            'overdue' => 'متأخرة',
            'cancelled' => 'ملغاة',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'gray',
            'sent' => 'blue',
            'paid' => 'green',
            'overdue' => 'red',
            'cancelled' => 'orange',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'credit_card' => 'بطاقة ائتمان',
            'check' => 'شيك',
            'other' => 'أخرى',
        ];

        return $labels[$this->payment_method] ?? $this->payment_method;
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isSent()
    {
        return $this->status === 'sent';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isOverdue()
    {
        return $this->status === 'overdue';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isUnpaid()
    {
        return in_array($this->status, ['draft', 'sent', 'overdue']);
    }

    public function canBeSent()
    {
        return $this->status === 'draft';
    }

    public function canBePaid()
    {
        return in_array($this->status, ['sent', 'overdue']);
    }

    public function canBeCancelled()
    {
        return !in_array($this->status, ['paid', 'cancelled']);
    }

    public function canBeEdited()
    {
        return $this->status === 'draft';
    }

    public function getDaysOverdue()
    {
        if (!$this->due_date || $this->status === 'paid') {
            return null;
        }

        return $this->due_date->diffInDays(now(), false);
    }

    public function getDaysUntilDue()
    {
        if (!$this->due_date) {
            return null;
        }

        return $this->due_date->diffInDays(now(), false);
    }

    public function isDueSoon($days = 7)
    {
        return $this->due_date && 
               $this->due_date > now() && 
               $this->due_date <= now()->addDays($days) &&
               $this->isUnpaid();
    }

    public function getTaxAmount()
    {
        return $this->subtotal * ($this->tax_rate / 100);
    }

    public function getTotalWithTax()
    {
        return $this->subtotal + $this->getTaxAmount();
    }

    public function getGrandTotal()
    {
        $total = $this->getTotalWithTax();
        
        if ($this->discount_amount) {
            $total -= $this->discount_amount;
        }

        return max(0, $total);
    }

    public function getTotalPaid()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceDue()
    {
        return $this->total_amount - $this->getTotalPaid();
    }

    public function isFullyPaid()
    {
        return $this->getBalanceDue() <= 0;
    }

    public function isPartiallyPaid()
    {
        return $this->getTotalPaid() > 0 && !$this->isFullyPaid();
    }

    public function addItem($description, $quantity, $unitPrice)
    {
        return $this->items()->create([
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $quantity * $unitPrice,
        ]);
    }

    public function removeItem($itemId)
    {
        return $this->items()->find($itemId)?->delete();
    }

    public function updateItem($itemId, $description, $quantity, $unitPrice)
    {
        $item = $this->items()->find($itemId);
        
        if ($item) {
            $item->update([
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $quantity * $unitPrice,
            ]);
        }

        return $item;
    }

    public function recalculateTotals()
    {
        $subtotal = $this->items()->sum('total');
        $taxAmount = $subtotal * ($this->tax_rate / 100);
        $totalAmount = $subtotal + $taxAmount - $this->discount_amount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => max(0, $totalAmount),
        ]);
    }

    public function send($email)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_to' => $email,
        ]);

        return $this;
    }

    public function markAsPaid($paymentMethod, $paymentDate, $amount = null, $reference = null, $notes = null)
    {
        $paymentAmount = $amount ?? $this->getBalanceDue();

        // Create payment record
        $this->payments()->create([
            'amount' => $paymentAmount,
            'payment_date' => $paymentDate,
            'payment_method' => $paymentMethod,
            'reference' => $reference,
            'notes' => $notes,
            'user_id' => auth()->id(),
        ]);

        // Update invoice status if fully paid
        if ($this->isFullyPaid()) {
            $this->update([
                'status' => 'paid',
                'paid_at' => $paymentDate,
                'payment_method' => $paymentMethod,
                'payment_reference' => $reference,
                'payment_notes' => $notes,
            ]);
        }

        return $this;
    }

    public function markAsOverdue()
    {
        if ($this->isUnpaid() && $this->due_date < now()) {
            $this->update([
                'status' => 'overdue',
                'overdue_at' => now(),
            ]);
        }

        return $this;
    }

    public function cancel($reason, $cancelledBy)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'cancelled_by' => $cancelledBy,
        ]);

        return $this;
    }

    public function duplicate()
    {
        $newInvoice = $this->replicate([
            'invoice_number',
            'invoice_code',
            'status',
            'sent_at',
            'sent_to',
            'paid_at',
            'payment_method',
            'payment_reference',
            'payment_notes',
            'overdue_at',
            'cancelled_at',
            'cancellation_reason',
            'cancelled_by',
        ]);

        $newInvoice->invoice_number = 'INV-' . date('Y') . '-' . str_pad(MaintenanceInvoice::count() + 1, 4, '0', STR_PAD_LEFT);
        $newInvoice->invoice_code = 'INV-' . date('Y') . '-' . str_pad(MaintenanceInvoice::count() + 2, 4, '0', STR_PAD_LEFT);
        $newInvoice->status = 'draft';
        $newInvoice->created_by = auth()->id();
        $newInvoice->invoice_date = now();
        $newInvoice->due_date = now()->addDays(30);
        $newInvoice->save();

        // Duplicate items
        foreach ($this->items as $item) {
            $newInvoice->items()->create([
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ]);
        }

        return $newInvoice;
    }

    public function getItemCount()
    {
        return $this->items()->count();
    }

    public function getPaymentCount()
    {
        return $this->payments()->count();
    }

    public function getLatestPayment()
    {
        return $this->payments()->latest('payment_date')->first();
    }

    public function getPaymentHistory()
    {
        return $this->payments()->with('user')->orderBy('payment_date')->get();
    }

    public function getInvoiceSummary()
    {
        return [
            'invoice_number' => $this->invoice_code,
            'service_provider' => $this->serviceProvider->name ?? 'N/A',
            'invoice_date' => $this->invoice_date,
            'due_date' => $this->due_date,
            'status' => $this->status_label,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'total_paid' => $this->getTotalPaid(),
            'balance_due' => $this->getBalanceDue(),
            'days_overdue' => $this->getDaysOverdue(),
            'days_until_due' => $this->getDaysUntilDue(),
            'item_count' => $this->getItemCount(),
            'payment_count' => $this->getPaymentCount(),
        ];
    }

    public function generateInvoiceData()
    {
        return [
            'invoice' => $this->load(['serviceProvider', 'items', 'payments']),
            'items' => $this->items()->get(),
            'payments' => $this->payments()->with('user')->get(),
            'summary' => $this->getInvoiceSummary(),
        ];
    }
}
