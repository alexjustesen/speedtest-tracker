<?php

namespace App\Listeners;

use App\Actions\Webhooks\BuildWebhookPayload;
use App\Enums\WebhookEvent;
use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Events\SpeedtestFailed;
use App\Models\Webhook;
use Illuminate\Events\Dispatcher;
use Spatie\WebhookServer\WebhookCall;

class DispatchWebhooks
{
    /**
     * Handle a speedtest lifecycle event by dispatching subscribed webhooks.
     */
    public function handle(object $event): void
    {
        // Don't dispatch webhooks for attempts that will still be retried.
        if (($event instanceof SpeedtestFailed || $event instanceof SpeedtestBenchmarkUnhealthy) && $event->attempt <= (int) config('speedtest.retry_times')) {
            return;
        }

        $webhookEvent = WebhookEvent::fromEventClass(get_class($event));

        if ($webhookEvent === null) {
            return;
        }

        $webhooks = Webhook::enabled()
            ->get()
            ->filter(fn (Webhook $webhook): bool => $webhook->subscribesTo($webhookEvent));

        if ($webhooks->isEmpty()) {
            return;
        }

        $payload = BuildWebhookPayload::run($event->result, $webhookEvent);

        foreach ($webhooks as $webhook) {
            $call = WebhookCall::create()
                ->url($webhook->url)
                ->payload($payload);

            $webhook->secret
                ? $call->useSecret($webhook->secret)
                : $call->doNotSign();

            $call->dispatch();
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<class-string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return collect(WebhookEvent::cases())
            ->mapWithKeys(fn (WebhookEvent $event): array => [$event->eventClass() => 'handle'])
            ->all();
    }
}
