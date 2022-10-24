<?php

namespace App\Observers;

use App\Jobs\SendDataToInfluxDbV2;
use App\Models\Result;
use App\Settings\InfluxDbSettings;

class ResultObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    public $influxDbSettings;

    public function __construct(InfluxDbSettings $influxDbSettings)
    {
        $this->influxDbSettings = $influxDbSettings;
    }

    /**
     * Handle the Result "created" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function created(Result $result)
    {
        // Notifications


        // Send data to time series databases
        if ($this->influxDbSettings->v2_enabled) {
            SendDataToInfluxDbV2::dispatch($result);
        }
    }

    /**
     * Handle the Result "updated" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function updated(Result $result)
    {
        //
    }

    /**
     * Handle the Result "deleted" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function deleted(Result $result)
    {
        //
    }

    /**
     * Handle the Result "restored" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function restored(Result $result)
    {
        //
    }

    /**
     * Handle the Result "force deleted" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function forceDeleted(Result $result)
    {
        //
    }
}
