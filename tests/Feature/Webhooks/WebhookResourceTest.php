<?php

use App\Enums\UserRole;
use App\Enums\WebhookEvent;
use App\Filament\Resources\Webhooks\Pages\ListWebhooks;
use App\Filament\Resources\Webhooks\WebhookResource;
use App\Models\User;
use App\Models\Webhook;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Spatie\WebhookServer\CallWebhookJob;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
});

it('lists webhooks for an admin', function () {
    $webhooks = Webhook::factory()->count(3)->create();

    actingAs($this->admin);

    Livewire::test(ListWebhooks::class)
        ->assertOk()
        ->assertCanSeeTableRecords($webhooks);
});

it('creates a webhook through the form', function () {
    actingAs($this->admin);

    Livewire::test(ListWebhooks::class)
        ->callAction('create', [
            'url' => 'https://example.com/hook',
            'events' => [WebhookEvent::Completed->value, WebhookEvent::Failed->value],
            'enabled' => true,
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas('webhooks', [
        'url' => 'https://example.com/hook',
        'enabled' => true,
    ]);
});

it('accepts HTTP and HTTPS webhook URLs', function (string $url) {
    actingAs($this->admin);

    Livewire::test(ListWebhooks::class)
        ->callAction('create', [
            'url' => $url,
            'events' => [WebhookEvent::Completed->value],
            'enabled' => true,
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas('webhooks', ['url' => $url]);
})->with([
    'HTTP container hostname' => 'http://webhook-receiver:8080/hook',
    'HTTPS loopback address' => 'https://127.0.0.1:8443/hook',
]);

it('rejects non-HTTP webhook URL protocols', function (string $url) {
    actingAs($this->admin);

    Livewire::test(ListWebhooks::class)
        ->callAction('create', [
            'url' => $url,
            'events' => [WebhookEvent::Completed->value],
            'enabled' => true,
        ])
        ->assertHasActionErrors(['url' => 'url']);
})->with([
    'FTP' => 'ftp://webhook-receiver/hook',
    'file' => 'file:///tmp/webhook',
    'Gopher' => 'gopher://webhook-receiver/hook',
]);

it('validates the webhook form', function () {
    actingAs($this->admin);

    Livewire::test(ListWebhooks::class)
        ->callAction('create', [
            'url' => 'not-a-valid-url',
            'events' => [],
        ])
        ->assertHasActionErrors([
            'url' => 'url',
            'events' => 'required',
        ]);
});

it('queues a test webhook and notifies the user', function () {
    Bus::fake();

    $webhook = Webhook::factory()->create();

    actingAs($this->admin);

    Livewire::test(ListWebhooks::class)
        ->callAction(TestAction::make('test')->table($webhook))
        ->assertNotified(__('webhooks.test_queued'));

    Bus::assertDispatchedTimes(CallWebhookJob::class, 1);
});

it('denies access to non-admin users', function () {
    actingAs(User::factory()->create(['role' => UserRole::User]));

    expect(WebhookResource::canAccess())->toBeFalse();
});
