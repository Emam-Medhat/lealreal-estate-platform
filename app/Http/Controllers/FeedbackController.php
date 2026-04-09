<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Property;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::with(['user', 'feedbackable'])
            ->when(!Auth::user()->isAdmin(), function($query) {
                return $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('feedback.index', compact('feedbacks'));
    }

    public function create($type = null, $id = null)
    {
        $feedbackable = null;
        
        if ($type && $id) {
            $feedbackable = $this->getFeedbackableModel($type, $id);
        }

        $feedbackTypes = $this->getFeedbackTypes();
        $categories = $this->getCategories();

        return view('feedback.create', compact('feedbackable', 'type', 'feedbackTypes', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'feedbackable_type' => 'required|string|in:property,agent,user,system',
            'feedbackable_id' => 'nullable|integer|required_without:feedbackable_type,system',
            'type' => 'required|string',
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:2000',
            'rating' => 'nullable|integer|min:1|max:10',
            'priority' => 'required|in:low,medium,high,critical',
            'tags' => 'nullable|array',
            'is_anonymous' => 'boolean'
        ]);

        DB::beginTransaction();
        
        try {
            $feedbackable = null;
            if ($request->feedbackable_type !== 'system') {
                $feedbackable = $this->getFeedbackableModel($request->feedbackable_type, $request->feedbackable_id);
            }

            $feedback = Feedback::create([
                'user_id' => Auth::id(),
                'feedbackable_type' => $feedbackable ? get_class($feedbackable) : null,
                'feedbackable_id' => $feedbackable ? $feedbackable->id : null,
                'type' => $request->type,
                'category' => $request->category,
                'title' => $request->title,
                'content' => $request->content,
                'rating' => $request->rating,
                'priority' => $request->priority,
                'tags' => $request->tags ?? [],
                'is_anonymous' => $request->has('is_anonymous'),
                'status' => 'pending'
            ]);

            // Auto-categorize and prioritize based on content
            $this->processFeedback($feedback);

            // Send notifications for high-priority feedback
            if ($feedback->priority === 'critical' || $feedback->priority === 'high') {
                $this->sendHighPriorityNotification($feedback);
            }

            DB::commit();

            return redirect()->route('feedback.show', $feedback->id)
                ->with('success', 'تم إرسال التغذية الراجعة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إرسال التغذية الراجعة: ' . $e->getMessage());
        }
    }

    public function show(Feedback $feedback)
    {
        if ($feedback->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $feedback->load(['user', 'feedbackable']);

        return view('feedback.show', compact('feedback'));
    }

    public function update(Request $request, Feedback $feedback)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_review,acknowledged,resolved,closed',
            'admin_notes' => 'nullable|string|max:2000',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,critical'
        ]);

        $feedback->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'reviewed_at' => $request->status === 'in_review' ? now() : $feedback->reviewed_at,
            'resolved_at' => $request->status === 'resolved' ? now() : $feedback->resolved_at
        ]);

        // Notify user about status change
        $this->notifyStatusChange($feedback);

        return back()->with('success', 'تم تحديث التغذية الراجعة بنجاح');
    }

    public function respond(Request $request, Feedback $feedback)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'response' => 'required|string|min:10'
        ]);

        $feedback->update([
            'response' => $request->response,
            'responded_at' => now(),
            'responded_by' => Auth::id()
        ]);

        // Notify user
        $feedback->user->notifications()->create([
            'type' => 'feedback_response',
            'title' => 'رد على تغذيتك الراجعة',
            'message' => 'تم الرد على تغذيتك الراجعة',
            'data' => ['feedback_id' => $feedback->id]
        ]);

        return back()->with('success', 'تم إضافة الرد بنجاح');
    }

    public function myFeedback()
    {
        $feedbacks = Feedback::where('user_id', Auth::id())
            ->with(['feedbackable'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('feedback.my-feedback', compact('feedbacks'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $feedbacks = Feedback::with(['user', 'feedbackable'])
            ->when(!Auth::user()->isAdmin(), function($query) {
                return $query->where('user_id', Auth::id());
            })
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhereJsonContains('tags', $query);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('feedback.search', compact('feedbacks', 'query'));
    }

    public function getAnalytics()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $analytics = [
            'total' => Feedback::count(),
            'by_status' => Feedback::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get(),
            'by_priority' => Feedback::selectRaw('priority, count(*) as count')
                ->groupBy('priority')
                ->get(),
            'by_category' => Feedback::selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->get(),
            'by_type' => Feedback::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get(),
            'average_rating' => Feedback::whereNotNull('rating')->avg('rating'),
            'recent_trend' => Feedback::where('created_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(created_at) as date, count(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'response_rate' => Feedback::whereNotNull('responded_at')->count() / Feedback::count() * 100,
            'resolution_rate' => Feedback::where('status', 'resolved')->count() / Feedback::count() * 100
        ];

        return response()->json($analytics);
    }

    public function bulkProcess(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'feedback_ids' => 'required|array',
            'feedback_ids.*' => 'exists:feedbacks,id',
            'action' => 'required|in:acknowledge,resolve,assign,close',
            'assigned_to' => 'required_if:action,assign|exists:users,id',
            'response' => 'required_if:action,resolve|string|min:10'
        ]);

        $feedbacks = Feedback::whereIn('id', $request->feedback_ids)->get();
        $updatedCount = 0;

        foreach ($feedbacks as $feedback) {
            switch ($request->action) {
                case 'acknowledge':
                    $feedback->update(['status' => 'acknowledged', 'reviewed_at' => now()]);
                    break;
                case 'resolve':
                    $feedback->update([
                        'status' => 'resolved',
                        'response' => $request->response,
                        'resolved_at' => now(),
                        'responded_by' => Auth::id()
                    ]);
                    break;
                case 'assign':
                    $feedback->update([
                        'assigned_to' => $request->assigned_to,
                        'status' => 'in_review'
                    ]);
                    break;
                case 'close':
                    $feedback->update(['status' => 'closed']);
                    break;
            }
            $updatedCount++;
        }

        return back()->with('success', "تم معالجة {$updatedCount} من التغذية الراجعة بنجاح");
    }

    public function export(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'format' => 'required|in:csv,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|array',
            'category' => 'nullable|array'
        ]);

        $query = Feedback::with(['user', 'feedbackable']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->status) {
            $query->whereIn('status', $request->status);
        }

        if ($request->category) {
            $query->whereIn('category', $request->category);
        }

        $feedbacks = $query->get();

        // Export logic here (using Laravel Excel or similar)
        return response()->json([
            'message' => 'Export functionality to be implemented',
            'count' => $feedbacks->count()
        ]);
    }

    private function getFeedbackableModel($type, $id)
    {
        switch ($type) {
            case 'property':
                return Property::findOrFail($id);
            case 'agent':
                return Agent::findOrFail($id);
            case 'user':
                return User::findOrFail($id);
            case 'system':
                return null;
            default:
                return null;
        }
    }

    private function getFeedbackTypes()
    {
        return [
            'bug_report' => 'بلاغ عن خطأ',
            'feature_request' => 'طلب ميزة جديدة',
            'improvement' => 'اقتراح تحسين',
            'complaint' => 'شكوى',
            'compliment' => 'إشادة',
            'general' => 'عام',
            'usability' => 'سهولة الاستخدام',
            'performance' => 'أداء',
            'security' => 'أمان'
        ];
    }

    private function getCategories()
    {
        return [
            'user_interface' => 'واجهة المستخدم',
            'functionality' => 'الوظائف',
            'performance' => 'الأداء',
            'security' => 'الأمان',
            'documentation' => 'التوثيق',
            'customer_service' => 'خدمة العملاء',
            'pricing' => 'التسعير',
            'features' => 'الميزات',
            'other' => 'أخرى'
        ];
    }

    private function processFeedback(Feedback $feedback)
    {
        // Auto-categorization based on keywords
        $content = strtolower($feedback->content);
        
        if (str_contains($content, 'bug') || str_contains($content, 'error') || str_contains($content, 'crash')) {
            $feedback->update(['category' => 'bug_report']);
        } elseif (str_contains($content, 'slow') || str_contains($content, 'lag') || str_contains($content, 'performance')) {
            $feedback->update(['category' => 'performance']);
        }

        // Auto-prioritization
        if ($feedback->rating <= 3 || str_contains($content, 'urgent') || str_contains($content, 'critical')) {
            $feedback->update(['priority' => 'high']);
        }

        // Extract tags from content
        $tags = $this->extractTags($feedback->content);
        if (!empty($tags)) {
            $feedback->update(['tags' => $tags]);
        }
    }

    private function extractTags($content)
    {
        $tags = [];
        $keywords = ['mobile', 'desktop', 'api', 'database', 'search', 'payment', 'upload', 'email'];
        
        foreach ($keywords as $keyword) {
            if (str_contains(strtolower($content), $keyword)) {
                $tags[] = $keyword;
            }
        }

        return $tags;
    }

    private function sendHighPriorityNotification(Feedback $feedback)
    {
        User::where('role', 'admin')->get()->each(function($admin) use ($feedback) {
            $admin->notifications()->create([
                'type' => 'high_priority_feedback',
                'title' => 'تغذية راجعة عالية الأولوية',
                'message' => "تم استلام تغذية راجعة {$feedback->priority}: {$feedback->title}",
                'data' => ['feedback_id' => $feedback->id]
            ]);
        });
    }

    private function notifyStatusChange(Feedback $feedback)
    {
        if ($feedback->status === 'resolved' || $feedback->status === 'acknowledged') {
            $feedback->user->notifications()->create([
                'type' => 'feedback_status_change',
                'title' => 'تحديث حالة التغذية الراجعة',
                'message' => "تم {$feedback->status} تغذيتك الراجعة",
                'data' => ['feedback_id' => $feedback->id]
            ]);
        }
    }
}
