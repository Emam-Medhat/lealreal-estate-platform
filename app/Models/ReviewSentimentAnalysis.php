<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewSentimentAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'sentiment',
        'confidence',
        'positive_score',
        'negative_score',
        'neutral_score',
        'keywords',
        'emotions',
        'analyzed_at'
    ];

    protected $casts = [
        'confidence' => 'decimal:3',
        'positive_score' => 'decimal:3',
        'negative_score' => 'decimal:3',
        'neutral_score' => 'decimal:3',
        'keywords' => 'array',
        'emotions' => 'array',
        'analyzed_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'analyzed_at'
    ];

    // Relationships
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    // Scopes
    public function scopePositive($query)
    {
        return $query->where('sentiment', 'positive');
    }

    public function scopeNegative($query)
    {
        return $query->where('sentiment', 'negative');
    }

    public function scopeNeutral($query)
    {
        return $query->where('sentiment', 'neutral');
    }

    public function scopeHighConfidence($query, $threshold = 0.8)
    {
        return $query->where('confidence', '>=', $threshold);
    }

    public function scopeLowConfidence($query, $threshold = 0.5)
    {
        return $query->where('confidence', '<=', $threshold);
    }

    // Methods
    public function getSentimentText()
    {
        $sentiments = [
            'positive' => 'إيجابي',
            'negative' => 'سلبي',
            'neutral' => 'محايد'
        ];

        return $sentiments[$this->sentiment] ?? $this->sentiment;
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

    public function getSentimentIcon()
    {
        $icons = [
            'positive' => 'fas fa-smile',
            'negative' => 'fas fa-frown',
            'neutral' => 'fas fa-meh'
        ];

        return $icons[$this->sentiment] ?? 'fas fa-question';
    }

    public function getConfidencePercentage()
    {
        return round($this->confidence * 100, 1) . '%';
    }

    public function getConfidenceColor()
    {
        if ($this->confidence >= 0.8) {
            return 'green';
        } elseif ($this->confidence >= 0.6) {
            return 'yellow';
        } else {
            return 'red';
        }
    }

    public function isPositive()
    {
        return $this->sentiment === 'positive';
    }

    public function isNegative()
    {
        return $this->sentiment === 'negative';
    }

    public function isNeutral()
    {
        return $this->sentiment === 'neutral';
    }

    public function isHighConfidence()
    {
        return $this->confidence >= 0.8;
    }

    public function isLowConfidence()
    {
        return $this->confidence <= 0.5;
    }

    public function getDominantScore()
    {
        $scores = [
            'positive' => $this->positive_score,
            'negative' => $this->negative_score,
            'neutral' => $this->neutral_score
        ];

        return array_search(max($scores), $scores);
    }

    public function getScorePercentage($type)
    {
        $score = $this->{$type . '_score'} ?? 0;
        return round($score * 100, 1) . '%';
    }

    public function getScoreColor($type)
    {
        $score = $this->{$type . '_score'} ?? 0;
        
        if ($score >= 0.7) {
            return 'green';
        } elseif ($score >= 0.4) {
            return 'yellow';
        } else {
            return 'red';
        }
    }

    public function hasKeywords()
    {
        return !empty($this->keywords);
    }

    public function getKeywordList()
    {
        return $this->keywords ?? [];
    }

    public function getTopKeywords($limit = 5)
    {
        return array_slice($this->getKeywordList(), 0, $limit);
    }

    public function hasEmotions()
    {
        return !empty($this->emotions);
    }

    public function getEmotionList()
    {
        return $this->emotions ?? [];
    }

    public function getTopEmotions($limit = 3)
    {
        return array_slice($this->getEmotionList(), 0, $limit);
    }

    public function getEmotionText($emotion)
    {
        $emotions = [
            'joy' => 'فرح',
            'sadness' => 'حزن',
            'anger' => 'غضب',
            'fear' => 'خوف',
            'surprise' => 'مفاجأة',
            'disgust' => 'اشمئزاز',
            'trust' => 'ثقة',
            'anticipation' => 'ترقب'
        ];

        return $emotions[$emotion] ?? $emotion;
    }

    public function getEmotionIcon($emotion)
    {
        $icons = [
            'joy' => 'fas fa-laugh',
            'sadness' => 'fas fa-sad-tear',
            'anger' => 'fas fa-angry',
            'fear' => 'fas fa-scared',
            'surprise' => 'fas fa-surprise',
            'disgust' => 'fas fa-meh-rolling-eyes',
            'trust' => 'fas fa-handshake',
            'anticipation' => 'fas fa-eye'
        ];

        return $icons[$emotion] ?? 'fas fa-smile';
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

    public function getAnalyzedDate()
    {
        return $this->analyzed_at ? $this->analyzed_at->format('Y-m-d H:i') : null;
    }

    public function getAnalyzedDateArabic()
    {
        return $this->analyzed_at ? $this->analyzed_at->locale('ar')->translatedFormat('d F Y') : null;
    }

    public function getAnalysisTime()
    {
        if (!$this->analyzed_at) {
            return null;
        }

        return $this->created_at->diffInMinutes($this->analyzed_at);
    }

    public function getAnalysisTimeText()
    {
        $minutes = $this->getAnalysisTime();
        
        if (!$minutes) {
            return null;
        }

        if ($minutes < 1) {
            return 'أقل من دقيقة';
        } elseif ($minutes < 60) {
            return "{$minutes} دقائق";
        } elseif ($minutes < 1440) { // 24 hours
            $hours = round($minutes / 60);
            return "{$hours} ساعات";
        } else {
            $days = round($minutes / 1440);
            return "{$days} أيام";
        }
    }

    public function getExcerpt($length = 100)
    {
        if (!$this->review) {
            return null;
        }

        $content = strip_tags($this->review->content);
        return strlen($content) > $length ? substr($content, 0, $length) . '...' : $content;
    }

    public function getMetaDescription()
    {
        return "تحليل المشاعر - {$this->getSentimentText()} ({$this->getConfidencePercentage()})";
    }

    public function getMetaKeywords()
    {
        $keywords = ['تحليل المشاعر', $this->getSentimentText()];
        
        if ($this->hasKeywords()) {
            $keywords = array_merge($keywords, $this->getKeywordList());
        }
        
        if ($this->hasEmotions()) {
            $keywords = array_merge($keywords, $this->getEmotionList());
        }
        
        return array_unique($keywords);
    }

    public function getStructuredData()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $this->review->rating ?? 0,
                'bestRating' => 5
            ],
            'reviewBody' => $this->review->content ?? '',
            'sentiment' => $this->sentiment,
            'confidence' => $this->confidence
        ];
    }

    // Static methods
    public static function getSentimentDistribution()
    {
        return [
            'positive' => self::positive()->count(),
            'negative' => self::negative()->count(),
            'neutral' => self::neutral()->count()
        ];
    }

    public static function getConfidenceDistribution()
    {
        return [
            'high' => self::highConfidence()->count(),
            'medium' => self::where('confidence', '>', 0.5)->where('confidence', '<', 0.8)->count(),
            'low' => self::lowConfidence()->count()
        ];
    }

    public static function getAverageConfidence()
    {
        return self::avg('confidence') ?? 0;
    }

    public static function getTopKeywords($limit = 20)
    {
        $allKeywords = self::whereNotNull('keywords')->get()->flatMap(function($analysis) {
            return $analysis->getKeywordList();
        });

        return $allKeywords->countBy()->sortDesc()->take($limit);
    }

    public static function getTopEmotions($limit = 10)
    {
        $allEmotions = self::whereNotNull('emotions')->get()->flatMap(function($analysis) {
            return $analysis->getEmotionList();
        });

        return $allEmotions->countBy()->sortDesc()->take($limit);
    }

    public static function getSentimentByRating()
    {
        return self::join('reviews', 'review_sentiment_analyses.review_id', '=', 'reviews.id')
            ->selectRaw('reviews.rating, sentiment, COUNT(*) as count')
            ->groupBy('reviews.rating', 'sentiment')
            ->orderBy('reviews.rating')
            ->get();
    }

    public static function getStatistics()
    {
        return [
            'total_analyses' => self::count(),
            'positive_count' => self::positive()->count(),
            'negative_count' => self::negative()->count(),
            'neutral_count' => self::neutral()->count(),
            'average_confidence' => self::avg('confidence'),
            'sentiment_distribution' => self::getSentimentDistribution(),
            'confidence_distribution' => self::getConfidenceDistribution(),
            'top_keywords' => self::getTopKeywords(10),
            'top_emotions' => self::getTopEmotions(5),
            'by_rating' => self::getSentimentByRating(),
            'recent_analyses' => self::with('review')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
        ];
    }

    protected static function booted()
    {
        static::created(function ($analysis) {
            // Update review sentiment
            if ($analysis->review) {
                $analysis->review->update(['sentiment' => $analysis->sentiment]);
            }
        });

        static::updated(function ($analysis) {
            if ($analysis->wasChanged('sentiment') && $analysis->review) {
                $analysis->review->update(['sentiment' => $analysis->sentiment]);
            }
        });
    }
}
