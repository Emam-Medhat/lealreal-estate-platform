<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'survey_id',
        'user_id',
        'responses',
        'ip_address',
        'user_agent',
        'completed_at'
    ];

    protected $casts = [
        'responses' => 'array',
        'completed_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'completed_at'
    ];

    // Relationships
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeIncomplete($query)
    {
        return $query->whereNull('completed_at');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByIP($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    // Methods
    public function getResponse($questionId)
    {
        return $this->responses["question_{$questionId}"] ?? null;
    }

    public function hasResponse($questionId)
    {
        return array_key_exists("question_{$questionId}", $this->responses);
    }

    public function getAnswerText($questionId, $options = [])
    {
        $response = $this->getResponse($questionId);
        
        if (!$response) {
            return null;
        }

        if (is_array($response)) {
            // For checkbox questions
            $texts = [];
            foreach ($response as $key) {
                $texts[] = $options[$key] ?? $key;
            }
            return implode(', ', $texts);
        }

        return $options[$response] ?? $response;
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

    public function getCompletionDate()
    {
        return $this->completed_at ? $this->completed_at->format('Y-m-d H:i') : null;
    }

    public function getCompletionDateArabic()
    {
        return $this->completed_at ? $this->completed_at->locale('ar')->translatedFormat('d F Y') : null;
    }

    public function getCompletionTime()
    {
        if (!$this->completed_at) {
            return null;
        }

        return $this->created_at->diffInMinutes($this->completed_at);
    }

    public function getCompletionTimeText()
    {
        $minutes = $this->getCompletionTime();
        
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

    public function isCompleted()
    {
        return !is_null($this->completed_at);
    }

    public function isIncomplete()
    {
        return is_null($this->completed_at);
    }

    public function isAnonymous()
    {
        return is_null($this->user_id);
    }

    public function getProgressPercentage()
    {
        if (!$this->survey) {
            return 0;
        }

        $totalQuestions = $this->survey->questions()->count();
        $answeredQuestions = count(array_filter($this->responses, function($value) {
            return $value !== null && $value !== '';
        }));

        return $totalQuestions > 0 ? ($answeredQuestions / $totalQuestions) * 100 : 0;
    }

    public function getAnsweredQuestionsCount()
    {
        return count(array_filter($this->responses, function($value) {
            return $value !== null && $value !== '';
        }));
    }

    public function getSkippedQuestionsCount()
    {
        if (!$this->survey) {
            return 0;
        }

        $totalQuestions = $this->survey->questions()->count();
        $answeredQuestions = $this->getAnsweredQuestionsCount();
        
        return $totalQuestions - $answeredQuestions;
    }

    public function getRequiredQuestionsAnswered()
    {
        if (!$this->survey) {
            return true;
        }

        $requiredQuestions = $this->survey->questions()->where('is_required', true)->get();
        
        foreach ($requiredQuestions as $question) {
            if (!$this->hasResponse($question->id) || empty($this->getResponse($question->id))) {
                return false;
            }
        }

        return true;
    }

    public function canBeCompleted()
    {
        return $this->getRequiredQuestionsAnswered();
    }

    public function complete()
    {
        $this->update(['completed_at' => now()]);
    }

    public function getMetaDescription()
    {
        return "إجابات استبيان - {$this->survey->title}";
    }

    public function getMetaKeywords()
    {
        return ['استبيان', 'إجابات', 'استقصاء', $this->survey->title];
    }

    // Static methods
    public static function getStatistics($surveyId)
    {
        return [
            'total_responses' => self::where('survey_id', $surveyId)->count(),
            'completed_responses' => self::where('survey_id', $surveyId)->completed()->count(),
            'incomplete_responses' => self::where('survey_id', $surveyId)->incomplete()->count(),
            'anonymous_responses' => self::where('survey_id', $surveyId)->whereNull('user_id')->count(),
            'completion_rate' => self::where('survey_id', $surveyId)->completed()->count() / self::where('survey_id', $surveyId)->count() * 100,
            'average_completion_time' => self::where('survey_id', $surveyId)->completed()
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as avg_minutes')
                ->first()
                ->avg_minutes ?? 0,
            'responses_by_date' => self::where('survey_id', $surveyId)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        ];
    }

    public static function getUserResponseHistory($userId, $limit = 10)
    {
        return self::where('user_id', $userId)
            ->with('survey')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    protected static function booted()
    {
        static::created(function ($response) {
            // Update survey response count
            $response->survey->increment('response_count');
        });

        static::deleted(function ($response) {
            // Update survey response count
            $response->survey->decrement('response_count');
        });

        static::updated(function ($response) {
            if ($response->wasChanged('completed_at') && $response->isCompleted()) {
                // Notify user about completion if needed
                if ($response->user && $response->survey->show_results) {
                    $response->user->notifications()->create([
                        'type' => 'survey_completed',
                        'title' => 'تم إكمال الاستبيان',
                        'message' => 'شكراً لإكمالك الاستبيان',
                        'data' => ['survey_id' => $response->survey_id]
                    ]);
                }
            }
        });
    }
}
