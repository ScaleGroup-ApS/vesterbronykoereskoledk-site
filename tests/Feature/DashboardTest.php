<?php

use App\Models\Booking;
use App\Models\Enrollment;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('admin receives bookings and enrollments props', function () {
    $admin = User::factory()->create();
    Booking::factory()->create();
    Enrollment::factory()->create();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->has('bookings', 1)
            ->has('enrollments', 1)
        );
});

test('instructor receives bookings prop with empty enrollments', function () {
    $instructor = User::factory()->instructor()->create();
    Booking::factory()->create();

    $this->actingAs($instructor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->has('bookings', 1)
            ->has('enrollments', 0)
        );
});

test('student visiting dashboard is redirected to student dashboard', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertRedirect(route('student.dashboard'));
});

test('booking can belong to a team', function () {
    $team = \App\Models\Team::factory()->create();
    $booking = Booking::factory()->create(['team_id' => $team->id]);

    expect($booking->fresh()->team)->not->toBeNull();
    expect($booking->fresh()->team->id)->toBe($team->id);
});
