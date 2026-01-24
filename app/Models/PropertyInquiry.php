<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyInquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'lead_id',
        'agent_id',
        'name',
        'email',
        'phone',
        'message',
        'type',
        'status',
        'priority',
        'source',
        'budget',
        'preferred_contact',
        'appointment_requested',
        'appointment_scheduled',
        'response_sent',
        'notes',
        'assigned_to',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'appointment_requested' => 'boolean',
        'appointment_scheduled' => 'boolean',
        'response_sent' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
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
