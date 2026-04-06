<?php

namespace App\Actions\Courses;

use App\Enums\BookingStatus;
use App\Models\CourseSession;

class CancelCourseSession
{
    public function handle(CourseSession $session): void
    {
        $session->update(['cancelled_at' => now()]);

        $session->bookings()
            ->where('status', BookingStatus::Scheduled)
            ->update(['status' => BookingStatus::Cancelled]);
    }
}
