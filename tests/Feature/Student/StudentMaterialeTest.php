<?php

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Models\Booking;
use App\Models\Offer;
use App\Models\Student;
use App\Models\User;

it('student can view the materiale page', function () {
    $user = User::factory()->create(['role' => 'student']);
    Student::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('student.materials'))
        ->assertInertia(fn ($page) => $page->component('student/materials'));
});

it('materials with unlock_at_lesson 0 are always unlocked', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();
    $offer = Offer::factory()->create();
    $student->offers()->attach($offer, ['assigned_at' => now()]);

    $media = $offer->addMediaFromString('test content')
        ->usingFileName('guide.pdf')
        ->toMediaCollection('materials');
    $media->setCustomProperty('unlock_at_lesson', 0)->save();

    $this->actingAs($user)
        ->get(route('student.materials'))
        ->assertInertia(fn ($page) => $page
            ->has('materials', 1, fn ($m) => $m
                ->where('is_unlocked', true)
                ->etc()
            )
        );
});

it('materials locked until a lesson count are shown as locked when student has not reached it', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();
    $offer = Offer::factory()->create();
    $student->offers()->attach($offer, ['assigned_at' => now()]);

    $media = $offer->addMediaFromString('test content')
        ->usingFileName('advanced.pdf')
        ->toMediaCollection('materials');
    $media->setCustomProperty('unlock_at_lesson', 5)->save();

    // Student has only 2 completed theory lessons
    Booking::factory()->for($student)->count(2)->create([
        'type' => BookingType::TheoryLesson,
        'status' => BookingStatus::Completed,
        'starts_at' => now()->subDays(5),
        'ends_at' => now()->subDays(5)->addHour(),
    ]);

    $this->actingAs($user)
        ->get(route('student.materials'))
        ->assertInertia(fn ($page) => $page
            ->has('materials', 1, fn ($m) => $m
                ->where('is_unlocked', false)
                ->where('unlock_at_lesson', 5)
                ->etc()
            )
        );
});
