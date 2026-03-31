<?php

namespace App\Http\Controllers\Student;

use App\Enums\BookingStatus;
use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StudentCalendarController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $student = $request->user()->student ?? throw new NotFoundHttpException;

        $events = [];

        $bookings = $student->bookings()
            ->whereNotIn('status', [BookingStatus::Cancelled, BookingStatus::NoShow])
            ->where('starts_at', '>=', now()->subMonths(1))
            ->orderBy('starts_at')
            ->get();

        foreach ($bookings as $booking) {
            $events[] = [
                'id' => 'booking-'.$booking->id,
                'title' => $booking->type->label(),
                'start' => $booking->starts_at->toIso8601String(),
                'end' => $booking->ends_at->toIso8601String(),
                'type' => $booking->type->value,
                'status' => $booking->status->value,
            ];
        }

        $enrollment = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', EnrollmentStatus::Completed)
            ->with('course')
            ->latest()
            ->first();

        if ($enrollment?->course !== null) {
            $course = $enrollment->course;
            $events[] = [
                'id' => 'course-hold-'.$course->id,
                'title' => 'Hold (periode)',
                'start' => $course->start_at->toIso8601String(),
                'end' => $course->end_at->toIso8601String(),
                'type' => 'course_hold',
                'status' => 'info',
            ];
        }

        return Inertia::render('student/kalender', [
            'events' => $events,
        ]);
    }
}
