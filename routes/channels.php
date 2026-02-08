<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin-alerts', function ($user) {
    return method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
});
