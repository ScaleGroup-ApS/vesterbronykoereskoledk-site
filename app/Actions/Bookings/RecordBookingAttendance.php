<?php

namespace App\Actions\Bookings;

use App\Enums\BookingStatus;
use App\Events\BookingNoShow;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RecordBookingAttendance
{
    public function __construct(
        private CompleteBooking $completeBooking,
    ) {}

    public function handle(Booking $booking, User $recorder, bool $attended): void
    {
        if ($booking->status !== BookingStatus::Scheduled) {
            throw ValidationException::withMessages([
                'booking' => 'Fremmøde kan kun registreres for planlagte bookinger.',
            ]);
        }

        if ($attended) {
            $booking->attended = true;
            $booking->attendance_recorded_at = now();
            $booking->attendance_recorded_by = $recorder->id;
            $booking->save();

            $this->completeBooking->handle($booking->fresh());

            return;
        }

        $booking->attended = false;
        $booking->attendance_recorded_at = now();
        $booking->attendance_recorded_by = $recorder->id;
        $booking->status = BookingStatus::NoShow;
        $booking->save();

        BookingNoShow::fire(
            student_id: $booking->student_id,
            booking_id: $booking->id,
            type: $booking->type->value,
        );
    }
}
