<?php

use App\Enums\UserRole;
use App\Models\Conversation;
use App\Models\User;

test('admin can add a user to a group conversation', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $newMember = User::factory()->create();
    $conversation = Conversation::factory()->group()->create();
    $conversation->users()->attach($admin->id);

    $this->actingAs($admin)
        ->post(route('chat.members.store', $conversation), ['user_id' => $newMember->id])
        ->assertRedirect();

    expect($conversation->users()->where('user_id', $newMember->id)->exists())->toBeTrue();
});

test('non-admin cannot add a user to a group conversation', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);
    $newMember = User::factory()->create();
    $conversation = Conversation::factory()->group()->create();
    $conversation->users()->attach($student->id);

    $this->actingAs($student)
        ->post(route('chat.members.store', $conversation), ['user_id' => $newMember->id])
        ->assertForbidden();
});

test('admin can remove a user from a group conversation', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $member = User::factory()->create();
    $conversation = Conversation::factory()->group()->create();
    $conversation->users()->attach([$admin->id, $member->id]);

    $this->actingAs($admin)
        ->delete(route('chat.members.destroy', [$conversation, $member]))
        ->assertRedirect();

    expect($conversation->users()->where('user_id', $member->id)->exists())->toBeFalse();
});

test('non-admin cannot remove a user from a group conversation', function () {
    $student = User::factory()->create(['role' => UserRole::Student]);
    $other = User::factory()->create();
    $conversation = Conversation::factory()->group()->create();
    $conversation->users()->attach([$student->id, $other->id]);

    $this->actingAs($student)
        ->delete(route('chat.members.destroy', [$conversation, $other]))
        ->assertForbidden();
});

test('adding an already existing member is idempotent', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $member = User::factory()->create();
    $conversation = Conversation::factory()->group()->create();
    $conversation->users()->attach([$admin->id, $member->id]);

    $this->actingAs($admin)
        ->post(route('chat.members.store', $conversation), ['user_id' => $member->id])
        ->assertRedirect();

    expect($conversation->users()->count())->toBe(2);
});

test('cannot add member to a direct conversation', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $other = User::factory()->create();
    $newUser = User::factory()->create();
    $conversation = Conversation::factory()->create(['type' => 'direct']);
    $conversation->users()->attach([$admin->id, $other->id]);

    $this->actingAs($admin)
        ->post(route('chat.members.store', $conversation), ['user_id' => $newUser->id])
        ->assertForbidden();
});

test('group conversation is created with specified user_ids', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $student1 = User::factory()->create();
    $student2 = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('chat.store'), [
            'name' => 'Hold A',
            'user_ids' => [$student1->id, $student2->id],
        ])
        ->assertRedirect();

    $conversation = Conversation::where('name', 'Hold A')->first();
    expect($conversation)->not->toBeNull();
    expect($conversation->users()->count())->toBe(3); // admin + 2 students
});

test('chat index includes users list for member management', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    User::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('chat.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('chat/index')
            ->has('users')
        );
});

test('chat index includes users on each conversation', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $conversation = Conversation::factory()->group()->create();
    $conversation->users()->attach($admin->id);

    $this->actingAs($admin)
        ->get(route('chat.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('chat/index')
            ->has('conversations.0.users')
        );
});
