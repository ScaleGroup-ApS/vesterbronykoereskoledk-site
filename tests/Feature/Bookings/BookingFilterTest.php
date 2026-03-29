<?php

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;

use function Pest\Laravel\actingAs;

test('admin can filter bookings by instructor', function () {
    $admin = User::factory()->create();
    $instructor = User::factory()->instructor()->create();
    $otherInstructor = User::factory()->instructor()->create();

    Booking::factory()->create(['instructor_id' => $instructor->id]);
    Booking::factory()->create(['instructor_id' => $otherInstructor->id]);

    actingAs($admin)
        ->get(route('bookings.index', ['instructor_id' => $instructor->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('bookings', 1)
            ->has('instructors')
            ->has('vehicles')
            ->where('filters.instructor_id', (string) $instructor->id)
        );
});

test('admin can filter bookings by vehicle', function () {
    $admin = User::factory()->create();
    $vehicle = Vehicle::factory()->create();
    $otherVehicle = Vehicle::factory()->create();

    Booking::factory()->create(['vehicle_id' => $vehicle->id]);
    Booking::factory()->create(['vehicle_id' => $otherVehicle->id]);

    actingAs($admin)
        ->get(route('bookings.index', ['vehicle_id' => $vehicle->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('bookings', 1)
            ->where('filters.vehicle_id', (string) $vehicle->id)
        );
});

test('bookings index returns instructors and vehicles for filters', function () {
    $admin = User::factory()->create();

    actingAs($admin)
        ->get(route('bookings.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('instructors')
            ->has('vehicles')
            ->has('filters')
        );
});
