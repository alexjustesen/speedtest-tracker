<?php

namespace Database\Factories;

use App\Enums\WebhookEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Webhook>
 */
class WebhookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->url(),
            'events' => Arr::map(
                fake()->randomElements(WebhookEvent::cases(), fake()->numberBetween(1, 3)),
                fn (WebhookEvent $event): string => $event->value,
            ),
            'enabled' => true,
            'secret' => null,
        ];
    }

    /**
     * Indicate that the webhook is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => false,
        ]);
    }
}
