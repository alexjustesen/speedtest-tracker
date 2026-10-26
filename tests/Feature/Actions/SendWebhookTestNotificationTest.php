<?php

use App\Actions\Notifications\SendWebhookTestNotification;
use Illuminate\Support\Facades\Bus;
use Spatie\WebhookServer\CallWebhookJob;

it('sends the sample download and upload speeds in Mbps to every test webhook', function () {
    Bus::fake([CallWebhookJob::class]);

    SendWebhookTestNotification::run([
        ['url' => 'https://first.example.test/webhook'],
        ['url' => 'https://second.example.test/webhook'],
    ]);

    Bus::assertDispatchedSyncTimes(CallWebhookJob::class, 2);

    foreach (['https://first.example.test/webhook', 'https://second.example.test/webhook'] as $url) {
        Bus::assertDispatchedSync(CallWebhookJob::class, function (CallWebhookJob $job) use ($url) {
            return $job->webhookUrl === $url
                && $job->payload['download'] === 925.0
                && $job->payload['upload'] === 910.0;
        });
    }
});

it('does not dispatch a test webhook without recipients', function () {
    Bus::fake([CallWebhookJob::class]);

    SendWebhookTestNotification::run([]);

    Bus::assertNothingDispatched();
});
