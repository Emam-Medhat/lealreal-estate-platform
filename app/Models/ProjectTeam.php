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
        'team_leader_id',
        'name',
        'description',
        'members',
        'status',
    ];

    protected $casts = [
        'members' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class, 'team_id');
    }
}
