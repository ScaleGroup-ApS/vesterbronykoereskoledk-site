<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\BookingFeedback;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookingFeedback>
 */
class BookingFeedbackFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory()->completed(),
            'student_id' => Student::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional()->sentence(),
            'confidence_scores' => null,
        ];
    }
}
