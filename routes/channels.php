<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->user_id === (int) $id;
});
Broadcast::channel('generating-app-release-reports.{id}', fn(User $user, $id) => (int) $user->user_id === (int) $id);
Broadcast::channel('generating-excel-events.{id}', fn(User $user, $id) => (int) $user->user_id === (int) $id);
Broadcast::channel('spgc-ledger-excel.{id}', fn(User $user, $id) => (int) $user->user_id === (int) $id);
Broadcast::channel('treasury-report.{id}', fn(User $user, $id) => (int) $user->user_id === (int) $id);
Broadcast::channel('accounting-report.{id}', fn(User $user, $id) => (int) $user->user_id === (int) $id);
Broadcast::channel('generate-verified-excel.{id}', fn(User $user, $id) => (int) $user->user_id === (int) $id);
