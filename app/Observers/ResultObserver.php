<?php

namespace App\Observers;

use App\Models\Result;
use App\Settings\InfluxDbSettings;
use Illuminate\Support\Facades\Log;
use InfluxDB2\Client;

class ResultObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    public $settings;

    public function __construct(InfluxDbSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Handle the Result "created" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function created(Result $result)
    {
        $influxdb = [
            'enabled' => $this->settings->v2_enabled,
            'url' => optional($this->settings)->v2_url,
            'org' => optional($this->settings)->v2_org,
            'bucket' => optional($this->settings)->v2_bucket,
            'token' => optional($this->settings)->v2_token,
        ];

        if ($influxdb['enabled'] == true) {
            $client = new Client([
                'url' => $influxdb['url'],
                'token' => $influxdb['token'],
                'bucket' => $influxdb['bucket'],
                'org' => $influxdb['org'],
                'precision' => \InfluxDB2\Model\WritePrecision::S,
            ]);

            $writeApi = $client->createWriteApi();

            $dataArray = [
                'name' => 'speedtest',
                'tags' => null,
                'fields' => $result->formatForInfluxDB2(),
                'time' => strtotime($result->created_at),
            ];

            try {
                $writeApi->write($dataArray);
            } catch (\Exception $e) {
                Log::error($e);
            }

            $writeApi->close();
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
