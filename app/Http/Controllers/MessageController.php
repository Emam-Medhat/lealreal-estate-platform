<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\SendMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $conversations = Conversation::where(function ($query) {
            $query->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
        })
        ->with(['lastMessage', 'sender.profile', 'receiver.profile'])
        ->latest('updated_at')
        ->paginate(20);

        // Get list of users for composing new messages
        $users = User::where('id', '!=', Auth::id())
            ->where('account_status', 'active')
            ->get(['id', 'first_name', 'last_name', 'email']);

        return view('messages.inbox', compact('conversations', 'users'));
    }

    public function show(Conversation $conversation)
    {
        // Manual authorization check
        if (!$conversation->canBeViewedByUser(Auth::id())) {
            abort(403, 'Unauthorized');
        }
        
        $conversation->load([
            'messages' => function ($query) {
                $query->with('attachments')->latest();
            },
            'sender.profile',
            'receiver.profile'
        ]);

        // Determine the other user in the conversation
        $otherUser = $conversation->sender_id === Auth::id() 
            ? $conversation->receiver 
            : $conversation->sender;

        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('messages.conversation', compact('conversation', 'otherUser'));
    }

    public function create(Request $request)
    {
        $recipient = null;
        
        if ($request->has('recipient_id')) {
            $recipient = User::findOrFail($request->recipient_id);
        }

        $users = User::where('id', '!=', Auth::id())
            ->where('account_status', 'active')
            ->get(['id', 'first_name', 'last_name', 'email']);

        return view('messages.create', compact('recipient', 'users'));
    }

    public function createConversation(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'content' => 'required|string|max:2000',
        ]);

        DB::beginTransaction();
        
        try {
            $recipient = User::findOrFail($request->recipient_id);
            
            // Check if conversation already exists
            $conversation = Conversation::where(function ($query) use ($recipient) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $recipient->id);
            })->orWhere(function ($query) use ($recipient) {
                $query->where('sender_id', $recipient->id)
                      ->where('receiver_id', Auth::id());
            })->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'sender_id' => Auth::id(),
                    'receiver_id' => $recipient->id,
                    'status' => 'active',
                ]);
            }

            // Create message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'receiver_id' => $recipient->id,
                'content' => $request->content,
                'type' => 'text',
                'is_read' => false,
            ]);

            // Update conversation timestamp
            $conversation->update(['updated_at' => now()]);

            // Create notification for recipient
            UserNotification::create([
                'user_id' => $recipient->id,
                'title' => 'New Message',
                'message' => "You have a new message from " . Auth::user()->full_name,
                'type' => 'message',
                'action_url' => route('messages.conversation', $conversation),
                'action_text' => 'View Message',
                'is_read' => false,
            ]);

            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'sent_message',
                'description' => "Sent message to {$recipient->full_name}",
                'ip_address' => $request->ip(),
            ]);

            // Broadcast message
            broadcast(new MessageSent($message, Auth::user()))->toOthers();

            DB::commit();

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'message' => 'Conversation created successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(SendMessageRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $recipient = User::findOrFail($request->recipient_id);
            
            // Find or create conversation
            $conversation = Conversation::where(function ($query) use ($recipient) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $recipient->id);
            })->orWhere(function ($query) use ($recipient) {
                $query->where('sender_id', $recipient->id)
                      ->where('receiver_id', Auth::id());
            })->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'sender_id' => Auth::id(),
                    'receiver_id' => $recipient->id,
                    'status' => 'active',
                ]);
            }

            // Create message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'receiver_id' => $recipient->id,
                'content' => $request->message,
                'type' => $request->type ?? 'text',
                'is_read' => false,
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('message-attachments', 'public');
                    
                    MessageAttachment::create([
                        'message_id' => $message->id,
                        'filename' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }
            }

            // Update conversation timestamp
            $conversation->update(['updated_at' => now()]);

            // Create notification for recipient
            UserNotification::create([
                'user_id' => $recipient->id,
                'title' => 'New Message',
                'message' => "You have a new message from " . Auth::user()->full_name,
                'type' => 'message',
                'action_url' => route('messages.conversation', $conversation),
                'action_text' => 'View Message',
                'is_read' => false,
            ]);

            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'sent_message',
                'description' => "Sent message to {$recipient->full_name}",
                'ip_address' => $request->ip(),
            ]);

            // Broadcast message
            broadcast(new MessageSent($message, Auth::user()))->toOthers();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message->load('attachments'),
                    'conversation_id' => $conversation->id,
                ]);
            }

            return redirect()->route('messages.conversation', $conversation)
                ->with('success', 'Message sent successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to send message: ' . $e->getMessage());
        }
    }

    public function reply(Request $request, Conversation $conversation)
    {
        // Manual authorization check
        if (!$conversation->canBeRepliedToByUser(Auth::id())) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'message' => 'required|string|max:2000',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        DB::beginTransaction();
        
        try {
            $recipient = $conversation->sender_id === Auth::id() 
                ? $conversation->receiver 
                : $conversation->sender;

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'receiver_id' => $recipient->id,
                'content' => $request->message,
                'type' => 'text',
                'is_read' => false,
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('message-attachments', 'public');
                    
                    MessageAttachment::create([
                        'message_id' => $message->id,
                        'filename' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                }
            }

            // Update conversation timestamp
            $conversation->update(['updated_at' => now()]);

            // Create notification for recipient
            UserNotification::create([
                'user_id' => $recipient->id,
                'title' => 'New Message',
                'message' => "You have a new message from " . Auth::user()->full_name,
                'type' => 'message',
                'action_url' => route('messages.conversation', $conversation),
                'action_text' => 'View Message',
                'is_read' => false,
            ]);

            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'replied_to_message',
                'description' => "Replied to message in conversation with {$recipient->full_name}",
                'ip_address' => $request->ip(),
            ]);

            // Broadcast message
            broadcast(new MessageSent($message, Auth::user()))->toOthers();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message->load('attachments'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reply: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAsRead(Message $message): JsonResponse
    {
        if ($message->receiver_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read'
        ]);
    }

    public function markAsUnread(Message $message): JsonResponse
    {
        if ($message->receiver_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $message->update([
            'is_read' => false,
            'read_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as unread'
        ]);
    }

    public function delete(Message $message): JsonResponse
    {
        if ($message->sender_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete attachments
        foreach ($message->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $message->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_message',
            'description' => 'Deleted a message',
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }

    public function deleteConversation(Conversation $conversation): JsonResponse
    {
        // Manual authorization check
        if (!$conversation->canBeViewedByUser(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        DB::beginTransaction();
        
        try {
            // Delete all attachments
            foreach ($conversation->messages as $message) {
                foreach ($message->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file_path);
                    $attachment->delete();
                }
                $message->delete();
            }

            $conversation->delete();

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted_conversation',
                'description' => 'Deleted a conversation',
                'ip_address' => request()->ip(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Conversation deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUnreadCount(): JsonResponse
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    public function getRecentMessages(Request $request): JsonResponse
    {
        $messages = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->with(['sender.profile', 'conversation'])
            ->latest()
            ->limit($request->limit ?? 5)
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    public function downloadAttachment(MessageAttachment $attachment)
    {
        $message = $attachment->message;
        
        if (!in_array(Auth::id(), [$message->sender_id, $message->receiver_id])) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->filename);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');
        
        if (strlen($query) < 2) {
            return response()->json(['conversations' => []]);
        }

        $conversations = Conversation::where(function ($q) {
            $q->where('sender_id', Auth::id())
              ->orWhere('receiver_id', Auth::id());
        })
        ->whereHas('messages', function ($messageQuery) use ($query) {
            $messageQuery->where('content', 'like', "%{$query}%");
        })
        ->with(['sender.profile', 'receiver.profile'])
        ->latest()
        ->limit(10)
        ->get();

        return response()->json([
            'success' => true,
            'conversations' => $conversations
        ]);
    }
}
