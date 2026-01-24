<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'bedrooms',
        'bathrooms',
        'floors',
        'parking_spaces',
        'year_built',
        'area',
        'area_unit',
        'land_area',
        'land_area_unit',
        'specifications',
        'materials',
        'interior_features',
        'exterior_features',
    ];

    protected $casts = [
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'floors' => 'integer',
        'parking_spaces' => 'integer',
        'year_built' => 'integer',
        'area' => 'decimal:2',
        'land_area' => 'decimal:2',
        'specifications' => 'array',
        'materials' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getFormattedAreaAttribute(): string
    {
        return number_format($this->area, 2) . ' ' . $this->area_unit;
    }

    public function getFormattedLandAreaAttribute(): ?string
    {
        if (!$this->land_area) {
            return null;
        }
        return number_format($this->land_area, 2) . ' ' . ($this->land_area_unit ?? $this->area_unit);
    }
}
