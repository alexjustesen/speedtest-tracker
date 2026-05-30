<?php

use App\Enums\WebhookEvent;
use App\Events\SpeedtestCompleted;
use App\Models\Webhook;

it('casts events to an array and enabled to a boolean', function () {
    $webhook = Webhook::factory()->create([
        'events' => [WebhookEvent::Completed->value, WebhookEvent::Failed->value],
        'enabled' => true,
    ]);

    expect($webhook->refresh()->events)->toBe([WebhookEvent::Completed->value, WebhookEvent::Failed->value])
        ->and($webhook->enabled)->toBeTrue();
});

it('scopes to only enabled webhooks', function () {
    Webhook::factory()->create();
    Webhook::factory()->create();
    Webhook::factory()->disabled()->create();

    expect(Webhook::enabled()->count())->toBe(2);
});

it('reports whether it subscribes to an event', function () {
    $webhook = Webhook::factory()->create([
        'events' => [WebhookEvent::Completed->value],
    ]);

    expect($webhook->subscribesTo(WebhookEvent::Completed))->toBeTrue()
        ->and($webhook->subscribesTo(WebhookEvent::Failed))->toBeFalse();
});

it('maps every webhook event to a resolvable event class', function () {
    foreach (WebhookEvent::cases() as $case) {
        expect(class_exists($case->eventClass()))->toBeTrue()
            ->and(WebhookEvent::fromEventClass($case->eventClass()))->toBe($case);
    }
});

it('returns null for an unknown event class', function () {
    expect(WebhookEvent::fromEventClass(\stdClass::class))->toBeNull()
        ->and(WebhookEvent::fromEventClass(SpeedtestCompleted::class))->toBe(WebhookEvent::Completed);
});
