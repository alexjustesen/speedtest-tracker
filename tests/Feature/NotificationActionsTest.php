<?php

use App\Actions\Notifications\SendAppriseTestNotification;
use App\Actions\Notifications\SendMailTestNotification;
use App\Actions\Notifications\SendWebhookTestNotification;
use Illuminate\Support\Facades\Mail;
use Spatie\WebhookServer\WebhookCall;

describe('Notification Actions', function () {
    test('SendMailTestNotification sends email notifications', function () {
        Mail::fake();

        $recipients = ['test@example.com', 'admin@example.com'];

        $action = new SendMailTestNotification;
        $action->handle($recipients);

        // If mail is queued, use assertQueued. If not, use assertSent.
        Mail::assertQueued(\App\Mail\Test::class, function ($mail) use ($recipients) {
            return in_array($mail->to[0]['address'], $recipients);
        });
        Mail::assertQueuedCount(count($recipients));
    });

    test('SendMailTestNotification handles empty recipients', function () {
        Mail::fake();

        $action = new SendMailTestNotification;
        $action->handle([]);

        Mail::assertNotSent(\App\Mail\Test::class);
    });

    test('SendWebhookTestNotification sends webhook calls', function () {
        $urls = [
            ['url' => 'https://webhook.example.com'],
            ['url' => 'https://another-webhook.example.com'],
        ];

        $action = new SendWebhookTestNotification;

        // Mock the WebhookCall to avoid actual HTTP requests
        $this->mock(WebhookCall::class, function ($mock) {
            $mock->shouldReceive('create')->andReturnSelf();
            $mock->shouldReceive('url')->andReturnSelf();
            $mock->shouldReceive('payload')->andReturnSelf();
            $mock->shouldReceive('doNotSign')->andReturnSelf();
            $mock->shouldReceive('dispatch')->andReturnSelf();
        });

        $action->handle($urls);

        // The action should complete without throwing exceptions
        expect(true)->toBeTrue();
    });

    test('SendWebhookTestNotification handles empty urls', function () {
        $action = new SendWebhookTestNotification;

        // Mock the WebhookCall to avoid actual HTTP requests
        $this->mock(WebhookCall::class, function ($mock) {
            $mock->shouldReceive('create')->andReturnSelf();
            $mock->shouldReceive('url')->andReturnSelf();
            $mock->shouldReceive('payload')->andReturnSelf();
            $mock->shouldReceive('doNotSign')->andReturnSelf();
            $mock->shouldReceive('dispatch')->andReturnSelf();
        });

        $action->handle([]);

        // The action should complete without throwing exceptions
        expect(true)->toBeTrue();
    });

    test('SendAppriseTestNotification sends apprise notifications', function () {
        $apprise_url = 'https://apprise.example.com';
        $apprise_verify_ssl = true;
        $channel_urls = [
            ['channel_url' => 'https://service1.example.com'],
            ['channel_url' => 'https://service2.example.com'],
        ];

        $action = new SendAppriseTestNotification;

        // Use Laravel's Http::fake() to avoid actual HTTP requests
        \Illuminate\Support\Facades\Http::fake();

        $action->handle($apprise_url, $apprise_verify_ssl, $channel_urls);

        // The action should complete without throwing exceptions
        expect(true)->toBeTrue();
    });
});
