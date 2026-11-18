<?php

use App\Actions\Notifications\BuildNotificationMessage;
use App\Enums\NotificationEvent;
use App\Mail\NotificationMail;
use App\Models\NotificationChannel;
use App\Models\Result;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Spatie\WebhookServer\CallWebhookJob;

function sendThrough(NotificationChannel $channel, NotificationEvent $event = NotificationEvent::Completed, array $result = []): void
{
    $channel->type->driver()->send(
        $channel,
        BuildNotificationMessage::run(Result::factory()->create($result), $event),
    );
}

describe('apprise', function () {
    it('posts every service url in a single request', function () {
        Http::fake(['*' => Http::response(['success' => true])]);

        sendThrough(NotificationChannel::factory()->apprise([
            'channel_urls' => ['discord://a/b', 'ntfy://topic'],
        ])->create());

        Http::assertSent(fn (Request $request) => $request->url() === 'http://apprise:8000/notify'
            && $request['urls'] === 'discord://a/b,ntfy://topic'
            && $request['type'] === 'success'
            && $request['format'] === 'markdown'
            && str_contains($request['body'], '[View Dashboard]('.url('/admin/results').')')
            && ! $request->hasHeader('Authorization'));
    });

    it('sends tags and basic auth credentials', function () {
        Http::fake(['*' => Http::response(['success' => true])]);

        sendThrough(NotificationChannel::factory()->apprise([
            'server_url' => 'https://apprise.example.com/notify/speedtest',
            'channel_urls' => [],
            'tags' => ['alerts', 'ops'],
            'username' => 'user',
            'password' => 'secret',
        ])->create(), NotificationEvent::BenchmarkUnhealthy);

        Http::assertSent(fn (Request $request) => $request['tag'] === 'alerts,ops'
            && ! isset($request['urls'])
            && $request['type'] === 'warning'
            && $request->header('Authorization')[0] === 'Basic '.base64_encode('user:secret'));
    });

    it('throws when apprise does not accept the notification', function () {
        Http::fake(['*' => Http::response(['error' => 'No valid URLs'], 424)]);

        sendThrough(NotificationChannel::factory()->apprise()->create());
    })->throws(RuntimeException::class, 'Apprise returned HTTP 424: No valid URLs');
});

describe('mail', function () {
    it('mails every recipient separately', function () {
        Mail::fake();

        sendThrough(NotificationChannel::factory()->mail(['a@example.com', 'b@example.com'])->create(), NotificationEvent::Failed);

        Mail::assertSent(NotificationMail::class, 2);
        Mail::assertSent(NotificationMail::class, fn (NotificationMail $mail) => str_contains($mail->render(), 'class="button')
            && ! str_contains($mail->render(), 'View Dashboard'));
        Mail::assertSent(NotificationMail::class, fn (NotificationMail $mail) => $mail->hasTo('a@example.com')
            && str_starts_with($mail->notification->title, 'Speedtest Failed'));
    });
});

describe('webhook', function () {
    it('queues a webhook call with the result payload', function () {
        Bus::fake([CallWebhookJob::class]);

        $channel = NotificationChannel::factory()->webhook(secret: 'super-secret')->create();

        sendThrough($channel);

        Bus::assertDispatched(CallWebhookJob::class, fn (CallWebhookJob $job) => $job->webhookUrl === $channel->config('url')
            && $job->payload['event'] === NotificationEvent::Completed->value
            && $job->meta['notification_channel_id'] === $channel->id
            && array_key_exists('Signature', $job->headers));
    });
});
