<?php

namespace App\Actions\Courses;

use App\Enums\BookingType;
use App\Models\Booking;
use App\Models\Enrollment;

class SyncNewEnrollmentBookings
{
    public function handle(Enrollment $enrollment): void
    {
        $enrollment->loadMissing('course.sessions');

        $course = $enrollment->course;

        if (! $course) {
            return;
        }

        $futureSessions = $course->sessions()
            ->where('starts_at', '>', now())
            ->whereNull('cancelled_at')
            ->get();

        foreach ($futureSessions as $session) {
            $exists = Booking::where('course_session_id', $session->id)
                ->where('student_id', $enrollment->student_id)
                ->exists();

            if (! $exists) {
                Booking::create([
                    'student_id' => $enrollment->student_id,
                    'course_session_id' => $session->id,
                    'type' => BookingType::TheoryLesson->value,
                    'starts_at' => $session->starts_at,
                    'ends_at' => $session->ends_at,
                ]);
            }
        }
    }
}
