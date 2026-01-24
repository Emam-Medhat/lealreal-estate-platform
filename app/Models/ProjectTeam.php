<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTeam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'leader_id',
        'created_by',
        'updated_by',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function getActiveMembersCount()
    {
        return $this->members()->where('status', 'active')->count();
    }

    public function addMember($userId, $roleId, $hourlyRate = null, $startDate = null)
    {
        return $this->members()->create([
            'user_id' => $userId,
            'role_id' => $roleId,
            'hourly_rate' => $hourlyRate,
            'start_date' => $startDate ?: now(),
            'joined_at' => now(),
            'status' => 'active',
        ]);
    }

    public function removeMember($userId)
    {
        return $this->members()->where('user_id', $userId)->delete();
    }

    public function hasMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }
}
