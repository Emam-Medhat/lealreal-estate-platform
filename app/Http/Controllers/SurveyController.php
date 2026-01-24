<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::with(['creator'])
            ->where('status', 'published')
            ->where('starts_at', '<=', now())
            ->where(function($query) {
                $query->where('expires_at', '>=', now())
                      ->orWhereNull('expires_at');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('surveys.index', compact('surveys'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $questionTypes = $this->getQuestionTypes();
        $targetAudiences = $this->getTargetAudiences();

        return view('surveys.create', compact('questionTypes', 'targetAudiences'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_audience' => 'required|string',
            'starts_at' => 'required|date|after_or_equal:today',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_anonymous' => 'boolean',
            'allow_multiple_responses' => 'boolean',
            'show_results' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:500',
            'questions.*.question_type' => 'required|string',
            'questions.*.is_required' => 'boolean',
            'questions.*.options' => 'required_if:questions.*.question_type,multiple_choice,checkbox,dropdown|array'
        ]);

        DB::beginTransaction();
        
        try {
            $survey = Survey::create([
                'created_by' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'target_audience' => $request->target_audience,
                'starts_at' => $request->starts_at,
                'expires_at' => $request->expires_at,
                'is_anonymous' => $request->has('is_anonymous'),
                'allow_multiple_responses' => $request->has('allow_multiple_responses'),
                'show_results' => $request->has('show_results'),
                'status' => 'draft'
            ]);

            // Create questions
            foreach ($request->questions as $index => $questionData) {
                SurveyQuestion::create([
                    'survey_id' => $survey->id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'options' => $questionData['options'] ?? [],
                    'is_required' => $request->has("questions.{$index}.is_required"),
                    'order' => $index + 1
                ]);
            }

            DB::commit();

            return redirect()->route('surveys.show', $survey->id)
                ->with('success', 'تم إنشاء الاستبيان بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الاستبيان: ' . $e->getMessage());
        }
    }

    public function show(Survey $survey)
    {
        if ($survey->status !== 'published' && !Auth::user()->isAdmin()) {
            abort(404);
        }

        $survey->load(['questions', 'creator']);

        // Check if user can participate
        $canParticipate = $this->canParticipate($survey);
        $hasResponded = false;
        
        if (Auth::check() && !$survey->allow_multiple_responses) {
            $hasResponded = $survey->responses()->where('user_id', Auth::id())->exists();
        }

        // Get statistics if user is admin or results are public
        $showStatistics = Auth::user()->isAdmin() || $survey->show_results;
        $statistics = $showStatistics ? $this->getSurveyStatistics($survey) : null;

        return view('surveys.show', compact('survey', 'canParticipate', 'hasResponded', 'statistics', 'showStatistics'));
    }

    public function edit(Survey $survey)
    {
        if (!Auth::user()->isAdmin() || $survey->status === 'published') {
            abort(403);
        }

        $survey->load(['questions' => function($query) {
            $query->orderBy('order');
        }]);

        $questionTypes = $this->getQuestionTypes();
        $targetAudiences = $this->getTargetAudiences();

        return view('surveys.edit', compact('survey', 'questionTypes', 'targetAudiences'));
    }

    public function update(Request $request, Survey $survey)
    {
        if (!Auth::user()->isAdmin() || $survey->status === 'published') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_audience' => 'required|string',
            'starts_at' => 'required|date|after_or_equal:today',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_anonymous' => 'boolean',
            'allow_multiple_responses' => 'boolean',
            'show_results' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:500',
            'questions.*.question_type' => 'required|string',
            'questions.*.is_required' => 'boolean',
            'questions.*.options' => 'required_if:questions.*.question_type,multiple_choice,checkbox,dropdown|array'
        ]);

        DB::beginTransaction();
        
        try {
            $survey->update([
                'title' => $request->title,
                'description' => $request->description,
                'target_audience' => $request->target_audience,
                'starts_at' => $request->starts_at,
                'expires_at' => $request->expires_at,
                'is_anonymous' => $request->has('is_anonymous'),
                'allow_multiple_responses' => $request->has('allow_multiple_responses'),
                'show_results' => $request->has('show_results')
            ]);

            // Update questions
            $survey->questions()->delete();
            foreach ($request->questions as $index => $questionData) {
                SurveyQuestion::create([
                    'survey_id' => $survey->id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'options' => $questionData['options'] ?? [],
                    'is_required' => $request->has("questions.{$index}.is_required"),
                    'order' => $index + 1
                ]);
            }

            DB::commit();

            return redirect()->route('surveys.show', $survey->id)
                ->with('success', 'تم تحديث الاستبيان بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الاستبيان: ' . $e->getMessage());
        }
    }

    public function submit(Request $request, Survey $survey)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'يجب تسجيل الدخول'], 401);
        }

        if (!$this->canParticipate($survey)) {
            return response()->json(['error' => 'غير مصرح لك بالمشاركة في هذا الاستبيان'], 403);
        }

        if (!$survey->allow_multiple_responses) {
            $hasResponded = $survey->responses()->where('user_id', Auth::id())->exists();
            if ($hasResponded) {
                return response()->json(['error' => 'لقد شاركت بالفعل في هذا الاستبيان'], 422);
            }
        }

        // Validate responses
        $responses = $request->get('responses', []);
        $validationRules = $this->getResponseValidationRules($survey);

        $validator = Validator::make($responses, $validationRules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        
        try {
            $surveyResponse = SurveyResponse::create([
                'survey_id' => $survey->id,
                'user_id' => $survey->is_anonymous ? null : Auth::id(),
                'responses' => $responses,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'completed_at' => now()
            ]);

            // Update survey statistics
            $survey->increment('response_count');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال إجاباتك بنجاح',
                'response_id' => $surveyResponse->id
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'حدث خطأ أثناء إرسال الإجابات'], 500);
        }
    }

    public function publish(Survey $survey)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($survey->status !== 'draft') {
            return back()->with('error', 'لا يمكن نشر هذا الاستبيان');
        }

        $survey->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        // Notify target audience
        $this->notifyTargetAudience($survey);

        return back()->with('success', 'تم نشر الاستبيان بنجاح');
    }

    public function close(Survey $survey)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $survey->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);

        return back()->with('success', 'تم إغلاق الاستبيان بنجاح');
    }

    public function destroy(Survey $survey)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $survey->delete();

        return redirect()->route('surveys.index')
            ->with('success', 'تم حذف الاستبيان بنجاح');
    }

    public function mySurveys()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $surveys = Survey::where('created_by', Auth::id())
            ->with(['responses'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('surveys.my-surveys', compact('surveys'));
    }

    public function getResults(Survey $survey)
    {
        if (!Auth::user()->isAdmin() && !$survey->show_results) {
            abort(403);
        }

        $statistics = $this->getSurveyStatistics($survey);
        $responses = $survey->responses()
            ->with('user')
            ->orderBy('completed_at', 'desc')
            ->paginate(50);

        return response()->json([
            'statistics' => $statistics,
            'responses' => $responses
        ]);
    }

    public function exportResults(Survey $survey, $format = 'csv')
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $responses = $survey->responses()->get();
        
        // Export logic here
        return response()->json([
            'message' => 'Export functionality to be implemented',
            'format' => $format,
            'response_count' => $responses->count()
        ]);
    }

    private function canParticipate(Survey $survey)
    {
        if (!Auth::check()) {
            return false;
        }

        // Check if survey is active
        if ($survey->status !== 'published') {
            return false;
        }

        if (now() < $survey->starts_at) {
            return false;
        }

        if ($survey->expires_at && now() > $survey->expires_at) {
            return false;
        }

        // Check target audience
        $user = Auth::user();
        switch ($survey->target_audience) {
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
            default:
                return false;
        }
    }

    private function getResponseValidationRules(Survey $survey)
    {
        $rules = [];
        
        foreach ($survey->questions as $question) {
            $key = 'question_' . $question->id;
            
            if ($question->is_required) {
                $rules[$key] = 'required';
            }

            switch ($question->question_type) {
                case 'text':
                    $rules[$key] .= '|string|max:1000';
                    break;
                case 'number':
                    $rules[$key] .= '|numeric';
                    break;
                case 'email':
                    $rules[$key] .= '|email';
                    break;
                case 'rating':
                    $rules[$key] .= '|integer|min:1|max:5';
                    break;
                case 'multiple_choice':
                case 'dropdown':
                    $rules[$key] .= '|in:' . implode(',', array_keys($question->options));
                    break;
                case 'checkbox':
                    $rules[$key] .= '|array';
                    $rules[$key . '.*'] = 'in:' . implode(',', array_keys($question->options));
                    break;
            }
        }

        return $rules;
    }

    private function getSurveyStatistics(Survey $survey)
    {
        $totalResponses = $survey->responses()->count();
        $statistics = ['total_responses' => $totalResponses];

        foreach ($survey->questions as $question) {
            $questionStats = [
                'question' => $question->question_text,
                'type' => $question->question_type,
                'responses_count' => 0
            ];

            if ($question->question_type === 'multiple_choice' || $question->question_type === 'dropdown') {
                $optionCounts = [];
                foreach ($question->options as $key => $option) {
                    $optionCounts[$option] = $survey->responses()
                        ->whereJsonContains("responses->question_{$question->id}", $key)
                        ->count();
                }
                $questionStats['option_counts'] = $optionCounts;
            } elseif ($question->question_type === 'rating') {
                $ratingDistribution = [];
                for ($i = 1; $i <= 5; $i++) {
                    $ratingDistribution[$i] = $survey->responses()
                        ->whereJsonContains("responses->question_{$question->id}", $i)
                        ->count();
                }
                $questionStats['rating_distribution'] = $ratingDistribution;
                $questionStats['average_rating'] = $survey->responses()
                    ->avg("responses->question_{$question->id}") ?? 0;
            } else {
                $questionStats['responses_count'] = $survey->responses()
                    ->whereNotNull("responses->question_{$question->id}")
                    ->count();
            }

            $statistics['questions'][] = $questionStats;
        }

        return $statistics;
    }

    private function getQuestionTypes()
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

    private function getTargetAudiences()
    {
        return [
            'all_users' => 'جميع المستخدمين',
            'property_owners' => 'أصحاب العقارات',
            'agents' => 'الوكلاء',
            'buyers' => 'المشترون',
            'sellers' => 'البائعون',
            'new_users' => 'المستخدمون الجدد',
            'active_users' => 'المستخدمون النشطون'
        ];
    }

    private function notifyTargetAudience(Survey $survey)
    {
        // Implementation for notifying target audience
        // This would depend on your notification system
    }
}
