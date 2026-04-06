<?php

namespace Database\Factories;

use App\Models\OfferPage;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentQuizAttempt>
 */
class StudentQuizAttemptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'offer_page_id' => OfferPage::factory(),
            'answers' => [0, 1],
            'score' => 1,
            'total' => 2,
            'attempted_at' => now(),
        ];
    }
}
