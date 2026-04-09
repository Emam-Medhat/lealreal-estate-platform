<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatRoomController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:public,private',
            'description' => 'nullable|string|max:1000',
            'invite_users' => 'array',
            'invite_users.*' => 'exists:users,id'
        ]);

        $room = ChatRoom::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'created_by' => Auth::id(),
            'participants_count' => 1,
            'is_active' => true
        ]);

        // Add creator as admin
        $room->addParticipant(Auth::user(), 'admin');

        // Add invited users
        if ($request->invite_users) {
            foreach ($request->invite_users as $userId) {
                $user = User::find($userId);
                $room->addParticipant($user, 'member');
            }
        }

        return response()->json([
            'success' => true,
            'room_id' => $room->id
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $room = ChatRoom::with(['participants.user', 'messages.user', 'messages.attachments'])
            ->findOrFail($id);

        // Check if user is participant or room is public
        if (!$room->canUserAccess($user)) {
            abort(403, 'You cannot access this room');
        }

        $messages = $room->messages()->orderBy('created_at', 'asc')->get();

        return view('messages.chat-room', compact('room', 'messages'));
    }

    public function update(Request $request, $id)
    {
        $room = ChatRoom::findOrFail($id);
        
        // Only creator can update
        if ($room->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        $room->update($request->only(['name', 'description']));

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $room = ChatRoom::findOrFail($id);
        
        // Only creator can delete
        if ($room->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $room->delete();

        return response()->json(['success' => true]);
    }

    public function inviteUsers(Request $request, $id)
    {
        $room = ChatRoom::findOrFail($id);
        
        // Check if user is admin or creator
        $participant = $room->chatParticipants()
            ->where('user_id', Auth::id())
            ->where('role', 'admin')
            ->first();

        if (!$participant && $room->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $invited = [];
        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);
            if ($room->addParticipant($user, 'member')) {
                $invited[] = $user->name;
            }
        }

        return response()->json([
            'success' => true,
            'invited' => $invited
        ]);
    }

    public function removeParticipant(Request $request, $id)
    {
        $room = ChatRoom::findOrFail($id);
        
        // Check if user is admin or creator
        $participant = $room->chatParticipants()
            ->where('user_id', Auth::id())
            ->where('role', 'admin')
            ->first();

        if (!$participant && $room->created_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::find($request->user_id);
        $room->removeParticipant($user);

        return response()->json(['success' => true]);
    }

    public function getRooms(Request $request)
    {
        $user = Auth::user();
        
        $query = ChatRoom::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $rooms = $query->with(['participants.user', 'latestMessage'])
            ->withCount('messages')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json(['rooms' => $rooms]);
    }
}
