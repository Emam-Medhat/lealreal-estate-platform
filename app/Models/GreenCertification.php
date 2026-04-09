<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GreenCertification extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'certification_name',
        'certification_body',
        'certification_level',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'status',
        'certification_criteria',
        'assessment_results',
        'notes',
        'certificate_file_path',
        'certification_score',
        'renewal_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'certification_criteria' => 'array',
        'assessment_results' => 'array',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewal_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(SmartProperty::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function getStatusAttribute($value): string
    {
        return match($value) {
            'pending' => 'معلق',
            'active' => 'نشط',
            'expired' => 'منتهي الصلاحية',
            'suspended' => 'معلق',
            'revoked' => 'ملغي',
            default => $value,
        };
    }

    public function isExpiring(): bool
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getDaysUntilExpiry(): int
    {
        if (!$this->expiry_date) {
            return 0;
        }

        return max(0, $this->expiry_date->diffInDays(now()));
    }
}
