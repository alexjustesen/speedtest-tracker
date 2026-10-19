<?php

namespace Database\Factories;

use App\Enums\NotificationChannelType;
use App\Enums\NotificationEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationChannel>
 */
class NotificationChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, asText: true),
            'type' => NotificationChannelType::Webhook,
            'enabled' => true,
            'config' => [
                'url' => fake()->url(),
                'secret' => null,
            ],
            'events' => [NotificationEvent::Completed->value],
            'include_unscheduled' => true,
        ];
    }

    /**
     * Indicate that the channel is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => false,
        ]);
    }

    /**
     * Subscribe the channel to the given events.
     */
    public function events(NotificationEvent ...$events): static
    {
        return $this->state(fn (array $attributes) => [
            'events' => array_map(fn (NotificationEvent $event): string => $event->value, $events),
        ]);
    }

    /**
     * Indicate that the channel is a mail channel.
     *
     * @param  array<int, string>  $recipients
     */
    public function mail(array $recipients = ['ops@example.com']): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => NotificationChannelType::Mail,
            'config' => ['recipients' => $recipients],
            'include_unscheduled' => false,
        ]);
    }

    /**
     * Indicate that the channel is an Apprise channel.
     *
     * @param  array<string, mixed>  $config
     */
    public function apprise(array $config = []): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => NotificationChannelType::Apprise,
            'config' => array_merge([
                'server_url' => 'http://apprise:8000/notify',
                'channel_urls' => ['discord://webhook-id/webhook-token'],
                'tags' => [],
                'username' => null,
                'password' => null,
                'verify_ssl' => true,
            ], $config),
            'include_unscheduled' => false,
        ]);
    }

    /**
     * Indicate that the channel is a webhook channel.
     */
    public function webhook(?string $secret = null): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => NotificationChannelType::Webhook,
            'config' => ['url' => fake()->url(), 'secret' => $secret],
            'include_unscheduled' => true,
        ]);
    }
}
