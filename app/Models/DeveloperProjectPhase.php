<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeveloperProjectPhase extends Model
{
    /** @use HasFactory<\Database\Factories\DeveloperProjectPhaseFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'phase_type',
        'status',
        'start_date',
        'end_date',
        'progress_percentage',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the project that owns this phase.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(DeveloperProject::class, 'project_id');
    }
}
