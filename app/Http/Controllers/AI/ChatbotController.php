<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Chatbot Controller
 *
 * POST /api/ai/chat           → send message, receive reply
 * DELETE /api/ai/chat/{sid}   → clear session
 * GET  /ai/chat               → render chatbot UI
 */
class ChatbotController extends Controller
{
    public function __construct(
        private readonly ChatbotService $chatbot
    ) {
    }

    public function index()
    {
        return view('ai.chatbot');
    }

    /**
     * POST /api/ai/chat
     *
     * Body:
     * {
     *   "session_id": "optional-uuid",
     *   "message":    "أريد شقة 3 غرف بالتجمع",
     *   "filters": {  // optional hints from current search state
     *     "city": "القاهرة",
     *     "listing_type": "sale"
     *   }
     * }
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => ['sometimes', 'nullable', 'string', 'max:64'],
            'message' => ['required', 'string', 'min:1', 'max:800'],
            'filters' => ['sometimes', 'nullable', 'array'],
        ]);

        // Generate session ID if not provided
        $sessionId = $validated['session_id'] ?? Str::uuid()->toString();

        try {
            $result = $this->chatbot->chat(
                $sessionId,
                $validated['message'],
                $validated['filters'] ?? []
            );

            // Fetch rich property data for the suggested IDs to render cards
            $suggestedProperties = [];
            if (!empty($result['suggested_ids'])) {
                $suggestedProperties = \App\Models\Property::whereIn('id', $result['suggested_ids'])
                    ->with(['pricing', 'location'])
                    ->get()
                    ->map(fn($p) => [
                        'id' => $p->id,
                        'title' => $p->title,
                        'price' => $p->pricing->price ?? 0,
                        'currency' => $p->pricing->currency ?? 'EGP',
                        'location' => ($p->location->city ?? '') . ' - ' . ($p->location->district ?? ''),
                        'image' => $p->main_image ?? asset('assets/images/placeholder.jpg'),
                        'url' => route('properties.show', $p->id),
                    ]);
            }

            return response()->json([
                'success' => $result['success'],
                'session_id' => $result['session_id'],
                'reply' => $result['reply'],
                'suggested_ids' => $result['suggested_ids'],
                'suggested_properties' => $suggestedProperties,
                'debug_error' => app()->environment('production') ? null : ($result['error_detail'] ?? null),
            ], $result['success'] ? 200 : 503);

        } catch (\InvalidArgumentException $e) {
            // Prompt injection attempt
            Log::warning('ChatbotController: Invalid input', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid input.',
                'debug_error' => app()->environment('production') ? null : $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('ChatbotController: Unhandled error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Temporary error. Please try again.',
                'debug_error' => app()->environment('production') ? null : $e->getMessage(),
            ], 503);
        }
    }

    /**
     * DELETE /api/ai/chat/{sessionId}
     */
    public function clearSession(string $sessionId): JsonResponse
    {
        $this->chatbot->clearSession($sessionId);

        return response()->json(['success' => true]);
    }
}
