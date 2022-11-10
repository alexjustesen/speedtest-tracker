<?php

namespace App\Observers;

use App\Jobs\SendDataToInfluxDbV2;
use App\Models\Result;
use App\Settings\InfluxDbSettings;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;

class ResultObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    public $influxDbSettings;

    public $notificationSettings;

    public $thresholdSettings;

    public function __construct()
    {
        $this->influxDbSettings = new (InfluxDbSettings::class);

        $this->notificationSettings = new (NotificationSettings::class);

        $this->thresholdSettings = new (ThresholdSettings::class);
    }

    /**
     * Handle the Result "created" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function created(Result $result)
    {
        // Send data to time series databases
        if ($this->influxDbSettings->v2_enabled) {
            SendDataToInfluxDbV2::dispatch($result, $this->influxDbSettings);
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
