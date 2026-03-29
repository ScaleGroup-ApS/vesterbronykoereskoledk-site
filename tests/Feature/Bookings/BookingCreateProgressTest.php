<?php

use App\Models\Offer;
use App\Models\Student;
use App\Models\User;

test('booking create shows lesson progress when student is selected via query', function () {
    $admin = User::factory()->create();
    $studentUser = User::factory()->student()->create();
    $student = Student::factory()->for($studentUser)->create();
    $offer = Offer::factory()->create([
        'theory_lessons' => 3,
        'driving_lessons' => 0,
        'track_required' => false,
        'slippery_required' => false,
        'requires_theory_exam' => false,
        'requires_practical_exam' => false,
    ]);
    $student->offers()->attach($offer);

    $this->actingAs($admin)
        ->get(route('bookings.create', ['student_id' => $student->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('bookings/create')
            ->where('selectedStudentId', $student->id)
            ->has('studentLessonProgress', fn ($rows) => $rows
                ->where('0.type', 'theory_lesson')
                ->where('0.required', 3)
            )
        );
});

test('booking create has null lesson progress without student query', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('bookings.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('selectedStudentId', null)
            ->where('studentLessonProgress', null)
        );
});
