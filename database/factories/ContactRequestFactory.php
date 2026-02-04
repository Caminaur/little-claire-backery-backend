<?php

namespace Database\Factories;

use App\Enums\ContactRequestType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactRequest>
 */
class ContactRequestFactory extends Factory
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
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'message' => $this->faker->text(120),
            'type' => $this->faker->randomElement(ContactRequestType::cases()),
            'is_read' => $this->faker->boolean(80),
        ];
    }
}
