<?php

namespace Database\Factories;

use App\Models\OfferModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfferPage>
 */
class OfferPageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'offer_module_id' => OfferModule::factory(),
            'title' => $this->faker->sentence(4),
            'body' => $this->faker->optional()->paragraphs(3, true),
            'video_url' => null,
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
