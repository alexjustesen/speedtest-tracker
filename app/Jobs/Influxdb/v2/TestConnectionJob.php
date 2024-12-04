<?php

namespace App\Jobs\Influxdb\v2;

use App\Actions\Influxdb\v2\BuildPointData;
use App\Actions\Influxdb\v2\CreateClient;
use App\Models\Result;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use InfluxDB2\ApiException;

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
        $result = Result::factory()->make();

        $client = CreateClient::run();

        $writeApi = $client->createWriteApi();

        $point = BuildPointData::run($result);

        try {
            $writeApi->write($point);
        } catch (ApiException $e) {
            Log::error('Failed to write test data to Influxdb.', [
                'error' => $e->getMessage(),
            ]);

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
