<?php

use App\Notifications\Apprise\TestNotification;
use App\Notifications\AppriseChannel;
use App\Settings\NotificationSettings;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Http;

it('appends /notify to server URL when not present', function () {
    $settings = app(NotificationSettings::class);
    $settings->apprise_server_url = 'http://localhost:8000';
    $settings->save();

    Http::fake([
        'http://localhost:8000/notify' => Http::response(['success' => true], 200),
    ]);

    $notifiable = new AnonymousNotifiable;
    $notifiable->route('apprise_urls', ['discord://webhook-id/webhook-token']);

    $notification = new TestNotification;
    $channel = new AppriseChannel;

    $channel->send($notifiable, $notification);

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:8000/notify';
    });
});

it('does not modify URL when /notify is already present', function () {
    $settings = app(NotificationSettings::class);
    $settings->apprise_server_url = 'http://localhost:8000/notify';
    $settings->save();

    Http::fake([
        'http://localhost:8000/notify' => Http::response(['success' => true], 200),
    ]);

    $notifiable = new AnonymousNotifiable;
    $notifiable->route('apprise_urls', ['discord://webhook-id/webhook-token']);

    $notification = new TestNotification;
    $channel = new AppriseChannel;

    $channel->send($notifiable, $notification);

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:8000/notify';
    });
});

it('preserves URL with /notify in a custom path', function () {
    $settings = app(NotificationSettings::class);
    $settings->apprise_server_url = 'http://localhost:8000/config/notify';
    $settings->save();

    Http::fake([
        'http://localhost:8000/config/notify' => Http::response(['success' => true], 200),
    ]);

    $notifiable = new AnonymousNotifiable;
    $notifiable->route('apprise_urls', ['discord://webhook-id/webhook-token']);

    $notification = new TestNotification;
    $channel = new AppriseChannel;

    $channel->send($notifiable, $notification);

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:8000/config/notify';
    });
});

it('handles trailing slash in server URL correctly', function () {
    $settings = app(NotificationSettings::class);
    $settings->apprise_server_url = 'http://localhost:8000/';
    $settings->save();

    Http::fake([
        'http://localhost:8000/notify' => Http::response(['success' => true], 200),
    ]);

    $notifiable = new AnonymousNotifiable;
    $notifiable->route('apprise_urls', ['discord://webhook-id/webhook-token']);

    $notification = new TestNotification;
    $channel = new AppriseChannel;

    $channel->send($notifiable, $notification);

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:8000/notify';
    });
});

it('sends correct payload to apprise server', function () {
    $settings = app(NotificationSettings::class);
    $settings->apprise_server_url = 'http://localhost:8000';
    $settings->save();

    Http::fake([
        'http://localhost:8000/notify' => Http::response(['success' => true], 200),
    ]);

    $notifiable = new AnonymousNotifiable;
    $notifiable->route('apprise_urls', ['discord://webhook-id/webhook-token']);

    $notification = new TestNotification;
    $channel = new AppriseChannel;

    $channel->send($notifiable, $notification);

    Http::assertSent(function ($request) {
        $body = $request->data();

        return $body['urls'] === ['discord://webhook-id/webhook-token']
            && $body['title'] === 'Test Notification'
            && $body['type'] === 'info'
            && $body['format'] === 'markdown'
            && str_contains($body['body'], 'test notification');
    });
});

it('skips sending when server URL is empty', function () {
    $settings = app(NotificationSettings::class);
    $settings->apprise_server_url = '';
    $settings->save();

    Http::fake();

    $notifiable = new AnonymousNotifiable;
    $notifiable->route('apprise_urls', ['discord://webhook-id/webhook-token']);

    $notification = new TestNotification;
    $channel = new AppriseChannel;

    $channel->send($notifiable, $notification);

    Http::assertNothingSent();
});

it('disables SSL verification when configured', function () {
    $settings = app(NotificationSettings::class);
    $settings->apprise_server_url = 'https://localhost:8000';
    $settings->apprise_verify_ssl = false;
    $settings->save();

    Http::fake([
        'https://localhost:8000/notify' => Http::response(['success' => true], 200),
    ]);

    $notifiable = new AnonymousNotifiable;
    $notifiable->route('apprise_urls', ['discord://webhook-id/webhook-token']);

    $notification = new TestNotification;
    $channel = new AppriseChannel;

    $channel->send($notifiable, $notification);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://localhost:8000/notify';
    });
});

it('throws exception when server responds with failure', function () {
    $settings = app(NotificationSettings::class);
    $settings->apprise_server_url = 'http://localhost:8000';
    $settings->save();

    Http::fake([
        'http://localhost:8000/notify' => Http::response('Invalid URL scheme', 400),
    ]);

    $notifiable = new AnonymousNotifiable;
    $notifiable->route('apprise_urls', ['invalid://webhook']);

    $notification = new TestNotification;
    $channel = new AppriseChannel;

    expect(fn () => $channel->send($notifiable, $notification))
        ->toThrow(Exception::class, 'Apprise returned an error, check Apprise logs for details');
});

it('throws exception on connection error', function () {
    $settings = app(NotificationSettings::class);
    $settings->apprise_server_url = 'http://invalid-host-that-does-not-exist.local';
    $settings->save();

    Http::fake(function () {
        throw new ConnectionException('Could not resolve host');
    });

    $notifiable = new AnonymousNotifiable;
    $notifiable->route('apprise_urls', ['discord://webhook-id/webhook-token']);

    $notification = new TestNotification;
    $channel = new AppriseChannel;

    expect(fn () => $channel->send($notifiable, $notification))
        ->toThrow(ConnectionException::class);
});

it('throws exception when apprise returns 204 no content', function () {
    $settings = app(NotificationSettings::class);
    $settings->apprise_server_url = 'http://localhost:8000';
    $settings->save();

    Http::fake([
        'http://localhost:8000/notify' => Http::response('', 204),
    ]);

    $notifiable = new AnonymousNotifiable;
    $notifiable->route('apprise_urls', ['discord://webhook-id/webhook-token']);

    $notification = new TestNotification;
    $channel = new AppriseChannel;

    expect(fn () => $channel->send($notifiable, $notification))
        ->toThrow(Exception::class, 'Apprise returned an error, check Apprise logs for details');
});
