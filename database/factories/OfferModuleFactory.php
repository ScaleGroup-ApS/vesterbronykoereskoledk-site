<?php

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfferModule>
 */
class OfferModuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'title' => $this->faker->sentence(3),
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
