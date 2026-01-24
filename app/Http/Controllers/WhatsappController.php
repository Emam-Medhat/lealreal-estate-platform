<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class WhatsappController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|string|max:20',
            'message' => 'required|string|max:1600',
            'type' => 'required|in:text,image,document,audio,video'
        ]);

        try {
            $response = $this->sendWhatsAppMessage($request->to, $request->message, $request->type);

            if ($response['success']) {
                Log::info('WhatsApp message sent', [
                    'from' => Auth::id(),
                    'to' => $request->to,
                    'type' => $request->type,
                    'message_id' => $response['message_id']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'WhatsApp message sent successfully',
                    'message_id' => $response['message_id']
                ]);
            } else {
                Log::error('WhatsApp message failed', [
                    'to' => $request->to,
                    'error' => $response['error']
                ]);

                return response()->json(['error' => 'Failed to send WhatsApp message'], 500);
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp sending failed', [
                'to' => $request->to,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to send WhatsApp message'], 500);
        }
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array',
            'recipients.*' => 'string|max:20',
            'message' => 'required|string|max:1600',
            'type' => 'required|in:text,image,document,audio,video'
        ]);

        $sent = 0;
        $failed = 0;

        foreach ($request->recipients as $recipient) {
            try {
                $response = $this->sendWhatsAppMessage($recipient, $request->message, $request->type);

                if ($response['success']) {
                    $sent++;
                } else {
                    $failed++;
                    Log::error('Bulk WhatsApp failed', [
                        'recipient' => $recipient,
                        'error' => $response['error']
                    ]);
                }

            } catch (\Exception $e) {
                $failed++;
                Log::error('Bulk WhatsApp failed', [
                    'recipient' => $recipient,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'sent' => $sent,
            'failed' => $failed,
            'total' => count($request->recipients)
        ]);
    }

    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1600',
            'type' => 'required|in:text,image,document,audio,video'
        ]);

        $user = User::findOrFail($request->user_id);

        if (!$user->phone) {
            return response()->json(['error' => 'User does not have a phone number'], 400);
        }

        $response = $this->sendWhatsAppMessage($user->phone, $request->message, $request->type);

        if ($response['success']) {
            // Create notification
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'WhatsApp Message',
                'message' => 'You received a WhatsApp message',
                'type' => 'whatsapp',
                'data' => json_encode(['message_id' => $response['message_id']])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp message sent successfully',
                'message_id' => $response['message_id']
            ]);
        }

        return response()->json(['error' => 'Failed to send WhatsApp message'], 500);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'notification_id' => 'required|exists:user_notifications,id'
        ]);

        $notification = UserNotification::findOrFail($request->notification_id);
        $user = User::findOrFail($notification->user_id);

        if (!$user->phone) {
            return response()->json(['error' => 'User does not have a phone number'], 400);
        }

        $message = $notification->title . "\n\n" . $notification->message;

        $response = $this->sendWhatsAppMessage($user->phone, $message, 'text');

        if ($response['success']) {
            $notification->update([
                'whatsapp_sent' => true,
                'whatsapp_sent_at' => now()
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Failed to send WhatsApp notification'], 500);
    }

    public function getTemplates()
    {
        // Get WhatsApp message templates from provider
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.whatsapp.token')
            ])->get(config('services.whatsapp.api_url') . '/message_templates');

            if ($response->successful()) {
                return response()->json(['templates' => $response->json()]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to fetch WhatsApp templates', [
                'error' => $e->getMessage()
            ]);
        }

        return response()->json(['templates' => []]);
    }

    public function getWebhookInfo()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.whatsapp.token')
            ])->get(config('services.whatsapp.api_url') . '/webhooks');

            if ($response->successful()) {
                return response()->json($response->json());
            }

        } catch (\Exception $e) {
            Log::error('Failed to fetch webhook info', [
                'error' => $e->getMessage()
            ]);
        }

        return response()->json(['error' => 'Failed to fetch webhook info'], 500);
    }

    public function webhook(Request $request)
    {
        Log::info('WhatsApp webhook received', $request->all());

        // Handle incoming WhatsApp messages
        if ($request->has('entry')) {
            foreach ($request->input('entry', []) as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    if (isset($change['value']['messages'])) {
                        foreach ($change['value']['messages'] as $message) {
                            $this->handleIncomingMessage($message);
                        }
                    }
                }
            }
        }

        return response()->json(['status' => 'received']);
    }

    private function handleIncomingMessage(array $message)
    {
        // Process incoming WhatsApp message
        $from = $message['from'] ?? '';
        $messageType = $message['type'] ?? '';
        $content = $this->extractMessageContent($message);

        Log::info('Incoming WhatsApp message', [
            'from' => $from,
            'type' => $messageType,
            'content' => $content
        ]);

        // Find user by phone number
        $user = User::where('phone', $from)->first();

        if ($user) {
            // Create notification for incoming message
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'New WhatsApp Message',
                'message' => 'You received a new WhatsApp message',
                'type' => 'whatsapp',
                'data' => json_encode([
                    'from' => $from,
                    'type' => $messageType,
                    'content' => $content
                ])
            ]);
        }
    }

    private function extractMessageContent(array $message): string
    {
        $type = $message['type'] ?? '';

        switch ($type) {
            case 'text':
                return $message['text']['body'] ?? '';
            case 'image':
                return '[Image: ' . ($message['image']['caption'] ?? 'No caption') . ']';
            case 'document':
                return '[Document: ' . ($message['document']['filename'] ?? 'Unknown file') . ']';
            case 'audio':
                return '[Audio message]';
            case 'video':
                return '[Video: ' . ($message['video']['caption'] ?? 'No caption') . ']';
            default:
                return '[Unsupported message type: ' . $type . ']';
        }
    }

    private function sendWhatsAppMessage(string $to, string $message, string $type): array
    {
        try {
            // Integration with WhatsApp Business API (Meta, Twilio, etc.)
            // This is a placeholder implementation
            
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => $type,
            ];

            switch ($type) {
                case 'text':
                    $payload['text'] = ['body' => $message];
                    break;
                case 'image':
                    $payload['image'] = ['link' => $message];
                    break;
                case 'document':
                    $payload['document'] = ['link' => $message];
                    break;
                case 'audio':
                    $payload['audio'] = ['link' => $message];
                    break;
                case 'video':
                    $payload['video'] = ['link' => $message];
                    break;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.whatsapp.token'),
                'Content-Type' => 'application/json'
            ])->post(config('services.whatsapp.api_url') . '/messages', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $response->json('messages.0.id')
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json('error.message', 'Unknown error')
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
