<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\ChatService;
use App\Repositories\Contracts\ChatRoomRepositoryInterface;
use App\Repositories\Contracts\ConversationRepositoryInterface;

class ChatController extends Controller
{
    protected $chatService;
    protected $chatRoomRepo;
    protected $conversationRepo;

    public function __construct(
        ChatService $chatService,
        ChatRoomRepositoryInterface $chatRoomRepo,
        ConversationRepositoryInterface $conversationRepo
    ) {
        $this->chatService = $chatService;
        $this->chatRoomRepo = $chatRoomRepo;
        $this->conversationRepo = $conversationRepo;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $chatData = $this->chatService->getChatData($user);
        $chatRooms = $chatData['chatRooms'];
        $conversations = $chatData['conversations'];
        
        $activeRoom = $request->get('room_id');
        $activeConversation = $request->get('conversation_id');
        $chatMessages = [];
        $activeRoomData = null;
        $activeConversationData = null;
        
        if ($activeRoom) {
            $activeRoomData = $this->chatRoomRepo->findById($activeRoom, ['*'], ['participants']);
            
            // Check if user is participant
            // Assuming participants is a hasMany relation to ChatParticipant
            $isParticipant = $activeRoomData->participants->contains('id', $user->id);
            
            if (!$isParticipant) {
                abort(403, 'You are not a participant in this chat room');
            }
            
            $chatMessages = $activeRoomData->messages()
                ->with(['user', 'attachments'])
                ->latest()
                ->take(50)
                ->get()
                ->reverse()
                ->values();
        } elseif ($activeConversation) {
            $activeConversationData = $this->conversationRepo->findById($activeConversation, ['*'], ['sender', 'receiver']);
            
            // Check if user is participant
            if ($activeConversationData->sender_id !== $user->id && $activeConversationData->receiver_id !== $user->id) {
                abort(403, 'Unauthorized');
            }
            
            $activeConversationData->other_user = $activeConversationData->sender_id == $user->id ? $activeConversationData->receiver : $activeConversationData->sender;
            
            $chatMessages = $activeConversationData->messages()
                ->with(['user', 'attachments'])
                ->latest()
                ->take(50)
                ->get()
                ->reverse()
                ->values();
        }
        
        $onlineUsers = User::where('is_online', true)
            ->where('id', '!=', $user->id)
            ->limit(12)
            ->get();
        
        // Get all users for inviting (excluding current user)
        // Check if full_name column exists or use name/first_name
        $users = User::where('id', '!=', $user->id)
            ->get()
            ->sortBy('name'); // Sort by name/full_name in memory to be safe if column doesn't exist
        
        return view('messages.chat', compact('chatRooms', 'conversations', 'activeRoom', 'activeConversation', 'chatMessages', 'activeRoomData', 'activeConversationData', 'onlineUsers', 'users'));
    }

    public function getRoom(Request $request, $id)
    {
        $user = Auth::user();
        // Use findById to avoid loading all messages via getWithDetails
        $room = $this->chatRoomRepo->findById($id, ['*'], ['participants']);
        
        if (!$room->participants->contains('id', $user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Optimize messages loading: limit to last 50
        $messages = $room->messages()
            ->with(['user', 'attachments'])
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'success' => true,
            'type' => 'room',
            'data' => $room,
            'messages' => $messages
        ]);
    }

