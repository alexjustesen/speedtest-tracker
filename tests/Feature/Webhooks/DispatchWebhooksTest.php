<?php

use App\Enums\WebhookEvent;
use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Models\Result;
use App\Models\Webhook;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Spatie\WebhookServer\CallWebhookJob;

beforeEach(function () {
    Bus::fake();
});

it('dispatches only enabled webhooks subscribed to the fired event', function () {
    Webhook::factory()->create(['events' => [WebhookEvent::Completed->value]]);
    Webhook::factory()->create(['events' => [WebhookEvent::Failed->value]]);
    Webhook::factory()->disabled()->create(['events' => [WebhookEvent::Completed->value]]);

    SpeedtestCompleted::dispatch(Result::factory()->create());

    Bus::assertDispatchedTimes(CallWebhookJob::class, 1);
});

it('dispatches a webhook subscribed to multiple events for each matching event', function () {
    Webhook::factory()->create([
        'events' => [WebhookEvent::Completed->value, WebhookEvent::Failed->value],
    ]);

    SpeedtestCompleted::dispatch(Result::factory()->create());

    Bus::assertDispatched(CallWebhookJob::class, function (CallWebhookJob $job) {
        return $job->webhookUrl !== null;
    });
});

it('does not dispatch when no webhook subscribes to the event', function () {
    Webhook::factory()->create(['events' => [WebhookEvent::Failed->value]]);

    SpeedtestCompleted::dispatch(Result::factory()->create());

    Bus::assertNotDispatched(CallWebhookJob::class);
});

it('dispatches a signed webhook when a secret is set', function () {
    Webhook::factory()->create([
        'events' => [WebhookEvent::Completed->value],
        'secret' => 'super-secret',
    ]);

    SpeedtestCompleted::dispatch(Result::factory()->create());

    Bus::assertDispatchedTimes(CallWebhookJob::class, 1);
});

it('does not dispatch webhooks for a failed attempt that will still be retried', function () {
    Config::set('speedtest.retry_times', 3);

    Webhook::factory()->create(['events' => [WebhookEvent::Failed->value]]);

    SpeedtestFailed::dispatch(Result::factory()->create(['scheduled' => true]), 1);

    Bus::assertNotDispatched(CallWebhookJob::class);
});

it('dispatches webhooks on the final failed attempt when retries are disabled', function () {
    Config::set('speedtest.retry_times', 0);

    Webhook::factory()->create(['events' => [WebhookEvent::Failed->value]]);

    SpeedtestFailed::dispatch(Result::factory()->create(['scheduled' => true]), 1);

    Bus::assertDispatchedTimes(CallWebhookJob::class, 1);
});

it('does not dispatch webhooks for a benchmark-unhealthy attempt that will still be retried', function () {
    Config::set('speedtest.retry_times', 3);

    Webhook::factory()->create(['events' => [WebhookEvent::BenchmarkUnhealthy->value]]);

    SpeedtestBenchmarkUnhealthy::dispatch(Result::factory()->create(['scheduled' => true]), 1);

    Bus::assertNotDispatched(CallWebhookJob::class);
});

it('dispatches webhooks on the final benchmark-unhealthy attempt when retries are disabled', function () {
    Config::set('speedtest.retry_times', 0);

    Webhook::factory()->create(['events' => [WebhookEvent::BenchmarkUnhealthy->value]]);

    SpeedtestBenchmarkUnhealthy::dispatch(Result::factory()->create(['scheduled' => true]), 1);

    Bus::assertDispatchedTimes(CallWebhookJob::class, 1);
});
