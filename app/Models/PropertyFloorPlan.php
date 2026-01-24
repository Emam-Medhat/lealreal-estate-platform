<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyFloorPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'title',
        'description',
        'file_path',
        'thumbnail_path',
        'area',
        'area_unit',
        'bedrooms',
        'bathrooms',
        'rooms',
        'dimensions',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'rooms' => 'array',
        'dimensions' => 'array',
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }
        return asset('storage/' . $this->thumbnail_path);
    }

    public function getFormattedAreaAttribute(): string
    {
        return number_format($this->area, 2) . ' ' . $this->area_unit;
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    public function setAsPrimary(): void
    {
        // Remove primary from other floor plans of this property
        self::where('property_id', $this->property_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        $this->update(['is_primary' => true]);
    }
}
