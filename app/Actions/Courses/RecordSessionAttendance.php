<?php

namespace App\Actions\Courses;

use App\Enums\BookingStatus;
use App\Models\CourseSession;

class RecordSessionAttendance
{
    /**
     * @param  array<int>  $presentStudentIds
     */
    public function handle(CourseSession $session, array $presentStudentIds): void
    {
        $session->bookings()
            ->where('status', '!=', BookingStatus::Cancelled)
            ->each(function ($booking) use ($presentStudentIds) {
                $booking->update([
                    'attended' => in_array($booking->student_id, $presentStudentIds, true),
                    'attendance_recorded_at' => now(),
                ]);
            });
    }
}
