<?php

namespace Database\Factories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
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
            'enabled' => true,
            'schedule' => '0 * * * *',
            'servers' => null,
            'blocked_servers' => null,
            'interface' => null,
            'skip_ips' => null,
        ];
    }

    public function disabled(): static
    {
        return $this->state(['enabled' => false]);
    }
}
