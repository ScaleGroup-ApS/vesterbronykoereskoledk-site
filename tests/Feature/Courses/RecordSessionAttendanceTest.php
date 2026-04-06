<?php

use App\Actions\Courses\RecordSessionAttendance;
use App\Models\Booking;
use App\Models\CourseSession;
use App\Models\Student;

test('it records attendance for specified students', function () {
    $session = CourseSession::factory()->create();
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

    app(RecordSessionAttendance::class)->handle($session, [$s1->id]);

    expect($b1->fresh()->attended)->toBeTrue();
    expect($b2->fresh()->attended)->toBeFalse();
});
