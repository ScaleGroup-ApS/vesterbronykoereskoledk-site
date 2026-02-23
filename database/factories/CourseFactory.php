<?php

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'start_date' => fake()->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'max_students' => null,
        ];
    }

    public function past(): static
    {
        return $this->state(fn () => [
            'start_date' => fake()->dateTimeBetween('-6 months', '-1 day')->format('Y-m-d'),
        ]);
    }
}
