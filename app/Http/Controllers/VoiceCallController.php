<?php

namespace App\Http\Controllers;

use App\Models\VoiceCall;
use App\Models\Conversation;
use App\Models\Appointment;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VoiceCallController extends Controller
{
    public function start($conversation_id)
    {
        $user = Auth::user();
        
        $conversation = Conversation::where(function ($query) use ($user) {
            $query->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
        })->findOrFail($conversation_id);

        $otherUserId = $conversation->user_one_id === $user->id ? $conversation->user_two_id : $conversation->user_one_id;

        // Check if there's already an active call
        $existingCall = VoiceCall::where('conversation_id', $conversation_id)
            ->where('status', 'active')
            ->first();

        if ($existingCall) {
            return redirect()->route('messages.voice-call.join', $existingCall->id);
        }

        // Create new voice call
        $call = VoiceCall::create([
            'conversation_id' => $conversation_id,
            'caller_id' => $user->id,
            'receiver_id' => $otherUserId,
            'call_id' => Str::random(12),
            'status' => 'waiting',
            'started_at' => now()
        ]);

        // Send notification to receiver
        UserNotification::create([
            'user_id' => $otherUserId,
            'title' => 'Incoming Voice Call',
            'message' => $user->name . ' is calling you',
            'type' => 'voice_call',
            'data' => json_encode(['call_id' => $call->id]),
            'action_url' => '/messages/voice-call/' . $call->id,
            'action_text' => 'Join Call'
        ]);

        return view('messages.voice-call', compact('call', 'conversation'));
    }

    public function startAppointment($appointment_id)
    {
        $user = Auth::user();
        
        $appointment = Appointment::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('participant_id', $user->id);
        })->findOrFail($appointment_id);

        $otherUserId = $appointment->user_id === $user->id ? $appointment->participant_id : $appointment->user_id;

        // Check if there's already an active call
        $existingCall = VoiceCall::where('appointment_id', $appointment_id)
            ->where('status', 'active')
            ->first();

        if ($existingCall) {
            return redirect()->route('messages.voice-call.join', $existingCall->id);
        }

        // Create new voice call
        $call = VoiceCall::create([
            'appointment_id' => $appointment_id,
            'caller_id' => $user->id,
            'receiver_id' => $otherUserId,
            'call_id' => Str::random(12),
            'status' => 'waiting',
            'started_at' => now()
        ]);

        // Send notification to receiver
        UserNotification::create([
            'user_id' => $otherUserId,
            'title' => 'Voice Call Started',
            'message' => $user->name . ' started a voice call for: ' . $appointment->title,
            'type' => 'voice_call',
            'data' => json_encode(['call_id' => $call->id]),
            'action_url' => '/messages/voice-call/' . $call->id,
            'action_text' => 'Join Call'
        ]);

        return view('messages.voice-call', compact('call', 'appointment'));
    }

    public function join(Request $request, $id)
    {
        $user = Auth::user();
        
        $call = VoiceCall::where(function ($query) use ($user) {
            $query->where('caller_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })->findOrFail($id);

        if ($call->status === 'ended') {
            return redirect()->route('messages.inbox')->with('error', 'Call has ended');
        }

        // Update call status if receiver joins
        if ($call->receiver_id === $user->id && $call->status === 'waiting') {
            $call->update([
                'status' => 'active',
                'answered_at' => now()
            ]);
        }

        return view('messages.voice-call-join', compact('call'));
    }

    public function end(Request $request, $id)
    {
        $user = Auth::user();
        
        $call = VoiceCall::where(function ($query) use ($user) {
            $query->where('caller_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })->findOrFail($id);

        $call->update([
            'status' => 'ended',
            'ended_at' => now(),
            'duration' => $call->started_at->diffInSeconds(now())
        ]);

        // Notify other party
        $otherUserId = $call->caller_id === $user->id ? $call->receiver_id : $call->caller_id;

        UserNotification::create([
            'user_id' => $otherUserId,
            'title' => 'Voice Call Ended',
            'message' => 'Voice call ended. Duration: ' . gmdate('H:i:s', $call->duration),
            'type' => 'voice_call',
            'data' => json_encode(['call_id' => $call->id])
        ]);

        return response()->json(['success' => true]);
    }

    public function getCallStatus($id)
    {
        $user = Auth::user();
        
        $call = VoiceCall::where(function ($query) use ($user) {
            $query->where('caller_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })->findOrFail($id);

        return response()->json([
            'status' => $call->status,
            'duration' => $call->duration,
            'started_at' => $call->started_at,
            'ended_at' => $call->ended_at
        ]);
    }

    public function getActiveCalls()
    {
        $user = Auth::user();
        
        $calls = VoiceCall::where(function ($query) use ($user) {
            $query->where('caller_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })->where('status', '!=', 'ended')
          ->with(['caller', 'receiver'])
          ->get();

        return response()->json(['calls' => $calls]);
    }
}
