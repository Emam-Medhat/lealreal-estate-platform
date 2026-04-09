<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'survey_id',
        'question_text',
        'question_type',
        'options',
        'is_required',
        'order'
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'order' => 'integer'
    ];

    // Relationships
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    // Scopes
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('question_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Methods
    public function getTypeText()
    {
        $types = [
            'text' => 'نص',
            'number' => 'رقم',
            'email' => 'بريد إلكتروني',
            'rating' => 'تقييم (1-5)',
            'multiple_choice' => 'اختيار من متعدد',
            'checkbox' => 'خيارات متعددة',
            'dropdown' => 'قائمة منسدلة',
            'date' => 'تاريخ',
            'textarea' => 'منطقة نصية'
        ];

        return $types[$this->question_type] ?? $this->question_type;
    }

    public function getTypeIcon()
    {
        $icons = [
            'text' => 'fas fa-font',
            'number' => 'fas fa-hashtag',
            'email' => 'fas fa-envelope',
            'rating' => 'fas fa-star',
            'multiple_choice' => 'fas fa-dot-circle',
            'checkbox' => 'fas fa-check-square',
            'dropdown' => 'fas fa-chevron-down',
            'date' => 'fas fa-calendar',
            'textarea' => 'fas fa-align-left'
        ];

        return $icons[$this->question_type] ?? 'fas fa-question';
    }

    public function hasOptions()
    {
        return in_array($this->question_type, ['multiple_choice', 'checkbox', 'dropdown']);
    }

    public function getOptionsList()
    {
        return $this->options ?? [];
    }

    public function getOptionByKey($key)
    {
        return ($this->options ?? [])[$key] ?? null;
    }

    public function getOptionKeys()
    {
        return array_keys($this->options ?? []);
    }

    public function getOptionValues()
    {
        return array_values($this->options ?? []);
    }

    public function isRequired()
    {
        return $this->is_required;
    }

    public function isOptional()
    {
        return !$this->is_required;
    }

    public function isTextBased()
    {
        return in_array($this->question_type, ['text', 'textarea', 'email']);
    }

    public function isNumeric()
    {
        return in_array($this->question_type, ['number', 'rating']);
    }

    public function isDateBased()
    {
        return $this->question_type === 'date';
    }

    public function isChoiceBased()
    {
        return in_array($this->question_type, ['multiple_choice', 'checkbox', 'dropdown']);
    }

    public function validateResponse($response)
    {
        // Check if required and empty
        if ($this->is_required && (is_null($response) || $response === '')) {
            return ['valid' => false, 'message' => 'هذا السؤال مطلوب'];
        }

        // Skip validation if not required and empty
        if (!$this->is_required && (is_null($response) || $response === '')) {
            return ['valid' => true, 'message' => null];
        }

        // Type-specific validation
        switch ($this->question_type) {
            case 'email':
                if (!filter_var($response, FILTER_VALIDATE_EMAIL)) {
                    return ['valid' => false, 'message' => 'يرجى إدخال بريد إلكتروني صحيح'];
                }
                break;

            case 'number':
                if (!is_numeric($response)) {
                    return ['valid' => false, 'message' => 'يرجى إدخال رقم صحيح'];
                }
                break;

            case 'rating':
                if (!is_numeric($response) || $response < 1 || $response > 5) {
                    return ['valid' => false, 'message' => 'يرجى إدخال تقييم بين 1 و 5'];
                }
                break;

            case 'multiple_choice':
            case 'dropdown':
                if (!in_array($response, $this->getOptionKeys())) {
                    return ['valid' => false, 'message' => 'يرجى اختيار خيار صحيح'];
                }
                break;

            case 'checkbox':
                if (!is_array($response)) {
                    return ['valid' => false, 'message' => 'يرجى اختيار خيار واحد على الأقل'];
                }
                
                foreach ($response as $value) {
                    if (!in_array($value, $this->getOptionKeys())) {
                        return ['valid' => false, 'message' => 'يرجى اختيار خيارات صحيحة'];
                    }
                }
                break;

            case 'date':
                if (!strtotime($response)) {
                    return ['valid' => false, 'message' => 'يرجى إدخال تاريخ صحيح'];
                }
                break;

            case 'text':
            case 'textarea':
                if (strlen($response) > 1000) {
                    return ['valid' => false, 'message' => 'النص طويل جداً (الحد الأقصى 1000 حرف)'];
                }
                break;
        }

        return ['valid' => true, 'message' => null];
    }

    public function formatResponseForDisplay($response)
    {
        if (is_null($response) || $response === '') {
            return 'لم يتم الإجابة';
        }

        switch ($this->question_type) {
            case 'checkbox':
                if (is_array($response)) {
                    $texts = [];
                    foreach ($response as $key) {
                        $texts[] = $this->getOptionByKey($key) ?? $key;
                    }
                    return implode(', ', $texts);
                }
                break;

            case 'multiple_choice':
            case 'dropdown':
                return $this->getOptionByKey($response) ?? $response;

            case 'rating':
                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= $i <= $response ? '★' : '☆';
                }
                return $stars . " ({$response})";

            case 'date':
                return date('Y-m-d', strtotime($response));

            default:
                return $response;
        }
    }

    public function getResponseStatistics($surveyId)
    {
        $responses = SurveyResponse::where('survey_id', $surveyId)->get();
        
        $statistics = [
            'question_id' => $this->id,
            'question_text' => $this->question_text,
            'type' => $this->question_type,
            'total_responses' => 0,
            'response_rate' => 0
        ];

        $totalResponses = 0;
        $responseData = [];

        foreach ($responses as $response) {
            $answer = $response->getResponse($this->id);
            
            if ($answer !== null && $answer !== '') {
                $totalResponses++;
                
                if ($this->question_type === 'multiple_choice' || $this->question_type === 'dropdown') {
                    $responseData[$answer] = ($responseData[$answer] ?? 0) + 1;
                } elseif ($this->question_type === 'checkbox') {
                    if (is_array($answer)) {
                        foreach ($answer as $value) {
                            $responseData[$value] = ($responseData[$value] ?? 0) + 1;
                        }
                    }
                } elseif ($this->question_type === 'rating') {
                    $responseData[$answer] = ($responseData[$answer] ?? 0) + 1;
                }
            }
        }

        $statistics['total_responses'] = $totalResponses;
        $statistics['response_rate'] = $responses->count() > 0 ? 
            ($totalResponses / $responses->count()) * 100 : 0;

        if ($this->question_type === 'multiple_choice' || $this->question_type === 'dropdown') {
            $optionStats = [];
            foreach ($this->getOptionsList() as $key => $option) {
                $optionStats[$option] = [
                    'count' => $responseData[$key] ?? 0,
                    'percentage' => $totalResponses > 0 ? 
                        (($responseData[$key] ?? 0) / $totalResponses) * 100 : 0
                ];
            }
            $statistics['option_statistics'] = $optionStats;
        } elseif ($this->question_type === 'checkbox') {
            $optionStats = [];
            foreach ($this->getOptionsList() as $key => $option) {
                $optionStats[$option] = [
                    'count' => $responseData[$key] ?? 0,
                    'percentage' => $totalResponses > 0 ? 
                        (($responseData[$key] ?? 0) / $totalResponses) * 100 : 0
                ];
            }
            $statistics['option_statistics'] = $optionStats;
        } elseif ($this->question_type === 'rating') {
            $ratingStats = [];
            for ($i = 1; $i <= 5; $i++) {
                $ratingStats[$i] = [
                    'count' => $responseData[$i] ?? 0,
                    'percentage' => $totalResponses > 0 ? 
                        (($responseData[$i] ?? 0) / $totalResponses) * 100 : 0
                ];
            }
            $statistics['rating_statistics'] = $ratingStats;
            
            // Calculate average rating
            $totalRating = 0;
            $ratingCount = 0;
            foreach ($responseData as $rating => $count) {
                $totalRating += $rating * $count;
                $ratingCount += $count;
            }
            $statistics['average_rating'] = $ratingCount > 0 ? $totalRating / $ratingCount : 0;
        }

        return $statistics;
    }

    public function getMetaDescription()
    {
        return "سؤال استبيان - {$this->question_text}";
    }

    public function getMetaKeywords()
    {
        return ['سؤال', 'استبيان', 'استقصاء', $this->getTypeText()];
    }

    // Static methods
    public static function getTypes()
    {
        return [
            'text' => 'نص',
            'number' => 'رقم',
            'email' => 'بريد إلكتروني',
            'rating' => 'تقييم (1-5)',
            'multiple_choice' => 'اختيار من متعدد',
            'checkbox' => 'خيارات متعددة',
            'dropdown' => 'قائمة منسدلة',
            'date' => 'تاريخ',
            'textarea' => 'منطقة نصية'
        ];
    }

    protected static function booted()
    {
        static::creating(function ($question) {
            // Set order if not provided
            if (!$question->order) {
                $maxOrder = SurveyQuestion::where('survey_id', $question->survey_id)
                    ->max('order') ?? 0;
                $question->order = $maxOrder + 1;
            }
        });

        static::deleted(function ($question) {
            // Reorder remaining questions
            SurveyQuestion::where('survey_id', $question->survey_id)
                ->where('order', '>', $question->order)
                ->decrement('order');
        });
    }
}
