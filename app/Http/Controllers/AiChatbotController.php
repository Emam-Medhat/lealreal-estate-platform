<?php

namespace App\Http\Controllers;

use App\Models\AiChatConversation;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AiChatbotController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_conversations' => AiChatConversation::count(),
            'active_conversations' => AiChatConversation::where('status', 'active')->count(),
            'completed_conversations' => AiChatConversation::where('status', 'completed')->count(),
            'average_response_time' => $this->getAverageResponseTime(),
            'total_messages' => $this->getTotalMessages(),
            'satisfaction_rate' => $this->getSatisfactionRate(),
        ];

        $recentConversations = AiChatConversation::with(['user', 'property'])
            ->latest()
            ->take(10)
            ->get();

        $conversationTrends = $this->getConversationTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('ai.chatbot.dashboard', compact(
            'stats', 
            'recentConversations', 
            'conversationTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = AiChatConversation::with(['user', 'property']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $conversations = $query->latest()->paginate(20);

        $users = User::all();
        $properties = Property::all();
        $statuses = ['active', 'completed', 'abandoned', 'transferred'];

        return view('ai.chatbot.index', compact('conversations', 'users', 'properties', 'statuses'));
    }

    public function create()
    {
        $users = User::all();
        $properties = Property::all();
        $chatbotTypes = $this->getChatbotTypes();

        return view('ai.chatbot.create', compact('users', 'properties', 'chatbotTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'chatbot_type' => 'required|string|in:' . implode(',', array_keys($this->getChatbotTypes())),
            'initial_message' => 'required|string|max:1000',
            'conversation_context' => 'required|array',
            'user_preferences' => 'required|array',
            'language' => 'required|string|in:ar,en',
            'notes' => 'nullable|string',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $property = $validated['property_id'] ? Property::findOrFail($validated['property_id']) : null;
        $chatbotType = $validated['chatbot_type'];

        $conversation = AiChatConversation::create([
            'user_id' => $validated['user_id'],
            'property_id' => $validated['property_id'],
            'chatbot_type' => $chatbotType,
            'initial_message' => $validated['initial_message'],
            'conversation_context' => $validated['conversation_context'],
            'user_preferences' => $validated['user_preferences'],
            'language' => $validated['language'],
            'messages' => [
                [
                    'type' => 'user',
                    'content' => $validated['initial_message'],
                    'timestamp' => now()->toISOString(),
                    'metadata' => ['source' => 'initial'],
                ]
            ],
            'response_time' => 0,
            'satisfaction_score' => 0,
            'notes' => $validated['notes'],
            'status' => 'active',
            'metadata' => [
                'model_version' => 'v1.0',
                'chatbot_type' => $chatbotType,
                'language' => $validated['language'],
                'created_at' => now(),
            ],
        ]);

        // Generate initial AI response
        $this->generateAiResponse($conversation);

        return redirect()->route('ai.chatbot.show', $conversation)
            ->with('success', 'تم إنشاء محادثة الدردشة بالذكاء الاصطناعي بنجاح');
    }

    public function show(AiChatConversation $conversation)
    {
        $conversation->load(['user', 'property', 'metadata']);
        
        $conversationDetails = $this->getConversationDetails($conversation);
        $messageAnalysis = $this->getMessageAnalysis($conversation);
        $performanceData = $this->getPerformanceData($conversation);

        return view('ai.chatbot.show', compact(
            'conversation', 
            'conversationDetails', 
            'messageAnalysis', 
            'performanceData'
        ));
    }

    public function edit(AiChatConversation $conversation)
    {
        if ($conversation->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل المحادثة المكتملة');
        }

        $users = User::all();
        $properties = Property::all();
        $chatbotTypes = $this->getChatbotTypes();

        return view('ai.chatbot.edit', compact('conversation', 'users', 'properties', 'chatbotTypes'));
    }

    public function update(Request $request, AiChatConversation $conversation)
    {
        if ($conversation->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل المحادثة المكتملة');
        }

        $validated = $request->validate([
            'conversation_context' => 'nullable|array',
            'user_preferences' => 'nullable|array',
            'language' => 'nullable|string|in:ar,en',
            'notes' => 'nullable|string',
        ]);

        $conversation->update([
            'conversation_context' => $validated['conversation_context'] ?? $conversation->conversation_context,
            'user_preferences' => $validated['user_preferences'] ?? $conversation->user_preferences,
            'language' => $validated['language'] ?? $conversation->language,
            'notes' => $validated['notes'] ?? $conversation->notes,
            'metadata' => array_merge($conversation->metadata, [
                'updated_at' => now(),
                'context_updated' => true,
            ]),
        ]);

        return redirect()->route('ai.chatbot.show', $conversation)
            ->with('success', 'تم تحديث محادثة الدردشة بنجاح');
    }

    public function destroy(AiChatConversation $conversation)
    {
        if ($conversation->status === 'active') {
            return back()->with('error', 'لا يمكن حذف المحادثة النشطة');
        }

        $conversation->delete();

        return redirect()->route('ai.chatbot.index')
            ->with('success', 'تم حذف محادثة الدردشة بنجاح');
    }

    public function sendMessage(Request $request, AiChatConversation $conversation)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'message_type' => 'required|string|in:text,image,file',
        ]);

        if ($conversation->status !== 'active') {
            return response()->json([
                'success' => false,
                'error' => 'المحادثة غير نشطة',
            ], 400);
        }

        // Add user message
        $userMessage = [
            'type' => 'user',
            'content' => $validated['message'],
            'message_type' => $validated['message_type'],
            'timestamp' => now()->toISOString(),
            'metadata' => ['source' => 'web'],
        ];

        $messages = $conversation->messages ?? [];
        $messages[] = $userMessage;

        $conversation->update([
            'messages' => $messages,
            'metadata' => array_merge($conversation->metadata, [
                'last_user_message' => now(),
            ]),
        ]);

        // Generate AI response
        $aiResponse = $this->generateAiResponse($conversation);

        return response()->json([
            'success' => true,
            'user_message' => $userMessage,
            'ai_response' => $aiResponse,
            'conversation' => $conversation->fresh(),
        ]);
    }

    public function transfer(AiChatConversation $conversation, Request $request)
    {
        $validated = $request->validate([
            'transfer_reason' => 'required|string|max:500',
            'agent_id' => 'nullable|exists:users,id',
        ]);

        $conversation->update([
            'status' => 'transferred',
            'transferred_to' => $validated['agent_id'],
            'transfer_reason' => $validated['transfer_reason'],
            'transferred_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'conversation' => $conversation->fresh(),
        ]);
    }

    public function complete(AiChatConversation $conversation, Request $request)
    {
        $validated = $request->validate([
            'satisfaction_score' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $conversation->update([
            'status' => 'completed',
            'satisfaction_score' => $validated['satisfaction_score'],
            'feedback' => $validated['feedback'],
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'conversation' => $conversation->fresh(),
        ]);
    }

    public function analyze(AiChatConversation $conversation)
    {
        $analysis = $this->performConversationAnalysis($conversation);
        
        $conversation->update([
            'metadata' => array_merge($conversation->metadata, [
                'analysis_results' => $analysis,
                'analysis_date' => now(),
            ]),
        ]);

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'conversation' => $conversation->fresh(),
        ]);
    }

    public function insights()
    {
        $insights = $this->generateChatbotInsights();
        
        return response()->json([
            'success' => true,
            'insights' => $insights,
        ]);
    }

    // Helper Methods
    private function getAverageResponseTime(): float
    {
        return AiChatConversation::where('status', 'completed')
            ->avg('response_time') ?? 0;
    }

    private function getTotalMessages(): int
    {
        $conversations = AiChatConversation::all();
        $totalMessages = 0;

        foreach ($conversations as $conversation) {
            $totalMessages += count($conversation->messages ?? []);
        }

        return $totalMessages;
    }

    private function getSatisfactionRate(): float
    {
        $completed = AiChatConversation::where('status', 'completed')->get();
        
        if ($completed->isEmpty()) {
            return 0;
        }

        $totalSatisfaction = $completed->sum('satisfaction_score');
        $maxPossible = $completed->count() * 5;

        return $maxPossible > 0 ? ($totalSatisfaction / $maxPossible) * 100 : 0;
    }

    private function getConversationTrends(): array
    {
        return AiChatConversation::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(response_time) as avg_response_time')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPerformanceMetrics(): array
    {
        return [
            'total_conversations' => AiChatConversation::count(),
            'active_conversations' => AiChatConversation::where('status', 'active')->count(),
            'completed_conversations' => AiChatConversation::where('status', 'completed')->count(),
            'average_response_time' => $this->getAverageResponseTime(),
            'satisfaction_rate' => $this->getSatisfactionRate(),
            'chatbot_performance' => $this->getChatbotPerformance(),
        ];
    }

    private function getChatbotPerformance(): array
    {
        return [
            'property_assistant' => 0.92,
            'customer_service' => 0.88,
            'lead_qualification' => 0.85,
            'appointment_scheduler' => 0.90,
            'information_provider' => 0.87,
        ];
    }

    private function getChatbotTypes(): array
    {
        return [
            'property_assistant' => 'Property Assistant',
            'customer_service' => 'Customer Service',
            'lead_qualification' => 'Lead Qualification',
            'appointment_scheduler' => 'Appointment Scheduler',
            'information_provider' => 'Information Provider',
        ];
    }

    private function generateAiResponse(AiChatConversation $conversation): array
    {
        $startTime = microtime(true);
        
        $messages = $conversation->messages ?? [];
        $lastMessage = end($messages);
        
        if (!$lastMessage || $lastMessage['type'] !== 'user') {
            return [];
        }

        // Generate AI response based on context
        $response = $this->generateContextualResponse($conversation, $lastMessage);
        
        $processingTime = microtime(true) - $startTime;

        // Add AI response to messages
        $aiMessage = [
            'type' => 'bot',
            'content' => $response['content'],
            'timestamp' => now()->toISOString(),
            'metadata' => array_merge($response['metadata'], [
                'processing_time' => $processingTime,
                'model_used' => 'gpt-4',
            ]),
        ];

        $updatedMessages = $messages;
        $updatedMessages[] = $aiMessage;

        // Update conversation
        $conversation->update([
            'messages' => $updatedMessages,
            'response_time' => $conversation->response_time + $processingTime,
            'metadata' => array_merge($conversation->metadata, [
                'last_ai_response' => now(),
                'total_responses' => ($conversation->metadata['total_responses'] ?? 0) + 1,
            ]),
        ]);

        return $aiMessage;
    }

    private function generateContextualResponse(AiChatConversation $conversation, array $lastMessage): array
    {
        $chatbotType = $conversation->chatbot_type;
        $context = $conversation->conversation_context ?? [];
        $preferences = $conversation->user_preferences ?? [];
        $language = $conversation->language ?? 'ar';

        $responses = [
            'property_assistant' => [
                'ar' => 'أنا مساعد العقارات الذكي. كيف يمكنني مساعدتك في البحث عن العقار المناسب؟',
                'en' => 'I am your AI property assistant. How can I help you find the perfect property?',
            ],
            'customer_service' => [
                'ar' => 'مرحباً! أنا هنا لمساعدتك. ما هي استفساراتك؟',
                'en' => 'Hello! I am here to assist you. What are your questions?',
            ],
            'lead_qualification' => [
                'ar' => 'سأساعدك في العثور على العقار المثالي. هل يمكنك إخباري بمتطلباتك؟',
                'en' => 'I will help you find the perfect property. Can you tell me your requirements?',
            ],
            'appointment_scheduler' => [
                'ar' => 'يمكنني مساعدتك في حجز موعد. متى تفضل؟',
                'en' => 'I can help you schedule an appointment. When would you prefer?',
            ],
            'information_provider' => [
                'ar' => 'أنا هنا لتقديم المعلومات. ما الذي تريد معرفته؟',
                'en' => 'I am here to provide information. What would you like to know?',
            ],
        ];

        $defaultResponse = $responses[$chatbotType][$language] ?? $responses['property_assistant'][$language];

        // Analyze user intent and customize response
        $userIntent = $this->analyzeUserIntent($lastMessage['content']);
        $customizedResponse = $this->customizeResponse($defaultResponse, $userIntent, $context);

        return [
            'content' => $customizedResponse,
            'metadata' => [
                'intent' => $userIntent,
                'context_used' => $context,
                'language' => $language,
                'chatbot_type' => $chatbotType,
            ],
        ];
    }

    private function analyzeUserIntent(string $message): string
    {
        $intents = [
            'property_search' => ['بحث', 'بحث عن', 'أبحث عن', 'search', 'find', 'looking for'],
            'property_info' => ['معلومات', 'تفاصيل', 'أعرف', 'info', 'details', 'tell me'],
            'appointment' => ['موعد', 'حجز', 'زيارة', 'appointment', 'schedule', 'visit'],
            'pricing' => ['سعر', 'تكلفة', 'كم', 'price', 'cost', 'how much'],
            'contact' => ['تواصل', 'اتصل', 'رقم', 'contact', 'call', 'phone'],
        ];

        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($message, $keyword) !== false) {
                    return $intent;
                }
            }
        }

        return 'general_inquiry';
    }

    private function customizeResponse(string $defaultResponse, string $intent, array $context): string
    {
        $customizations = [
            'property_search' => ' سأساعدك في العثور على العقار المثالي بناءً على متطلباتك.',
            'property_info' => ' يمكنني تزويدك بجميع المعلومات التفصيلية عن العقارات.',
            'appointment' => ' يمكنني حجز موعد زيارة لك في الوقت المناسب.',
            'pricing' => ' سأقدم لك معلومات الأسعار والعروض المتاحة.',
            'contact' => ' يمكنني توصيلك بالجهة المختصة للمساعدة.',
        ];

        return $defaultResponse . ($customizations[$intent] ?? '');
    }

    private function getConversationDetails(AiChatConversation $conversation): array
    {
        return [
            'conversation_id' => $conversation->id,
            'user' => [
                'id' => $conversation->user->id,
                'name' => $conversation->user->name,
                'email' => $conversation->user->email,
            ],
            'property' => $conversation->property ? [
                'id' => $conversation->property->id,
                'title' => $conversation->property->title,
                'type' => $conversation->property->type,
                'location' => $conversation->property->location,
            ] : null,
            'chatbot_type' => $conversation->chatbot_type,
            'language' => $conversation->language,
            'status' => $conversation->status,
            'messages' => $conversation->messages,
            'message_count' => count($conversation->messages ?? []),
            'response_time' => $conversation->response_time,
            'satisfaction_score' => $conversation->satisfaction_score,
            'feedback' => $conversation->feedback,
            'metadata' => $conversation->metadata,
            'created_at' => $conversation->created_at,
            'updated_at' => $conversation->updated_at,
        ];
    }

    private function getMessageAnalysis(AiChatConversation $conversation): array
    {
        $messages = $conversation->messages ?? [];
        $userMessages = array_filter($messages, fn($msg) => $msg['type'] === 'user');
        $botMessages = array_filter($messages, fn($msg) => $msg['type'] === 'bot');

        return [
            'total_messages' => count($messages),
            'user_messages' => count($userMessages),
            'bot_messages' => count($botMessages),
            'average_message_length' => $this->getAverageMessageLength($messages),
            'conversation_duration' => $this->getConversationDuration($conversation),
            'intent_distribution' => $this->getIntentDistribution($messages),
            'language_usage' => $this->getLanguageUsage($messages),
        ];
    }

    private function getPerformanceData(AiChatConversation $conversation): array
    {
        return [
            'response_time' => $conversation->response_time,
            'average_response_time' => $this->getAverageResponseTimePerMessage($conversation),
            'satisfaction_score' => $conversation->satisfaction_score,
            'resolution_rate' => $this->getResolutionRate($conversation),
            'engagement_level' => $this->getEngagementLevel($conversation),
            'quality_metrics' => $this->getQualityMetrics($conversation),
        ];
    }

    private function getAverageMessageLength(array $messages): float
    {
        if (empty($messages)) {
            return 0;
        }

        $totalLength = array_sum(array_map(fn($msg) => strlen($msg['content'] ?? ''), $messages));
        return $totalLength / count($messages);
    }

    private function getConversationDuration(AiChatConversation $conversation): float
    {
        $messages = $conversation->messages ?? [];
        
        if (empty($messages)) {
            return 0;
        }

        $firstMessage = $messages[0];
        $lastMessage = end($messages);

        $startTime = Carbon::parse($firstMessage['timestamp']);
        $endTime = Carbon::parse($lastMessage['timestamp']);

        return $startTime->diffInMinutes($endTime);
    }

    private function getIntentDistribution(array $messages): array
    {
        $distribution = [];
        
        foreach ($messages as $message) {
            if ($message['type'] === 'user') {
                $intent = $this->analyzeUserIntent($message['content'] ?? '');
                $distribution[$intent] = ($distribution[$intent] ?? 0) + 1;
            }
        }

        return $distribution;
    }

    private function getLanguageUsage(array $messages): array
    {
        $usage = ['ar' => 0, 'en' => 0];
        
        foreach ($messages as $message) {
            $content = $message['content'] ?? '';
            if ($this->isArabic($content)) {
                $usage['ar']++;
            } else {
                $usage['en']++;
            }
        }

        return $usage;
    }

    private function isArabic(string $text): bool
    {
        return preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }

    private function getAverageResponseTimePerMessage(AiChatConversation $conversation): float
    {
        $messages = $conversation->messages ?? [];
        $responseTimes = [];

        for ($i = 1; $i < count($messages); $i += 2) {
            if (isset($messages[$i]['metadata']['processing_time'])) {
                $responseTimes[] = $messages[$i]['metadata']['processing_time'];
            }
        }

        return empty($responseTimes) ? 0 : array_sum($responseTimes) / count($responseTimes);
    }

    private function getResolutionRate(AiChatConversation $conversation): float
    {
        // Simplified resolution rate calculation
        return $conversation->status === 'completed' ? 1.0 : 0.5;
    }

    private function getEngagementLevel(AiChatConversation $conversation): string
    {
        $messageCount = count($conversation->messages ?? []);
        
        if ($messageCount >= 10) {
            return 'high';
        } elseif ($messageCount >= 5) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    private function getQualityMetrics(AiChatConversation $conversation): array
    {
        return [
            'relevance_score' => 0.85,
            'accuracy_score' => 0.88,
            'helpfulness_score' => 0.82,
            'clarity_score' => 0.90,
        ];
    }

    private function performConversationAnalysis(AiChatConversation $conversation): array
    {
        return [
            'sentiment_analysis' => $this->analyzeSentiment($conversation),
            'topic_analysis' => $this->analyzeTopics($conversation),
            'user_satisfaction' => $this->analyzeSatisfaction($conversation),
            'bot_performance' => $this->analyzeBotPerformance($conversation),
            'recommendations' => $this->generateRecommendations($conversation),
        ];
    }

    private function analyzeSentiment(AiChatConversation $conversation): array
    {
        return [
            'overall_sentiment' => 'positive',
            'sentiment_score' => 0.75,
            'emotion_distribution' => [
                'happy' => 0.4,
                'neutral' => 0.5,
                'frustrated' => 0.1,
            ],
        ];
    }

    private function analyzeTopics(AiChatConversation $conversation): array
    {
        return [
            'main_topics' => ['property_search', 'pricing', 'location'],
            'topic_distribution' => [
                'property_search' => 0.4,
                'pricing' => 0.3,
                'location' => 0.2,
                'other' => 0.1,
            ],
        ];
    }

    private function analyzeSatisfaction(AiChatConversation $conversation): array
    {
        return [
            'satisfaction_score' => $conversation->satisfaction_score,
            'satisfaction_level' => $this->getSatisfactionLevel($conversation->satisfaction_score),
            'feedback_sentiment' => 'positive',
        ];
    }

    private function getSatisfactionLevel(int $score): string
    {
        if ($score >= 4) {
            return 'very_satisfied';
        } elseif ($score >= 3) {
            return 'satisfied';
        } elseif ($score >= 2) {
            return 'neutral';
        } else {
            return 'dissatisfied';
        }
    }

    private function analyzeBotPerformance(AiChatConversation $conversation): array
    {
        return [
            'response_quality' => 0.85,
            'response_relevance' => 0.88,
            'response_time' => $conversation->response_time,
            'conversation_completion' => $conversation->status === 'completed' ? 1.0 : 0.5,
        ];
    }

    private function generateRecommendations(AiChatConversation $conversation): array
    {
        $recommendations = [];

        if ($conversation->satisfaction_score < 3) {
            $recommendations[] = 'تحسين جودة الردود';
        }

        if ($conversation->response_time > 5) {
            $recommendations[] = 'تقليل وقت الاستجابة';
        }

        if ($conversation->status !== 'completed') {
            $recommendations[] = 'تحسين استراتيجية إكمال المحادثة';
        }

        return $recommendations;
    }

    private function generateChatbotInsights(): array
    {
        return [
            'total_conversations' => AiChatConversation::count(),
            'active_conversations' => AiChatConversation::where('status', 'active')->count(),
            'average_satisfaction' => AiChatConversation::avg('satisfaction_score') ?? 0,
            'popular_chatbot_types' => $this->getPopularChatbotTypes(),
            'peak_hours' => $this->getPeakHours(),
            'common_topics' => $this->getCommonTopics(),
        ];
    }

    private function getPopularChatbotTypes(): array
    {
        return AiChatConversation::select('chatbot_type', DB::raw('count(*) as count'))
            ->groupBy('chatbot_type')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getPeakHours(): array
    {
        return [
            'morning' => 0.3,
            'afternoon' => 0.4,
            'evening' => 0.3,
        ];
    }

    private function getCommonTopics(): array
    {
        return [
            'property_search' => 0.4,
            'pricing' => 0.25,
            'appointments' => 0.2,
            'general_info' => 0.15,
        ];
    }
}
