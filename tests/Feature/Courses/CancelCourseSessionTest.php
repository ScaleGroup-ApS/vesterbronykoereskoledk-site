<?php

use App\Actions\Courses\CancelCourseSession;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\CourseSession;
use App\Models\Student;

test('cancelling a session cancels all related bookings', function () {
    $session = CourseSession::factory()->create();
    $student = Student::factory()->create();

    $booking = Booking::factory()->theory()->create([
        'student_id' => $student->id,
        'course_session_id' => $session->id,
        'starts_at' => $session->starts_at,
        'ends_at' => $session->ends_at,
    ]);

    app(CancelCourseSession::class)->handle($session);

    expect($session->fresh()->cancelled_at)->not->toBeNull();
    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled);
});
