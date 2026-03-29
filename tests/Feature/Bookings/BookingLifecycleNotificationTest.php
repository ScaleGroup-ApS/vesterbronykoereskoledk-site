<?php

use App\Models\Booking;
use App\Models\Student;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\BookingRescheduledNotification;
use App\Notifications\BookingScheduledNotification;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;

test('student receives scheduled notification when booking is created', function () {
    Notification::fake();
    $admin = User::factory()->create();
    $student = Student::factory()->create();
    $instructor = User::factory()->instructor()->create();
    $vehicle = Vehicle::factory()->create();
    $starts = now()->addDays(5)->startOfHour();
    $ends = (clone $starts)->addMinutes(45);

    actingAs($admin)
        ->post(route('bookings.store'), [
            'student_id' => $student->id,
            'instructor_id' => $instructor->id,
            'vehicle_id' => $vehicle->id,
            'type' => 'driving_lesson',
            'starts_at' => $starts->toDateTimeString(),
            'ends_at' => $ends->toDateTimeString(),
        ])
        ->assertRedirect(route('bookings.index'));

    Notification::assertSentTo($student->user, BookingScheduledNotification::class);
});

test('student receives rescheduled notification when booking times change', function () {
    Notification::fake();
    $booking = Booking::factory()->create([
        'starts_at' => now()->addDays(10)->startOfHour(),
        'ends_at' => now()->addDays(10)->startOfHour()->addMinutes(45),
    ]);
    $newStart = $booking->starts_at->copy()->addDay();
    $newEnd = $booking->ends_at->copy()->addDay();

    $admin = User::factory()->create();

    actingAs($admin)
        ->patch(route('bookings.update', $booking), [
            'starts_at' => $newStart->toDateTimeString(),
            'ends_at' => $newEnd->toDateTimeString(),
        ])
        ->assertRedirect();

    Notification::assertSentTo($booking->student->user, BookingRescheduledNotification::class);
});

test('student receives cancellation notification when booking is deleted', function () {
    Notification::fake();
    $admin = User::factory()->create();
    $booking = Booking::factory()->create();

    actingAs($admin)
        ->delete(route('bookings.destroy', $booking))
        ->assertRedirect(route('bookings.index'));

    Notification::assertSentTo($booking->student->user, BookingCancelledNotification::class);
});
