<?php

use App\Models\Booking;
use App\Models\Student;
use App\Models\User;
use App\Models\Vehicle;
use App\Rules\NoBookingConflict;

// ── Unit tests for NoBookingConflict rule ─────────────────────────────────────

test('rule passes when no conflict exists', function () {
    $instructor = User::factory()->instructor()->create();
    $errors = [];

    (new NoBookingConflict('instructor_id', '2026-03-01 10:00:00', '2026-03-01 11:00:00', 'conflict'))
        ->validate('instructor_id', $instructor->id, function ($msg) use (&$errors) {
            $errors[] = $msg;
        });

    expect($errors)->toBeEmpty();
});

test('rule fails on instructor conflict', function () {
    $instructor = User::factory()->instructor()->create();

    Booking::factory()->create([
        'instructor_id' => $instructor->id,
        'starts_at' => '2026-03-01 10:00:00',
        'ends_at' => '2026-03-01 11:00:00',
    ]);

    $errors = [];
    (new NoBookingConflict('instructor_id', '2026-03-01 10:30:00', '2026-03-01 11:30:00', 'Instruktøren har allerede en booking i dette tidsrum.'))
        ->validate('instructor_id', $instructor->id, function ($msg) use (&$errors) {
            $errors[] = $msg;
        });

    expect($errors)->toHaveCount(1)
        ->and($errors[0])->toContain('Instruktøren');
});

test('rule fails on student conflict', function () {
    $student = Student::factory()->create();

    Booking::factory()->create([
        'student_id' => $student->id,
        'starts_at' => '2026-03-01 10:00:00',
        'ends_at' => '2026-03-01 11:00:00',
    ]);

    $errors = [];
    (new NoBookingConflict('student_id', '2026-03-01 10:30:00', '2026-03-01 11:30:00', 'Eleven har allerede en booking i dette tidsrum.'))
        ->validate('student_id', $student->id, function ($msg) use (&$errors) {
            $errors[] = $msg;
        });

    expect($errors)->toHaveCount(1)
        ->and($errors[0])->toContain('Eleven');
});

test('rule fails on vehicle conflict', function () {
    $vehicle = Vehicle::factory()->create();

    Booking::factory()->create([
        'vehicle_id' => $vehicle->id,
        'starts_at' => '2026-03-01 10:00:00',
        'ends_at' => '2026-03-01 11:00:00',
    ]);

    $errors = [];
    (new NoBookingConflict('vehicle_id', '2026-03-01 10:30:00', '2026-03-01 11:30:00', 'Køretøjet er allerede booket i dette tidsrum.'))
        ->validate('vehicle_id', $vehicle->id, function ($msg) use (&$errors) {
            $errors[] = $msg;
        });

    expect($errors)->toHaveCount(1)
        ->and($errors[0])->toContain('Køretøjet');
});

test('rule passes when null value given (optional field)', function () {
    $errors = [];

    (new NoBookingConflict('vehicle_id', '2026-03-01 10:00:00', '2026-03-01 11:00:00', 'conflict'))
        ->validate('vehicle_id', null, function ($msg) use (&$errors) {
            $errors[] = $msg;
        });

    expect($errors)->toBeEmpty();
});

test('excludeBookingId skips the booking being updated', function () {
    $instructor = User::factory()->instructor()->create();

    $existing = Booking::factory()->create([
        'instructor_id' => $instructor->id,
        'starts_at' => '2026-03-01 10:00:00',
        'ends_at' => '2026-03-01 11:00:00',
    ]);

    $errors = [];
    (new NoBookingConflict('instructor_id', '2026-03-01 10:00:00', '2026-03-01 11:00:00', 'conflict', $existing->id))
        ->validate('instructor_id', $instructor->id, function ($msg) use (&$errors) {
            $errors[] = $msg;
        });

    expect($errors)->toBeEmpty();
});

// ── HTTP integration: store conflicts ─────────────────────────────────────────

test('booking store rejects instructor conflict via validation', function () {
    $admin = User::factory()->create();
    $student = Student::factory()->create();
    $instructor = User::factory()->instructor()->create();
    $slot = now()->addDays(5)->startOfHour();

    Booking::factory()->create([
        'instructor_id' => $instructor->id,
        'starts_at' => $slot,
        'ends_at' => $slot->copy()->addMinutes(45),
    ]);

    $this->actingAs($admin)
        ->post(route('bookings.store'), [
            'student_id' => $student->id,
            'instructor_id' => $instructor->id,
            'type' => 'driving_lesson',
            'starts_at' => $slot->copy()->addMinutes(15)->toDateTimeString(),
            'ends_at' => $slot->copy()->addMinutes(75)->toDateTimeString(),
        ])
        ->assertSessionHasErrors('instructor_id');
});

test('booking store rejects student conflict via validation', function () {
    $admin = User::factory()->create();
    $student = Student::factory()->create();
    $instructor1 = User::factory()->instructor()->create();
    $instructor2 = User::factory()->instructor()->create();
    $slot = now()->addDays(5)->startOfHour();

    Booking::factory()->create([
        'student_id' => $student->id,
        'instructor_id' => $instructor1->id,
        'starts_at' => $slot,
        'ends_at' => $slot->copy()->addMinutes(45),
    ]);

    $this->actingAs($admin)
        ->post(route('bookings.store'), [
            'student_id' => $student->id,
            'instructor_id' => $instructor2->id,
            'type' => 'driving_lesson',
            'starts_at' => $slot->copy()->addMinutes(15)->toDateTimeString(),
            'ends_at' => $slot->copy()->addMinutes(75)->toDateTimeString(),
        ])
        ->assertSessionHasErrors('student_id');
});
