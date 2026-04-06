<?php

namespace App\Actions\Student;

use App\Enums\BookingStatus;
use App\Enums\EnrollmentStatus;
use App\Models\Booking;
use App\Models\Enrollment;
use App\Models\Student;

class FindNextStudentEvent
{
    /**
     * Find the next upcoming booking or course start for a student,
     * returning whichever happens sooner.
     *
     * @return array{type: string, title: string, starts_at: string, ends_at: string, range_label: string, instructor_name?: string|null}|null
     */
    public function handle(Student $student): ?array
    {
        $tz = config('app.timezone');

        $booking = Booking::query()
            ->with('instructor:id,name')
            ->where('student_id', $student->id)
            ->where('starts_at', '>=', now())
            ->whereNotIn('status', [BookingStatus::Cancelled->value, BookingStatus::NoShow->value])
            ->orderBy('starts_at')
            ->first();

        $enrollment = Enrollment::query()
            ->where('student_id', $student->id)
            ->whereIn('status', [EnrollmentStatus::Completed->value, EnrollmentStatus::PendingApproval->value])
            ->with(['course', 'offer'])
            ->whereHas('course', fn ($q) => $q->where('start_at', '>=', now()))
            ->first();

        if ($booking && $enrollment?->course) {
            return $booking->starts_at->lte($enrollment->course->start_at)
                ? $this->formatBooking($booking, $tz)
                : $this->formatCourse($enrollment, $tz);
        }

        if ($booking) {
            return $this->formatBooking($booking, $tz);
        }

        if ($enrollment?->course) {
            return $this->formatCourse($enrollment, $tz);
        }

        return null;
    }

    /**
     * @return array{type: string, title: string, starts_at: string, ends_at: string, range_label: string, instructor_name: string|null}
     */
    private function formatBooking(Booking $booking, string $tz): array
    {
        $start = $booking->starts_at->timezone($tz);
        $end = $booking->ends_at->timezone($tz);

        return [
            'type' => $booking->type->value,
            'title' => $booking->type->label(),
            'starts_at' => $booking->starts_at->toIso8601String(),
            'ends_at' => $booking->ends_at->toIso8601String(),
            'range_label' => $start->translatedFormat('l d. F Y').' · '.$start->format('H:i').'–'.$end->format('H:i'),
            'instructor_name' => $booking->instructor?->name,
        ];
    }

    /**
     * @return array{type: string, title: string, starts_at: string, ends_at: string, range_label: string}
     */
    private function formatCourse(Enrollment $enrollment, string $tz): array
    {
        $course = $enrollment->course;
        $start = $course->start_at->timezone($tz);
        $end = $course->end_at->timezone($tz);

        return [
            'type' => 'course_start',
            'title' => $enrollment->offer->name,
            'starts_at' => $course->start_at->toIso8601String(),
            'ends_at' => $course->end_at->toIso8601String(),
            'range_label' => $start->translatedFormat('l d. F Y').' · '.$start->format('H:i').'–'.$end->format('H:i'),
        ];
    }
}
