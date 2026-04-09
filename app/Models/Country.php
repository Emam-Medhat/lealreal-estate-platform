<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_ar', 'code'];

    public function governorates(): HasMany
    {
        return $this->hasMany(Governorate::class);
    }
}
