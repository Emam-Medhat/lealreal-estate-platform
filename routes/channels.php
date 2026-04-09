<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin-alerts', function ($user) {
    return method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
});

Broadcast::channel('chat.room.{roomId}', function ($user, $roomId) {
    return \App\Models\ChatParticipant::where('chat_room_id', $roomId)
        ->where('user_id', $user->id)
        ->exists();
});

Broadcast::channel('alerts.user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
