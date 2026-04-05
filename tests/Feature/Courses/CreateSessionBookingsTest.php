<?php

use App\Actions\Courses\CreateSessionBookings;
use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\Student;

test('it creates theory bookings for all enrolled students', function () {
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create();
    $session = CourseSession::factory()->for($course)->create([
        'starts_at' => now()->addWeek(),
        'ends_at' => now()->addWeek()->addHours(2),
        'session_number' => 1,
    ]);

    $student1 = Student::factory()->create();
    $student2 = Student::factory()->create();
    Enrollment::factory()->create([
        'student_id' => $student1->id,
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'status' => EnrollmentStatus::Completed,
    ]);
    Enrollment::factory()->create([
        'student_id' => $student2->id,
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'status' => EnrollmentStatus::Completed,
    ]);

    app(CreateSessionBookings::class)->handle($session);

    $bookings = $session->bookings()->get();
    expect($bookings)->toHaveCount(2);
    expect($bookings[0]->type)->toBe(BookingType::TheoryLesson);
    expect($bookings[0]->status)->toBe(BookingStatus::Scheduled);
    expect($bookings[0]->starts_at->equalTo($session->starts_at))->toBeTrue();
});

test('it does not create duplicate bookings for same student', function () {
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create();
    $session = CourseSession::factory()->for($course)->create([
        'starts_at' => now()->addWeek(),
        'ends_at' => now()->addWeek()->addHours(2),
        'session_number' => 1,
    ]);

    $student = Student::factory()->create();
    Enrollment::factory()->create([
        'student_id' => $student->id,
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'status' => EnrollmentStatus::Completed,
    ]);

    app(CreateSessionBookings::class)->handle($session);
    app(CreateSessionBookings::class)->handle($session);

    expect($session->bookings()->count())->toBe(1);
});
