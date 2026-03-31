<?php

use App\Models\Booking;
use App\Models\User;

it('allows admin to set an instructor note on a booking', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $booking = Booking::factory()->create();

    $this->actingAs($admin)
        ->patch(route('bookings.note', $booking), ['instructor_note' => 'Good session today.'])
        ->assertRedirect();

    expect($booking->fresh()->instructor_note)->toBe('Good session today.');
});

it('rejects a note longer than 2000 characters', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $booking = Booking::factory()->create();

    $this->actingAs($admin)
        ->patch(route('bookings.note', $booking), ['instructor_note' => str_repeat('x', 2001)])
        ->assertSessionHasErrors('instructor_note');
});

it('allows clearing the instructor note by passing null', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $booking = Booking::factory()->create(['instructor_note' => 'Old note']);

    $this->actingAs($admin)
        ->patch(route('bookings.note', $booking), ['instructor_note' => null])
        ->assertRedirect();

    expect($booking->fresh()->instructor_note)->toBeNull();
});
