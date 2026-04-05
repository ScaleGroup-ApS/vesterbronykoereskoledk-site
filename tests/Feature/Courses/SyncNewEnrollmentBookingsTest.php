<?php

use App\Actions\Courses\GenerateCourseSessions;
use App\Actions\Courses\SyncNewEnrollmentBookings;
use App\Enums\BookingType;
use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\Student;

test('it creates bookings for all future uncancelled sessions', function () {
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create([
        'start_at' => now()->subDays(3),
        'end_at' => now()->addWeeks(4),
        'theory_schedule' => [
            'weekdays' => [1, 3],
            'start_time' => '18:00',
            'end_time' => '20:00',
            'until' => now()->addWeeks(4)->format('Y-m-d'),
        ],
    ]);

    app(GenerateCourseSessions::class)->handle($course);
    $totalSessions = $course->sessions()->count();
    $futureSessions = $course->sessions()->where('starts_at', '>', now())->count();

    $student = Student::factory()->create();
    $enrollment = Enrollment::factory()->create([
        'student_id' => $student->id,
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'status' => EnrollmentStatus::Completed,
    ]);

    app(SyncNewEnrollmentBookings::class)->handle($enrollment);

    $bookings = $student->bookings()
        ->where('type', BookingType::TheoryLesson->value)
        ->whereNotNull('course_session_id')
        ->get();

    // Should only get bookings for future sessions, not past ones
    expect($bookings)->toHaveCount($futureSessions);
});

test('it skips cancelled sessions', function () {
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create([
        'start_at' => now(),
        'end_at' => now()->addWeeks(2),
        'theory_schedule' => [
            'weekdays' => [1],
            'start_time' => '18:00',
            'end_time' => '20:00',
            'until' => now()->addWeeks(2)->format('Y-m-d'),
        ],
    ]);

    app(GenerateCourseSessions::class)->handle($course);

    // Cancel one session
    $course->sessions()->first()->update(['cancelled_at' => now()]);

    $student = Student::factory()->create();
    $enrollment = Enrollment::factory()->create([
        'student_id' => $student->id,
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'status' => EnrollmentStatus::Completed,
    ]);

    app(SyncNewEnrollmentBookings::class)->handle($enrollment);

    $bookings = $student->bookings()->whereNotNull('course_session_id')->get();
    $futureSessions = $course->sessions()
        ->where('starts_at', '>', now())
        ->whereNull('cancelled_at')
        ->count();

    expect($bookings)->toHaveCount($futureSessions);
});
