<?php

namespace App\Listeners\Data;

use App\Events\SpeedtestCompleted;
use App\Jobs\SendDataToInfluxDbV2;
use App\Settings\InfluxDbSettings;
use Illuminate\Contracts\Queue\ShouldQueue;

class InfluxDb2Listener implements ShouldQueue
{
    public $influxDbSettings;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->influxDbSettings = new (InfluxDbSettings::class);
    }

    /**
     * Handle the event.
     */
    public function handle(SpeedtestCompleted $event): void
    {
        if ($this->influxDbSettings->v2_enabled) {
            SendDataToInfluxDbV2::dispatch($event->result, $this->influxDbSettings);
        }
    }
}
