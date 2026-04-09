<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'country_id',
        'governorate_id',
        'city_id',
        'district_id',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'neighborhood',
        'district',
        'coordinates',
        'nearby_landmarks',
        'transportation',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'coordinates' => 'array',
        'nearby_landmarks' => 'array',
        'transportation' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function country_ref(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function city_ref(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district_ref(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code
        ]);
        return implode(', ', $parts);
    }

    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function getCoordinatesArrayAttribute(): array
    {
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
        ];
    }
}
