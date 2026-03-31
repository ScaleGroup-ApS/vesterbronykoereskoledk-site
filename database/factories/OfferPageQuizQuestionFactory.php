<?php

namespace Database\Factories;

use App\Models\OfferPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfferPageQuizQuestion>
 */
class OfferPageQuizQuestionFactory extends Factory
{
    public function definition(): array
    {
        $optionCount = $this->faker->numberBetween(2, 4);

        return [
            'offer_page_id' => OfferPage::factory(),
            'question' => $this->faker->sentence().'?',
            'options' => array_map(fn () => $this->faker->sentence(3), range(1, $optionCount)),
            'correct_option' => $this->faker->numberBetween(0, $optionCount - 1),
            'explanation' => $this->faker->optional()->sentence(),
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
