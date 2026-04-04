<?php

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Models\Booking;
use App\Models\Student;
use App\Models\User;

it('student can view the faerdigheder page', function () {
    $user = User::factory()->create(['role' => 'student']);
    Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('student.skills'))
        ->assertInertia(fn ($page) => $page->component('student/skills'));
});

it('skill counts reflect completed driving lessons', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    Booking::factory()->for($student)->create([
        'type' => BookingType::DrivingLesson,
        'status' => BookingStatus::Completed,
        'driving_skills' => ['parking', 'roundabouts'],
        'starts_at' => now()->subDay(),
        'ends_at' => now()->subDay()->addHour(),
    ]);
    Booking::factory()->for($student)->create([
        'type' => BookingType::DrivingLesson,
        'status' => BookingStatus::Completed,
        'driving_skills' => ['parking'],
        'starts_at' => now()->subDays(2),
        'ends_at' => now()->subDays(2)->addHour(),
    ]);

    $this->actingAs($user)
        ->get(route('student.skills'))
        ->assertInertia(fn ($page) => $page
            ->component('student/skills')
            ->has('skills')
            ->where('skills.0.key', 'parking')
            ->where('skills.0.count', 2)
        );
});
