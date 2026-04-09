<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Esignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'signer_email',
        'signer_name',
        'signature_data',
        'signature_type',
        'message',
        'status',
        'token',
        'verification_token',
        'signed_at',
        'signed_by',
        'expires_at',
        'requested_by',
        'cancelled_at',
        'cancelled_by',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'pending')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    });
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function canBeSigned()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function generateVerificationToken()
    {
        $this->verification_token = \Str::random(60);
        $this->save();
    }
}
