<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
        'author_id',
        'category',
        'views',
        'is_featured',
        'seo_data',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'seo_data' => 'array',
        'is_featured' => 'boolean',
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
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
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

    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
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

    public function isPublished()
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    protected static function booted()
    {
        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
            
            if ($news->status === 'published' && !$news->published_at) {
                $news->published_at = now();
            }
        });

        static::updating(function ($news) {
            if ($news->isDirty('title') && empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
            
            if ($news->isDirty('status') && $news->status === 'published' && !$news->published_at) {
                $news->published_at = now();
            }
        });
    }
}
