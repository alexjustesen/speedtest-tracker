<?php

namespace App\Actions\InfluxDBv2;

use App\Jobs\InfluxDBv2\WriteSpeedtestResult;
use App\Models\Result;
use App\Settings\DataIntegrationSettings;
use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class TestInfluxDB
{
    use AsAction;

    /**
     * Execute the action.
     */
    public function handle(): void
    {
        // Resolve the settings within the action
        $settings = app(DataIntegrationSettings::class);

        $influxdb = [
            'enabled' => $settings->influxdb_v2_enabled,
            'url' => $settings?->influxdb_v2_url,
            'org' => $settings?->influxdb_v2_org,
            'bucket' => $settings?->influxdb_v2_bucket,
            'token' => $settings?->influxdb_v2_token,
        ];

        if ($influxdb['enabled'] == true) {
            // Create a test result if InfluxDB is enabled
            $result = Result::factory()->create();

            // Dispatch the job to write the result to InfluxDB
            dispatch(new WriteSpeedtestResult($result, $settings));
        }

        // Optional: Notification
        Notification::make()
            ->title('Success')
            ->body('A test log has been sent to InfluxDB. Check if the data is received!')
            ->success()
            ->send();
    }
}
