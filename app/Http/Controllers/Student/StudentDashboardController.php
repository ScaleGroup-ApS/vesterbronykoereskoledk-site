<?php

namespace App\Http\Controllers\Student;

use App\Actions\Payments\CalculateBalance;
use App\Actions\Progression\CheckExamReadiness;
use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentDashboardController extends Controller
{
    public function __invoke(Request $request, CheckExamReadiness $readiness, CalculateBalance $balance): Response
    {
        $student = $request->user()->student;

        abort_unless($student, 404);

        $student->load('offers');

        $booking = Booking::query()
            ->where('student_id', $student->id)
            ->where('starts_at', '>=', now())
            ->whereNotIn('status', [BookingStatus::Cancelled->value, BookingStatus::NoShow->value])
            ->orderBy('starts_at')
            ->first();

        return Inertia::render('student/index', [
            'booking' => $booking ? [
                'type' => $booking->type->value,
                'starts_at' => $booking->starts_at->toIso8601String(),
                'ends_at' => $booking->ends_at->toIso8601String(),
            ] : null,
            'readiness' => $readiness->handle($student),
            'balance' => $balance->handle($student),
        ]);
    }
}
