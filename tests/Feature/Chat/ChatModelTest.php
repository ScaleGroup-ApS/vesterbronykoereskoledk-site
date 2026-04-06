<?php

use App\Models\Conversation;
use App\Models\User;

test('pivot tracks last_read_at', function () {
    $conversation = Conversation::factory()->create();
    $user = User::factory()->create();

    $readAt = now();
    $conversation->users()->attach($user->id, ['last_read_at' => $readAt]);

    $pivot = $conversation->users()->withPivot('last_read_at')->first()->pivot;
    expect($pivot->last_read_at)->not->toBeNull();
});
