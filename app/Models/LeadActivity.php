<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadActivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'agent_id',
        'type',
        'description',
        'details',
        'direction',
        'duration',
        'status',
        'outcome',
        'next_action',
        'next_action_date',
        'location',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'duration' => 'integer',
        'next_action_date' => 'datetime',
    ];

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
