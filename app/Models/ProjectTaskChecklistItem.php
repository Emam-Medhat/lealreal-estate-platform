<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTaskChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id',
        'title',
        'description',
        'is_completed',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(ProjectTaskChecklist::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
