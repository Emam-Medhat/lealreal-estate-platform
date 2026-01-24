<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'agent_id',
        'title',
        'content',
        'type',
        'priority',
        'is_private',
        'reminder_date',
        'reminder_sent',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'reminder_sent' => 'boolean',
        'reminder_date' => 'datetime',
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
