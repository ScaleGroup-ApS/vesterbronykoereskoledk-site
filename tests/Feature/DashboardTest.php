<?php

use App\Enums\EnrollmentStatus;
use App\Models\Booking;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\Student;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('guests are redirected to the login page', function () {
    get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();

    actingAs($user)->get(route('dashboard'))->assertOk();
});

test('admin dashboard includes kpis courses and enrollments', function () {
    $admin = User::factory()->create();
    Booking::factory()->create();
    Enrollment::factory()->create();

    actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->has('kpis')
            ->has('kpis.total_students')
            ->has('courses')
            ->has('enrollments', 1)
        );
});

test('instructor dashboard includes kpis and empty enrollments when none pending', function () {
    $instructor = User::factory()->instructor()->create();
    Booking::factory()->create();

    actingAs($instructor)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->has('kpis.upcoming_bookings')
            ->has('enrollments', 0)
        );
});

test('admin dashboard courses include occupancy counts', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create([
        'max_students' => 10,
        'start_at' => now()->addWeek(),
        'end_at' => now()->addWeek()->addHours(8),
    ]);
    Enrollment::factory()->create([
        'student_id' => Student::factory(),
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'status' => EnrollmentStatus::Completed,
    ]);

    actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->has('courses', 1)
            ->where('courses.0.enrollments_completed_count', 1)
            ->where('courses.0.enrollments_pending_count', 0)
            ->where('courses.0.max_students', 10)
        );
});

test('student visiting dashboard is redirected to student dashboard', function () {
    $student = User::factory()->student()->create();

    actingAs($student)
        ->get(route('dashboard'))
        ->assertRedirect(route('student.dashboard'));
});

test('booking can belong to a team', function () {
    $team = Team::factory()->create();
    $booking = Booking::factory()->create(['team_id' => $team->id]);

    expect($booking->fresh()->team)->not->toBeNull();
    expect($booking->fresh()->team->id)->toBe($team->id);
});
