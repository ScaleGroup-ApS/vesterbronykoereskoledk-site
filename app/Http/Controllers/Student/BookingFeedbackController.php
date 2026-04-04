<?php

namespace App\Http\Controllers\Student;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingFeedback;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingFeedbackController extends Controller
{
    public function index(Request $request): Response
    {
        $student = $this->student($request);

        $pendingFeedback = $student->bookings()
            ->where('status', BookingStatus::Completed->value)
            ->where('attended', true)
            ->whereDoesntHave('feedback')
            ->with('instructor:id,name')
            ->orderByDesc('starts_at')
            ->limit(5)
            ->get()
            ->map(fn (Booking $b) => [
                'id' => $b->id,
                'type_label' => $b->type->label(),
                'range_label' => $this->formatRange($b),
                'instructor_name' => $b->instructor?->name,
                'driving_skills' => $b->driving_skills ?? [],
            ]);

        $recentFeedback = $student->bookingFeedback()
            ->with('booking.instructor:id,name')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn (BookingFeedback $f) => [
                'id' => $f->id,
                'rating' => $f->rating,
                'comment' => $f->comment,
                'confidence_scores' => $f->confidence_scores,
                'type_label' => $f->booking->type->label(),
                'range_label' => $this->formatRange($f->booking),
                'instructor_name' => $f->booking->instructor?->name,
                'created_at' => $f->created_at->toIso8601String(),
            ]);

        $avgRating = $student->bookingFeedback()->avg('rating');

        return Inertia::render('student/feedback', [
            'pending_feedback' => $pendingFeedback,
            'recent_feedback' => $recentFeedback,
            'avg_rating' => $avgRating ? round($avgRating, 1) : null,
        ]);
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $student = $this->student($request);
        abort_unless($booking->student_id === $student->id, 403);
        abort_unless($booking->status === BookingStatus::Completed, 422);
        abort_if($booking->feedback()->exists(), 422);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'confidence_scores' => ['nullable', 'array'],
            'confidence_scores.*' => ['integer', 'min:1', 'max:5'],
        ]);

        BookingFeedback::create([
            'booking_id' => $booking->id,
            'student_id' => $student->id,
            ...$validated,
        ]);

        return back()->with('success', 'Tak for din feedback!');
    }

    private function student(Request $request): Student
    {
        $student = $request->user()->student;
        abort_unless($student, 404);

        return $student;
    }

    private function formatRange(Booking $booking): string
    {
        $tz = config('app.timezone');
        $start = $booking->starts_at->timezone($tz);
        $end = $booking->ends_at->timezone($tz);

        return $start->translatedFormat('d. M Y').' · '.$start->format('H:i').'–'.$end->format('H:i');
    }
}
