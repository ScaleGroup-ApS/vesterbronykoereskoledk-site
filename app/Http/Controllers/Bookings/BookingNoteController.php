<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bookings\UpdateBookingNoteRequest;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;

class BookingNoteController extends Controller
{
    public function __invoke(UpdateBookingNoteRequest $request, Booking $booking): RedirectResponse
    {
        $booking->update(['instructor_note' => $request->input('instructor_note')]);

        return back()->with('success', 'Note gemt.');
    }
}
