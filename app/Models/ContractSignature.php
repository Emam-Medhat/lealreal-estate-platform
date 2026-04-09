<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'user_id',
        'signature_data',
        'signature_type',
        'ip_address',
        'user_agent',
        'signed_at',
        'revoked_at',
        'revocation_reason',
        'verification_attempts',
        'last_verified_at'
    ];

    protected $casts = [
        'signature_data' => 'string',
        'signed_at' => 'datetime',
        'revoked_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'verification_attempts' => 'integer'
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isValid(): bool
    {
        return !$this->isRevoked();
    }

    public function isElectronic(): bool
    {
        return $this->signature_type === 'electronic';
    }

    public function isDigital(): bool
    {
        return $this->signature_type === 'digital';
    }

    public function canBeRevoked(): bool
    {
        // Check if signature is not revoked
        if ($this->isRevoked()) {
            return false;
        }

        // Check if contract is not completed
        if ($this->contract->status === 'completed') {
            return false;
        }

        // Check if signature was placed recently (within 24 hours)
        if (now()->diffInHours($this->signed_at) > 24) {
            return false;
        }

        return true;
    }

    public function revoke(string $reason = null)
    {
        $this->update([
            'revoked_at' => now(),
            'revocation_reason' => $reason ?? 'User requested revocation'
        ]);
    }

    public function verify(): bool
    {
        $this->increment('verification_attempts');
        $this->update(['last_verified_at' => now()]);

        // Implement signature verification logic
        if ($this->isElectronic()) {
            return !empty($this->signature_data);
        }

        if ($this->isDigital()) {
            // Implement digital signature verification
            return true; // Placeholder
        }

        return false;
    }

    public function getSignatureTypeLabel(): string
    {
        $labels = [
            'electronic' => 'Electronic',
            'digital' => 'Digital'
        ];

        return $labels[$this->signature_type] ?? 'Unknown';
    }

    public function getStatusLabel(): string
    {
        return $this->isRevoked() ? 'Revoked' : 'Valid';
    }

    public function getTimeAgo(): string
    {
        return $this->signed_at->diffForHumans();
    }

    public function getRevokeTimeAgo(): string
    {
        return $this->revoked_at ? $this->revoked_at->diffForHumans() : 'Never';
    }

    public function getVerificationCode(): string
    {
        return 'SIG-' . $this->id . '-' . strtoupper(substr(md5($this->id . $this->signed_at), 0, 8));
    }

    public function scopeValid($query)
    {
        return $query->whereNull('revoked_at');
    }

    public function scopeRevoked($query)
    {
        return $query->whereNotNull('revoked_at');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByContract($query, $contractId)
    {
        return $query->where('contract_id', $contractId);
    }

    public function scopeElectronic($query)
    {
        return $query->where('signature_type', 'electronic');
    }

    public function scopeDigital($query)
    {
        return $query->where('signature_type', 'digital');
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('signed_at', '>', now()->subHours($hours));
    }
}
