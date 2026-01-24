<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'weight',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weight' => 'integer',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('weight', 'desc')->orderBy('name');
    }
}