    public function getConversation(Request $request, $id)
    {
        $user = Auth::user();
        // Use findById to avoid loading all messages
        $conversation = $this->conversationRepo->findById($id, ['*'], ['sender', 'receiver']);
        
        if ($conversation->sender_id !== $user->id && $conversation->receiver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $otherUser = $conversation->sender_id == $user->id ? $conversation->receiver : $conversation->sender;
        $conversation->other_user = $otherUser;

        // Optimize messages loading: limit to last 50
        $messages = $conversation->messages()
            ->with(['user', 'attachments'])
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'success' => true,
            'type' => 'conversation',
            'data' => $conversation,
            'messages' => $messages
        ]);
    }

    public function showRoom($id)
    {
        return redirect()->route('messages.chat', ['room_id' => $id]);
    }
    
    public function createRoom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:public,private',
            'description' => 'nullable|string|max:1000',
            'invite_users' => 'nullable|array',
            'invite_users.*' => 'exists:users,id'
        ]);

        try {
            $room = $this->chatService->createRoom($request->all(), Auth::user());

            return response()->json([
                'success' => true,
                'room_id' => $room->id,
                'message' => 'Chat room created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create chat room: ' . $e->getMessage()
            ], 500);
        }
    }

    public function joinRoom(Request $request, $id)
    {
        $user = Auth::user();
        $room = $this->chatRoomRepo->findById($id);
        
        if ($room->type === 'private') {
            return response()->json(['error' => 'This is a private room'], 403);
        }
        
        // Check if already participant
        $isParticipant = $room->chatParticipants()->where('user_id', $user->id)->exists();
        
        if ($isParticipant) {
            return response()->json(['success' => true, 'message' => 'Already a participant']);
        }
        
        // Use repo or service? Ideally service for consistency, but for now direct model relation is fine or add method to Repo
        // $room->addParticipant($user, 'member'); // If method exists on model
        
        // Using ChatParticipant model directly as in original controller
        \App\Models\ChatParticipant::create([
            'chat_room_id' => $id,
            'user_id' => $user->id,
            'role' => 'member',
            'joined_at' => now()
        ]);
        
        $room->participants_count = $room->participants()->count();
        $room->save();
        
        return response()->json(['success' => true]);
    }

    public function leaveRoom(Request $request, $id)
    {
        $user = Auth::user();
        
        $participant = \App\Models\ChatParticipant::where('chat_room_id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        $room = $this->chatRoomRepo->findById($id);
        
        $participant->delete();
        
        $room->participants_count = $room->participants()->count();
        $room->save();
        
        if ($room->participants_count === 0) {
            $this->chatRoomRepo->deleteById($id);
        }
        
        return response()->json(['success' => true]);
    }

    public function getOnlineUsers()
    {
        $onlineUsers = User::where('is_online', true)
            ->where('id', '!=', Auth::id())
            ->select('id', 'name', 'avatar') // Assuming 'name' exists
            ->get();
        
        return response()->json(['users' => $onlineUsers]);
    }
    
    public function getRoomParticipants($id)
    {
        $user = Auth::user();
        $room = $this->chatRoomRepo->findById($id);
        
        // Check if user is participant
        // Assuming participants is hasMany ChatParticipant
        $isParticipant = $room->chatParticipants()->where('user_id', $user->id)->exists();
        
        if (!$isParticipant) {
            return response()->json(['error' => 'You are not a participant in this chat room'], 403);
        }
        
        $participants = $room->chatParticipants()->with('user')->get();
        
        return response()->json(['participants' => $participants]);
    }

    public function updateRoom(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:public,private',
            'description' => 'nullable|string|max:1000',
        ]);

        $room = $this->chatRoomRepo->findById($id);
        $user = Auth::user();

        // Check if user is admin of the room
        $isAdmin = $room->chatParticipants()
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->exists();

        if (!$isAdmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->chatRoomRepo->update($id, [
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'message' => 'Room updated successfully']);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
            'type' => 'required|in:text,image,file,voice',
            'chat_room_id' => 'nullable|exists:chat_rooms,id',
            'conversation_id' => 'nullable|exists:conversations,id',
            'receiver_id' => 'nullable|exists:users,id',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        if (!$request->content && !$request->hasFile('attachments')) {
             return response()->json(['error' => 'Message content or attachment is required'], 422);
        }

        try {
            $files = $request->file('attachments') ?? [];
            if (!is_array($files)) {
                $files = [$files];
            }
            
            $message = $this->chatService->sendMessage(Auth::user(), $request->all(), $files);
            
            $message->load(['user', 'attachments']);
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMessages(Request $request, $id)
    {
        $user = Auth::user();
        $room = $this->chatRoomRepo->findById($id);
        
        // Check if user is participant
        $isParticipant = $room->chatParticipants()->where('user_id', $user->id)->exists();
        
        if (!$isParticipant) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $query = $room->messages()->with(['user', 'attachments']);
        
        if ($request->has('after_id')) {
            $query->where('id', '>', $request->after_id);
        }
        
        $messages = $query->orderBy('created_at', 'asc')->get();
        
        return response()->json(['messages' => $messages]);
    }

    public function getConversationMessages(Request $request, $id)
    {
        $user = Auth::user();
        $conversation = $this->conversationRepo->findById($id);
        
        // Check if user is participant
        if ($conversation->sender_id !== $user->id && $conversation->receiver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $query = $conversation->messages()->with(['user', 'attachments']);
        
        if ($request->has('after_id')) {
            $query->where('id', '>', $request->after_id);
        }
        
        $messages = $query->orderBy('created_at', 'asc')->get();
        
        return response()->json(['messages' => $messages]);
    }
}
