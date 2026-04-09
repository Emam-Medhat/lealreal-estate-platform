<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class Feedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'feedbackable_type',
        'feedbackable_id',
        'type',
        'category',
        'title',
        'content',
        'rating',
        'priority',
        'tags',
        'is_anonymous',
        'status',
        'admin_notes',
        'assigned_to',
        'response',
        'responded_at',
        'responded_by',
        'reviewed_at'
    ];

    protected $casts = [
        'rating' => 'integer',
        'tags' => 'array',
        'is_anonymous' => 'boolean',
        'responded_at' => 'datetime',
        'reviewed_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'responded_at',
        'reviewed_at'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedbackable(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInReview($query)
    {
        return $query->where('status', 'in_review');
    }

    public function scopeAcknowledged($query)
    {
        return $query->where('status', 'acknowledged');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assigned_to');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeWithResponse($query)
    {
        return $query->whereNotNull('response');
    }

    public function scopeAnonymous($query)
    {
        return $query->where('is_anonymous', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_anonymous', false);
    }

    // Methods
    public function getTypeText()
    {
        $types = [
            'bug_report' => 'بلاغ عن خطأ',
            'feature_request' => 'طلب ميزة جديدة',
            'improvement' => 'اقتراح تحسين',
            'complaint' => 'شكوى',
            'compliment' => 'إشادة',
            'general' => 'عام',
            'usability' => 'سهولة الاستخدام',
            'performance' => 'أداء',
            'security' => 'أمان'
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getTypeIcon()
    {
        $icons = [
            'bug_report' => 'fas fa-bug',
            'feature_request' => 'fas fa-lightbulb',
            'improvement' => 'fas fa-arrow-up',
            'complaint' => 'fas fa-exclamation-triangle',
            'compliment' => 'fas fa-thumbs-up',
            'general' => 'fas fa-comment',
            'usability' => 'fas fa-mouse-pointer',
            'performance' => 'fas fa-tachometer-alt',
            'security' => 'fas fa-shield-alt'
        ];

        return $icons[$this->type] ?? 'fas fa-comment';
    }

    public function getCategoryText()
    {
        $categories = [
            'user_interface' => 'واجهة المستخدم',
            'functionality' => 'الوظائف',
            'performance' => 'الأداء',
            'security' => 'الأمان',
            'documentation' => 'التوثيق',
            'customer_service' => 'خدمة العملاء',
            'pricing' => 'التسعير',
            'features' => 'الميزات',
            'other' => 'أخرى'
        ];

        return $categories[$this->category] ?? $this->category;
    }

    public function getCategoryIcon()
    {
        $icons = [
            'user_interface' => 'fas fa-desktop',
            'functionality' => 'fas fa-cogs',
            'performance' => 'fas fa-rocket',
            'security' => 'fas fa-lock',
            'documentation' => 'fas fa-book',
            'customer_service' => 'fas fa-headset',
            'pricing' => 'fas fa-tag',
            'features' => 'fas fa-star',
            'other' => 'fas fa-ellipsis-h'
        ];

        return $icons[$this->category] ?? 'fas fa-ellipsis-h';
    }

    public function getPriorityText()
    {
        $priorities = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'critical' => 'حرج'
        ];

        return $priorities[$this->priority] ?? $this->priority;
    }

    public function getPriorityColor()
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red'
        ];

        return $colors[$this->priority] ?? 'gray';
    }

    public function getStatusText()
    {
        $statuses = [
            'pending' => 'في انتظار المراجعة',
            'in_review' => 'قيد المراجعة',
            'acknowledged' => 'تم الاعتراف',
            'resolved' => 'تم الحل',
            'closed' => 'مغلق'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        $colors = [
            'pending' => 'yellow',
            'in_review' => 'blue',
            'acknowledged' => 'purple',
            'resolved' => 'green',
            'closed' => 'gray'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getRatingStars()
    {
        if (!$this->rating) {
            return [];
        }

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
        if (!$this->rating) {
            return null;
        }

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
        if (!$this->rating) {
            return 'gray';
        }

        $colors = [
            1 => 'red',
            2 => 'orange',
            3 => 'yellow',
            4 => 'lime',
            5 => 'green'
        ];

        return $colors[$this->rating] ?? 'gray';
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

    public function getResponseDate()
    {
        return $this->responded_at ? $this->responded_at->format('Y-m-d H:i') : null;
    }

    public function getResponseTime()
    {
        if (!$this->responded_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->responded_at);
    }

    public function getResponseTimeText()
    {
        $hours = $this->getResponseTime();
        
        if (!$hours) {
            return null;
        }

        if ($hours < 1) {
            return 'أقل من ساعة';
        } elseif ($hours < 24) {
            return "{$hours} ساعات";
        } elseif ($hours < 168) { // 7 days
            $days = round($hours / 24);
            return "{$days} أيام";
        } else {
            $weeks = round($hours / 168);
            return "{$weeks} أسابيع";
        }
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isInReview()
    {
        return $this->status === 'in_review';
    }

    public function isAcknowledged()
    {
        return $this->status === 'acknowledged';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isAnonymous()
    {
        return $this->is_anonymous;
    }

    public function isAssigned()
    {
        return !is_null($this->assigned_to);
    }

    public function hasResponse()
    {
        return !is_null($this->response);
    }

    public function isHighPriority()
    {
        return in_array($this->priority, ['high', 'critical']);
    }

    public function isCritical()
    {
        return $this->priority === 'critical';
    }

    public function hasTags()
    {
        return !empty($this->tags);
    }

    public function getTagList()
    {
        return $this->tags ?? [];
    }

    public function hasTag($tag)
    {
        return in_array($tag, $this->getTagList());
    }

    public function canBeEditedBy($user)
    {
        return $this->user_id === $user->id || $user->isAdmin();
    }

    public function canBeAssignedTo($user)
    {
        return $user->isAdmin() || $user->hasRole(['manager', 'support']);
    }

    public function canBeRespondedBy($user)
    {
        return $this->assigned_to === $user->id || $user->isAdmin();
    }

    public function canBeClosedBy($user)
    {
        return $user->isAdmin();
    }

    public function getExcerpt($length = 150)
    {
        $content = strip_tags($this->content);
        return strlen($content) > $length ? substr($content, 0, $length) . '...' : $content;
    }

    public function getResponseExcerpt($length = 150)
    {
        if (!$this->response) {
            return null;
        }

        $response = strip_tags($this->response);
        return strlen($response) > $length ? substr($response, 0, $length) . '...' : $response;
    }

    public function getMetaDescription()
    {
        return "تغذية راجعة - {$this->title}";
    }

    public function getMetaKeywords()
    {
        $keywords = ['تغذية راجعة', $this->title, $this->getTypeText(), $this->getCategoryText()];
        
        if ($this->hasTags()) {
            $keywords = array_merge($keywords, $this->getTagList());
        }
        
        return array_unique($keywords);
    }

    public function assignTo($userId)
    {
        $this->update([
            'assigned_to' => $userId,
            'status' => 'in_review',
            'reviewed_at' => now()
        ]);
    }

    public function acknowledge()
    {
        $this->update([
            'status' => 'acknowledged',
            'reviewed_at' => now()
        ]);
    }

    public function resolve($response = null)
    {
        $this->update([
            'status' => 'resolved',
            'response' => $response,
            'responded_at' => now(),
            'responded_by' => auth()->user()->id
        ]);
    }

    public function close()
    {
        $this->update(['status' => 'closed']);
    }

    public function addResponse($response)
    {
        $this->update([
            'response' => $response,
            'responded_at' => now(),
            'responded_by' => auth()->user()->id
        ]);
    }

    // Static methods
    public static function getStatistics()
    {
        return [
            'total' => self::count(),
            'pending' => self::pending()->count(),
            'in_review' => self::inReview()->count(),
            'acknowledged' => self::acknowledged()->count(),
            'resolved' => self::resolved()->count(),
            'closed' => self::closed()->count(),
            'unassigned' => self::unassigned()->count(),
            'with_response' => self::withResponse()->count(),
            'by_type' => self::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'by_category' => self::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get(),
            'by_priority' => self::selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->get(),
            'average_rating' => self::whereNotNull('rating')->avg('rating'),
            'response_rate' => self::withResponse()->count() / self::count() * 100,
            'resolution_rate' => self::resolved()->count() / self::count() * 100,
            'average_response_time' => self::whereNotNull('responded_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, responded_at)) as avg_hours')
                ->first()
        ];
    }

    public static function getTagStatistics()
    {
        $allTags = self::whereNotNull('tags')->get()->flatMap(function($feedback) {
            return $feedback->getTagList();
        });

        return $allTags->countBy()->sortDesc()->take(20);
    }

    protected static function booted()
    {
        static::created(function ($feedback) {
            // Auto-categorize and prioritize based on content
            $feedback->processContent();
        });

        static::updated(function ($feedback) {
            if ($feedback->wasChanged('status') && $feedback->status === 'resolved') {
                // Notify user about resolution
                $feedback->user->notifications()->create([
                    'type' => 'feedback_resolved',
                    'title' => 'تم حل تغذيتك الراجعة',
                    'message' => 'تم حل تغذيتك الراجعة بنجاح',
                    'data' => ['feedback_id' => $feedback->id]
                ]);
            }
        });
    }

    private function processContent()
    {
        $content = strtolower($this->content);
        
        // Auto-categorization based on keywords
        if (str_contains($content, 'خطأ') || str_contains($content, 'bug') || str_contains($content, 'crash')) {
            $this->category = 'bug_report';
        } elseif (str_contains($content, 'بطيء') || str_contains($content, 'slow') || str_contains($content, 'performance')) {
            $this->category = 'performance';
        } elseif (str_contains($content, 'واجهة') || str_contains($content, 'interface') || str_contains($content, 'ui')) {
            $this->category = 'user_interface';
        }

        // Auto-prioritization
        if (str_contains($content, 'عاجل') || str_contains($content, 'urgent') || str_contains($content, 'critical')) {
            $this->priority = 'critical';
        } elseif (str_contains($content, 'مهم') || str_contains($content, 'important') || str_contains($content, 'high')) {
            $this->priority = 'high';
        }

        // Extract tags from content
        $tags = $this->extractKeywords($content);
        if (!empty($tags)) {
            $this->tags = array_unique(array_merge($this->tags ?? [], $tags));
        }
    }

    private function extractKeywords($content)
    {
        $keywords = [];
        $commonKeywords = ['mobile', 'desktop', 'api', 'database', 'search', 'payment', 'upload', 'email', 'login', 'register'];
        
        foreach ($commonKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                $keywords[] = $keyword;
            }
        }

        return $keywords;
    }
}
