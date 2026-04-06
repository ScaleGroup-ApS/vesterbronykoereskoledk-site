<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Offer;
use App\Models\Student;
use App\Models\User;

test('admin can cancel a session', function () {
    $admin = User::factory()->create();
    $course = Course::factory()->create();
    $session = CourseSession::factory()->for($course)->create();
    $booking = Booking::factory()->theory()->create([
        'course_session_id' => $session->id,
        'starts_at' => $session->starts_at,
        'ends_at' => $session->ends_at,
    ]);

    $this->actingAs($admin)
        ->post(route('courses.sessions.cancel', [$course, $session]))
        ->assertRedirect();

    expect($session->fresh()->cancelled_at)->not->toBeNull();
    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled);
});

test('admin can record session attendance', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create();
    $session = CourseSession::factory()->for($course)->create();

    $s1 = Student::factory()->create();
    $s2 = Student::factory()->create();
    $b1 = Booking::factory()->theory()->create([
        'student_id' => $s1->id,
        'course_session_id' => $session->id,
        'starts_at' => $session->starts_at,
        'ends_at' => $session->ends_at,
    ]);
    $b2 = Booking::factory()->theory()->create([
        'student_id' => $s2->id,
        'course_session_id' => $session->id,
        'starts_at' => $session->starts_at,
        'ends_at' => $session->ends_at,
    ]);

    $this->actingAs($admin)
        ->patch(route('courses.sessions.attendance', [$course, $session]), [
            'present_student_ids' => [$s1->id],
        ])
        ->assertRedirect();

    expect($b1->fresh()->attended)->toBeTrue();
    expect($b2->fresh()->attended)->toBeFalse();
});
