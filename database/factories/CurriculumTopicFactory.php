<?php

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CurriculumTopic>
 */
class CurriculumTopicFactory extends Factory
{
    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'lesson_number' => $this->faker->unique()->numberBetween(1, 29),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
        ];
    }
}
