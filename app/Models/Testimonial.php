<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Testimonial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'client_name',
        'client_position',
        'client_company',
        'client_image',
        'project_type',
        'project_location',
        'rating',
        'video_url',
        'featured',
        'status',
        'published_at',
        'rejected_at',
        'featured_at'
    ];

    protected $casts = [
        'rating' => 'integer',
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'rejected_at' => 'datetime',
        'featured_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'published_at',
        'rejected_at',
        'featured_at'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByProjectType($query, $type)
    {
        return $query->where('project_type', $type);
    }

    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    // Methods
    public function getRatingStars()
    {
        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars[] = 'filled';
            } elseif ($i - 0.5 <= $this->rating) {
                $stars[] = 'half';
            } else {
                $stars[] = 'empty';
            }
        }
        return $stars;
    }

    public function getRatingText()
    {
        $texts = [
            1 => 'سيء جداً',
            2 => 'سيء',
            3 => 'متوسط',
            4 => 'جيد',
            5 => 'ممتاز'
        ];

        return $texts[$this->rating] ?? 'غير محدد';
    }

    public function getRatingColor()
    {
        $colors = [
            1 => 'red',
            2 => 'orange',
            3 => 'yellow',
            4 => 'lime',
            5 => 'green'
        ];

        return $colors[$this->rating] ?? 'gray';
    }

    public function getStatusText()
    {
        $statuses = [
            'pending' => 'في انتظار المراجعة',
            'published' => 'منشور',
            'rejected' => 'مرفوض'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        $colors = [
            'pending' => 'yellow',
            'published' => 'green',
            'rejected' => 'red'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getProjectTypeText()
    {
        $types = [
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'industrial' => 'صناعي',
            'land' => 'أرض',
            'villa' => 'فيلا',
            'apartment' => 'شقة',
            'office' => 'مكتب',
            'warehouse' => 'مستودع',
            'retail' => 'تجزئة',
            'other' => 'أخرى'
        ];

        return $types[$this->project_type] ?? $this->project_type;
    }

    public function getProjectTypeIcon()
    {
        $icons = [
            'residential' => 'fas fa-home',
            'commercial' => 'fas fa-store',
            'industrial' => 'fas fa-industry',
            'land' => 'fas fa-mountain',
            'villa' => 'fas fa-home',
            'apartment' => 'fas fa-building',
            'office' => 'fas fa-briefcase',
            'warehouse' => 'fas fa-warehouse',
            'retail' => 'fas fa-shopping-cart',
            'other' => 'fas fa-star'
        ];

        return $icons[$this->project_type] ?? 'fas fa-star';
    }

    public function getClientImageUrl()
    {
        if ($this->client_image) {
            return asset('storage/' . $this->client_image);
        }

        return asset('images/default-avatar.png');
    }

    public function getExcerpt($length = 150)
    {
        $content = strip_tags($this->content);
        return strlen($content) > $length ? substr($content, 0, $length) . '...' : $content;
    }

    public function getFormattedDate()
    {
        return $this->created_at->format('Y-m-d H:i');
    }

    public function getFormattedDateArabic()
    {
        return $this->created_at->locale('ar')->translatedFormat('d F Y');
    }

    public function getTimeAgo()
    {
        return $this->created_at->diffForHumans();
    }

    public function getPublishedDate()
    {
        return $this->published_at ? $this->published_at->format('Y-m-d') : null;
    }

    public function getPublishedDateArabic()
    {
        return $this->published_at ? $this->published_at->locale('ar')->translatedFormat('d F Y') : null;
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isFeatured()
    {
        return $this->featured;
    }

    public function hasVideo()
    {
        return !empty($this->video_url);
    }

    public function getVideoEmbedUrl()
    {
        if (!$this->video_url) {
            return null;
        }

        // Convert YouTube URL to embed URL
        if (str_contains($this->video_url, 'youtube.com')) {
            $videoId = $this->extractYouTubeId($this->video_url);
            return $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
        }

        // Convert Vimeo URL to embed URL
        if (str_contains($this->video_url, 'vimeo.com')) {
            $videoId = $this->extractVimeoId($this->video_url);
            return $videoId ? "https://player.vimeo.com/video/{$videoId}" : null;
        }

        return $this->video_url;
    }

    public function getVideoThumbnail()
    {
        if (!$this->video_url) {
            return null;
        }

        if (str_contains($this->video_url, 'youtube.com')) {
            $videoId = $this->extractYouTubeId($this->video_url);
            return $videoId ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg" : null;
        }

        return null;
    }

    public function canBeEditedBy($user)
    {
        return $this->user_id === $user->id || $user->isAdmin();
    }

    public function canBeDeletedBy($user)
    {
        return $this->user_id === $user->id || $user->isAdmin();
    }

    public function canBePublishedBy($user)
    {
        return $user->isAdmin();
    }

    public function canBeFeaturedBy($user)
    {
        return $user->isAdmin();
    }

    public function isRecent($days = 30)
    {
        return $this->created_at->greaterThan(now()->subDays($days));
    }

    public function getMetaDescription()
    {
        return "شهادة عميل - {$this->title}";
    }

    public function getMetaKeywords()
    {
        $keywords = ['شهادة', 'عميل', 'توصية', $this->title, $this->client_name];
        
        if ($this->project_type) {
            $keywords[] = $this->getProjectTypeText();
        }
        
        if ($this->client_company) {
            $keywords[] = $this->client_company;
        }
        
        return array_unique($keywords);
    }

    public function getStructuredData()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'author' => [
                '@type' => 'Person',
                'name' => $this->client_name,
                'jobTitle' => $this->client_position
            ],
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $this->rating,
                'bestRating' => 5
            ],
            'reviewBody' => $this->content,
            'datePublished' => $this->published_at ? $this->published_at->toISOString() : $this->created_at->toISOString()
        ];
    }

    private function extractYouTubeId($url)
    {
        preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }

        preg_match('/youtu\.be\/([^?]+)/', $url, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }

        return null;
    }

    private function extractVimeoId($url)
    {
        preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    // Static methods
    public static function getFeaturedTestimonials($limit = 6)
    {
        return self::published()
            ->featured()
            ->orderBy('featured_at', 'desc')
            ->take($limit)
            ->get();
    }

    public static function getTestimonialsByType($type, $limit = 12)
    {
        return self::published()
            ->byProjectType($type)
            ->orderBy('published_at', 'desc')
            ->take($limit)
            ->get();
    }

    public static function getStatistics()
    {
        return [
            'total' => self::count(),
            'published' => self::published()->count(),
            'pending' => self::pending()->count(),
            'rejected' => self::rejected()->count(),
            'featured' => self::featured()->count(),
            'average_rating' => self::published()->avg('rating'),
            'by_type' => self::published()->selectRaw('project_type, COUNT(*) as count')
                ->groupBy('project_type')
                ->get(),
            'by_rating' => self::published()->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->get()
        ];
    }
}
