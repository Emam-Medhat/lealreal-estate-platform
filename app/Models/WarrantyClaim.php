<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarrantyClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_id',
        'claim_number',
        'description',
        'amount',
        'claim_date',
        'incident_date',
        'status',
        'resolution',
        'resolved_at',
        'created_by',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'incident_date' => 'date',
        'resolved_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function warranty(): BelongsTo
    {
        return $this->belongsTo(Warranty::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
