<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Result>
 */
class ResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ping' => fake()->randomFloat(2, 0, 100),
            'download' => fake()->randomNumber(),
            'upload' => fake()->randomNumber(),
            'server_id' => fake()->randomNumber(5, true),
            'server_host' => fake()->url(),
            'server_name' => fake()->word(),
            'url' => fake()->url(),
            'data' => json_encode(fake()->words()),
        ];
    }
}
