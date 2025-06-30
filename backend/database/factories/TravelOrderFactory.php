<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelOrder>
 */
class TravelOrderFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'requester_name' => fake()->name(),
            'destination'    => fake()->city(),
            'departure_date' => fake()
                ->dateTimeBetween('now', '+1 year')
                ->format('Y-m-d'),
            'return_date'    => fake()
                ->dateTimeBetween('+1 year', '+2 years')
                ->format('Y-m-d'),
            'price'          => fake()->randomFloat(2, 0, 1000),
            'hosting'        => fake()->company(),
            'transportation' => fake()->randomElement(['car', 'bus', 'train', 'plane']),
            'description'    => fake()->sentence(),
        ];
    }
}
