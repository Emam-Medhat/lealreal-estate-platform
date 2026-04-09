<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Guide extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'author_id',
        'category',
        'difficulty',
        'reading_time',
        'estimated_time',
        'views',
        'is_featured',
        'seo_data',
        'published_at',
        'meta_title',
        'meta_description',
        'prerequisites',
        'learning_objectives',
    ];

    protected $casts = [
        'seo_data' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function seoMeta()
    {
        return $this->morphOne(SeoMeta::class, 'model');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function getFormattedExcerpt($length = 150)
    {
        if ($this->excerpt) {
            return Str::limit($this->excerpt, $length);
        }
        return Str::limit(strip_tags($this->content), $length);
    }

    public function getDifficultyLabel()
    {
        return match($this->difficulty) {
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            default => ucfirst($this->difficulty),
        };
    }

    public function getDifficultyColor()
    {
        return match($this->difficulty) {
            'beginner' => 'green',
            'intermediate' => 'yellow',
            'advanced' => 'red',
            default => 'gray',
        };
    }

    protected static function booted()
    {
        static::creating(function ($guide) {
            if (empty($guide->slug)) {
                $guide->slug = Str::slug($guide->title);
            }
            
            if (empty($guide->reading_time)) {
                $wordCount = str_word_count(strip_tags($guide->content));
                $guide->reading_time = max(1, ceil($wordCount / 200));
            }
        });

        static::updating(function ($guide) {
            if ($guide->isDirty('title') && empty($guide->slug)) {
                $guide->slug = Str::slug($guide->title);
            }
            
            if ($guide->isDirty('content') && empty($guide->reading_time)) {
                $wordCount = str_word_count(strip_tags($guide->content));
                $guide->reading_time = max(1, ceil($wordCount / 200));
            }
        });
    }
}
