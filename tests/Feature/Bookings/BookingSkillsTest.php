<?php

use App\Models\Booking;
use App\Models\User;

it('allows admin to set driving skills on a booking', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $booking = Booking::factory()->create(['type' => 'driving_lesson']);

    $this->actingAs($admin)
        ->patch(route('bookings.skills', $booking), ['driving_skills' => ['parking', 'roundabouts']])
        ->assertRedirect();

    expect($booking->fresh()->driving_skills)->toBe(['parking', 'roundabouts']);
});

it('rejects unknown skill values', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $booking = Booking::factory()->create(['type' => 'driving_lesson']);

    $this->actingAs($admin)
        ->patch(route('bookings.skills', $booking), ['driving_skills' => ['flying']])
        ->assertSessionHasErrors('driving_skills.0');
});

it('allows an empty skills array', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $booking = Booking::factory()->create(['type' => 'driving_lesson', 'driving_skills' => ['parking']]);

    $this->actingAs($admin)
        ->patch(route('bookings.skills', $booking), ['driving_skills' => []])
        ->assertRedirect();

    expect($booking->fresh()->driving_skills)->toBe([]);
});
