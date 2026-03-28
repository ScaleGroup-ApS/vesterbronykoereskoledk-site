<?php

namespace App\Http\Controllers\Bookings;

use App\Actions\Bookings\RecordBookingAttendance;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bookings\RecordBookingAttendanceRequest;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;

class BookingAttendanceController extends Controller
{
    public function __invoke(RecordBookingAttendanceRequest $request, Booking $booking, RecordBookingAttendance $action): RedirectResponse
    {
        $action->handle($booking, $request->user(), $request->boolean('attended'));

        return back()->with('success', 'Fremmøde registreret.');
    }
}
