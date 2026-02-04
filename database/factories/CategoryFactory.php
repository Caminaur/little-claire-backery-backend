<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(10, true),
            'image_url' => 'https://placehold.co/400x600/000000/FFFFFF/png',
            'is_visible' => $this->faker->boolean(80),
            'position' => $this->faker->numberBetween(1, 20),
        ];
    }
}
