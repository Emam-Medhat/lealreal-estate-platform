<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AiChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'session_id',
        'conversation_type',
        'messages',
        'intent_analysis',
        'sentiment_analysis',
        'entity_extraction',
        'context_data',
        'user_preferences',
        'conversation_summary',
        'satisfaction_score',
        'resolution_status',
        'ai_responses',
        'human_intervention',
        'escalation_level',
        'ai_model_version',
        'chat_metadata',
        'processing_time',
        'confidence_level',
        'status',
        'started_at',
        'ended_at',
        'duration',
        'message_count',
        'resolved_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'messages' => 'array',
        'intent_analysis' => 'array',
        'sentiment_analysis' => 'array',
        'entity_extraction' => 'array',
        'context_data' => 'array',
        'user_preferences' => 'array',
        'conversation_summary' => 'array',
        'ai_responses' => 'array',
        'chat_metadata' => 'array',
        'processing_time' => 'decimal:3',
        'confidence_level' => 'decimal:2',
        'satisfaction_score' => 'decimal:2',
        'human_intervention' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration' => 'integer',
        'message_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the property associated with the conversation.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user who initiated the conversation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the conversation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the conversation.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who resolved the conversation.
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope a query to only include active conversations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include resolved conversations.
     */
    public function scopeResolved($query)
    {
        return $query->where('resolution_status', 'resolved');
    }

    /**
     * Scope a query to only include conversations by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('conversation_type', $type);
    }

    /**
     * Scope a query to only include recent conversations.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to only include conversations requiring human intervention.
     */
    public function scopeRequiresIntervention($query)
    {
        return $query->where('human_intervention', true);
    }

    /**
     * Get conversation type label in Arabic.
     */
    public function getConversationTypeLabelAttribute(): string
    {
        $types = [
            'property_inquiry' => 'استفسار عن العقار',
            'general_info' => 'معلومات عامة',
            'booking_request' => 'طلب حجز',
            'complaint' => 'شكوى',
            'support' => 'دعم فني',
            'negotiation' => 'مفاوضات',
            'document_request' => 'طلب وثائق',
            'price_inquiry' => 'استفسار عن السعر',
        ];

        return $types[$this->conversation_type] ?? 'غير معروف';
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'active' => 'نشط',
            'paused' => 'متوقف',
            'ended' => 'منتهي',
            'transferred' => 'منقول',
            'escalated' => 'مرفع',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get resolution status label in Arabic.
     */
    public function getResolutionStatusLabelAttribute(): string
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'تم الحل',
            'escalated' => 'تم الرفع',
            'unresolved' => 'غير محلول',
            'cancelled' => 'ملغي',
        ];

        return $statuses[$this->resolution_status] ?? 'غير معروف';
    }

    /**
     * Get sentiment level text.
     */
    public function getSentimentLevelAttribute(): string
    {
        $sentiment = $this->sentiment_analysis ?? [];
        
        if (isset($sentiment['overall_sentiment'])) {
            $score = $sentiment['overall_sentiment'];
            
            if ($score >= 0.6) return 'إيجابي';
            if ($score >= 0.2) return 'محايد';
            return 'سلبي';
        }
        
        return 'غير محدد';
    }

    /**
     * Get confidence level text.
     */
    public function getConfidenceLevelTextAttribute(): string
    {
        if ($this->confidence_level >= 0.9) return 'عالي جداً';
        if ($this->confidence_level >= 0.8) return 'عالي';
        if ($this->confidence_level >= 0.7) return 'متوسط';
        if ($this->confidence_level >= 0.6) return 'منخفض';
        return 'منخفض جداً';
    }

    /**
     * Get satisfaction level text.
     */
    public function getSatisfactionLevelAttribute(): string
    {
        if ($this->satisfaction_score >= 4.5) return 'راضٍ جداً';
        if ($this->satisfaction_score >= 3.5) return 'راضٍ';
        if ($this->satisfaction_score >= 2.5) return 'محايد';
        if ($this->satisfaction_score >= 1.5) return 'غير راضٍ';
        return 'غير راضٍ جداً';
    }

    /**
     * Get escalation level text.
     */
    public function getEscalationLevelTextAttribute(): string
    {
        $levels = [
            0 => 'لا يوجد',
            1 => 'منخفض',
            2 => 'متوسط',
            3 => 'مرتفع',
            4 => 'حرج',
        ];

        return $levels[$this->escalation_level] ?? 'غير معروف';
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration < 60) {
            return $this->duration . ' ثانية';
        } elseif ($this->duration < 3600) {
            return round($this->duration / 60) . ' دقيقة';
        } else {
            return round($this->duration / 3600, 1) . ' ساعة';
        }
    }

    /**
     * Get last message.
     */
    public function getLastMessageAttribute(): ?array
    {
        $messages = $this->messages ?? [];
        return !empty($messages) ? end($messages) : null;
    }

    /**
     * Get user messages count.
     */
    public function getUserMessagesCountAttribute(): int
    {
        $messages = $this->messages ?? [];
        return count(array_filter($messages, fn($msg) => ($msg['sender'] ?? 'user') === 'user'));
    }

    /**
     * Get AI responses count.
     */
    public function getAiResponsesCountAttribute(): int
    {
        $messages = $this->messages ?? [];
        return count(array_filter($messages, fn($msg) => ($msg['sender'] ?? 'ai') === 'ai'));
    }

    /**
     * Check if conversation is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && is_null($this->ended_at);
    }

    /**
     * Check if conversation is resolved.
     */
    public function isResolved(): bool
    {
        return $this->resolution_status === 'resolved';
    }

    /**
     * Check if conversation needs escalation.
     */
    public function needsEscalation(): bool
    {
        return $this->escalation_level >= 3 || $this->human_intervention;
    }

    /**
     * Get primary intent.
     */
    public function getPrimaryIntentAttribute(): ?string
    {
        $intent = $this->intent_analysis ?? [];
        
        if (isset($intent['primary_intent'])) {
            $intents = [
                'property_info' => 'معلومات العقار',
                'price_inquiry' => 'استفسار عن السعر',
                'booking' => 'حجز موعد',
                'complaint' => 'شكوى',
                'support' => 'دعم فني',
                'general_chat' => 'دردشة عامة',
            ];
            
            return $intents[$intent['primary_intent']] ?? 'غير معروف';
        }
        
        return null;
    }

    /**
     * Get extracted entities.
     */
    public function getExtractedEntitiesAttribute(): array
    {
        $entities = $this->entity_extraction ?? [];
        return $entities['entities'] ?? [];
    }

    /**
     * Add message to conversation.
     */
    public function addMessage(array $message): bool
    {
        $messages = $this->messages ?? [];
        $messages[] = array_merge($message, [
            'timestamp' => now()->toISOString(),
            'message_id' => uniqid('msg_'),
        ]);
        
        $this->messages = $messages;
        $this->message_count = count($messages);
        
        return $this->save();
    }

    /**
     * End conversation.
     */
    public function endConversation(): bool
    {
        $this->status = 'ended';
        $this->ended_at = now();
        
        if ($this->started_at) {
            $this->duration = $this->started_at->diffInSeconds($this->ended_at);
        }
        
        return $this->save();
    }

    /**
     * Escalate conversation.
     */
    public function escalate(int $level): bool
    {
        $this->escalation_level = $level;
        $this->human_intervention = true;
        $this->status = 'escalated';
        
        return $this->save();
    }

    /**
     * Resolve conversation.
     */
    public function resolve(int $resolvedBy = null): bool
    {
        $this->resolution_status = 'resolved';
        $this->resolved_by = $resolvedBy;
        
        if ($this->isActive()) {
            $this->endConversation();
        }
        
        return $this->save();
    }

    /**
     * Create a new AI chat conversation.
     */
    public static function startConversation(array $data): self
    {
        $conversationType = $data['conversation_type'] ?? 'general_info';
        $sessionId = $data['session_id'] ?? uniqid('chat_');
        
        // Initialize intent analysis
        $intentAnalysis = [
            'primary_intent' => 'general_chat',
            'confidence' => 0.8,
            'secondary_intents' => [],
            'intent_history' => [],
        ];
        
        // Initialize sentiment analysis
        $sentimentAnalysis = [
            'overall_sentiment' => 0.5,
            'sentiment_trend' => 'neutral',
            'emotion_detection' => [],
            'sentiment_history' => [],
        ];
        
        // Initialize entity extraction
        $entityExtraction = [
            'entities' => [],
            'entity_types' => [],
            'confidence_scores' => [],
        ];
        
        // Initialize context data
        $contextData = [
            'property_context' => [],
            'user_history' => [],
            'session_variables' => [],
            'conversation_state' => 'initial',
        ];
        
        // Initialize user preferences
        $userPreferences = [
            'language' => 'ar',
            'response_style' => 'professional',
            'detail_level' => 'medium',
            'communication_channel' => 'chat',
        ];

        return static::create([
            'property_id' => $data['property_id'] ?? null,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'session_id' => $sessionId,
            'conversation_type' => $conversationType,
            'messages' => [],
            'intent_analysis' => $intentAnalysis,
            'sentiment_analysis' => $sentimentAnalysis,
            'entity_extraction' => $entityExtraction,
            'context_data' => $contextData,
            'user_preferences' => $userPreferences,
            'conversation_summary' => [],
            'satisfaction_score' => null,
            'resolution_status' => 'pending',
            'ai_responses' => [],
            'human_intervention' => false,
            'escalation_level' => 0,
            'ai_model_version' => '8.3.1',
            'chat_metadata' => [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'platform' => 'web',
                'browser_language' => request()->getPreferredLanguage(),
                'timezone' => config('app.timezone'),
                'start_time' => now()->toDateTimeString(),
            ],
            'processing_time' => 0.0,
            'confidence_level' => 0.8,
            'status' => 'active',
            'started_at' => now(),
            'ended_at' => null,
            'duration' => 0,
            'message_count' => 0,
            'resolved_by' => null,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Generate AI response.
     */
    public function generateAiResponse(string $userMessage): array
    {
        // Simulate AI response generation
        $startTime = microtime(true);
        
        // Analyze user message
        $this->analyzeUserMessage($userMessage);
        
        // Generate appropriate response
        $responseTemplates = [
            'property_info' => 'يمكنني مساعدتك في الحصول على معلومات مفصلة عن هذا العقار. ما هي المعلومات التي تهمك تحديداً؟',
            'price_inquiry' => 'سأتحقق لك من أحدث معلومات الأسعار والعروض المتاحة. هل تود معرفة سعر معين أم تبحث عن عروض خاصة؟',
            'booking' => 'بالتأكيد! يمكنني حجز موعد لك لزيارة العقار. متى يناسبك وقت الزيارة؟',
            'complaint' => 'أعتذر عن أي إزعاج. سأقوم بتسجيل شكواك ومساعدتك في حل المشكلة بأسرع وقت ممكن.',
            'support' => 'أنا هنا لمساعدتك. يرجى وصف المشكلة التي تواجهها وسأقدم لك الحل المناسب.',
            'general_chat' => 'أهلاً بك! كيف يمكنني مساعدتك اليوم؟',
        ];
        
        $primaryIntent = $this->primary_intent ?? 'general_chat';
        $responseText = $responseTemplates[$primaryIntent] ?? $responseTemplates['general_chat'];
        
        // Add contextual information if available
        if ($this->property_id) {
            $responseText .= ' العقار الذي تستفسر عنه متوفر حالياً للمعاينة.';
        }
        
        $response = [
            'text' => $responseText,
            'type' => 'text',
            'sender' => 'ai',
            'confidence' => rand(75, 95) / 100,
            'response_time' => microtime(true) - $startTime,
            'intent_matched' => $primaryIntent,
            'suggestions' => $this->generateSuggestions(),
        ];
        
        // Update processing time
        $this->processing_time += $response['response_time'];
        
        return $response;
    }

    /**
     * Analyze user message.
     */
    private function analyzeUserMessage(string $message): void
    {
        // Simulate intent analysis
        $intents = [
            'property_info' => ['معلومات', 'تفاصيل', 'مواصفات', 'مساحة', 'غرف'],
            'price_inquiry' => ['سعر', 'تكلفة', 'قيمة', 'سوم', 'عرض'],
            'booking' => ['حجز', 'موعد', 'زيارة', 'معاينة', 'لقاء'],
            'complaint' => ['شكوى', 'مشكلة', 'إزعاج', 'سيء', 'غير راضي'],
            'support' => ['مساعدة', 'دعم', 'حل', 'مشكلة فنية', 'خطأ'],
        ];
        
        $matchedIntent = 'general_chat';
        $maxMatches = 0;
        
        foreach ($intents as $intent => $keywords) {
            $matches = 0;
            foreach ($keywords as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    $matches++;
                }
            }
            
            if ($matches > $maxMatches) {
                $maxMatches = $matches;
                $matchedIntent = $intent;
            }
        }
        
        // Update intent analysis
        $this->intent_analysis['primary_intent'] = $matchedIntent;
        $this->intent_analysis['confidence'] = min(1.0, 0.5 + ($maxMatches * 0.2));
        
        // Simulate sentiment analysis
        $positiveWords = ['ممتاز', 'رائع', 'جيد', 'سعيد', 'مبسوط'];
        $negativeWords = ['سيء', 'سيء جداً', 'غاضب', 'غير راضي', 'مزعج'];
        
        $sentiment = 0.5; // Neutral
        foreach ($positiveWords as $word) {
            if (strpos($message, $word) !== false) {
                $sentiment += 0.2;
            }
        }
        foreach ($negativeWords as $word) {
            if (strpos($message, $word) !== false) {
                $sentiment -= 0.2;
            }
        }
        
        $this->sentiment_analysis['overall_sentiment'] = max(-1, min(1, $sentiment));
    }

    /**
     * Generate response suggestions.
     */
    private function generateSuggestions(): array
    {
        $suggestions = [
            'property_info' => ['ما هي مساحة العقار؟', 'كم عدد الغرف؟', 'هل يوجد موقف سيارات؟'],
            'price_inquiry' => ['ما هو السعر الحالي؟', 'هل يوجد خصم؟', 'ما هي طرق الدفع؟'],
            'booking' => ['حجز موعد اليوم', 'حجز موعد غداً', 'اختر وقتاً مناسباً'],
            'complaint' => ['تسجيل شكوى جديدة', 'متابعة شكوى سابقة', 'التحدث مع موظف'],
            'support' => ['مشكلة في الحجز', 'مشكلة في الدفع', 'مشكلة فنية'],
            'general_chat' => ['استفسار عن عقار', 'معلومات عامة', 'التحدث مع موظف'],
        ];
        
        $primaryIntent = $this->primary_intent ?? 'general_chat';
        return $suggestions[$primaryIntent] ?? $suggestions['general_chat'];
    }

    /**
     * Get conversation summary for dashboard.
     */
    public function getDashboardSummary(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'property_id' => $this->property_id,
            'session_id' => $this->session_id,
            'type' => $this->conversation_type_label,
            'status' => $this->status_label,
            'resolution_status' => $this->resolution_status_label,
            'sentiment' => $this->sentiment_level,
            'message_count' => $this->message_count,
            'duration' => $this->formatted_duration,
            'needs_intervention' => $this->human_intervention,
            'escalation_level' => $this->escalation_level_text,
            'is_active' => $this->isActive(),
            'is_resolved' => $this->isResolved(),
            'started_at' => $this->started_at?->format('Y-m-d H:i'),
            'last_message' => $this->last_message['text'] ?? null,
        ];
    }

    /**
     * Get detailed conversation report.
     */
    public function getDetailedReport(): array
    {
        return [
            'basic_info' => [
                'id' => $this->id,
                'session_id' => $this->session_id,
                'type' => $this->conversation_type_label,
                'status' => $this->status_label,
                'resolution_status' => $this->resolution_status_label,
                'started_at' => $this->started_at?->format('Y-m-d H:i:s'),
                'ended_at' => $this->ended_at?->format('Y-m-d H:i:s'),
                'duration' => $this->formatted_duration,
            ],
            'participants' => [
                'user_id' => $this->user_id,
                'property_id' => $this->property_id,
                'resolved_by' => $this->resolved_by,
            ],
            'analytics' => [
                'message_count' => $this->message_count,
                'user_messages' => $this->user_messages_count,
                'ai_responses' => $this->ai_responses_count,
                'processing_time' => $this->processing_time . 's',
                'confidence_level' => $this->confidence_level_text,
            ],
            'ai_analysis' => [
                'intent_analysis' => $this->intent_analysis,
                'primary_intent' => $this->primary_intent,
                'sentiment_analysis' => $this->sentiment_analysis,
                'sentiment_level' => $this->sentiment_level,
                'entity_extraction' => $this->entity_extraction,
                'extracted_entities' => $this->extracted_entities,
            ],
            'context' => [
                'context_data' => $this->context_data,
                'user_preferences' => $this->user_preferences,
                'conversation_summary' => $this->conversation_summary,
            ],
            'performance' => [
                'satisfaction_score' => $this->satisfaction_score,
                'satisfaction_level' => $this->satisfaction_level,
                'human_intervention' => $this->human_intervention,
                'escalation_level' => $this->escalation_level_text,
            ],
            'messages' => $this->messages,
        ];
    }
}
