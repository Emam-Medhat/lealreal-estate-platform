<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Widget extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'content',
        'config',
        'location',
        'sort_order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function render()
    {
        return match($this->type) {
            'text' => $this->renderTextWidget(),
            'html' => $this->renderHtmlWidget(),
            'image' => $this->renderImageWidget(),
            'recent_posts' => $this->renderRecentPostsWidget(),
            'categories' => $this->renderCategoriesWidget(),
            'tags' => $this->renderTagsWidget(),
            'search' => $this->renderSearchWidget(),
            default => $this->content,
        };
    }

    private function renderTextWidget()
    {
        return '<div class="widget widget-text">' . nl2br(e($this->content)) . '</div>';
    }

    private function renderHtmlWidget()
    {
        return '<div class="widget widget-html">' . $this->content . '</div>';
    }

    private function renderImageWidget()
    {
        $config = $this->config ?? [];
        $imageUrl = $config['image_url'] ?? '';
        $alt = $config['alt_text'] ?? $this->title;
        $link = $config['link_url'] ?? '';
        
        $image = '<img src="' . $imageUrl . '" alt="' . $alt . '" class="widget-image">';
        
        if ($link) {
            $image = '<a href="' . $link . '">' . $image . '</a>';
        }
        
        return '<div class="widget widget-image">' . $image . '</div>';
    }

    private function renderRecentPostsWidget()
    {
        $config = $this->config ?? [];
        $limit = $config['limit'] ?? 5;
        $posts = BlogPost::published()->take($limit)->get();
        
        $html = '<div class="widget widget-recent-posts">';
        $html .= '<h3>' . $this->title . '</h3>';
        $html .= '<ul class="recent-posts-list">';
        
        foreach ($posts as $post) {
            $html .= '<li><a href="' . route('blog.show', $post->slug) . '">' . $post->title . '</a></li>';
        }
        
        $html .= '</ul></div>';
        
        return $html;
    }

    private function renderCategoriesWidget()
    {
        $categories = BlogCategory::active()->withCount('activePosts')->get();
        
        $html = '<div class="widget widget-categories">';
        $html .= '<h3>' . $this->title . '</h3>';
        $html .= '<ul class="categories-list">';
        
        foreach ($categories as $category) {
            if ($category->active_posts_count > 0) {
                $html .= '<li><a href="' . route('blog.category', $category->slug) . '">' . $category->name . ' (' . $category->active_posts_count . ')</a></li>';
            }
        }
        
        $html .= '</ul></div>';
        
        return $html;
    }

    private function renderTagsWidget()
    {
        $tags = BlogTag::popular()->limit(20)->get();
        
        $html = '<div class="widget widget-tags">';
        $html .= '<h3>' . $this->title . '</h3>';
        $html .= '<div class="tags-cloud">';
        
        foreach ($tags as $tag) {
            $size = min(1.5, 0.8 + ($tag->usage_count / 10));
            $html .= '<a href="' . route('blog.tag', $tag->slug) . '" style="font-size: ' . $size . 'em;">' . $tag->name . '</a> ';
        }
        
        $html .= '</div></div>';
        
        return $html;
    }

    private function renderSearchWidget()
    {
        $html = '<div class="widget widget-search">';
        $html .= '<h3>' . $this->title . '</h3>';
        $html .= '<form action="' . route('blog.search') . '" method="GET">';
        $html .= '<input type="text" name="q" placeholder="Search..." class="form-control">';
        $html .= '<button type="submit" class="btn btn-primary">Search</button>';
        $html .= '</form></div>';
        
        return $html;
    }
}
