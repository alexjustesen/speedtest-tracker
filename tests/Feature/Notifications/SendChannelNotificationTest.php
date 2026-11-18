<?php

use App\Enums\NotificationEvent;
use App\Jobs\SendChannelNotification;
use App\Models\NotificationChannel;
use App\Models\Result;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Spatie\WebhookServer\CallWebhookJob;

it('records a successful delivery on the channel', function () {
    Http::fake(['apprise:8000/*' => Http::response(['success' => true])]);

    $channel = NotificationChannel::factory()->apprise()->create(['last_error' => 'old error']);

    (new SendChannelNotification($channel, Result::factory()->create(), NotificationEvent::Completed))->handle();

    $channel->refresh();

    expect($channel->last_sent_at)->not->toBeNull()
        ->and($channel->last_error)->toBeNull()
        ->and($channel->isFailing())->toBeFalse();
});

it('records a failed delivery on the channel', function () {
    $channel = NotificationChannel::factory()->apprise()->create();

    $job = new SendChannelNotification($channel, Result::factory()->create(), NotificationEvent::Completed);

    $job->failed(new RuntimeException('Apprise returned HTTP 500'));

    $channel->refresh();

    expect($channel->last_failed_at)->not->toBeNull()
        ->and($channel->last_error)->toBe('Apprise returned HTTP 500')
        ->and($channel->isFailing())->toBeTrue();
});

it('leaves recording webhook deliveries to the webhook events', function () {
    Bus::fake([CallWebhookJob::class]);

    $channel = NotificationChannel::factory()->webhook()->create();

    (new SendChannelNotification($channel, Result::factory()->create(), NotificationEvent::Completed))->handle();

    Bus::assertDispatchedTimes(CallWebhookJob::class, 1);

    expect($channel->refresh()->last_sent_at)->toBeNull();
});
