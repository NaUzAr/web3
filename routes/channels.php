<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('device.{deviceId}', function ($user, $deviceId) {
    // If user is admin, allow access to all devices
    if ($user->role === 'admin' || $user->is_admin) {
        return true;
    }
    return \App\Models\UserDevice::where('user_id', $user->id)->where('device_id', $deviceId)->exists();
});
