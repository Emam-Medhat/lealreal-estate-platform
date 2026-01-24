<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TelegramBotController extends Controller
{
    public function webhook(Request $request)
    {
        Log::info('Telegram webhook received', $request->all());

        $update = $request->all();

        // Handle different types of updates
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        } elseif (isset($update['inline_query'])) {
            $this->handleInlineQuery($update['inline_query']);
        }

        return response()->json(['ok' => true]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required',
            'text' => 'required|string|max:4096',
            'parse_mode' => 'nullable|in:HTML,Markdown',
            'disable_web_page_preview' => 'boolean',
            'disable_notification' => 'boolean',
            'reply_to_message_id' => 'integer'
        ]);

        try {
            $response = $this->sendTelegramMessage($request->all());

            if ($response['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'message_id' => $response['message_id']
                ]);
            } else {
                return response()->json(['error' => $response['error']], 500);
            }

        } catch (\Exception $e) {
            Log::error('Telegram message failed', [
                'error' => $e->getMessage(),
                'chat_id' => $request->chat_id
            ]);

            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'text' => 'required|string|max:4096'
        ]);

        $user = User::findOrFail($request->user_id);

        if (!$user->telegram_chat_id) {
            return response()->json(['error' => 'User has not linked their Telegram account'], 400);
        }

        $response = $this->sendTelegramMessage([
            'chat_id' => $user->telegram_chat_id,
            'text' => $request->text
        ]);

        if ($response['success']) {
            // Create notification
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Telegram Message',
                'message' => 'You received a Telegram message',
                'type' => 'telegram',
                'data' => json_encode(['message_id' => $response['message_id']])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully'
            ]);
        }

        return response()->json(['error' => 'Failed to send message'], 500);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'notification_id' => 'required|exists:user_notifications,id'
        ]);

        $notification = UserNotification::findOrFail($request->notification_id);
        $user = User::findOrFail($notification->user_id);

        if (!$user->telegram_chat_id) {
            return response()->json(['error' => 'User has not linked their Telegram account'], 400);
        }

        $text = $notification->title . "\n\n" . $notification->message;

        $response = $this->sendTelegramMessage([
            'chat_id' => $user->telegram_chat_id,
            'text' => $text
        ]);

        if ($response['success']) {
            $notification->update([
                'telegram_sent' => true,
                'telegram_sent_at' => now()
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Failed to send notification'], 500);
    }

    public function setWebhook(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        try {
            $response = Http::post(config('services.telegram.bot_api') . '/setWebhook', [
                'url' => $request->url
            ]);

            if ($response->json('ok')) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['error' => 'Failed to set webhook'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Telegram webhook setup failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to set webhook'], 500);
        }
    }

    public function getWebhookInfo()
    {
        try {
            $response = Http::get(config('services.telegram.bot_api') . '/getWebhookInfo');

            if ($response->json('ok')) {
                return response()->json($response->json('result'));
            }

        } catch (\Exception $e) {
            Log::error('Failed to get webhook info', [
                'error' => $e->getMessage()
            ]);
        }

        return response()->json(['error' => 'Failed to get webhook info'], 500);
    }

    public function getBotInfo()
    {
        try {
            $response = Http::get(config('services.telegram.bot_api') . '/getMe');

            if ($response->json('ok')) {
                return response()->json($response->json('result'));
            }

        } catch (\Exception $e) {
            Log::error('Failed to get bot info', [
                'error' => $e->getMessage()
            ]);
        }

        return response()->json(['error' => 'Failed to get bot info'], 500);
    }

    private function handleMessage(array $message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $from = $message['from'];

        Log::info('Telegram message received', [
            'chat_id' => $chatId,
            'text' => $text,
            'from' => $from
        ]);

        // Find user by telegram chat ID
        $user = User::where('telegram_chat_id', $chatId)->first();

        if ($user) {
            // Create notification for incoming message
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'New Telegram Message',
                'message' => 'You received a new Telegram message',
                'type' => 'telegram',
                'data' => json_encode([
                    'chat_id' => $chatId,
                    'text' => $text,
                    'from' => $from
                ])
            ]);

            // Handle commands
            if (str_starts_with($text, '/')) {
                $this->handleCommand($user, $text);
            }
        }
    }

    private function handleCallbackQuery(array $callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];

        Log::info('Telegram callback query received', [
            'chat_id' => $chatId,
            'data' => $data
        ]);

        // Handle callback query
        $user = User::where('telegram_chat_id', $chatId)->first();

        if ($user) {
            // Process callback data
            $this->processCallback($user, $data);
        }
    }

    private function handleInlineQuery(array $inlineQuery)
    {
        $queryId = $inlineQuery['id'];
        $query = $inlineQuery['query'];

        Log::info('Telegram inline query received', [
            'query_id' => $queryId,
            'query' => $query
        ]);

        // Handle inline query
        // This would typically return inline results
    }

    private function handleCommand(User $user, string $command)
    {
        switch ($command) {
            case '/start':
                $this->sendTelegramMessage([
                    'chat_id' => $user->telegram_chat_id,
                    'text' => 'Welcome to the Real Estate Platform bot!'
                ]);
                break;

            case '/help':
                $this->sendTelegramMessage([
                    'chat_id' => $user->telegram_chat_id,
                    'text' => 'Available commands:\n/start - Start the bot\n/help - Show help\n/status - Check your status'
                ]);
                break;

            case '/status':
                $this->sendTelegramMessage([
                    'chat_id' => $user->telegram_chat_id,
                    'text' => 'Your account is active and linked to this bot.'
                ]);
                break;

            default:
                $this->sendTelegramMessage([
                    'chat_id' => $user->telegram_chat_id,
                    'text' => 'Unknown command. Type /help for available commands.'
                ]);
        }
    }

    private function processCallback(User $user, string $data)
    {
        // Process callback data
        // This would handle button clicks and other interactive elements
    }

    private function sendTelegramMessage(array $data): array
    {
        try {
            $response = Http::post(config('services.telegram.bot_api') . '/sendMessage', $data);

            if ($response->json('ok')) {
                return [
                    'success' => true,
                    'message_id' => $response->json('result.message_id')
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json('description', 'Unknown error')
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
