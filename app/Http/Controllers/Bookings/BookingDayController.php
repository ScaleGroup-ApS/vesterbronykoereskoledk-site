<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingDayController extends Controller
{
    public function __invoke(Request $request, string $date): Response
    {
        $this->authorize('viewAny', Booking::class);

        $bookings = Booking::with(['student.user', 'instructor', 'vehicle', 'team'])
            ->whereDate('starts_at', $date)
            ->orderBy('starts_at')
            ->get();

        $individualEvents = $bookings
            ->whereNull('team_id')
            ->map(fn (Booking $booking) => [
                'id' => 'booking-'.$booking->id,
                'title' => $booking->student->user->name,
                'start' => $booking->starts_at->toIso8601String(),
                'end' => $booking->ends_at->toIso8601String(),
                'type' => $booking->type->value,
                'status' => $booking->status->value,
                'team_id' => null,
                'instructor' => $booking->instructor?->name,
                'vehicle' => $booking->vehicle?->name,
                'notes' => $booking->notes,
            ]);

        $teamEvents = $bookings
            ->whereNotNull('team_id')
            ->groupBy(fn (Booking $b) => $b->team_id.'_'.$b->starts_at->toIso8601String())
            ->map(fn ($group) => $group->first())
            ->map(fn (Booking $booking) => [
                'id' => 'team-'.$booking->team_id.'-'.$booking->starts_at->timestamp,
                'title' => $booking->team->name,
                'start' => $booking->starts_at->toIso8601String(),
                'end' => $booking->ends_at->toIso8601String(),
                'type' => $booking->type->value,
                'status' => $booking->status->value,
                'team_id' => $booking->team_id,
                'instructor' => $booking->instructor?->name,
                'vehicle' => $booking->vehicle?->name,
                'notes' => $booking->notes,
            ]);

        return Inertia::render('bookings/day', [
            'date' => $date,
            'events' => collect($individualEvents->values())->merge(collect($teamEvents->values()))->values()->all(),
        ]);
    }
}
