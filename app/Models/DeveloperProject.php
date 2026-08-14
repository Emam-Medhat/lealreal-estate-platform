<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeveloperProject extends Model
{
    /** @use HasFactory<\Database\Factories\DeveloperProjectFactory> */
    use HasFactory;

    public function phases(): HasMany
    {
        return $this->hasMany(DeveloperProjectPhase::class, 'project_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(DeveloperProjectUnit::class, 'project_id');
    }
}
