<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ReviewVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'response_id',
        'user_id',
        'vote_type'
    ];

    protected $casts = [
        'vote_type' => 'string'
    ];

    // Relationships
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function response()
    {
        return $this->belongsTo(ReviewResponse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeHelpful($query)
    {
        return $query->where('vote_type', 'helpful');
    }

    public function scopeNotHelpful($query)
    {
        return $query->where('vote_type', 'not_helpful');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForReview($query, $reviewId)
    {
        return $query->where('review_id', $reviewId);
    }

    public function scopeForResponse($query, $responseId)
    {
        return $query->where('response_id', $responseId);
    }

    // Methods
    public function getVoteTypeText()
    {
        $types = [
            'helpful' => 'مفيد',
            'not_helpful' => 'غير مفيد'
        ];

        return $types[$this->vote_type] ?? $this->vote_type;
    }

    public function getVoteTypeIcon()
    {
        $icons = [
            'helpful' => 'fas fa-thumbs-up',
            'not_helpful' => 'fas fa-thumbs-down'
        ];

        return $icons[$this->vote_type] ?? 'fas fa-question';
    }

    public function getVoteTypeColor()
    {
        $colors = [
            'helpful' => 'green',
            'not_helpful' => 'red'
        ];

        return $colors[$this->vote_type] ?? 'gray';
    }

    public function isHelpful()
    {
        return $this->vote_type === 'helpful';
    }

    public function isNotHelpful()
    {
        return $this->vote_type === 'not_helpful';
    }

    public function isForReview()
    {
        return !is_null($this->review_id);
    }

    public function isForResponse()
    {
        return !is_null($this->response_id);
    }

    public function getVotable()
    {
        if ($this->isForReview()) {
            return $this->review;
        } elseif ($this->isForResponse()) {
            return $this->response;
        }

        return null;
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

    public function canBeDeletedBy($user)
    {
        return $this->user_id === $user->id || $user->isAdmin();
    }

    public function getMetaDescription()
    {
        $votable = $this->getVotable();
        $type = $this->isForReview() ? 'تقييم' : 'رد';
        
        return "تصويت {$this->getVoteTypeText()} على {$type}";
    }

    public function getMetaKeywords()
    {
        return ['تصويت', $this->getVoteTypeText(), 'تقييم', 'رد'];
    }

    // Static methods
    public static function getVoteCounts($reviewId = null, $responseId = null)
    {
        $query = self::query();

        if ($reviewId) {
            $query->where('review_id', $reviewId);
        }

        if ($responseId) {
            $query->where('response_id', $responseId);
        }

        return [
            'helpful' => $query->helpful()->count(),
            'not_helpful' => $query->notHelpful()->count(),
            'total' => $query->count()
        ];
    }

    public static function getUserVote($userId, $reviewId = null, $responseId = null)
    {
        $query = self::where('user_id', $userId);

        if ($reviewId) {
            $query->where('review_id', $reviewId);
        }

        if ($responseId) {
            $query->where('response_id', $responseId);
        }

        return $query->first();
    }

    public static function hasUserVoted($userId, $reviewId = null, $responseId = null)
    {
        return self::getUserVote($userId, $reviewId, $responseId) !== null;
    }

    public static function getStatistics()
    {
        return [
            'total_votes' => self::count(),
            'helpful_votes' => self::helpful()->count(),
            'not_helpful_votes' => self::notHelpful()->count(),
            'unique_voters' => self::distinct('user_id')->count(),
            'votes_by_type' => self::selectRaw('vote_type, COUNT(*) as count')
                ->groupBy('vote_type')
                ->get(),
            'recent_votes' => self::with(['user', 'review', 'response'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
        ];
    }

    protected static function booted()
    {
        static::created(function ($vote) {
            // Update vote counts on the votable item
            $votable = $vote->getVotable();
            
            if ($votable) {
                // This would trigger any necessary cache updates or recalculations
                // The actual count updates are handled by the votable model's relationships
            }
        });

        static::deleted(function ($vote) {
            // Update vote counts on the votable item
            $votable = $vote->getVotable();
            
            if ($votable) {
                // This would trigger any necessary cache updates or recalculations
                // The actual count updates are handled by the votable model's relationships
            }
        });
    }
}
