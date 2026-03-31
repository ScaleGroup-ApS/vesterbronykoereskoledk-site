<?php

namespace App\Actions\Bookings;

use App\Enums\BookingStatus;
use App\Events\BookingUpdated;
use App\Models\Booking;
use App\Notifications\BookingRescheduledNotification;

class UpdateBooking
{
    /**
     * @param  array{instructor_id?: int|null, vehicle_id?: int|null, starts_at?: string, ends_at?: string, notes?: string|null}  $data
     */
    public function handle(Booking $booking, array $data): Booking
    {
        $originalStarts = $booking->starts_at->copy();
        $originalEnds = $booking->ends_at->copy();

        if (array_key_exists('instructor_id', $data)) {
            $booking->instructor_id = $data['instructor_id'];
        }

        if (array_key_exists('vehicle_id', $data)) {
            $booking->vehicle_id = $data['vehicle_id'];
        }

        $booking->starts_at = $data['starts_at'] ?? $booking->starts_at;
        $booking->ends_at = $data['ends_at'] ?? $booking->ends_at;
        $booking->notes = $data['notes'] ?? $booking->notes;
        $booking->save();

        BookingUpdated::fire(
            student_id: $booking->student_id,
            booking_id: $booking->id,
            starts_at: $booking->starts_at->toDateTimeString(),
            ends_at: $booking->ends_at->toDateTimeString(),
        );

        if ($booking->status === BookingStatus::Scheduled) {
            $timeChanged = ! $booking->starts_at->equalTo($originalStarts)
                || ! $booking->ends_at->equalTo($originalEnds);

            if ($timeChanged) {
                $booking->load('student.user');
                $booking->student->user->notify(new BookingRescheduledNotification($booking));
            }
        }

        return $booking->refresh();
    }
}
