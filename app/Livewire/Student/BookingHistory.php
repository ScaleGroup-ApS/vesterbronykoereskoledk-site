<?php

namespace App\Livewire\Student;

use App\Enums\BookingStatus;
use App\Models\Student;
use Illuminate\View\View;
use Livewire\Component;

class BookingHistory extends Component
{
    public function render(): View
    {
        $student = auth()->user()?->student;

        $pastBookings = [];

        if ($student instanceof Student) {
            $tz = config('app.timezone');

            $pastBookings = $student->bookings()
                ->with('instructor:id,name')
                ->whereIn('status', [
                    BookingStatus::Completed->value,
                    BookingStatus::NoShow->value,
                    BookingStatus::Cancelled->value,
                ])
                ->orderByDesc('starts_at')
                ->limit(50)
                ->get()
                ->map(function ($booking) use ($tz) {
                    $start = $booking->starts_at->timezone($tz);
                    $end = $booking->ends_at->timezone($tz);

                    return [
                        'id' => $booking->id,
                        'type_label' => $booking->type->label(),
                        'status' => $booking->status->value,
                        'range_label' => $start->translatedFormat('d. MMM Y').' · '.$start->format('H:i').'–'.$end->format('H:i'),
                        'attended' => $booking->attended,
                        'instructor_note' => $booking->instructor_note,
                        'driving_skills' => $booking->driving_skills ?? [],
                        'instructor_name' => $booking->instructor?->name,
                    ];
                })
                ->all();
        }

        return view('livewire.student.booking-history', [
            'pastBookings' => $pastBookings,
        ]);
    }
}
