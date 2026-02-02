<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMeta extends Model
{
    use HasFactory;

    protected $table = 'seo_meta';

    protected $fillable = [
        'metaable_type',
        'metaable_id',
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
        'robots',
        'structured_data',
    ];

    protected $casts = [
        'structured_data' => 'array',
    ];

    public function metaable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForModel($query, $model)
    {
        return $query->where('metaable_type', get_class($model))
            ->where('metaable_id', $model->id);
    }
}
