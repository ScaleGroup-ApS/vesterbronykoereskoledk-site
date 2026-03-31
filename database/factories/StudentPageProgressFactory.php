<?php

namespace Database\Factories;

use App\Models\OfferPage;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentPageProgress>
 */
class StudentPageProgressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'offer_page_id' => OfferPage::factory(),
            'completed_at' => now(),
        ];
    }
}
