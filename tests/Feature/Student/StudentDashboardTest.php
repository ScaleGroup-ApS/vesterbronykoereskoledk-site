<?php

use App\Models\Booking;
use App\Models\Offer;
use App\Models\Student;
use App\Models\User;

test('student visiting /dashboard is redirected to student dashboard', function () {
    $user = User::factory()->student()->create();
    Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('student.dashboard'));
});

test('student can visit their dashboard', function () {
    $user = User::factory()->student()->create();
    Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('student.dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/index')
            ->has('booking')
            ->has('journey')
            ->has('readiness')
            ->has('lesson_progress')
            ->has('balance')
            ->has('materials')
        );
});

test('student can visit mit forloeb page', function () {
    $user = User::factory()->student()->create();
    Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('student.progress'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/progress')
            ->has('past_bookings')
            ->has('journey')
            ->has('readiness')
            ->has('lesson_progress')
            ->has('balance')
            ->has('materials')
        );
});

test('admin cannot visit student forloeb page', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('student.progress'))
        ->assertForbidden();
});

test('admin cannot visit student dashboard', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('student.dashboard'))
        ->assertForbidden();
});

test('instructor cannot visit student dashboard', function () {
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($instructor)
        ->get(route('student.dashboard'))
        ->assertForbidden();
});

test('student dashboard shows next upcoming booking', function () {
    $user = User::factory()->student()->create();
    $student = Student::factory()->for($user)->create();
    Booking::factory()->for($student)->create([
        'starts_at' => now()->addDays(2),
        'ends_at' => now()->addDays(2)->addHours(1),
    ]);

    $this->actingAs($user)
        ->get(route('student.dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->whereNot('booking', null)
        );
});

test('student dashboard booking is null when no upcoming bookings', function () {
    $user = User::factory()->student()->create();
    Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('student.dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('booking', null)
        );
});

test('student lesson progress reflects offer requirements and scheduled bookings', function () {
    $user = User::factory()->student()->create();
    $student = Student::factory()->for($user)->create();
    $offer = Offer::factory()->create([
        'theory_lessons' => 4,
        'driving_lessons' => 2,
        'track_required' => false,
        'slippery_required' => false,
        'requires_theory_exam' => false,
        'requires_practical_exam' => false,
    ]);
    $student->offers()->attach($offer);

    Booking::factory()->for($student)->theory()->create([
        'starts_at' => now()->addDays(3),
        'ends_at' => now()->addDays(3)->addHour(),
    ]);

    $this->actingAs($user)
        ->get(route('student.progress'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('lesson_progress.0.type', 'theory_lesson')
            ->where('lesson_progress.0.required', 4)
            ->where('lesson_progress.0.completed', 0)
            ->where('lesson_progress.0.scheduled', 1)
            ->where('lesson_progress.0.remaining', 3)
        );
});
