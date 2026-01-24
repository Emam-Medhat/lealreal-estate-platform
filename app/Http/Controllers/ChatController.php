<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $chatRooms = ChatRoom::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['participants.user', 'latestMessage'])
          ->withCount('messages')
          ->orderBy('updated_at', 'desc')
          ->get();
        
        $activeRoom = $request->get('room_id');
        $chatMessages = [];
        $activeRoomData = null;
        
        if ($activeRoom) {
            $activeRoomData = ChatRoom::with(['participants.user', 'messages.user', 'messages.attachments'])
                ->findOrFail($activeRoom);
            
            // Check if user is participant
            $isParticipant = $activeRoomData->participants->contains('user_id', $user->id);
            if (!$isParticipant) {
                abort(403, 'You are not a participant in this chat room');
            }
            
            $chatMessages = $activeRoomData->messages()->orderBy('created_at', 'asc')->get();
        }
        
        $onlineUsers = User::where('is_online', true)
            ->where('id', '!=', $user->id)
            ->limit(12)
            ->get();
        
        return view('messages.chat', compact('chatRooms', 'activeRoom', 'chatMessages', 'activeRoomData', 'onlineUsers'));
    }
    
    public function showRoom($id)
    {
        $user = Auth::user();
        
        $room = ChatRoom::with(['participants.user', 'messages.user', 'messages.attachments'])
            ->findOrFail($id);
        
        // Check if user is participant
        $isParticipant = $room->participants->contains('user_id', $user->id);
        if (!$isParticipant) {
            abort(403, 'You are not a participant in this chat room');
        }
        
        $messages = $room->messages()->orderBy('created_at', 'asc')->get();
        
        return view('messages.chat-room', compact('room', 'messages'));
    }
    
    public function sendMessage(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:chat_rooms,id',
            'content' => 'required|string|max:1000'
        ]);
        
        $user = Auth::user();
        
        // Check if user is participant
        $room = ChatRoom::findOrFail($request->room_id);
        $isParticipant = $room->participants->contains('user_id', $user->id);
        if (!$isParticipant) {
            return response()->json(['error' => 'You are not a participant in this chat room'], 403);
        }
        
        $message = Message::create([
            'user_id' => $user->id,
            'chat_room_id' => $request->room_id,
            'content' => $request->content,
            'type' => 'chat'
        ]);
        
        // Update room's last activity
        $room->updated_at = now();
        $room->save();
        
        // Load relationships for response
        $message->load('user');
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    public function joinRoom(Request $request, $id)
    {
        $user = Auth::user();
        
        $room = ChatRoom::findOrFail($id);
        
        // Check if room is public or user has invite
        if ($room->type === 'private') {
            // Add logic for private room invites
            return response()->json(['error' => 'This is a private room'], 403);
        }
        
        // Check if already participant
        $existingParticipant = ChatParticipant::where('chat_room_id', $id)
            ->where('user_id', $user->id)
            ->first();
        
        if ($existingParticipant) {
            return response()->json(['error' => 'Already a participant'], 400);
        }
        
        // Add as participant
        ChatParticipant::create([
            'chat_room_id' => $id,
            'user_id' => $user->id,
            'role' => 'member',
            'joined_at' => now()
        ]);
        
        // Update participant count
        $room->participants_count = $room->participants()->count();
        $room->save();
        
        return response()->json(['success' => true]);
    }
    
    public function leaveRoom(Request $request, $id)
    {
        $user = Auth::user();
        
        $participant = ChatParticipant::where('chat_room_id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        $room = ChatRoom::findOrFail($id);
        
        // Remove participant
        $participant->delete();
        
        // Update participant count
        $room->participants_count = $room->participants()->count();
        $room->save();
        
        // If room is empty, delete it
        if ($room->participants_count === 0) {
            $room->delete();
        }
        
        return response()->json(['success' => true]);
    }
    
    public function getOnlineUsers()
    {
        $onlineUsers = User::where('is_online', true)
            ->where('id', '!=', Auth::id())
            ->select('id', 'name', 'avatar')
            ->get();
        
        return response()->json(['users' => $onlineUsers]);
    }
    
    public function getRoomParticipants($id)
    {
        $user = Auth::user();
        
        $room = ChatRoom::findOrFail($id);
        
        // Check if user is participant
        $isParticipant = $room->participants->contains('user_id', $user->id);
        if (!$isParticipant) {
            return response()->json(['error' => 'You are not a participant in this chat room'], 403);
        }
        
        $participants = $room->participants()->with('user')->get();
        
        return response()->json(['participants' => $participants]);
    }
}
