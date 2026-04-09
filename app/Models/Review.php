<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewable_type',
        'reviewable_id',
        'title',
        'content',
        'rating',
        'pros',
        'cons',
        'recommendation',
        'is_verified',
        'is_anonymous',
        'status',
        'sentiment',
        'has_response',
        'verified_at',
        'verified_by',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason'
    ];

    protected $casts = [
        'rating' => 'integer',
        'recommendation' => 'boolean',
        'is_verified' => 'boolean',
        'is_anonymous' => 'boolean',
        'has_response' => 'boolean',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'verified_at',
        'approved_at',
        'rejected_at'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function responses()
    {
        return $this->hasMany(ReviewResponse::class);
    }

    public function votes()
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function flags()
    {
        return $this->hasMany(ReviewFlag::class);
    }

    public function verification()
    {
        return $this->hasOne(ReviewVerification::class);
    }

    public function sentimentAnalysis()
    {
        return $this->hasOne(ReviewSentimentAnalysis::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeWithSentiment($query, $sentiment)
    {
        return $query->where('sentiment', $sentiment);
    }

    // Methods
    public function isHelpful()
    {
        return $this->votes()->where('vote_type', 'helpful')->count();
    }

    public function isNotHelpful()
    {
        return $this->votes()->where('vote_type', 'not_helpful')->count();
    }

    public function getUserVote($userId)
    {
        return $this->votes()->where('user_id', $userId)->first();
    }

    public function hasUserVoted($userId)
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function canBeEditedBy($user)
    {
        return $this->user_id === $user->id || $user->isAdmin();
    }

    public function canBeDeletedBy($user)
    {
        return $this->user_id === $user->id || $user->isAdmin();
    }

    public function getDisplayRating()
    {
        return number_format($this->rating, 1);
    }

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

    public function getStatusText()
    {
        $statuses = [
            'pending' => 'في انتظار المراجعة',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        $colors = [
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getSentimentText()
    {
        $sentiments = [
            'positive' => 'إيجابي',
            'negative' => 'سلبي',
            'neutral' => 'محايد'
        ];

        return $sentiments[$this->sentiment] ?? 'غير محدد';
    }

    public function getSentimentColor()
    {
        $colors = [
            'positive' => 'green',
            'negative' => 'red',
            'neutral' => 'gray'
        ];

        return $colors[$this->sentiment] ?? 'gray';
    }

    public function getRecommendationText()
    {
        return $this->recommendation ? 'يوصي' : 'لا يوصي';
    }

    public function getRecommendationColor()
    {
        return $this->recommendation ? 'green' : 'red';
    }

    public function getExcerpt($length = 150)
    {
        $content = strip_tags($this->content);
        return strlen($content) > $length ? substr($content, 0, $length) . '...' : $content;
    }

    public function isRecent($days = 30)
    {
        return $this->created_at->greaterThan(now()->subDays($days));
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

    public function getMetaDescription()
    {
        return "تقييم {$this->rating} نجوم - {$this->title}";
    }

    public function getMetaKeywords()
    {
        $keywords = [$this->title, 'تقييم', 'مراجعة'];
        
        if ($this->pros) {
            $keywords = array_merge($keywords, explode(' ', strip_tags($this->pros)));
        }
        
        if ($this->cons) {
            $keywords = array_merge($keywords, explode(' ', strip_tags($this->cons)));
        }
        
        return array_unique(array_slice($keywords, 0, 10));
    }

    public function canBeRespondedTo()
    {
        return $this->status === 'approved';
    }

    public function hasOfficialResponse()
    {
        return $this->responses()->where('is_official', true)->exists();
    }

    public function getOfficialResponse()
    {
        return $this->responses()->where('is_official', true)->first();
    }

    public function isFlaggedBy($userId)
    {
        return $this->flags()->where('user_id', $userId)->exists();
    }

    public function getFlagCount()
    {
        return $this->flags()->count();
    }

    public function shouldBeAutoApproved()
    {
        // Auto-approval criteria
        if ($this->user->is_verified && $this->rating >= 3) {
            return true;
        }
        
        if ($this->user->review_count >= 5 && strlen($this->content) >= 100) {
            return true;
        }
        
        return false;
    }

    protected static function booted()
    {
        static::created(function ($review) {
            // Update user review count
            $review->user->increment('review_count');
            
            // Update reviewable average rating
            $review->updateAverageRating();
        });

        static::updated(function ($review) {
            if ($review->wasChanged('status') && $review->status === 'approved') {
                $review->updateAverageRating();
            }
        });

        static::deleted(function ($review) {
            // Update user review count
            $review->user->decrement('review_count');
            
            // Update reviewable average rating
            $review->updateAverageRating();
        });
    }

    private function updateAverageRating()
    {
        if ($this->reviewable) {
            $averageRating = $this->reviewable->reviews()
                ->where('status', 'approved')
                ->avg('rating');

            $this->reviewable->update(['average_rating' => $averageRating]);
        }
    }
}
