<?php

namespace App\Actions\Courses;

use App\Models\Course;
use App\Models\CourseSession;
use Carbon\Carbon;

class GenerateCourseSessions
{
    public function handle(Course $course): void
    {
        $schedule = $course->theory_schedule;

        if (empty($schedule)) {
            return;
        }

        // Clear existing sessions (cascade will remove related bookings via FK)
        $course->sessions()->delete();

        $weekdays = $schedule['weekdays'];
        $startTime = $schedule['start_time'];
        $endTime = $schedule['end_time'];
        $until = Carbon::parse($schedule['until'])->endOfDay();

        $cursor = Carbon::parse($course->start_at)->startOfDay();
        $sessionNumber = 0;

        while ($cursor->lte($until)) {
            // ISO-8601: 1=Monday, 7=Sunday
            if (in_array($cursor->dayOfWeekIso, $weekdays, true)) {
                $sessionNumber++;

                CourseSession::create([
                    'course_id' => $course->id,
                    'starts_at' => $cursor->copy()->setTimeFromTimeString($startTime),
                    'ends_at' => $cursor->copy()->setTimeFromTimeString($endTime),
                    'session_number' => $sessionNumber,
                ]);
            }

            $cursor->addDay();
        }
    }
}
