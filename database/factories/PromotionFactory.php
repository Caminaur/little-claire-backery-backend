<?php

namespace Database\Factories;

use App\Enums\PromotionDiscountType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = now()->subDays(2);
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->text(50),
            'discount_type' => $this->faker->randomElement(PromotionDiscountType::cases()),
            'discount_value' => $this->faker->randomFloat(2, 0, 12),
            'starts_at' => $start,
            'ends_at' =>  $start->copy()->addWeek(),
            'is_active' => true,
        ];
    }
}
