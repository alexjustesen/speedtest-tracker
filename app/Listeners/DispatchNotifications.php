<?php

namespace App\Listeners;

use App\Enums\NotificationEvent;
use App\Jobs\SendChannelNotification;
use App\Models\NotificationChannel;
use App\Models\Result;
use Illuminate\Events\Dispatcher;

class DispatchNotifications
{
    /**
     * Handle a speedtest event by queueing a delivery for each subscribed channel.
     */
    public function handle(object $event): void
    {
        $notificationEvent = NotificationEvent::fromEventClass(get_class($event));

        if ($notificationEvent === null) {
            return;
        }

        $result = $event->result;

        NotificationChannel::enabled()
            ->subscribedTo($notificationEvent)
            ->get()
            ->filter(fn (NotificationChannel $channel): bool => $this->shouldNotify($channel, $result, $notificationEvent))
            ->each(fn (NotificationChannel $channel) => SendChannelNotification::dispatch($channel, $result, $notificationEvent));
    }

    /**
     * Determine if the channel should be notified about the result.
     */
    private function shouldNotify(NotificationChannel $channel, Result $result, NotificationEvent $event): bool
    {
        if ($result->unscheduled && ! $channel->include_unscheduled) {
            return false;
        }

        if ($event === NotificationEvent::Completed) {
            return ! $this->coveredByMoreSpecificEvent($channel, $result);
        }

        return true;
    }

    /**
     * Determine if the channel gets a more specific message for this result
     * (benchmark alarm or a recovery), so "completed" would be a duplicate.
     */
    private function coveredByMoreSpecificEvent(NotificationChannel $channel, Result $result): bool
    {
        if ($result->healthy === false && $channel->subscribesTo(NotificationEvent::BenchmarkUnhealthy)) {
            return true;
        }

        if ($channel->subscribesTo(NotificationEvent::Recovered) && $result->recoveredFromFailure()) {
            return true;
        }

        return $channel->subscribesTo(NotificationEvent::BenchmarkRecovered) && $result->recoveredFromBenchmarkFailure();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<class-string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return collect(NotificationEvent::cases())
            ->mapWithKeys(fn (NotificationEvent $event): array => [$event->eventClass() => 'handle'])
            ->all();
    }
}
