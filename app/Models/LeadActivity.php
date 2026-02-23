<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'agent_id',
        'user_id', // Added user_id
        'type',
        'title', // Added title
        'description',
        'details',
        'direction',
        'duration',
        'status',
        'outcome',
        'next_action',
        'next_action_date',
        'activity_date', // Added activity_date
        'location',
        'notes',
        'metadata', // Added metadata
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'duration' => 'integer',
        'next_action_date' => 'datetime',
        'activity_date' => 'datetime', // Added cast
        'metadata' => 'array', // Added cast
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
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
