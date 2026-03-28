<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bookings\UpdateBookingSkillsRequest;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;

class BookingSkillsController extends Controller
{
    public function __invoke(UpdateBookingSkillsRequest $request, Booking $booking): RedirectResponse
    {
        $booking->update(['driving_skills' => $request->validated('driving_skills')]);

        return back()->with('success', 'Færdigheder gemt.');
    }
}
