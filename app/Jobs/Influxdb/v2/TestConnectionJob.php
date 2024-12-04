<?php

namespace App\Jobs\Influxdb\v2;

use App\Actions\Influxdb\v2\BuildPointData;
use App\Models\Result;
use App\Models\User;
use App\Settings\DataIntegrationSettings;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use InfluxDB2\ApiException;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;

class TestConnectionJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $settings = app(DataIntegrationSettings::class);

        $result = Result::factory()->make();

        $client = new Client([
            'url' => $settings->influxdb_v2_url,
            'token' => $settings->influxdb_v2_token,
            'bucket' => $settings->influxdb_v2_bucket,
            'org' => $settings->influxdb_v2_org,
            'verifySSL' => $settings->influxdb_v2_verify_ssl,
            'precision' => WritePrecision::S,
        ]);

        $writeApi = $client->createWriteApi();

        $point = BuildPointData::run($result);

        try {
            $writeApi->write($point);
        } catch (ApiException $e) {
            Log::error('Writing test data to Influxdb failed.', ['output' => $e]);

            Notification::make()
                ->title('Influxdb test failed')
                ->body('Check the logs for more details.')
                ->danger()
                ->sendToDatabase($this->user);

            $writeApi->close();

            return;
        }

        $writeApi->close();

        Notification::make()
            ->title('Successfully sent test data to Influxdb')
            ->body('Test data has been sent to InfluxDB, check if the data was received.')
            ->success()
            ->sendToDatabase($this->user);
    }
}
