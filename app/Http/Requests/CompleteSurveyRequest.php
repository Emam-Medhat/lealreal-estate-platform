<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteSurveyRequest extends FormRequest
{
    public function authorize()
    {
        $survey = $this->route('survey');
        
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return false;
        }

        // Check if user can participate in this survey
        return $survey && $survey->canBeParticipatedBy(\Illuminate\Support\Facades\Auth::user());
    }

    public function rules()
    {
        $survey = $this->route('survey');
        $rules = [];

        if ($survey) {
            // Get all questions for this survey
            $questions = $survey->questions;
            
            foreach ($questions as $question) {
                $questionKey = "question_{$question->id}";
                
                // Base rule for all questions
                if ($question->is_required) {
                    $rules[$questionKey] = 'required';
                } else {
                    $rules[$questionKey] = 'nullable';
                }

                // Add type-specific validation
                switch ($question->question_type) {
                    case 'text':
                        $rules[$questionKey] .= '|string|max:255';
                        break;
                    case 'textarea':
                        $rules[$questionKey] .= '|string|max:2000';
                        break;
                    case 'number':
                        $rules[$questionKey] .= '|numeric';
                        break;
                    case 'email':
                        $rules[$questionKey] .= '|email';
                        break;
                    case 'rating':
                        $rules[$questionKey] .= '|integer|min:1|max:5';
                        break;
                    case 'multiple_choice':
                    case 'dropdown':
                        $options = array_keys($question->getOptionsList());
                        $rules[$questionKey] .= '|in:' . implode(',', $options);
                        break;
                    case 'checkbox':
                        $rules[$questionKey] .= '|array';
                        $options = array_keys($question->getOptionsList());
                        if (!empty($options)) {
                            $rules[$questionKey . '.*'] = 'in:' . implode(',', $options);
                        }
                        break;
                    case 'date':
                        $rules[$questionKey] .= '|date';
                        break;
                }
            }
        }

        return $rules;
    }

    public function messages()
    {
        $survey = $this->route('survey');
        $messages = [];

        if ($survey) {
            $questions = $survey->questions;
            
            foreach ($questions as $question) {
                $questionKey = "question_{$question->id}";
                
                if ($question->is_required) {
                    $messages[$questionKey . '.required'] = "سؤال '{$question->question_text}' مطلوب";
                }

                // Type-specific messages
                switch ($question->question_type) {
                    case 'text':
                        $messages[$questionKey . '.max'] = "يجب ألا ي exceed إجابة السؤال '{$question->question_text}' عن 255 حرفاً";
                        break;
                    case 'textarea':
                        $messages[$questionKey . '.max'] = "يجب ألا ي exceed إجابة السؤال '{$question->question_text}' عن 2000 حرف";
                        break;
                    case 'number':
                        $messages[$questionKey . '.numeric'] = "يجب أن تكون إجابة السؤال '{$question->question_text}' رقماً";
                        break;
                    case 'email':
                        $messages[$questionKey . '.email'] = "يجب أن تكون إجابة السؤال '{$question->question_text}' بريداً إلكترونياً صالحاً";
                        break;
                    case 'rating':
                        $messages[$questionKey . '.integer'] = "يجب أن تكون إجابة السؤال '{$question->question_text}' رقماً";
                        $messages[$questionKey . '.min'] = "أقل تقييم للسؤال '{$question->question_text}' هو 1";
                        $messages[$questionKey . '.max'] = "أعلى تقييم للسؤال '{$question->question_text}' هو 5";
                        break;
                    case 'multiple_choice':
                    case 'dropdown':
                        $messages[$questionKey . '.in'] = "الخيار المحدد للسؤال '{$question->question_text}' غير صالح";
                        break;
                    case 'checkbox':
                        $messages[$questionKey . '.array'] = "يجب أن تكون إجابة السؤال '{$question->question_text}' مصفوفة";
                        $messages[$questionKey . '.*.in'] = "بعض الخيارات المحددة للسؤال '{$question->question_text}' غير صالحة";
                        break;
                    case 'date':
                        $messages[$questionKey . '.date'] = "يجب أن تكون إجابة السؤال '{$question->question_text}' تاريخاً صالحاً";
                        break;
                }
            }
        }

        return $messages;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $survey = $this->route('survey');
            
            if (!$survey) {
                return;
            }

            // Check if all required questions are answered
            $questions = $survey->questions()->where('is_required', true)->get();
            
            foreach ($questions as $question) {
                $questionKey = "question_{$question->id}";
                $answer = $this->input($questionKey);
                
                if ($question->is_required && (is_null($answer) || $answer === '')) {
                    $validator->errors()->add($questionKey, "سؤال '{$question->question_text}' مطلوب");
                }
            }

            // Validate checkbox answers have at least one option if required
            foreach ($questions as $question) {
                if ($question->question_type === 'checkbox' && $question->is_required) {
                    $questionKey = "question_{$question->id}";
                    $answer = $this->input($questionKey);
                    
                    if (!is_array($answer) || empty($answer)) {
                        $validator->errors()->add($questionKey, "يجب اختيار خيار واحد على الأقل للسؤال '{$question->question_text}'");
                    }
                }
            }
        });
    }

    protected function prepareForValidation()
    {
        $survey = $this->route('survey');
        
        if ($survey) {
            $questions = $survey->questions;
            $preparedData = [];

            foreach ($questions as $question) {
                $questionKey = "question_{$question->id}";
                $answer = $this->input($questionKey);

                // Prepare checkbox answers
                if ($question->question_type === 'checkbox' && is_array($answer)) {
                    $preparedData[$questionKey] = array_filter($answer, function($value) {
                        return $value !== null && $value !== '';
                    });
                } else {
                    $preparedData[$questionKey] = $answer;
                }
            }

            $this->merge($preparedData);
        }
    }

    public function getSurveyResponses()
    {
        $survey = $this->route('survey');
        $responses = [];

        if ($survey) {
            $questions = $survey->questions;
            
            foreach ($questions as $question) {
                $questionKey = "question_{$question->id}";
                $responses[$question->id] = $this->input($questionKey);
            }
        }

        return $responses;
    }
}
