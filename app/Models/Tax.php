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
        'description',
        'tax_type',
        'rate',
        'is_active',
        'effective_date',
        'expiry_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_date' => 'date',
        'expiry_date' => 'date',
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
        return $query->where('tax_type', $type);
    }

    public function isCurrentlyEffective(): bool
    {
        $now = now();
        return $this->is_active && 
               $this->effective_date <= $now && 
               (!$this->expiry_date || $this->expiry_date >= $now);
    }
}
