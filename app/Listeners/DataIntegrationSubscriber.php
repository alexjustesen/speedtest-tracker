<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Influxdb\v2\WriteResult;
use App\Settings\DataIntegrationSettings;
use Illuminate\Events\Dispatcher;

class DataIntegrationSubscriber
{
    /**
     * Create the event listener.
     */
    public function __construct(
        public DataIntegrationSettings $settings,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($this->settings->influxdb_v2_enabled) {
            WriteResult::dispatch($event->result);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            SpeedtestCompleted::class => 'handleSpeedtestCompleted',
            SpeedtestFailed::class => 'handleSpeedtestFailed',
        ];
    }
}
