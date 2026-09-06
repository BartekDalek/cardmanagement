<?php

namespace Database\Factories;

use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'card_number' => fake()->unique()->numerify(str_repeat('#', 20)),
            'pin' => fake()->numerify('####'),
            'activation_date' => now()->subDay()->startOfMinute(),
            'expiration_date' => now()->addYear()->toDateString(),
            'balance' => fake()->randomFloat(2, 0, 1000),
        ];
    }
}
