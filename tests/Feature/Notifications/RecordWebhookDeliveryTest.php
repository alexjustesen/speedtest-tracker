<?php

use App\Models\NotificationChannel;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

function webhookDeliveryEvent(string $class, array $meta): object
{
    return new $class(
        httpVerb: 'post',
        webhookUrl: 'https://example.com/hook',
        payload: [],
        headers: [],
        meta: $meta,
        tags: [],
        attempt: 3,
        response: null,
        errorType: null,
        errorMessage: 'Connection refused',
        uuid: 'test-uuid',
        transferStats: null,
    );
}

it('records a successful webhook delivery on its channel', function () {
    $channel = NotificationChannel::factory()->webhook()->create(['last_error' => 'old error']);

    event(webhookDeliveryEvent(WebhookCallSucceededEvent::class, ['notification_channel_id' => $channel->id]));

    $channel->refresh();

    expect($channel->last_sent_at)->not->toBeNull()
        ->and($channel->last_error)->toBeNull();
});

it('records a failed webhook delivery on its channel', function () {
    $channel = NotificationChannel::factory()->webhook()->create();

    event(webhookDeliveryEvent(FinalWebhookCallFailedEvent::class, ['notification_channel_id' => $channel->id]));

    $channel->refresh();

    expect($channel->isFailing())->toBeTrue()
        ->and($channel->last_error)->toBe('Connection refused');
});

it('ignores webhook calls that do not belong to a channel', function () {
    $channel = NotificationChannel::factory()->webhook()->create();

    event(webhookDeliveryEvent(FinalWebhookCallFailedEvent::class, ['webhook_test' => true]));

    expect($channel->refresh()->last_failed_at)->toBeNull();
});
