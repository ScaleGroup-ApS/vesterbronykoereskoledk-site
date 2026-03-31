<?php

namespace App\Actions\Bookings;

use App\Events\BookingCreated;
use App\Models\Booking;
use App\Notifications\BookingScheduledNotification;

class CreateBooking
{
    /**
     * @param  array{student_id: int, instructor_id?: int|null, vehicle_id?: int|null, type: string, starts_at: string, ends_at: string, notes?: string|null}  $data
     */
    public function handle(array $data): Booking
    {
        $booking = Booking::create($data);

        BookingCreated::fire(
            student_id: $booking->student_id,
            booking_id: $booking->id,
            type: $booking->type->value,
            starts_at: $booking->starts_at->toDateTimeString(),
        );

        $booking->load('student.user');
        $booking->student->user->notify(new BookingScheduledNotification($booking));

        return $booking;
    }
}
