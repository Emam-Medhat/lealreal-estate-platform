<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class ReviewResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'review_id',
        'user_id',
        'content',
        'is_official',
        'status',
        'edited_at',
        'edited_by'
    ];

    protected $casts = [
        'is_official' => 'boolean',
        'edited_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'edited_at'
    ];

    // Relationships
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function votes()
    {
        return $this->hasMany(ReviewVote::class, 'response_id');
    }

    public function flags()
    {
        return $this->hasMany(ReviewFlag::class, 'response_id');
    }

    // Scopes
    public function scopeOfficial($query)
    {
        return $query->where('is_official', true);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
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

    public function isEdited()
    {
        return !is_null($this->edited_at);
    }

    public function getStatusText()
    {
        $statuses = [
            'published' => 'منشور',
            'hidden' => 'مخفي',
            'deleted' => 'محذوف'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        $colors = [
            'published' => 'green',
            'hidden' => 'yellow',
            'deleted' => 'red'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getResponseTypeText()
    {
        if ($this->is_official) {
            return 'رد رسمي';
        }

        return 'رد مستخدم';
    }

    public function getResponseTypeColor()
    {
        return $this->is_official ? 'blue' : 'gray';
    }

    public function isFromOwner()
    {
        if (!$this->review || !$this->review->reviewable) {
            return false;
        }

        $reviewable = $this->review->reviewable;
        
        if (method_exists($reviewable, 'user_id')) {
            return $reviewable->user_id === $this->user_id;
        }

        return false;
    }

    public function isFromAdmin()
    {
        return $this->user && $this->user->isAdmin();
    }

    public function getAuthorName()
    {
        if ($this->is_anonymous) {
            return 'مستخدم مجهول';
        }

        return $this->user ? $this->user->name : 'مستخدم محذوف';
    }

    public function getAuthorAvatar()
    {
        if ($this->is_anonymous) {
            return 'images/default-avatar.png';
        }

        return $this->user ? $this->user->avatar : 'images/default-avatar.png';
    }

    public function canBeVotedBy($user)
    {
        if (!$user) {
            return false;
        }

        // Can't vote on own response
        if ($this->user_id === $user->id) {
            return false;
        }

        // Can't vote if already voted
        if ($this->hasUserVoted($user->id)) {
            return false;
        }

        return true;
    }

    public function isFlaggedBy($userId)
    {
        return $this->flags()->where('user_id', $userId)->exists();
    }

    public function getFlagCount()
    {
        return $this->flags()->count();
    }

    public function shouldBeAutoHidden()
    {
        // Auto-hide criteria
        if ($this->getFlagCount() >= 3) {
            return true;
        }

        if ($this->isNotHelpful() > $this->isHelpful() * 2) {
            return true;
        }

        return false;
    }

    public function getMetaDescription()
    {
        return "رد على تقييم - " . $this->getExcerpt(100);
    }

    protected static function booted()
    {
        static::created(function ($response) {
            // Update review response status
            $response->review->update(['has_response' => true]);
        });

        static::deleted(function ($response) {
            // Check if review still has responses
            $hasOtherResponses = $response->review->responses()->count() > 0;
            $response->review->update(['has_response' => $hasOtherResponses]);
        });
    }
}
