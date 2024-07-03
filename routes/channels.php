<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->user_id === (int) $id;
});
Broadcast::channel('generating-app-release-reports.{id}', fn(User $user, $id) => (int) $user->user_id === (int) $id);
Broadcast::channel('generating-app-release-reports-header.{id}', fn(User $user, $id) => (int) $user->user_id === (int) $id);
Broadcast::channel('generating-app-release-reports-inner.{id}', fn(User $user, $id) => (int) $user->user_id === (int) $id);
