<?php

use App\Models\User;
use Filament\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

beforeEach(function () {
    Notification::fake();
});

function testWebhookEvent(string $class, array $meta): object
{
    return new $class(
        httpVerb: 'post',
        webhookUrl: 'https://example.com/hook',
        payload: [],
        headers: [],
        meta: $meta,
        tags: [],
        attempt: 1,
        response: null,
        errorType: null,
        errorMessage: 'Connection refused',
        uuid: 'test-uuid',
        transferStats: null,
    );
}

it('notifies the user on a successful test delivery', function () {
    $user = User::factory()->create();

    event(testWebhookEvent(WebhookCallSucceededEvent::class, [
        'webhook_test' => true,
        'user_id' => $user->id,
    ]));

    Notification::assertSentTo($user, DatabaseNotification::class);
});

it('notifies the user on a failed test delivery', function () {
    $user = User::factory()->create();

    event(testWebhookEvent(FinalWebhookCallFailedEvent::class, [
        'webhook_test' => true,
        'user_id' => $user->id,
    ]));

    Notification::assertSentTo($user, DatabaseNotification::class);
});

it('ignores webhook events that are not tests', function () {
    User::factory()->create();

    event(testWebhookEvent(WebhookCallSucceededEvent::class, []));

    Notification::assertNothingSent();
});
