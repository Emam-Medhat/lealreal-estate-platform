<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $conversations = Conversation::where(function ($query) use ($user) {
            $query->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
        })->with(['userOne', 'userTwo', 'latestMessage'])
          ->withCount('messages')
          ->orderBy('updated_at', 'desc')
          ->get();
        
        return view('messages.conversations', compact('conversations'));
    }
    
    public function show($id)
    {
        $user = Auth::user();
        
        $conversation = Conversation::where(function ($query) use ($user) {
            $query->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
        })->with(['userOne', 'userTwo', 'messages.user', 'messages.attachments'])
          ->findOrFail($id);
        
        $otherUser = $conversation->user_one_id === $user->id ? $conversation->userTwo : $conversation->userOne;
        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();
        
        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return view('messages.conversation', compact('conversation', 'otherUser', 'messages'));
    }
    
    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|different:' . Auth::id()
        ]);
        
        $user = Auth::user();
        $otherUser = User::findOrFail($request->user_id);
        
        // Check if conversation already exists
        $existingConversation = Conversation::where(function ($query) use ($user, $otherUser) {
            $query->where('user_one_id', $user->id)
                  ->where('user_two_id', $otherUser->id);
        })->orWhere(function ($query) use ($user, $otherUser) {
            $query->where('user_one_id', $otherUser->id)
                  ->where('user_two_id', $user->id);
        })->first();
        
        if ($existingConversation) {
            return response()->json([
                'success' => true,
                'conversation_id' => $existingConversation->id,
                'message' => 'Conversation already exists'
            ]);
        }
        
        // Create new conversation
        $conversation = Conversation::create([
            'user_one_id' => $user->id,
            'user_two_id' => $otherUser->id,
            'last_message_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $conversation = Conversation::where(function ($query) {
            $query->where('user_one_id', Auth::id())
                  ->orWhere('user_two_id', Auth::id());
        })->findOrFail($id);
        
        $request->validate([
            'is_archived' => 'boolean',
            'is_muted' => 'boolean'
        ]);
        
        $conversation->update($request->all());
        
        return response()->json(['success' => true]);
    }
    
    public function delete(Request $request, $id)
    {
        $conversation = Conversation::where(function ($query) {
            $query->where('user_one_id', Auth::id())
                  ->orWhere('user_two_id', Auth::id());
        })->findOrFail($id);
        
        // Soft delete by marking as deleted for current user
        if ($conversation->user_one_id === Auth::id()) {
            $conversation->update(['deleted_by_user_one' => true]);
        } else {
            $conversation->update(['deleted_by_user_two' => true]);
        }
        
        // Hard delete if both users deleted
        if ($conversation->deleted_by_user_one && $conversation->deleted_by_user_two) {
            $conversation->delete();
        }
        
        return response()->json(['success' => true]);
    }
    
    public function search(Request $request)
    {
        $user = Auth::user();
        
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json(['conversations' => []]);
        }
        
        $conversations = Conversation::where(function ($q) use ($user) {
            $q->where('user_one_id', $user->id)
              ->orWhere('user_two_id', $user->id);
        })->whereHas('messages', function ($q) use ($query) {
            $q->where('content', 'like', '%' . $query . '%');
        })->with(['userOne', 'userTwo', 'latestMessage'])
          ->get();
        
        return response()->json(['conversations' => $conversations]);
    }
    
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $unreadCount = Conversation::where(function ($query) use ($user) {
            $query->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
        })->whereHas('messages', function ($query) use ($user) {
            $query->where('sender_id', '!=', $user->id)
                  ->whereNull('read_at');
        })->count();
        
        return response()->json(['unread_count' => $unreadCount]);
    }
    
    public function markAsRead($id)
    {
        $conversation = Conversation::where(function ($query) {
            $query->where('user_one_id', Auth::id())
                  ->orWhere('user_two_id', Auth::id());
        })->findOrFail($id);
        
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }
    
    public function archive($id)
    {
        $conversation = Conversation::where(function ($query) {
            $query->where('user_one_id', Auth::id())
                  ->orWhere('user_two_id', Auth::id());
        })->findOrFail($id);
        
        $conversation->update(['is_archived' => true]);
        
        return response()->json(['success' => true]);
    }
    
    public function unarchive($id)
    {
        $conversation = Conversation::where(function ($query) {
            $query->where('user_one_id', Auth::id())
                  ->orWhere('user_two_id', Auth::id());
        })->findOrFail($id);
        
        $conversation->update(['is_archived' => false]);
        
        return response()->json(['success' => true]);
    }
}
