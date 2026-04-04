<?php

use App\Models\Student;
use App\Models\User;

it('student can view notifications page', function () {
    $user = User::factory()->create(['role' => 'student']);
    Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('student.notifications'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/notifications')
            ->has('notifications')
            ->has('unread_count')
        );
});

it('student can mark a notification as read', function () {
    $user = User::factory()->create(['role' => 'student']);
    Student::factory()->for($user)->create();

    $notification = $user->notifications()->create([
        'id' => fake()->uuid(),
        'type' => 'App\\Notifications\\TestNotification',
        'data' => ['title' => 'Test'],
        'read_at' => null,
    ]);

    $this->actingAs($user)
        ->post(route('student.notifications.read', $notification->id))
        ->assertRedirect();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('student can mark all notifications as read', function () {
    $user = User::factory()->create(['role' => 'student']);
    Student::factory()->for($user)->create();

    $user->notifications()->create([
        'id' => fake()->uuid(),
        'type' => 'App\\Notifications\\TestNotification',
        'data' => ['title' => 'Test 1'],
        'read_at' => null,
    ]);
    $user->notifications()->create([
        'id' => fake()->uuid(),
        'type' => 'App\\Notifications\\TestNotification',
        'data' => ['title' => 'Test 2'],
        'read_at' => null,
    ]);

    $this->actingAs($user)
        ->post(route('student.notifications.read-all'))
        ->assertRedirect();

    expect($user->unreadNotifications()->count())->toBe(0);
});
