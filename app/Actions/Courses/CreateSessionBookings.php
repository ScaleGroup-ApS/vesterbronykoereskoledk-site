<?php

namespace App\Actions\Courses;

use App\Enums\BookingType;
use App\Enums\EnrollmentStatus;
use App\Models\Booking;
use App\Models\CourseSession;

class CreateSessionBookings
{
    public function handle(CourseSession $session): void
    {
        $course = $session->course;

        $enrolledStudentIds = $course->enrollments()
            ->where('status', EnrollmentStatus::Completed)
            ->pluck('student_id');

        $existingStudentIds = $session->bookings()
            ->pluck('student_id');

        $newStudentIds = $enrolledStudentIds->diff($existingStudentIds);

        foreach ($newStudentIds as $studentId) {
            Booking::create([
                'student_id' => $studentId,
                'course_session_id' => $session->id,
                'type' => BookingType::TheoryLesson->value,
                'starts_at' => $session->starts_at,
                'ends_at' => $session->ends_at,
            ]);
        }
    }
}
