<?php

namespace Database\Factories;

use App\Enums\BenchmarkMetric;
use App\Enums\BenchmarkType;
use App\Models\Benchmark;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Benchmark>
 */
class BenchmarkFactory extends Factory
{
    /**
     * Cursor used to cycle through metrics so that `metric`, which is
     * unique per row, never collides within a single test.
     */
    private static int $metricCursor = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $metrics = BenchmarkMetric::cases();

        $metric = $metrics[self::$metricCursor % count($metrics)];

        self::$metricCursor++;

        return [
            'metric' => $metric,
            'enabled' => true,
            'type' => BenchmarkType::Absolute,
            'absolute_value' => fake()->randomFloat(2, 1, 500),
            'baseline_value' => null,
            'relative_percentage' => null,
        ];
    }

    /**
     * Indicate that the benchmark is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => false,
        ]);
    }

    /**
     * Indicate that the benchmark is relative to a recorded baseline.
     */
    public function relative(float $baseline = 100, float $percentage = 80): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => BenchmarkType::Relative,
            'absolute_value' => null,
            'baseline_value' => $baseline,
            'relative_percentage' => $percentage,
        ]);
    }
}
