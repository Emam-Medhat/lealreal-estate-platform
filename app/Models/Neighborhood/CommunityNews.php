<?php

namespace App\Models\Neighborhood;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CommunityNews extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'community_id',
        'title',
        'content',
        'summary',
        'news_type',
        'status',
        'author_name',
        'author_email',
        'author_phone',
        'author_role',
        'published_at',
        'expires_at',
        'priority',
        'is_featured',
        'is_pinned',
        'allow_comments',
        'send_notifications',
        'target_audience',
        'tags',
        'category',
        'images',
        'cover_image',
        'gallery',
        'videos',
        'attachments',
        'related_links',
        'contact_info',
        'social_sharing',
        'metadata',
        'view_count',
        'like_count',
        'comment_count',
        'share_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'community_id' => 'integer',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'priority' => 'string',
        'is_featured' => 'boolean',
        'is_pinned' => 'boolean',
        'allow_comments' => 'boolean',
        'send_notifications' => 'boolean',
        'target_audience' => 'array',
        'tags' => 'array',
        'images' => 'array',
        'cover_image' => 'string',
        'gallery' => 'array',
        'videos' => 'array',
        'attachments' => 'array',
        'related_links' => 'array',
        'contact_info' => 'array',
        'social_sharing' => 'array',
        'metadata' => 'array',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'comment_count' => 'integer',
        'share_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the community that owns the news.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class, 'community_id');
    }

    /**
     * Scope a query to only include published news.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to filter by community.
     */
    public function scopeByCommunity(Builder $query, int $communityId): Builder
    {
        return $query->where('community_id', $communityId);
    }

    /**
     * Scope a query to filter by news type.
     */
    public function scopeByType(Builder $query, string $newsType): Builder
    {
        return $query->where('news_type', $newsType);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get featured news.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to get pinned news.
     */
    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope a query to get news by priority.
     */
    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to get news today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('published_at', today());
    }

    /**
     * Scope a query to get news this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('published_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope a query to get news this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('published_at', now()->month());
    }

    /**
     * Scope a query to get news that allow comments.
     */
    public function scopeAllowComments(Builder $query): Builder
    {
        return $query->where('allow_comments', true);
    }

    /**
     * Scope a query to get news that have expired.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope a query to get news that are still active.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>=', now());
        });
    }

    /**
     * Scope a query to get breaking news.
     */
    public function scopeBreaking(Builder $query): Builder
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'draft' => 'مسودة',
            'published' => 'منشور',
            'archived' => 'مؤرشف',
            'hidden' => 'مخفي',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get the news type label.
     */
    public function getNewsTypeLabelAttribute(): string
    {
        $types = [
            'announcement' => 'إعلان',
            'event' => 'فعالية',
            'update' => 'تحديث',
            'warning' => 'تحذير',
            'celebration' => 'احتفال',
            'policy' => 'سياسة',
            'maintenance' => 'صيانة',
            'community' => 'مجتمع',
            'other' => 'أخرى',
        ];

        return $types[$this->news_type] ?? 'غير معروف';
    }

    /**
     * Get the priority label.
     */
    public function getPriorityLabelAttribute(): string
    {
        $priorities = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
        ];

        return $priorities[$this->priority] ?? 'غير معروف';
    }

    /**
     * Get the view count label.
     */
    public function getViewCountLabelAttribute(): string
    {
        return number_format($this->view_count) . ' مشاهدة';
    }

    /**
     * Get the like count label.
     */
    public function getLikeCountLabelAttribute(): string
    {
        return number_format($this->like_count) . ' إعجاب';
    }

    /**
     * Get the comment count label.
     */
    public function getCommentCountLabelAttribute(): string
    {
        return number_format($this->comment_count) . ' تعليق';
    }

    /**
     * Get the share count label.
     */
    public function getShareCountLabelAttribute(): string
    {
        return number_format($this->share_count) . ' مشاركة';
    }

    /**
     * Get the published date label.
     */
    public function getPublishedDateLabelAttribute(): string
    {
        return $this->published_at ? $this->published_at->format('Y-m-d H:i') : 'غير محدد';
    }

    /**
     * Get the expires date label.
     */
    public function getExpiresDateLabelAttribute(): string
    {
        return $this->expires_at ? $this->expires_at->format('Y-m-d H:i') : 'لا ينتهي';
    }

    /**
     * Get the tags list.
     */
    public function getTagsListAttribute(): string
    {
        return implode(', ', $this->tags ?? []);
    }

    /**
     * Get the images list.
     */
    public function getImagesListAttribute(): array
    {
        return $this->images ?? [];
    }

    /**
     * Get the gallery list.
     */
    public function getGalleryListAttribute(): array
    {
        return $this->gallery ?? [];
    }

    /**
     * Get the videos list.
     */
    public function getVideosListAttribute(): array
    {
        return $this->videos ?? [];
    }

    /**
     * Get the attachments list.
     */
    public function getAttachmentsListAttribute(): array
    {
        return $this->attachments ?? [];
    }

    /**
     * Get the related links list.
     */
    public function getRelatedLinksListAttribute(): array
    {
        return $this->related_links ?? [];
    }

    /**
     * Get the social sharing links.
     */
    public function getSocialSharingLinksAttribute(): array
    {
        return $this->social_sharing ?? [];
    }

    /**
     * Get the metadata as JSON.
     */
    public function getMetadataAttribute(): string
    {
        return json_encode($this->metadata ?? []);
    }

    /**
     * Get the target audience.
     */
    public function getTargetAudienceAttribute(): array
    {
        return $this->target_audience ?? [];
    }

    /**
     * Check if the news is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if the news is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the news is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Check if the news is hidden.
     */
    public function isHidden(): bool
    {
        return $this->status === 'hidden';
    }

    /**
     * Check if the news is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if the news is pinned.
     */
    public function isPinned(): bool
    {
        return $this->is_pinned;
    }

    /**
     * Check if the news allows comments.
     */
    public function allowsComments(): bool
    {
        return $this->allow_comments;
    }

    /**
     * Check if the news has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }

    /**
     * Check if the news is still active.
     */
    public function isActive(): bool
    {
        return !$this->expires_at || $this->expires_at >= now();
    }

    /**
     * Check if the news is urgent.
     */
    public function isUrgent(): bool
    {
        return $this->priority === 'urgent';
    }

    /**
     * Check if the news is breaking news.
     */
    public function isBreaking(): bool
    {
        return $this->isUrgent() && $this->isFeatured();
    }

    /**
     * Check if the news is high priority.
     */
    public function isHighPriority(): bool
    {
        return $this->priority === 'high';
    }

    /**
     * Check if the news is medium priority.
     */
    public function isMediumPriority(): bool
    {
        return $this->priority === 'medium';
    }

    /**
     * Check if the news is low priority.
     */
    public function isLowPriority(): bool
    {
        return $this->priority === 'low';
    }

    /**
     * Check if the news has images.
     */
    public function hasImages(): bool
    {
        return !empty($this->images);
    }

    /**
     * Check if the news has a cover image.
     */
    public function hasCoverImage(): bool
    {
        return !empty($this->cover_image);
    }

    /**
     * Check if the news has a gallery.
     */
    public function hasGallery(): bool
    {
        return !empty($this->gallery);
    }

    /**
     * Check if the news has videos.
     */
    public function hasVideos(): bool
    {
        return !empty($this->videos);
    }

    /**
     * Check if the news has attachments.
     */
    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    /**
     * Check if the news has related links.
     */
    public function hasRelatedLinks(): bool
    {
        return !empty($this->related_links);
    }

    /**
     * Check if the news has social sharing.
     */
    public function hasSocialSharing(): bool
    {
        return !empty($this->social_sharing);
    }

    /**
     * Check if the news has tags.
     */
    public function hasTags(): bool
    {
        return !empty($this->tags);
    }

    /**
     * Check if the news has a summary.
     */
    public function hasSummary(): bool
    {
        return !empty($this->summary);
    }

    /**
     * Check if the news is popular.
     */
    public function isPopular(): bool
    {
        return $this->view_count > 500 || $this->like_count > 50 || $this->comment_count > 25;
    }

    /**
     * Check if the news is trending.
     */
    public function isTrending(): bool
    {
        return $this->view_count > 1000 || $this->like_count > 100 || $this->comment_count > 50;
    }

    /**
     * Get the engagement score.
     */
    public function getEngagementScore(): float
    {
        // Calculate engagement based on views, likes, comments, and shares
        $viewScore = min($this->view_count / 2000, 1) * 0.3;
        $likeScore = min($this->like_count / 200, 1) * 0.3;
        $commentScore = min($this->comment_count / 100, 1) * 0.3;
        $shareScore = min($this->share_count / 50, 1) * 0.1;

        return $viewScore + $likeScore + $commentScore + $shareScore;
    }

    /**
     * Get the engagement label.
     */
    public function getEngagementLabelAttribute(): string
    {
        $score = $this->engagement_score;

        if ($score >= 0.8) {
            return 'تفاعل عالي جداً';
        } elseif ($score >= 0.6) {
            return 'تفاعل عالي';
        } elseif ($score >= 0.4) {
            return 'تفاعل متوسط';
        } elseif ($score >= 0.2) {
            return 'تفاعل منخفض';
        } else {
            return 'تفاعل ضعيف جداً';
        }
    }

    /**
     * Get the popularity score.
     */
    public function getPopularityScore(): float
    {
        // Calculate popularity based on engagement and recency
        $engagementScore = $this->engagement_score * 0.7;
        
        // Add recency bonus (newer news get higher score)
        $daysSincePublished = $this->published_at ? $this->published_at->diffInDays(now()) : 365;
        $recencyScore = max(0, 1 - ($daysSincePublished / 365)) * 0.3;

        return $engagementScore + $recencyScore;
    }

    /**
     * Get the popularity label.
     */
    public function getPopularityLabelAttribute(): string
    {
        $score = $this->popularity_score;

        if ($score >= 0.8) {
            return 'شعبي جداً';
        } elseif ($score >= 0.6) {
            return 'شعبي';
        } elseif ($score >= 0.4) {
            return 'متوسط';
        } elseif ($score >= 0.2) {
            return 'قليل';
        } else {
            return 'ضعيف جداً';
        }
    }

    /**
     * Get the full title with community.
     */
    public function getFullTitleAttribute(): string
    {
        if ($this->community) {
            return $this->title . ' - ' . $this->community->name;
        }
        return $this->title;
    }

    /**
     * Get the search index.
     */
    public function getSearchIndex(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'summary' => $this->summary,
            'news_type' => $this->news_type,
            'status' => $this->status,
            'author_name' => $this->author_name,
            'author_role' => $this->author_role,
            'priority' => $this->priority,
            'tags' => $this->tags,
            'category' => $this->category,
            'community' => $this->community?->name ?? '',
            'neighborhood' => $this->community?->neighborhood?->name ?? '',
            'city' => $this->community?->neighborhood?->city ?? '',
            'district' => $this->community?->neighborhood?->district ?? '',
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Increment like count.
     */
    public function incrementLikeCount(): void
    {
        $this->increment('like_count');
    }

    /**
     * Decrement like count.
     */
    public function decrementLikeCount(): void
    {
        if ($this->like_count > 0) {
            $this->decrement('like_count');
        }
    }

    /**
     * Increment comment count.
     */
    public function incrementCommentCount(): void
    {
        $this->increment('comment_count');
    }

    /**
     * Decrement comment count.
     */
    public function decrementCommentCount(): void
    {
        if ($this->comment_count > 0) {
            $this->decrement('comment_count');
        }
    }

    /**
     * Increment share count.
     */
    public function incrementShareCount(): void
    {
        $this->increment('share_count');
    }

    /**
     * Bootstrap the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($builder) {
            $builder->whereNull('deleted_at');
        });
    }
}
