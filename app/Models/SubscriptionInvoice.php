<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Subscription;

class SubscriptionInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subscription_id',
        'renewal_id',
        'user_id',
        'amount',
        'currency',
        'billing_date',
        'due_date',
        'status',
        'description',
        'items',
        'payment_method',
        'transaction_id',
        'paid_at',
        'payment_notes',
        'last_sent_at',
        'voided_at',
        'void_reason'
    ];

    protected $casts = [
        'billing_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'voided_at' => 'datetime',
        'amount' => 'decimal:2',
        'items' => 'array'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function renewal()
    {
        return $this->belongsTo(SubscriptionRenewal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isOverdue()
    {
        return $this->status === 'pending' && $this->due_date < now();
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->where('due_date', '<', now());
    }
}
