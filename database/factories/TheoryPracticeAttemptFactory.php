<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\TheoryPracticeAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TheoryPracticeAttempt>
 */
class TheoryPracticeAttemptFactory extends Factory
{
    public function definition(): array
    {
        $total = 25;
        $score = fake()->numberBetween(10, $total);

        return [
            'student_id' => Student::factory(),
            'score' => $score,
            'total' => $total,
            'duration_seconds' => fake()->numberBetween(300, 1500),
            'answers' => array_map(fn () => fake()->numberBetween(0, 3), range(1, $total)),
            'question_ids' => range(1, $total),
            'attempted_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
