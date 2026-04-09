<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'buyer_id',
        'seller_id',
        'amount',
        'terms',
        'status',
        'created_by',
        'offer_id',
        'counter_offer_id',
        'negotiation_id',
        'expires_at',
        'signed_at',
        'completed_at',
        'terminated_at',
        'termination_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'terms' => 'array',
        'expires_at' => 'datetime',
        'signed_at' => 'datetime',
        'completed_at' => 'datetime',
        'terminated_at' => 'datetime'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function counterOffer(): BelongsTo
    {
        return $this->belongsTo(CounterOffer::class);
    }

    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(ContractSignature::class);
    }

    public function amendments(): HasMany
    {
        return $this->hasMany(ContractAmendment::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }

    public function isAmended(): bool
    {
        return $this->status === 'amended';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isTerminated(): bool
    {
        return $this->status === 'terminated';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isFullySigned(): bool
    {
        $buyerSigned = $this->signatures()->where('user_id', $this->buyer_id)->exists();
        $sellerSigned = $this->signatures()->where('user_id', $this->seller_id)->exists();

        return $buyerSigned && $sellerSigned;
    }

    public function canUserSign(User $user): bool
    {
        // Check if user is buyer or seller
        if (!in_array($user->id, [$this->buyer_id, $this->seller_id])) {
            return false;
        }

        // Check if contract is in signable state
        if (!in_array($this->status, ['pending', 'amended'])) {
            return false;
        }

        // Check if user hasn't already signed
        if ($this->signatures()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return true;
    }

    public function canUserAmend(User $user): bool
    {
        // Check if user is buyer or seller
        if (!in_array($user->id, [$this->buyer_id, $this->seller_id])) {
            return false;
        }

        // Check if contract is signed
        if ($this->status !== 'signed') {
            return false;
        }

        // Check if there's no pending amendment
        $pendingAmendment = $this->amendments()
            ->where('status', 'pending')
            ->first();

        if ($pendingAmendment) {
            return false;
        }

        return true;
    }

    public function sign(User $user, array $signatureData)
    {
        return $this->signatures()->create([
            'user_id' => $user->id,
            'signature_data' => $signatureData['signature_data'] ?? null,
            'signature_type' => $signatureData['signature_type'] ?? 'electronic',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'signed_at' => now()
        ]);
    }

    public function amend(array $amendments, string $reason, User $user)
    {
        $amendment = $this->amendments()->create([
            'user_id' => $user->id,
            'amendments' => $amendments,
            'reason' => $reason,
            'status' => 'pending'
        ]);

        // Update contract status
        $this->update(['status' => 'amended']);

        // Reset signatures
        $this->signatures()->delete();

        return $amendment;
    }

    public function terminate(string $reason)
    {
        $this->update([
            'status' => 'terminated',
            'termination_reason' => $reason,
            'terminated_at' => now()
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function getFormattedAmount(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'Pending Signature',
            'signed' => 'Signed',
            'amended' => 'Amended',
            'completed' => 'Completed',
            'terminated' => 'Terminated'
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getDaysUntilExpiration(): int
    {
        if (!$this->expires_at) {
            return -1;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    public function scopeAmended($query)
    {
        return $query->where('status', 'amended');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByBuyer($query, $userId)
    {
        return $query->where('buyer_id', $userId);
    }

    public function scopeBySeller($query, $userId)
    {
        return $query->where('seller_id', $userId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        });
    }
}
