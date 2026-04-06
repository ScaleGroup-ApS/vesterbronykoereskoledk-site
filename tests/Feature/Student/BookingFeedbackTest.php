<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingFeedback;
use App\Models\Student;
use App\Models\User;

it('student can submit feedback for a completed booking', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    $booking = Booking::factory()->for($student)->create([
        'status' => BookingStatus::Completed,
        'attended' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->subDay()->addHour(),
    ]);

    $this->actingAs($user)
        ->post(route('student.feedback.store', $booking), [
            'rating' => 4,
            'comment' => 'God lektion!',
            'confidence_scores' => ['parking' => 3, 'city_driving' => 4],
        ])
        ->assertRedirect();

    expect(BookingFeedback::where('booking_id', $booking->id)->count())->toBe(1);
    expect(BookingFeedback::first())
        ->rating->toBe(4)
        ->comment->toBe('God lektion!');
});

it('student cannot submit feedback for another students booking', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    $otherStudent = Student::factory()->create();
    $booking = Booking::factory()->for($otherStudent)->completed()->create();

    $this->actingAs($user)
        ->post(route('student.feedback.store', $booking), [
            'rating' => 5,
        ])
        ->assertForbidden();
});

it('student cannot submit duplicate feedback', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    $booking = Booking::factory()->for($student)->completed()->create();

    BookingFeedback::factory()->create([
        'booking_id' => $booking->id,
        'student_id' => $student->id,
    ]);

    $this->actingAs($user)
        ->post(route('student.feedback.store', $booking), [
            'rating' => 5,
        ])
        ->assertStatus(422);
});

it('feedback requires a rating between 1 and 5', function () {
    $user = User::factory()->create(['role' => 'student']);
    $student = Student::factory()->for($user)->create();

    $booking = Booking::factory()->for($student)->completed()->create();

    $this->actingAs($user)
        ->post(route('student.feedback.store', $booking), [
            'rating' => 0,
        ])
        ->assertSessionHasErrors('rating');
});
