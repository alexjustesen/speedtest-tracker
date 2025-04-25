<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Influxdb\v2\WriteResult;
use App\Settings\DataIntegrationSettings;
use Illuminate\Events\Dispatcher;

class SpeedtestEventSubscriber
{
    /**
     * Handle speedtest failed events.
     */
    public function handleSpeedtestFailed(SpeedtestFailed $event): void {}

    /**
     * Handle speedtest completed events.
     */
    public function handleSpeedtestCompleted(SpeedtestCompleted $event): void
    {
        $settings = app(DataIntegrationSettings::class);

        if ($settings->influxdb_v2_enabled) {
            WriteResult::dispatch($event->result);
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            SpeedtestFailed::class,
            [SpeedtestEventSubscriber::class, 'handleSpeedtestFailed']
        );

        $events->listen(
            SpeedtestCompleted::class,
            [SpeedtestEventSubscriber::class, 'handleSpeedtestCompleted']
        );
    }
}
