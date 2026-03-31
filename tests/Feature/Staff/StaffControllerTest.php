<?php

use App\Enums\UserRole;
use App\Models\User;

test('admin can view staff index', function () {
    $admin = User::factory()->create();
    User::factory()->instructor()->create();

    $this->actingAs($admin)
        ->get(route('staff.index'))
        ->assertOk();
});

test('non-admin cannot view staff index', function () {
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($instructor)
        ->get(route('staff.index'))
        ->assertForbidden();
});

test('admin can view staff create form', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('staff.create'))
        ->assertOk();
});

test('admin can create an instructor', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('staff.store'), [
            'name' => 'Ny Instruktør',
            'email' => 'instruktor@example.com',
            'password' => 'password123',
            'role' => 'instructor',
        ])
        ->assertRedirect(route('staff.index'));

    $user = User::where('email', 'instruktor@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe(UserRole::Instructor);
    expect($user->name)->toBe('Ny Instruktør');
});

test('admin can create another admin', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('staff.store'), [
            'name' => 'Ny Admin',
            'email' => 'admin2@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ])
        ->assertRedirect(route('staff.index'));

    $user = User::where('email', 'admin2@example.com')->first();
    expect($user->role)->toBe(UserRole::Admin);
});

test('admin cannot create a student via staff route', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('staff.store'), [
            'name' => 'Sneaky Student',
            'email' => 'student@example.com',
            'password' => 'password123',
            'role' => 'student',
        ])
        ->assertSessionHasErrors('role');
});

test('staff creation requires unique email', function () {
    $admin = User::factory()->create();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($admin)
        ->post(route('staff.store'), [
            'name' => 'Duplicate',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'role' => 'instructor',
        ])
        ->assertSessionHasErrors('email');
});

test('staff creation requires password of at least 8 characters', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('staff.store'), [
            'name' => 'Short Pass',
            'email' => 'short@example.com',
            'password' => '1234567',
            'role' => 'instructor',
        ])
        ->assertSessionHasErrors('password');
});
