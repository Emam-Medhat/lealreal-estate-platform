<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'plan_id', // If using a Plan model
        'plan_name', // If string based
        'status', // active, cancelled, expired
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'cancellation_reason',
        'payment_method',
        'last_payment_at',
        'amount',
        'currency'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // public function plan(): BelongsTo ...
}
