<?php

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Models\Booking;
use App\Models\Student;
use App\Models\User;

it('student can view their historik page', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('student.history'))
        ->assertInertia(fn ($page) => $page->component('student/history'));
});

it('historik includes instructor note and driving skills', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();
    Booking::factory()->for($student)->create([
        'type' => BookingType::DrivingLesson,
        'status' => BookingStatus::Completed,
        'instructor_note' => 'Good highway driving today.',
        'driving_skills' => ['motorvej', 'lane_change'],
        'starts_at' => now()->subDay(),
        'ends_at' => now()->subDay()->addHour(),
    ]);

    $this->actingAs($user)
        ->get(route('student.history'))
        ->assertInertia(fn ($page) => $page
            ->component('student/history')
            ->has('past_bookings', 1, fn ($b) => $b
                ->where('instructor_note', 'Good highway driving today.')
                ->where('driving_skills', ['motorvej', 'lane_change'])
                ->etc()
            )
        );
});
