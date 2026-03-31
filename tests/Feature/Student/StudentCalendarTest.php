<?php

use App\Models\Booking;
use App\Models\Student;
use App\Models\User;

test('student can visit calendar page', function () {
    $user = User::factory()->student()->create();
    Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('student.kalender'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/kalender')
            ->has('events')
        );
});

test('student calendar includes upcoming bookings', function () {
    $user = User::factory()->student()->create();
    $student = Student::factory()->for($user)->create();
    $booking = Booking::factory()->for($student)->create([
        'starts_at' => now()->addDays(2),
        'ends_at' => now()->addDays(2)->addHour(),
    ]);

    $this->actingAs($user)
        ->get(route('student.kalender'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('events', fn ($events) => $events
                ->where('0.id', 'booking-'.$booking->id)
                ->etc()
            )
        );
});

test('admin cannot visit student calendar', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('student.kalender'))
        ->assertForbidden();
});
