<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Survey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'target_audience',
        'starts_at',
        'expires_at',
        'is_anonymous',
        'allow_multiple_responses',
        'show_results',
        'status',
        'published_at',
        'closed_at',
        'response_count'
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'allow_multiple_responses' => 'boolean',
        'show_results' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'published_at' => 'datetime',
        'closed_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'starts_at',
        'expires_at',
        'published_at',
        'closed_at'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeActive($query)
    {
        return $query->published()
            ->where('starts_at', '<=', now())
            ->where(function($q) {
                $q->where('expires_at', '>=', now())
                  ->orWhereNull('expires_at');
            });
    }

    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeByAudience($query, $audience)
    {
        return $query->where('target_audience', $audience);
    }

    // Methods
    public function getStatusText()
    {
        $statuses = [
            'draft' => 'مسودة',
            'published' => 'منشور',
            'closed' => 'مغلق'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        $colors = [
            'draft' => 'gray',
            'published' => 'green',
            'closed' => 'red'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getTargetAudienceText()
    {
        $audiences = [
            'all_users' => 'جميع المستخدمين',
            'property_owners' => 'أصحاب العقارات',
            'agents' => 'الوكلاء',
            'buyers' => 'المشترون',
            'sellers' => 'البائعون',
            'new_users' => 'المستخدمون الجدد',
            'active_users' => 'المستخدمون النشطون'
        ];

        return $audiences[$this->target_audience] ?? $this->target_audience;
    }

    public function getTargetAudienceIcon()
    {
        $icons = [
            'all_users' => 'fas fa-users',
            'property_owners' => 'fas fa-home',
            'agents' => 'fas fa-user-tie',
            'buyers' => 'fas fa-shopping-cart',
            'sellers' => 'fas fa-tag',
            'new_users' => 'fas fa-user-plus',
            'active_users' => 'fas fa-user-check'
        ];

        return $icons[$this->target_audience] ?? 'fas fa-users';
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

    public function getStartDate()
    {
        return $this->starts_at ? $this->starts_at->format('Y-m-d') : null;
    }

    public function getStartDateArabic()
    {
        return $this->starts_at ? $this->starts_at->locale('ar')->translatedFormat('d F Y') : null;
    }

    public function getEndDate()
    {
        return $this->expires_at ? $this->expires_at->format('Y-m-d') : null;
    }

    public function getEndDateArabic()
    {
        return $this->expires_at ? $this->expires_at->locale('ar')->translatedFormat('d F Y') : null;
    }

    public function getPublishedDate()
    {
        return $this->published_at ? $this->published_at->format('Y-m-d') : null;
    }

    public function getPublishedDateArabic()
    {
        return $this->published_at ? $this->published_at->locale('ar')->translatedFormat('d F Y') : null;
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isActive()
    {
        return $this->isPublished() && 
               $this->starts_at->lessThanOrEqualTo(now()) && 
               (!$this->expires_at || $this->expires_at->greaterThanOrEqualTo(now()));
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->lessThan(now());
    }

    public function isUpcoming()
    {
        return $this->starts_at->greaterThan(now());
    }

    public function canBeParticipatedBy($user)
    {
        if (!$user) {
            return false;
        }

        if ($this->isClosed() || $this->isExpired()) {
            return false;
        }

        if ($this->isUpcoming()) {
            return false;
        }

        if (!$this->allow_multiple_responses) {
            $hasResponded = $this->responses()->where('user_id', $user->id)->exists();
            if ($hasResponded) {
                return false;
            }
        }

        return $this->isInTargetAudience($user);
    }

    public function isInTargetAudience($user)
    {
        switch ($this->target_audience) {
            case 'all_users':
                return true;
            case 'property_owners':
                return $user->properties()->exists();
            case 'agents':
                return $user->agent()->exists();
            case 'buyers':
                return $user->offers()->exists();
            case 'sellers':
                return $user->properties()->exists();
            case 'new_users':
                return $user->created_at->greaterThan(now()->subDays(30));
            case 'active_users':
                return $user->last_login_at && $user->last_login_at->greaterThan(now()->subDays(7));
            default:
                return false;
        }
    }

    public function hasUserResponded($userId)
    {
        return $this->responses()->where('user_id', $userId)->exists();
    }

    public function getUserResponse($userId)
    {
        return $this->responses()->where('user_id', $userId)->first();
    }

    public function getCompletionRate()
    {
        if ($this->response_count === 0) {
            return 0;
        }

        // Calculate based on target audience size
        $targetAudienceSize = $this->getTargetAudienceSize();
        
        if ($targetAudienceSize === 0) {
            return 0;
        }

        return ($this->response_count / $targetAudienceSize) * 100;
    }

    public function getTargetAudienceSize()
    {
        switch ($this->target_audience) {
            case 'all_users':
                return User::count();
            case 'property_owners':
                return User::whereHas('properties')->count();
            case 'agents':
                return User::whereHas('agent')->count();
            case 'buyers':
                return User::whereHas('offers')->count();
            case 'sellers':
                return User::whereHas('properties')->count();
            case 'new_users':
                return User::where('created_at', '>=', now()->subDays(30))->count();
            case 'active_users':
                return User::where('last_login_at', '>=', now()->subDays(7))->count();
            default:
                return 0;
        }
    }

    public function getAverageCompletionTime()
    {
        return $this->responses()
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as avg_minutes')
            ->first()
            ->avg_minutes ?? 0;
    }

    public function getQuestionStatistics()
    {
        $statistics = [];
        
        foreach ($this->questions as $question) {
            $stats = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'type' => $question->question_type,
                'total_responses' => 0,
                'response_rate' => 0
            ];

            if ($question->question_type === 'multiple_choice' || $question->question_type === 'dropdown') {
                $optionCounts = [];
                foreach ($question->options as $key => $option) {
                    $count = $this->responses()
                        ->whereJsonContains("responses->question_{$question->id}", $key)
                        ->count();
                    $optionCounts[$option] = $count;
                }
                $stats['option_counts'] = $optionCounts;
                $stats['total_responses'] = array_sum($optionCounts);
            } elseif ($question->question_type === 'rating') {
                $ratingDistribution = [];
                for ($i = 1; $i <= 5; $i++) {
                    $count = $this->responses()
                        ->whereJsonContains("responses->question_{$question->id}", $i)
                        ->count();
                    $ratingDistribution[$i] = $count;
                }
                $stats['rating_distribution'] = $ratingDistribution;
                $stats['total_responses'] = array_sum($ratingDistribution);
                $stats['average_rating'] = $this->responses()
                    ->avg("responses->question_{$question->id}") ?? 0;
            } else {
                $stats['total_responses'] = $this->responses()
                    ->whereNotNull("responses->question_{$question->id}")
                    ->count();
            }

            $stats['response_rate'] = $this->response_count > 0 ? 
                ($stats['total_responses'] / $this->response_count) * 100 : 0;

            $statistics[] = $stats;
        }

        return $statistics;
    }

    public function getExcerpt($length = 100)
    {
        $description = strip_tags($this->description);
        return strlen($description) > $length ? substr($description, 0, $length) . '...' : $description;
    }

    public function getDurationText()
    {
        if (!$this->starts_at) {
            return 'غير محدد';
        }

        $start = $this->starts_at;
        $end = $this->expires_at ?: now();

        $days = $start->diffInDays($end);
        
        if ($days === 0) {
            return 'يوم واحد';
        } elseif ($days <= 7) {
            return "{$days} أيام";
        } elseif ($days <= 30) {
            $weeks = round($days / 7);
            return "{$weeks} أسابيع";
        } else {
            $months = round($days / 30);
            return "{$months} أشهر";
        }
    }

    public function getRemainingDays()
    {
        if (!$this->expires_at) {
            return null;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    public function getRemainingDaysText()
    {
        $days = $this->getRemainingDays();
        
        if ($days === null) {
            return 'غير محدد';
        } elseif ($days < 0) {
            return 'منتهي';
        } elseif ($days === 0) {
            return 'ينتهي اليوم';
        } elseif ($days === 1) {
            return 'يوم واحد';
        } elseif ($days <= 7) {
            return "{$days} أيام";
        } else {
            return "{$days} يوم";
        }
    }

    public function canBeEditedBy($user)
    {
        return $this->created_by === $user->id || $user->isAdmin();
    }

    public function canBePublishedBy($user)
    {
        return $this->created_by === $user->id || $user->isAdmin();
    }

    public function canBeClosedBy($user)
    {
        return $this->created_by === $user->id || $user->isAdmin();
    }

    public function canBeDeletedBy($user)
    {
        return $this->created_by === $user->id || $user->isAdmin();
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now()
        ]);
    }

    public function close()
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);
    }

    public function getMetaDescription()
    {
        return "استبيان - {$this->title}";
    }

    public function getMetaKeywords()
    {
        return ['استبيان', 'استقصاء', 'رأي', $this->title, $this->getTargetAudienceText()];
    }

    // Static methods
    public static function getActiveSurveys()
    {
        return self::active()->get();
    }

    public static function getStatistics()
    {
        return [
            'total' => self::count(),
            'draft' => self::draft()->count(),
            'published' => self::published()->count(),
            'closed' => self::closed()->count(),
            'active' => self::active()->count(),
            'by_audience' => self::selectRaw('target_audience, COUNT(*) as count')
                ->groupBy('target_audience')
                ->get(),
            'total_responses' => self::sum('response_count'),
            'average_responses' => self::avg('response_count'),
            'highest_response_rate' => self::published()
                ->withCount('responses')
                ->get()
                ->map(function($survey) {
                    return [
                        'survey' => $survey,
                        'rate' => $survey->getCompletionRate()
                    ];
                })
                ->sortByDesc('rate')
                ->first()
        ];
    }

    protected static function booted()
    {
        static::created(function ($survey) {
            // Update response count when responses are added
            $survey->updateResponseCount();
        });

        static::updated(function ($survey) {
            if ($survey->wasChanged('status') && $survey->status === 'published') {
                // Notify target audience
                $survey->notifyTargetAudience();
            }
        });
    }

    private function updateResponseCount()
    {
        $this->response_count = $this->responses()->count();
        $this->save();
    }

    private function notifyTargetAudience()
    {
        // Implementation for notifying target audience
        // This would depend on your notification system
    }
}
