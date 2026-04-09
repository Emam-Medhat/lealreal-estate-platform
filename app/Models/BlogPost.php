<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'images',
        'status',
        'published_at',
        'author_id',
        'category',
        'tags',
        'view_count',
        'like_count',
        'comment_count',
        'is_featured',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'images' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'comment_count' => 'integer',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category', 'slug');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(ContentRevision::class)
            ->where('model_type', 'blog_post')
            ->orderBy('created_at', 'desc');
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

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function incrementViews()
    {
        $this->increment('view_count');
    }

    public function getReadingTime()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200)); // Assuming 200 words per minute
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

    public function canBePublished()
    {
        return !empty($this->title) && !empty($this->content);
    }

    protected static function booted()
    {
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            
            if ($post->status === 'published' && !$post->published_at) {
                $post->published_at = now();
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            
            if ($post->isDirty('status') && $post->status === 'published' && !$post->published_at) {
                $post->published_at = now();
            }
        });
    }
}
