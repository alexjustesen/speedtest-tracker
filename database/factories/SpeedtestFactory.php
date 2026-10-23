<?php

namespace Database\Factories;

use App\Enums\ServerMode;
use App\Models\Speedtest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Speedtest>
 */
class SpeedtestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'server_mode' => ServerMode::Auto,
            'servers' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Speedtest $speedtest) {
            $speedtest->schedule()->create(Speedtest::cronScheduleAttributes('0 * * * *', enabled: true));
        });
    }

    public function withCron(string $expression): static
    {
        return $this->afterCreating(function (Speedtest $speedtest) use ($expression) {
            $speedtest->schedule->update(Speedtest::cronScheduleAttributes($expression, enabled: true));
        });
    }

    public function disabled(): static
    {
        return $this->afterCreating(function (Speedtest $speedtest) {
            $speedtest->schedule->disable();
        });
    }

    /**
     * @param  array<string>  $servers
     */
    public function prefer(array $servers): static
    {
        return $this->state([
            'server_mode' => ServerMode::Prefer,
            'servers' => $servers,
        ]);
    }
}
