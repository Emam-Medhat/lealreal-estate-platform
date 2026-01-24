<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class Rating extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'ratingable_type',
        'ratingable_id',
        'rating',
        'category',
        'ip_address',
        'user_agent',
        'session_id'
    ];

    protected $casts = [
        'rating' => 'integer'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratingable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeHighRating($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeLowRating($query)
    {
        return $query->where('rating', '<=', 2);
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

    public function getCategoryText()
    {
        $categories = [
            'overall' => 'إجمالي',
            'cleanliness' => 'النظافة',
            'location' => 'الموقع',
            'service' => 'الخدمة',
            'value' => 'القيمة',
            'facilities' => 'المرافق',
            'communication' => 'التواصل',
            'professionalism' => 'الاحترافية',
            'response_time' => 'وقت الاستجابة',
            'expertise' => 'الخبرة'
        ];

        return $categories[$this->category] ?? $this->category;
    }

    public function getCategoryIcon()
    {
        $icons = [
            'overall' => 'fas fa-star',
            'cleanliness' => 'fas fa-broom',
            'location' => 'fas fa-map-marker-alt',
            'service' => 'fas fa-concierge-bell',
            'value' => 'fas fa-dollar-sign',
            'facilities' => 'fas fa-building',
            'communication' => 'fas fa-comments',
            'professionalism' => 'fas fa-user-tie',
            'response_time' => 'fas fa-clock',
            'expertise' => 'fas fa-award'
        ];

        return $icons[$this->category] ?? 'fas fa-star';
    }

    public function isPositive()
    {
        return $this->rating >= 4;
    }

    public function isNegative()
    {
        return $this->rating <= 2;
    }

    public function isNeutral()
    {
        return $this->rating === 3;
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

    public function isFromSameSession($sessionId)
    {
        return $this->session_id === $sessionId;
    }

    public function isFromSameIP($ipAddress)
    {
        return $this->ip_address === $ipAddress;
    }

    public function isRecent($days = 30)
    {
        return $this->created_at->greaterThan(now()->subDays($days));
    }

    public function getMetaDescription()
    {
        return "تقييم {$this->rating} نجوم - {$this->getCategoryText()}";
    }

    // Static methods
    public static function getAverageRating($ratingable, $category = null)
    {
        $query = $ratingable->ratings();
        
        if ($category) {
            $query->where('category', $category);
        }

        return $query->avg('rating') ?? 0;
    }

    public static function getTotalRatings($ratingable, $category = null)
    {
        $query = $ratingable->ratings();
        
        if ($category) {
            $query->where('category', $category);
        }

        return $query->count();
    }

    public static function getRatingDistribution($ratingable, $category = null)
    {
        $query = $ratingable->ratings();
        
        if ($category) {
            $query->where('category', $category);
        }

        return $query->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->pluck('count', 'rating')
            ->toArray();
    }

    public static function getCategoryAverages($ratingable)
    {
        return $ratingable->ratings()
            ->selectRaw('category, AVG(rating) as average, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('average', 'desc')
            ->get();
    }

    public static function getUserRating($ratingable, $userId, $category = null)
    {
        $query = $ratingable->ratings()->where('user_id', $userId);
        
        if ($category) {
            $query->where('category', $category);
        }

        return $query->first();
    }

    public static function hasUserRated($ratingable, $userId, $category = null)
    {
        $query = $ratingable->ratings()->where('user_id', $userId);
        
        if ($category) {
            $query->where('category', $category);
        }

        return $query->exists();
    }

    protected static function booted()
    {
        static::created(function ($rating) {
            // Update ratingable average rating
            $rating->updateAverageRating();
        });

        static::updated(function ($rating) {
            if ($rating->wasChanged('rating')) {
                $rating->updateAverageRating();
            }
        });

        static::deleted(function ($rating) {
            // Update ratingable average rating
            $rating->updateAverageRating();
        });
    }

    private function updateAverageRating()
    {
        if ($this->ratingable) {
            $query = $this->ratingable->ratings();
            
            if ($this->category) {
                $query->where('category', $this->category);
                $averageRating = $query->avg('rating');
                
                // Update category-specific rating
                $ratingData = $this->ratingable->rating_data ?? [];
                $ratingData[$this->category] = $averageRating;
                $this->ratingable->update(['rating_data' => $ratingData]);
            } else {
                $averageRating = $query->avg('rating');
                $this->ratingable->update(['average_rating' => $averageRating]);
            }

            // Update total ratings count
            $totalRatings = $this->ratingable->ratings()->count();
            $this->ratingable->update(['total_ratings' => $totalRatings]);
        }
    }
}
