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
