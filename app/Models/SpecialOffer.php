<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialOffer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'agent_id',
        'title',
        'description',
        'offer_type',
        'discount_percentage',
        'discount_amount',
        'original_price',
        'offer_price',
        'currency',
        'start_date',
        'end_date',
        'status',
        'is_active',
        'is_featured',
        'terms_conditions',
        'eligibility_criteria',
        'max_redemptions',
        'current_redemptions',
        'redemption_code',
        'auto_approve',
        'notification_settings',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'original_price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'max_redemptions' => 'integer',
        'current_redemptions' => 'integer',
        'auto_approve' => 'boolean',
        'notification_settings' => 'json',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
