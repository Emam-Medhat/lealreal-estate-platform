<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'role',
        'status',
        'title',
        'permissions',
        'joined_at',
        'invited_by',
        'invitation_token',
        'invitation_accepted_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'joined_at' => 'datetime',
        'invitation_accepted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // Direct relation to leads/tasks if needed, or through User
    public function leads()
    {
        return $this->hasMany(Lead::class, 'assigned_to', 'user_id'); // Assuming assignment is by user_id
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to', 'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'agent_id', 'user_id');
    }
}
