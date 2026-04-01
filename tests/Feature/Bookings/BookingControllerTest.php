<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Student;
use App\Models\User;
use App\Models\Vehicle;

use function Pest\Laravel\actingAs;

test('admin can view bookings index', function () {
    $admin = User::factory()->create();

    actingAs($admin)->get(route('bookings.index'))->assertOk();
});

test('instructor can view bookings index', function () {
    $instructor = User::factory()->instructor()->create();

    actingAs($instructor)->get(route('bookings.index'))->assertOk();
});

test('student cannot view bookings index', function () {
    $student = Student::factory()->create();

    actingAs($student->user)->get(route('bookings.index'))->assertForbidden();
});

test('admin can create a booking', function () {
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

    expect(Booking::count())->toBe(1);
});

test('booking store rejects conflict', function () {
    $admin = User::factory()->create();
    $student = Student::factory()->create();
    $instructor = User::factory()->instructor()->create();
    $slotStart = now()->addDays(5)->startOfHour();
    $slotEnd = (clone $slotStart)->addMinutes(45);

    Booking::factory()->create([
        'student_id' => $student->id,
        'instructor_id' => $instructor->id,
        'starts_at' => $slotStart,
        'ends_at' => $slotEnd,
    ]);

    actingAs($admin)
        ->post(route('bookings.store'), [
            'student_id' => $student->id,
            'instructor_id' => $instructor->id,
            'type' => 'driving_lesson',
            'starts_at' => $slotStart->copy()->addMinutes(15)->toDateTimeString(),
            'ends_at' => $slotStart->copy()->addMinutes(75)->toDateTimeString(),
        ])
        ->assertSessionHasErrors(['student_id', 'instructor_id']);
});

test('admin can cancel a booking', function () {
    $admin = User::factory()->create();
    $booking = Booking::factory()->create();

    actingAs($admin)
        ->delete(route('bookings.destroy', $booking))
        ->assertRedirect(route('bookings.index'));

    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled);
});

test('admin can record attendance as present and complete booking', function () {
    $admin = User::factory()->create();
    $booking = Booking::factory()->create();

    actingAs($admin)
        ->from(route('bookings.index'))
        ->post(route('bookings.attendance.store', $booking), [
            'attended' => true,
        ])
        ->assertRedirect();

    $booking->refresh();

    expect($booking->status)->toBe(BookingStatus::Completed);
    expect($booking->attended)->toBeTrue();
    expect($booking->attendance_recorded_by)->toBe($admin->id);
});

test('admin can record attendance as absent as no-show', function () {
    $admin = User::factory()->create();
    $booking = Booking::factory()->create();

    actingAs($admin)
        ->post(route('bookings.attendance.store', $booking), [
            'attended' => false,
        ])
        ->assertRedirect();

    $booking->refresh();

    expect($booking->status)->toBe(BookingStatus::NoShow);
    expect($booking->attended)->toBeFalse();
});

test('instructor can only update own bookings', function () {
    $instructor = User::factory()->instructor()->create();
    $otherInstructor = User::factory()->instructor()->create();

    $booking = Booking::factory()->create([
        'instructor_id' => $otherInstructor->id,
    ]);

    actingAs($instructor)
        ->patch(route('bookings.update', $booking), [
            'notes' => 'test',
        ])
        ->assertForbidden();
});
