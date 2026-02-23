<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'description_ar',
        'tax_type',
        'rate',
        'is_active',
        'effective_date',
        'expiry_date',
        'applicable_to',
        'exemptions',
        'calculation_rules',
        'payment_terms',
        'filing_requirements',
        'penalty_rules',
        'reporting_requirements',
        'compliance_rules',
        'jurisdiction',
        'tax_codes',
        'forms',
        'deadlines',
        'thresholds',
        'brackets',
        'deductions',
        'credits',
        'adjustments',
        'notes',
        'notes_ar',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'exemptions' => 'array',
        'calculation_rules' => 'array',
        'payment_terms' => 'array',
        'filing_requirements' => 'array',
        'penalty_rules' => 'array',
        'reporting_requirements' => 'array',
        'compliance_rules' => 'array',
        'tax_codes' => 'array',
        'forms' => 'array',
        'deadlines' => 'array',
        'thresholds' => 'array',
        'brackets' => 'array',
        'deductions' => 'array',
        'credits' => 'array',
        'adjustments' => 'array',
    ];

    public function propertyTaxes(): HasMany
    {
        return $this->hasMany(PropertyTax::class);
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
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function isCurrentlyEffective(): bool
    {
        $now = now();
        return $this->is_active && 
               $this->effective_date <= $now && 
               (!$this->expiry_date || $this->expiry_date >= $now);
    }
}
