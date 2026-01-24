<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'order',
        'is_active',
        'is_converted',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_converted' => 'boolean',
        'order' => 'integer',
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
        return $query->orderBy('order');
    }

    public function scopeConverted($query)
    {
        return $query->where('is_converted', true);
    }
}
