<?php

namespace App\Livewire\Student;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingFeedback;
use App\Models\Student;
use Illuminate\View\View;
use Livewire\Component;

class Feedback extends Component
{
    /** @var array<int, int|null> Booking ID → rating (1-5) */
    public array $ratings = [];

    /** @var array<int, string> Booking ID → comment */
    public array $comments = [];

    /** @var array<int, string|null> Booking ID → validation error */
    public array $errors = [];

    /** @var array<int, bool> */
    public array $submitted = [];

    public function submitFeedback(int $bookingId): void
    {
        $student = auth()->user()?->student;

        if (! $student instanceof Student) {
            return;
        }

        $rating = $this->ratings[$bookingId] ?? null;

        if (! $rating || $rating < 1 || $rating > 5) {
            $this->errors[$bookingId] = 'Vælg en bedømmelse fra 1 til 5.';

            return;
        }

        $booking = Booking::find($bookingId);

        if (! $booking || $booking->student_id !== $student->id) {
            return;
        }

        if ($booking->feedback()->exists()) {
            return;
        }

        BookingFeedback::create([
            'booking_id' => $bookingId,
            'student_id' => $student->id,
            'rating' => $rating,
            'comment' => $this->comments[$bookingId] ?? null,
        ]);

        unset($this->errors[$bookingId], $this->ratings[$bookingId], $this->comments[$bookingId]);
        $this->submitted[$bookingId] = true;
    }

    public function render(): View
    {
        $student = auth()->user()?->student;

        if (! $student instanceof Student) {
            return view('livewire.student.feedback', [
                'pendingBookings' => [],
                'recentFeedback' => [],
                'avgRating' => null,
            ]);
        }

        $tz = config('app.timezone');

        $pendingBookings = $student->bookings()
            ->where('status', BookingStatus::Completed->value)
            ->where('attended', true)
            ->whereDoesntHave('feedback')
            ->whereNotIn('id', array_keys($this->submitted))
            ->with('instructor:id,name')
            ->orderByDesc('starts_at')
            ->limit(5)
            ->get()
            ->map(fn (Booking $b) => [
                'id' => $b->id,
                'type_label' => $b->type->label(),
                'range_label' => $b->starts_at->timezone($tz)->translatedFormat('d. M Y').' · '.$b->starts_at->timezone($tz)->format('H:i').'–'.$b->ends_at->timezone($tz)->format('H:i'),
                'instructor_name' => $b->instructor?->name,
            ])
            ->values()
            ->all();

        $recentFeedback = $student->bookingFeedback()
            ->with('booking.instructor:id,name')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn (BookingFeedback $f) => [
                'rating' => $f->rating,
                'comment' => $f->comment,
                'type_label' => $f->booking->type->label(),
                'range_label' => $f->booking->starts_at->timezone($tz)->translatedFormat('d. M Y'),
                'instructor_name' => $f->booking->instructor?->name,
            ])
            ->values()
            ->all();

        $avgRating = $student->bookingFeedback()->avg('rating');

        return view('livewire.student.feedback', [
            'pendingBookings' => $pendingBookings,
            'recentFeedback' => $recentFeedback,
            'avgRating' => $avgRating ? round($avgRating, 1) : null,
        ]);
    }
}
