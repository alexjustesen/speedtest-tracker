<?php

namespace Database\Factories;

use App\Models\Speedtest;
use DirectoryTree\Cadence\Drivers\CronSchedule;
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
            'servers' => null,
            'blocked_servers' => null,
            'interface' => null,
            'skip_ips' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Speedtest $speedtest) {
            $speedtest->addSchedule(new CronSchedule('0 * * * *'));
        });
    }

    public function withCron(string $expression): static
    {
        return $this->afterCreating(function (Speedtest $speedtest) use ($expression) {
            $speedtest->cronSchedule()?->delete();
            $speedtest->unsetRelation('schedules');
            $speedtest->addSchedule(new CronSchedule($expression));
        });
    }

    public function disabled(): static
    {
        return $this->afterCreating(function (Speedtest $speedtest) {
            $speedtest->cronSchedule()?->disable();
        });
    }
}
